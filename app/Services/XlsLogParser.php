<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

/**
 * XlsLogParser
 *
 * Reads a ZKTeco / BioTime attendance export in .xls / .xlsx format and
 * converts it into the clean per-employee DTR row format that DtrCalculator
 * expects.
 *
 * ── FILE STRUCTURE ───────────────────────────────────────────────────────────
 * The workbook contains multiple sheets:
 *   Sheet 0  "Summary"  – one row per employee, totals only (not used for DTR)
 *   Sheet 1  "Logs"     – one block per employee, punch times stored in cells
 *                         as newline-delimited strings, one column per calendar day
 *   Sheet 2+ individual attendance report sheets (3 employees each, redundant)
 *
 * We parse the "Logs" sheet because it is the most compact and reliable source.
 *
 * Logs sheet block structure (repeats every 3 rows for each employee):
 *   Row N+0  day-of-month header row  (cols 0-27 = days 1-28/29/30/31 as floats)
 *   Row N+1  employee header:  col[0]="No :", col[2]=emp_no, col[8]="Name :", col[10]=name, col[18]="Dept :", col[20]=dept
 *   Row N+2  punch data row:   each non-empty cell = newline-separated HH:MM times for that day
 *
 * Day 1 is always in col 1 (col 0 is blank/label space in the data row).
 * Day N is in col N (0-based, so day 1 → col 1, day 28 → col 27).
 *
 * ── MATCHING STRATEGY ────────────────────────────────────────────────────────
 * XLS employee number  ←→  users.employee_id
 * Comparison is done in PHP (trimmed strings), never in SQL, to avoid
 * MySQL VARCHAR vs INT coercion silently dropping matches.
 * ─────────────────────────────────────────────────────────────────────────────
 */
class XlsLogParser
{
    // ── Punch assignment thresholds (minutes) ─────────────────────────────────
    private const MORNING_CUTOFF = '12:30'; // ≤ this → morning session
    private const DOUBLE_TAP_GRACE = '12:45'; // solo PM punch ≤ this → MorningOut if MorningIn exists
    private const AFTERNOON_CUTOFF_IN = '14:00'; // solo PM punch ≤ this (but > double-tap) → AfternoonIn

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Scan the XLS and return ONLY employees that exist in the users table.
     *
     * @param  string $filePath  Absolute path to the .xls / .xlsx file
     * @return Collection  keyed by users.id, each value has keys:
     *                     user_id, db_name, employee_id (device no.), xls_name,
     *                     department, day_count, punch_count, days[]
     * @throws \Exception
     */
    public function detectEmployees(string $filePath): Collection
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees = $this->parseLogsSheet($spreadsheet);

        Log::info('[XlsLogParser] detectEmployees — XLS employees found', [
            'count' => $employees->count(),
            'ids' => $employees->keys()->values()->toArray(),
        ]);

        if ($employees->isEmpty()) {
            return collect();
        }

        // ── Load all registered employees from DB ─────────────────────────────
        $allDbUsers = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', '')
            ->get();

        Log::info('[XlsLogParser] DB employees loaded', [
            'total' => $allDbUsers->count(),
            'db_employee_ids' => $allDbUsers->pluck('employee_id', 'id')->toArray(),
        ]);

        // Keyed by trimmed string employee_id for PHP-side exact matching
        $dbLookup = $allDbUsers->keyBy(fn($u) => trim((string) $u->employee_id));

        // ── Match XLS device numbers to DB users ──────────────────────────────
        $matched = collect();

        foreach ($employees as $xlsNo => $xlsData) {
            $trimmedNo = trim((string) $xlsNo);
            $dbUser = $dbLookup->get($trimmedNo);

            if (!$dbUser) {
                Log::debug('[XlsLogParser] No DB match — skipped', [
                    'xls_no' => $xlsNo,
                    'xls_name' => $xlsData['xls_name'],
                ]);
                continue;
            }

            $matched->put($dbUser->id, [
                'user_id' => $dbUser->id,
                'db_name' => $dbUser->name,
                'employee_id' => trim((string) $dbUser->employee_id),
                'xls_name' => $xlsData['xls_name'],
                'department' => $xlsData['department'],
                'days' => $xlsData['days'],
                'day_count' => count($xlsData['days']),
                'punch_count' => $xlsData['punch_count'],
            ]);

            Log::info('[XlsLogParser] Matched', [
                'user_id' => $dbUser->id,
                'db_name' => $dbUser->name,
                'employee_id' => $dbUser->employee_id,
                'days' => count($xlsData['days']),
                'punches' => $xlsData['punch_count'],
            ]);
        }

        Log::info('[XlsLogParser] Match complete', [
            'matched' => $matched->count(),
            'skipped' => $employees->count() - $matched->count(),
        ]);

        return $matched->sortBy('db_name')->values()->keyBy('user_id');
    }

    /**
     * Parse the XLS and return DTR rows for ONE specific employee.
     *
     * @param  string     $filePath    Absolute path to the .xls / .xlsx file
     * @param  int|string $employeeId  Device employee number (= users.employee_id)
     * @param  string     $period      Period string from the file header (e.g. "2026/02/01 ~ 02/28")
     * @return array  DTR rows consumable by DtrCalculator::calculateFromArray()
     * @throws \Exception
     */
    public function parseForEmployee(string $filePath, int|string $employeeId, string $period = ''): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees = $this->parseLogsSheet($spreadsheet);

        $normalizedId = trim((string) $employeeId);
        $xlsData = $employees->get($normalizedId);

        if (!$xlsData) {
            throw new \Exception("No attendance records found in XLS for Employee ID: {$employeeId}");
        }

        return $this->buildDtrRows($xlsData, $period);
    }

    /**
     * Extract the period string from the Logs sheet header (row 2, col 2).
     * Returns empty string if not found.
     */
    public function extractPeriod(string $filePath): string
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet = $this->getLogsSheet($spreadsheet);

        $raw = trim((string) $sheet->getCell('C3')->getValue()); // col C, row 3 (1-indexed)
        // "2026/02/01 ~ 02/28\t( ATI 11 )" → extract just the date range
        $raw = preg_replace('/\t.*$/', '', $raw);

        return trim($raw);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function loadSpreadsheet(string $filePath): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        if (!file_exists($filePath)) {
            throw new \Exception("XLS file not found: {$filePath}");
        }

        return IOFactory::load($filePath);
    }

    private function getLogsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        // Try by name first
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (strtolower(trim($name)) === 'logs') {
                return $spreadsheet->getSheetByName($name);
            }
        }

        // Fallback: sheet index 1
        if ($spreadsheet->getSheetCount() > 1) {
            return $spreadsheet->getSheet(1);
        }

        throw new \Exception('Could not find the "Logs" sheet in the uploaded XLS file.');
    }

    /**
     * Parse the entire Logs sheet and return a collection keyed by XLS employee number.
     *
     * Each value:
     *   xls_no, xls_name, department, punch_count, days (sorted date strings),
     *   punches_by_day (assoc array date => [HH:MM, ...])
     */
    private function parseLogsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): Collection
    {
        $sheet = $this->getLogsSheet($spreadsheet);
        $maxRow = $sheet->getHighestRow();
        $maxCol = $sheet->getHighestColumn();
        $maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

        // Read all rows into a plain PHP array first (faster than repeated cell reads)
        // Note: getCellByColumnAndRow() was removed in PhpSpreadsheet 2.x.
        // Use getCell() with a coordinate string instead.
        $data = [];
        for ($r = 1; $r <= $maxRow; $r++) {
            $row = [];
            for ($c = 1; $c <= $maxColIdx; $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $row[] = (string) $sheet->getCell($colLetter . $r)->getValue();
            }
            $data[] = $row;
        }

        // ── Find the period and the year / month ──────────────────────────────
        // Row 3 (index 2), col 3 (index 2) typically: "2026/02/01 ~ 02/28\t( ATI 11 )"
        $periodStr = $data[2][2] ?? '';
        [$year, $month] = $this->parseYearMonth($periodStr);

        // ── Parse blocks ──────────────────────────────────────────────────────
        // Pattern: every 3 rows starting from row 4 (index 3):
        //   index N:   day-header row  (0.0 1.0 2.0 … in cols 0-27)
        //   index N+1: employee header ("No :" in col 0, name in col 10, etc.)
        //   index N+2: punch data row  (newline-separated HH:MM per day-col)
        //
        // BUT some employees have 0 punch rows (absent all month), so the
        // punch data row may itself be empty. We detect employee headers by
        // checking for "No :" in col[0].

        $employees = collect();
        $i = 0;

        while ($i < count($data)) {
            $row = $data[$i];

            // Detect employee header row
            if (trim($row[0]) === 'No :') {
                $xlsNo = trim($row[2]);
                $xlsName = trim($row[10] ?? '');
                $dept = trim($row[20] ?? '');

                if ($xlsNo === '') {
                    $i++;
                    continue;
                }

                // Next row should be the punch data row
                $punchRow = ($i + 1 < count($data)) ? $data[$i + 1] : [];

                // Build per-day punch lists
                // The day-header row just before this employee header has the day numbers.
                // But we know: col index K corresponds to day K (col 0 is blank, col 1 = day 1, …, col 28 = day 28)
                $punchesByDay = [];
                $punchCount = 0;

                foreach ($punchRow as $colIdx => $cellValue) {
                    $cellValue = trim($cellValue);
                    if ($cellValue === '' || $colIdx === 0) {
                        continue;
                    }

                    // Day number = column index (col 1 → day 1)
                    $dayNum = $colIdx;
                    if ($dayNum < 1 || $dayNum > 31) {
                        continue;
                    }

                    // Build date string; validate it's a real calendar day
                    try {
                        $date = Carbon::createFromDate($year, $month, $dayNum)->format('Y-m-d');
                    } catch (\Exception) {
                        continue;
                    }

                    // Times are newline-delimited within the cell
                    $times = array_filter(
                        array_map('trim', explode("\n", $cellValue)),
                        fn($t) => preg_match('/^\d{1,2}:\d{2}$/', $t)
                    );

                    if (!empty($times)) {
                        $punchesByDay[$date] = array_values($times);
                        $punchCount += count($times);
                    }
                }

                ksort($punchesByDay);

                $employees->put($xlsNo, [
                    'xls_no' => $xlsNo,
                    'xls_name' => $xlsName,
                    'department' => $dept,
                    'punch_count' => $punchCount,
                    'days' => array_keys($punchesByDay),
                    'punches_by_day' => $punchesByDay,
                ]);

                $i += 2; // skip the punch data row we just consumed
            } else {
                $i++;
            }
        }

        return $employees;
    }

    /**
     * Convert an employee's punch data into DTR rows for DtrCalculator.
     */
    private function buildDtrRows(array $xlsData, string $period): array
    {
        $rows = [];
        $tz = config('app.timezone', 'Asia/Manila');

        // Build the full date range from the period string so we include absent days
        [$startDate, $endDate] = $this->parsePeriodRange($period);

        // Walk every calendar day in the period
        $current = $startDate ? $startDate->copy() : null;
        $end = $endDate;

        if ($current && $end) {
            while ($current->lte($end)) {
                $dateStr = $current->format('Y-m-d');
                $punches = $xlsData['punches_by_day'][$dateStr] ?? [];

                $sessions = $this->assignSessions($punches);

                $rows[] = [
                    'EmployeeID' => $xlsData['xls_no'],
                    'Name' => $xlsData['xls_name'],
                    'Date' => $dateStr,
                    'MorningIn' => $sessions['MorningIn'],
                    'MorningOut' => $sessions['MorningOut'],
                    'AfternoonIn' => $sessions['AfternoonIn'],
                    'AfternoonOut' => $sessions['AfternoonOut'],
                ];

                $current->addDay();
            }
        } else {
            // Fallback: only days with punches
            foreach ($xlsData['punches_by_day'] as $dateStr => $punches) {
                $sessions = $this->assignSessions($punches);

                $rows[] = [
                    'EmployeeID' => $xlsData['xls_no'],
                    'Name' => $xlsData['xls_name'],
                    'Date' => $dateStr,
                    'MorningIn' => $sessions['MorningIn'],
                    'MorningOut' => $sessions['MorningOut'],
                    'AfternoonIn' => $sessions['AfternoonIn'],
                    'AfternoonOut' => $sessions['AfternoonOut'],
                ];
            }
        }

        return $rows;
    }

    /**
     * Assign a list of raw punch times (sorted chronologically) to the four
     * DTR slots: MorningIn, MorningOut, AfternoonIn, AfternoonOut.
     *
     * Logic mirrors BiometricLogParser::assignSessions() (infer mode only,
     * since ZKTeco XLS does not carry punch direction):
     *
     *   Morning  ≤ 12:30 → 1 punch = MorningIn; 2+ = first/last
     *   Afternoon > 12:30:
     *     solo ≤ 12:45 + MorningIn present + no MorningOut → MorningOut (double-tap)
     *     solo ≤ 14:00                                      → AfternoonIn
     *     solo > 14:00                                      → AfternoonOut
     *     2+   → first (≤14:00) = AfternoonIn, last = AfternoonOut
     */
    private function assignSessions(array $times): array
    {
        $morningIn = null;
        $morningOut = null;
        $afternoonIn = null;
        $afternoonOut = null;

        $cutoffMorning = $this->toMinutes(self::MORNING_CUTOFF);
        $cutoffDoubleTap = $this->toMinutes(self::DOUBLE_TAP_GRACE);
        $cutoffAfternoonIn = $this->toMinutes(self::AFTERNOON_CUTOFF_IN);

        $morning = array_values(array_filter($times, fn($t) => $this->toMinutes($t) <= $cutoffMorning));
        $afternoon = array_values(array_filter($times, fn($t) => $this->toMinutes($t) > $cutoffMorning));

        // Morning session
        if (count($morning) === 1) {
            $morningIn = $morning[0];
        } elseif (count($morning) >= 2) {
            $morningIn = $morning[0];
            $morningOut = end($morning);
        }

        // Afternoon session
        if (count($afternoon) === 1) {
            $solo = $afternoon[0];
            $soloMins = $this->toMinutes($solo);

            if ($soloMins <= $cutoffDoubleTap && $morningIn !== null && $morningOut === null) {
                $morningOut = $solo; // double-tap: same punch as morning out
            } elseif ($soloMins <= $cutoffAfternoonIn) {
                $afternoonIn = $solo;
            } else {
                $afternoonOut = $solo;
            }
        } elseif (count($afternoon) >= 2) {
            $first = $afternoon[0];
            if ($this->toMinutes($first) <= $cutoffAfternoonIn) {
                $afternoonIn = $first;
            }
            $afternoonOut = end($afternoon);
        }

        return [
            'MorningIn' => $morningIn ?? '',
            'MorningOut' => $morningOut ?? '',
            'AfternoonIn' => $afternoonIn ?? '',
            'AfternoonOut' => $afternoonOut ?? '',
        ];
    }

    /**
     * Parse the year and month from a period string like "2026/02/01 ~ 02/28\t( ATI 11 )".
     * Returns [year, month] as integers, defaulting to current year/month.
     */
    private function parseYearMonth(string $period): array
    {
        // "2026/02/01 ~ 02/28"
        if (preg_match('/(\d{4})\/(\d{2})/', $period, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }
        return [now()->year, now()->month];
    }

    /**
     * Parse start and end Carbon dates from a period string.
     * Returns [Carbon|null, Carbon|null].
     */
    private function parsePeriodRange(string $period): array
    {
        // "2026/02/01 ~ 02/28"
        if (preg_match('/(\d{4})\/(\d{2})\/(\d{2})\s*~\s*(\d{2})\/(\d{2})/', $period, $m)) {
            $year = (int) $m[1];
            $start = Carbon::createFromDate($year, (int) $m[2], (int) $m[3]);
            $end = Carbon::createFromDate($year, (int) $m[4], (int) $m[5]);
            return [$start, $end];
        }
        return [null, null];
    }

    private function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time . ':00'));
        return ($h * 60) + $m;
    }
}
