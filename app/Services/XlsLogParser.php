<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    // ── Punch assignment thresholds ───────────────────────────────────────────
    private const MORNING_CUTOFF     = '12:30'; // ≤ this → morning session
    private const DOUBLE_TAP_GRACE   = '12:45'; // solo PM punch ≤ this → MorningOut if MorningIn exists
    private const AFTERNOON_CUTOFF_IN = '14:00'; // solo PM punch ≤ this (but > double-tap) → AfternoonIn

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Scan the XLS and return ONLY employees that exist in the users table.
     *
     * Loads the spreadsheet ONCE, parses all employee blocks, then matches
     * against the DB. The spreadsheet is freed from memory before returning.
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
        $employees   = $this->parseLogsSheet($spreadsheet);

        // Free spreadsheet memory immediately — we only need the parsed array
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        Log::info('[XlsLogParser] detectEmployees — XLS employees found', [
            'count' => $employees->count(),
            'ids'   => $employees->keys()->values()->toArray(),
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
            'total'           => $allDbUsers->count(),
            'db_employee_ids' => $allDbUsers->pluck('employee_id', 'id')->toArray(),
        ]);

        // Keyed by trimmed string employee_id for PHP-side exact matching
        $dbLookup = $allDbUsers->keyBy(fn($u) => trim((string) $u->employee_id));

        // ── Match XLS device numbers to DB users ──────────────────────────────
        $matched = collect();

        foreach ($employees as $xlsNo => $xlsData) {
            $trimmedNo = trim((string) $xlsNo);
            $dbUser    = $dbLookup->get($trimmedNo);

            if (!$dbUser) {
                Log::debug('[XlsLogParser] No DB match — skipped', [
                    'xls_no'   => $xlsNo,
                    'xls_name' => $xlsData['xls_name'],
                ]);
                continue;
            }

            $matched->put($dbUser->id, [
                'user_id'     => $dbUser->id,
                'db_name'     => $dbUser->name,
                'employee_id' => trim((string) $dbUser->employee_id),
                'xls_name'    => $xlsData['xls_name'],
                'department'  => $xlsData['department'],
                'days'        => $xlsData['days'],
                'day_count'   => count($xlsData['days']),
                'punch_count' => $xlsData['punch_count'],
            ]);

            Log::info('[XlsLogParser] Matched', [
                'user_id'     => $dbUser->id,
                'db_name'     => $dbUser->name,
                'employee_id' => $dbUser->employee_id,
                'days'        => count($xlsData['days']),
                'punches'     => $xlsData['punch_count'],
            ]);
        }

        Log::info('[XlsLogParser] Match complete', [
            'matched' => $matched->count(),
            'skipped' => $employees->count() - $matched->count(),
        ]);

        return $matched->sortBy('db_name')->values()->keyBy('user_id');
    }

    /**
     * Parse the XLS ONCE and return ALL employees' DTR rows keyed by device ID.
     *
     * This is the correct method to use in the import action loop.
     * Previously, parseForEmployee() was called per employee which reloaded
     * the entire XLS file on every iteration — with 57 employees and a 575 KB
     * file this caused memory exhaustion and a white screen crash.
     *
     * Now the spreadsheet is loaded exactly once, all employee blocks are
     * parsed into a plain PHP array, and the spreadsheet is freed immediately.
     * Each per-employee lookup inside the loop is then a simple array access.
     *
     * @param  string $filePath  Absolute path to the .xls / .xlsx file
     * @param  string $period    Period string e.g. "2026/02/01 ~ 02/28"
     * @return array  [ 'device_id' => [ dtr_rows... ], ... ]
     * @throws \Exception
     */
    public function parseAllEmployees(string $filePath, string $period = ''): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees   = $this->parseLogsSheet($spreadsheet);

        // Free the spreadsheet from memory immediately after parsing —
        // buildDtrRows() works only on the already-extracted plain PHP arrays
        // so we do not need the Spreadsheet object anymore.
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $result = [];

        foreach ($employees as $xlsNo => $xlsData) {
            $result[trim((string) $xlsNo)] = $this->buildDtrRows($xlsData, $period);
        }

        return $result;
    }

    /**
     * Parse the XLS and return DTR rows for ONE specific employee.
     *
     * Kept for backwards compatibility / single-employee use cases.
     * For bulk imports use parseAllEmployees() instead.
     *
     * @param  string     $filePath    Absolute path to the .xls / .xlsx file
     * @param  int|string $employeeId  Device employee number (= users.employee_id)
     * @param  string     $period      Period string from the file header
     * @return array  DTR rows consumable by DtrCalculator::calculateFromArray()
     * @throws \Exception
     */
    public function parseForEmployee(string $filePath, int|string $employeeId, string $period = ''): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees   = $this->parseLogsSheet($spreadsheet);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $normalizedId = trim((string) $employeeId);
        $xlsData      = $employees->get($normalizedId);

        if (!$xlsData) {
            throw new \Exception("No attendance records found in XLS for Employee ID: {$employeeId}");
        }

        return $this->buildDtrRows($xlsData, $period);
    }

    /**
     * Extract the period string from the Logs sheet header (row 3, col C).
     * Returns empty string if not found.
     *
     * Example raw value: "2026/02/01 ~ 02/28\t( ATI 11 )"
     * Returns:           "2026/02/01 ~ 02/28"
     */
    public function extractPeriod(string $filePath): string
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet       = $this->getLogsSheet($spreadsheet);

        $raw = trim((string) $sheet->getCell('C3')->getValue());

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        // Strip trailing tab + department name
        $raw = preg_replace('/\t.*$/', '', $raw);

        return trim($raw);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Load a spreadsheet from an absolute file path.
     * IOFactory::load() auto-detects .xls vs .xlsx.
     *
     * @throws \Exception if the file does not exist.
     */
    private function loadSpreadsheet(string $filePath): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        if (!file_exists($filePath)) {
            throw new \Exception("XLS file not found: {$filePath}");
        }

        return IOFactory::load($filePath);
    }

    /**
     * Return the "Logs" worksheet.
     * Tries by sheet name first, falls back to sheet index 1.
     *
     * @throws \Exception if neither is found.
     */
    private function getLogsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (strtolower(trim($name)) === 'logs') {
                return $spreadsheet->getSheetByName($name);
            }
        }

        if ($spreadsheet->getSheetCount() > 1) {
            return $spreadsheet->getSheet(1);
        }

        throw new \Exception('Could not find the "Logs" sheet in the uploaded XLS file.');
    }

    /**
     * Parse the entire Logs sheet into a Collection keyed by XLS employee number.
     *
     * Each value contains:
     *   xls_no, xls_name, department, punch_count,
     *   days          (sorted array of date strings with at least 1 punch),
     *   punches_by_day (assoc: date => [HH:MM, ...])
     *
     * Strategy: read every cell into a plain 2-D PHP array first (one pass),
     * then walk that array to detect employee header rows ("No :" in col 0)
     * and parse the punch data row that immediately follows each header.
     */
    private function parseLogsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): Collection
    {
        $sheet     = $this->getLogsSheet($spreadsheet);
        $maxRow    = $sheet->getHighestRow();
        $maxCol    = $sheet->getHighestColumn();
        $maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

        // ── Read all cells into a plain PHP array (single pass) ───────────────
        // This is significantly faster than calling getCell() repeatedly inside
        // the detection loop because it avoids per-call overhead.
        $data = [];
        for ($r = 1; $r <= $maxRow; $r++) {
            $row = [];
            for ($c = 1; $c <= $maxColIdx; $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $row[]     = (string) $sheet->getCell($colLetter . $r)->getValue();
            }
            $data[] = $row;
        }

        // ── Extract year/month from the period header ─────────────────────────
        // Row 3 (index 2), col C (index 2): "2026/02/01 ~ 02/28\t( ATI 11 )"
        $periodStr       = $data[2][2] ?? '';
        [$year, $month] = $this->parseYearMonth($periodStr);

        // ── Walk rows and detect employee blocks ──────────────────────────────
        // Each block:
        //   Row i   — employee header  (col[0] === "No :")
        //   Row i+1 — punch data row   (col K = newline-separated HH:MM for day K)
        $employees = collect();
        $i         = 0;
        $total     = count($data);

        while ($i < $total) {
            $row = $data[$i];

            if (trim($row[0]) !== 'No :') {
                $i++;
                continue;
            }

            // ── Employee header row ───────────────────────────────────────────
            $xlsNo   = trim($row[2]  ?? '');
            $xlsName = trim($row[10] ?? '');
            $dept    = trim($row[20] ?? '');

            if ($xlsNo === '') {
                $i++;
                continue;
            }

            // ── Punch data row (immediately follows the header) ───────────────
            $punchRow = ($i + 1 < $total) ? $data[$i + 1] : [];

            $punchesByDay = [];
            $punchCount   = 0;

            foreach ($punchRow as $colIdx => $cellValue) {
                $cellValue = trim($cellValue);

                // Col 0 is always a label/blank; skip it
                if ($cellValue === '' || $colIdx === 0) {
                    continue;
                }

                // Column index directly maps to day-of-month (col 1 → day 1, …)
                $dayNum = $colIdx;
                if ($dayNum < 1 || $dayNum > 31) {
                    continue;
                }

                // Validate that this day actually exists in the calendar month
                try {
                    $date = Carbon::createFromDate($year, $month, $dayNum)->format('Y-m-d');
                } catch (\Exception) {
                    continue;
                }

                // Times are stored as newline-delimited "HH:MM" strings within one cell
                $times = array_values(array_filter(
                    array_map('trim', explode("\n", $cellValue)),
                    fn($t) => preg_match('/^\d{1,2}:\d{2}$/', $t)
                ));

                if (!empty($times)) {
                    $punchesByDay[$date] = $times;
                    $punchCount         += count($times);
                }
            }

            ksort($punchesByDay);

            $employees->put($xlsNo, [
                'xls_no'       => $xlsNo,
                'xls_name'     => $xlsName,
                'department'   => $dept,
                'punch_count'  => $punchCount,
                'days'         => array_keys($punchesByDay),
                'punches_by_day' => $punchesByDay,
            ]);

            // Skip the punch data row we just consumed
            $i += 2;
        }

        return $employees;
    }

    /**
     * Convert one employee's punch data into a flat array of DTR rows
     * consumable by DtrCalculator::calculateFromArray().
     *
     * Walks every calendar day in the period (including absent days) so the
     * calculator always receives a complete month-long dataset.
     * Falls back to punch-days-only if the period string cannot be parsed.
     */
    private function buildDtrRows(array $xlsData, string $period): array
    {
        $rows = [];

        [$startDate, $endDate] = $this->parsePeriodRange($period);

        if ($startDate && $endDate) {
            // ── Full period walk (preferred) ──────────────────────────────────
            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                $dateStr  = $current->format('Y-m-d');
                $punches  = $xlsData['punches_by_day'][$dateStr] ?? [];
                $sessions = $this->assignSessions($punches);

                $rows[] = [
                    'EmployeeID'   => $xlsData['xls_no'],
                    'Name'         => $xlsData['xls_name'],
                    'Date'         => $dateStr,
                    'MorningIn'    => $sessions['MorningIn'],
                    'MorningOut'   => $sessions['MorningOut'],
                    'AfternoonIn'  => $sessions['AfternoonIn'],
                    'AfternoonOut' => $sessions['AfternoonOut'],
                ];

                $current->addDay();
            }
        } else {
            // ── Fallback: only days with punches ──────────────────────────────
            foreach ($xlsData['punches_by_day'] as $dateStr => $punches) {
                $sessions = $this->assignSessions($punches);

                $rows[] = [
                    'EmployeeID'   => $xlsData['xls_no'],
                    'Name'         => $xlsData['xls_name'],
                    'Date'         => $dateStr,
                    'MorningIn'    => $sessions['MorningIn'],
                    'MorningOut'   => $sessions['MorningOut'],
                    'AfternoonIn'  => $sessions['AfternoonIn'],
                    'AfternoonOut' => $sessions['AfternoonOut'],
                ];
            }
        }

        return $rows;
    }

    /**
     * Assign a chronologically-sorted list of raw punch times to the four
     * DTR slots: MorningIn, MorningOut, AfternoonIn, AfternoonOut.
     *
     * Rules (infer mode — ZKTeco XLS carries no punch direction):
     *
     *   Morning punches (≤ 12:30):
     *     1 punch  → MorningIn only
     *     2+ punches → first = MorningIn, last = MorningOut
     *
     *   Afternoon punches (> 12:30):
     *     1 solo punch ≤ 12:45 AND MorningIn set AND MorningOut not set
     *                  → treat as MorningOut (double-tap on same reader)
     *     1 solo punch ≤ 14:00 → AfternoonIn
     *     1 solo punch > 14:00 → AfternoonOut
     *     2+ punches  → first (≤ 14:00) = AfternoonIn, last = AfternoonOut
     */
    private function assignSessions(array $times): array
    {
        $morningIn    = null;
        $morningOut   = null;
        $afternoonIn  = null;
        $afternoonOut = null;

        $cutoffMorning     = $this->toMinutes(self::MORNING_CUTOFF);
        $cutoffDoubleTap   = $this->toMinutes(self::DOUBLE_TAP_GRACE);
        $cutoffAfternoonIn = $this->toMinutes(self::AFTERNOON_CUTOFF_IN);

        $morning   = array_values(array_filter($times, fn($t) => $this->toMinutes($t) <= $cutoffMorning));
        $afternoon = array_values(array_filter($times, fn($t) => $this->toMinutes($t) > $cutoffMorning));

        // ── Morning session ───────────────────────────────────────────────────
        if (count($morning) === 1) {
            $morningIn = $morning[0];
        } elseif (count($morning) >= 2) {
            $morningIn  = $morning[0];
            $morningOut = end($morning);
        }

        // ── Afternoon session ─────────────────────────────────────────────────
        if (count($afternoon) === 1) {
            $solo     = $afternoon[0];
            $soloMins = $this->toMinutes($solo);

            if ($soloMins <= $cutoffDoubleTap && $morningIn !== null && $morningOut === null) {
                // Double-tap: employee tapped out right after morning in
                $morningOut = $solo;
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
            'MorningIn'    => $morningIn    ?? '',
            'MorningOut'   => $morningOut   ?? '',
            'AfternoonIn'  => $afternoonIn  ?? '',
            'AfternoonOut' => $afternoonOut ?? '',
        ];
    }

    /**
     * Parse year and month from a period string like "2026/02/01 ~ 02/28\t( ATI 11 )".
     * Returns [year, month] as integers; defaults to current year/month if unparseable.
     */
    private function parseYearMonth(string $period): array
    {
        if (preg_match('/(\d{4})\/(\d{2})/', $period, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        return [now()->year, now()->month];
    }

    /**
     * Parse start and end Carbon instances from a period string.
     * Returns [Carbon, Carbon] on success, [null, null] if unparseable.
     *
     * Supported format: "2026/02/01 ~ 02/28"
     */
    private function parsePeriodRange(string $period): array
    {
        if (preg_match('/(\d{4})\/(\d{2})\/(\d{2})\s*~\s*(\d{2})\/(\d{2})/', $period, $m)) {
            $year  = (int) $m[1];
            $start = Carbon::createFromDate($year, (int) $m[2], (int) $m[3]);
            $end   = Carbon::createFromDate($year, (int) $m[4], (int) $m[5]);
            return [$start, $end];
        }

        return [null, null];
    }

    /**
     * Convert "HH:MM" to total minutes since midnight for threshold comparisons.
     */
    private function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time . ':00'));
        return ($h * 60) + $m;
    }
}