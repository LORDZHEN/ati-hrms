<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * XlsLogParser  —  corrected edition
 *
 * ══════════════════════════════════════════════════════════════════════════════
 * THREE BUGS FIXED vs. THE PREVIOUS VERSION
 * ══════════════════════════════════════════════════════════════════════════════
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ BUG 1 (CRITICAL) — Off-by-one in column → day-of-month mapping         │
 * │                                                                         │
 * │ Root cause                                                               │
 * │   The inner loop that reads the spreadsheet into a PHP array uses a     │
 * │    0-based index ($c − 1, because PHP arrays are 0-based):              │
 * │     $row[] = $sheet->getCell(colLetter($c) . $r)->getValue();           │
 * │   Afterwards the code iterates `foreach ($punchRow as $colIdx => $val)` │
 * │   and sets `$dayNum = $colIdx`.  Since the first real column            │
 * │   (PhpSpreadsheet col A / index 1) ends up at PHP array key 0,         │
 * │   $dayNum = 0 everywhere, silently mapping every day to day 0.         │
 * │                                                                         │
 * │ What the XLS actually contains                                           │
 * │   The header row reads: col A = 1, col B = 2, … col AB = 28.           │
 * │   Punch data rows: col A (key 0 in PHP) = day 1, col B (key 1) = day 2 │
 * │   etc.  So the correct mapping is:                                       │
 * │     day_of_month = php_array_key + 1                                    │
 * │                                                                         │
 * │ Visible symptom in the screenshots                                       │
 * │   Every employee's time entries appeared one row too early (e.g.        │
 * │   Feb 2 data showed up on Feb 1, Feb 3 data on Feb 2, etc.), and       │
 * │   the last working day was missing entirely.                             │
 * │                                                                         │
 * │ Fix applied                                                              │
 * │   In parseLogsSheet():                                                   │
 * │     OLD: $dayNum = $colIdx;                                              │
 * │     NEW: $dayNum = $colIdx + 1;   // col A (key 0) = day 1             │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ BUG 2 — Wrong handling of 4-tap "adjacent duplicate" case               │
 * │                                                                         │
 * │ Root cause                                                               │
 * │   The old assignSessions() had a special branch:                        │
 * │     if ($times[1] === $times[2])                                         │
 * │         → MI=$t[0], MO=$t[1], AI='', AO=$t[3]                          │
 * │   The idea was that a double-tap on the reader meant MO was recorded    │
 * │   twice and AI should be blank.                                          │
 * │                                                                         │
 * │ What BioTime actually does                                               │
 * │   Cross-referencing the Logs raw data against the per-employee DTR      │
 * │   sheets (e.g. emp 1 "1.2.3" sheet, emp 2) confirms that BioTime       │
 * │   always uses positional assignment for 4 taps regardless of duplicates:│
 * │     MI=$t[0], MO=$t[1], AI=$t[2], AO=$t[3]                             │
 * │   Evidence: ['07:19','12:50','12:50','17:12'] → MI=07:19 MO=12:50      │
 * │             AI=12:50 AO=17:12  (AI kept even though it equals MO)       │
 * │                                                                         │
 * │ Fix applied                                                              │
 * │   The adjacent-duplicate branch is removed.  N=4 is always positional. │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ BUG 3 — Wrong slot assignment for N ≥ 5 taps                            │
 * │                                                                         │
 * │ Root cause                                                               │
 * │   Old rule: MI=tap[0], MO=tap[1], AI=tap[2], AO=tap[last]              │
 * │   This assigns the second tap as MO and the third as AI regardless of   │
 * │   whether they are in the correct time zone.                             │
 * │                                                                         │
 * │ What BioTime actually does (verified from 1.2.3 sheet)                  │
 * │   Evidence case: ['06:30','12:03','12:05','12:40','16:31']              │
 * │   BioTime result: AM_in=06:30 AM_out=12:40 PM_in=12:05 PM_out=16:31   │
 * │   = MI=tap[0], AI=tap[N-3], MO=tap[N-2], AO=tap[N-1]                  │
 * │   For N=5: MI=tap[0], AI=tap[2], MO=tap[3], AO=tap[4]                  │
 * │                                                                         │
 * │ Fix applied                                                              │
 * │   N ≥ 5 now uses:                                                        │
 * │     MI = tap[0]                                                          │
 * │     AI = tap[N-3]   (second-to-second-last)                              │
 * │     MO = tap[N-2]   (second-to-last)                                    │
 * │     AO = tap[N-1]   (last)                                               │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * ── FILE STRUCTURE ───────────────────────────────────────────────────────────
 * Sheet 0  "Summary"  – totals only (not used)
 * Sheet 1  "Logs"     – one employee block per 2 rows; punch times stored in
 *                       cells as newline-delimited strings, one column per
 *                       calendar day
 *
 * Logs sheet block structure (repeats every 2 rows per employee):
 *   Row N    employee header: col[0]="No :", col[2]=emp_no, col[10]=name
 *   Row N+1  punch data:      cell value = newline-separated HH:MM times
 *                             PHP array key 0 = day 1, key 1 = day 2, …
 *
 * ── PUNCH-SLOT ASSIGNMENT RULES (corrected) ──────────────────────────────────
 *
 *  N = 0  → all empty (absence)
 *  N = 1  → MI=tap[0]
 *  N = 2  → time-zone disambiguation:
 *              Both AM  (≤ 12:30)  → MI=tap[0], MO=tap[1]
 *              Both PM  (> 12:30)  → MO=tap[0], AO=tap[1]   (PM-only shift)
 *              AM + PM             → MI=tap[0], AO=tap[1]   (full day, no mid-taps)
 *  N = 3  → positional: MI=tap[0], MO=tap[1], AI=tap[2]
 *  N = 4  → positional: MI=tap[0], MO=tap[1], AI=tap[2], AO=tap[3]
 *            (the old adjacent-duplicate exception has been removed — BioTime
 *             keeps all four slots regardless of repeated time values)
 *  N ≥ 5  → MI=tap[0], AI=tap[N-3], MO=tap[N-2], AO=tap[N-1]
 *
 * ── MATCHING STRATEGY ────────────────────────────────────────────────────────
 * XLS employee number ←→ users.employee_id  (trimmed-string comparison)
 */
class XlsLogParser
{
    // Boundary between "morning zone" and "afternoon zone" for 2-tap logic
    private const NOON_MINUTES = 750; // 12:30 in minutes

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Scan the XLS and return ONLY employees that exist in the users table.
     *
     * @param  string $filePath  Absolute path to the .xls / .xlsx file
     * @return Collection  keyed by users.id
     */
    public function detectEmployees(string $filePath): Collection
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees   = $this->parseLogsSheet($spreadsheet);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        Log::info('[XlsLogParser] detectEmployees — XLS employees found', [
            'count' => $employees->count(),
            'ids'   => $employees->keys()->values()->toArray(),
        ]);

        if ($employees->isEmpty()) {
            return collect();
        }

        $allDbUsers = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', '')
            ->get();

        Log::info('[XlsLogParser] DB employees loaded', [
            'total'           => $allDbUsers->count(),
            'db_employee_ids' => $allDbUsers->pluck('employee_id', 'id')->toArray(),
        ]);

        $dbLookup = $allDbUsers->keyBy(fn($u) => trim((string) $u->employee_id));
        $matched  = collect();

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
     * @param  string $filePath  Absolute path to the .xls / .xlsx file
     * @param  string $period    Period string e.g. "2026/02/01 ~ 02/28"
     * @return array  [ 'device_id' => [ dtr_rows... ], ... ]
     */
    public function parseAllEmployees(string $filePath, string $period = ''): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $employees   = $this->parseLogsSheet($spreadsheet);

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
     * For bulk imports use parseAllEmployees() instead.
     */
    public function parseForEmployee(string $filePath, int|string $employeeId, string $period = ''): array
    {
        $spreadsheet  = $this->loadSpreadsheet($filePath);
        $employees    = $this->parseLogsSheet($spreadsheet);

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
     * ── COLUMN → DAY MAPPING (BUG 1 FIX) ────────────────────────────────────
     * The Logs sheet day-header row contains: col A = 1, col B = 2, … col AB = 28.
     * Punch data is stored one column per calendar day, aligned with the headers.
     *
     * When PhpSpreadsheet reads the sheet into a PHP array via the inner loop:
     *   for ($c = 1; $c <= $maxColIdx; $c++) {
     *       $row[] = $sheet->getCell(col($c) . $r)->getValue();
     *   }
     * the resulting PHP array is 0-based:
     *   $row[0] = col A (PhpSpreadsheet col 1) = day 1
     *   $row[1] = col B (PhpSpreadsheet col 2) = day 2
     *   …
     *
     * Therefore the correct mapping is:
     *   day_of_month = PHP_array_index + 1
     *
     * The old code used `$dayNum = $colIdx` (i.e. 0-based index = day number),
     * which shifted every entry one day earlier than it should be.
     */
    private function parseLogsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): Collection
    {
        $sheet     = $this->getLogsSheet($spreadsheet);
        $maxRow    = $sheet->getHighestRow();
        $maxCol    = $sheet->getHighestColumn();
        $maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

        // Read all cells into a plain PHP array (single pass for speed)
        $data = [];
        for ($r = 1; $r <= $maxRow; $r++) {
            $row = [];
            for ($c = 1; $c <= $maxColIdx; $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $row[]     = (string) $sheet->getCell($colLetter . $r)->getValue();
            }
            $data[] = $row;
        }

        // Extract year/month from period header (row 3, col C = index [2][2])
        $periodStr       = $data[2][2] ?? '';
        [$year, $month] = $this->parseYearMonth($periodStr);

        $employees = collect();
        $i         = 0;
        $total     = count($data);

        while ($i < $total) {
            $row = $data[$i];

            if (trim($row[0]) !== 'No :') {
                $i++;
                continue;
            }

            // Employee header row
            $xlsNo   = trim($row[2]  ?? '');
            $xlsName = trim($row[10] ?? '');
            $dept    = trim($row[20] ?? '');

            if ($xlsNo === '') {
                $i++;
                continue;
            }

            // Punch data row immediately follows
            $punchRow = ($i + 1 < $total) ? $data[$i + 1] : [];

            $punchesByDay = [];
            $punchCount   = 0;

            foreach ($punchRow as $colIdx => $cellValue) {
                $cellValue = trim($cellValue);

                if ($cellValue === '') {
                    continue;
                }

                // ── BUG 1 FIX ────────────────────────────────────────────────
                // PHP array is 0-based; col index 0 = column A = day 1.
                // day_of_month = colIdx + 1
                // ─────────────────────────────────────────────────────────────
                $dayNum = $colIdx + 1;

                if ($dayNum < 1 || $dayNum > 31) {
                    continue;
                }

                try {
                    $date = Carbon::createFromDate($year, $month, $dayNum)->format('Y-m-d');
                } catch (\Exception) {
                    continue;
                }

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
                'xls_no'         => $xlsNo,
                'xls_name'       => $xlsName,
                'department'     => $dept,
                'punch_count'    => $punchCount,
                'days'           => array_keys($punchesByDay),
                'punches_by_day' => $punchesByDay,
            ]);

            $i += 2;
        }

        return $employees;
    }

    /**
     * Convert one employee's punch data into a flat array of DTR rows
     * consumable by DtrCalculator::calculateFromArray().
     */
    private function buildDtrRows(array $xlsData, string $period): array
    {
        $rows = [];

        [$startDate, $endDate] = $this->parsePeriodRange($period);

        if ($startDate && $endDate) {
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
            // Fallback: only days with punches
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
     * Assign a list of raw ZKTeco punch times to the four DTR slots.
     *
     * ── CORRECTED ALGORITHM ───────────────────────────────────────────────────
     *
     *   N = 0  → all empty (absence)
     *
     *   N = 1  → MorningIn only
     *
     *   N = 2  → time-zone disambiguation:
     *     Both AM  (≤ 12:30)  → MI, MO
     *     Both PM  (> 12:30)  → MO, AO          (afternoon-only shift)
     *     AM + PM  (gap)      → MI, AO           (full day, no mid-day taps)
     *
     *   N = 3  → positional: MI=tap[0], MO=tap[1], AI=tap[2]
     *
     *   N = 4  → positional: MI=tap[0], MO=tap[1], AI=tap[2], AO=tap[3]
     *     NOTE: The old "adjacent duplicate" exception (blanking AI when
     *     tap[1]==tap[2]) has been removed. BioTime keeps all four slots
     *     regardless of repeated values — verified against the 1.2.3 sheet.
     *
     *   N ≥ 5  → MI=tap[0], AI=tap[N-3], MO=tap[N-2], AO=tap[N-1]
     *     Rationale: BioTime anchors the outer taps (first = MI, last = AO)
     *     and the inner-last two become MO and AI respectively, discarding
     *     intermediate "noise" taps in the middle.
     *     Evidence: ['06:30','12:03','12:05','12:40','16:31']
     *       → MI=06:30, AI=12:05, MO=12:40, AO=16:31
     *       = tap[0], tap[2], tap[3], tap[4]
     *       = tap[0], tap[N-3], tap[N-2], tap[N-1]  (N=5)
     *
     * @param  string[] $times  Chronologically sorted HH:MM strings
     * @return array   ['MorningIn'=>..., 'MorningOut'=>..., 'AfternoonIn'=>..., 'AfternoonOut'=>...]
     */
    private function assignSessions(array $times): array
    {
        $mi = $mo = $ai = $ao = '';
        $n  = count($times);

        if ($n === 0) {
            // No punches — absence day

        } elseif ($n === 1) {
            $mi = $times[0];

        } elseif ($n === 2) {
            $m0 = $this->toMinutes($times[0]);
            $m1 = $this->toMinutes($times[1]);

            if ($m0 <= self::NOON_MINUTES && $m1 <= self::NOON_MINUTES) {
                // Both morning: MI + MO
                $mi = $times[0];
                $mo = $times[1];
            } elseif ($m0 > self::NOON_MINUTES && $m1 > self::NOON_MINUTES) {
                // Both afternoon: MO + AO (employee only present in PM)
                $mo = $times[0];
                $ao = $times[1];
            } else {
                // One AM + one PM: MI + AO (full day, no mid-day taps recorded)
                $mi = $times[0];
                $ao = $times[1];
            }

        } elseif ($n === 3) {
            // Positional: MI, MO, AI
            $mi = $times[0];
            $mo = $times[1];
            $ai = $times[2];

        } elseif ($n === 4) {
            // ── BUG 2 FIX ─────────────────────────────────────────────────────
            // Always positional for 4 taps.
            // The old adjacent-duplicate exception blanked AI when tap[1]==tap[2],
            // but BioTime keeps all four slots (verified from 1.2.3 reference sheet).
            // ──────────────────────────────────────────────────────────────────
            $mi = $times[0];
            $mo = $times[1];
            $ai = $times[2];
            $ao = $times[3];

        } else {
            // N ≥ 5
            // ── BUG 3 FIX ─────────────────────────────────────────────────────
            // Old rule: MI=tap[0], MO=tap[1], AI=tap[2], AO=tap[last]
            //   → wrong: MO was assigned to tap[1] which may be a noon-area tap,
            //     not the true end-of-morning punch.
            // Correct rule (verified against BioTime 1.2.3 reference sheet):
            //   MI = tap[0]         (first punch = arrival)
            //   AI = tap[N-3]       (second-to-second-last = afternoon arrival)
            //   MO = tap[N-2]       (second-to-last = end of morning)
            //   AO = tap[N-1]       (last punch = end of day)
            // Evidence: N=5 ['06:30','12:03','12:05','12:40','16:31']
            //   → MI=tap[0]=06:30, AI=tap[2]=12:05, MO=tap[3]=12:40, AO=tap[4]=16:31
            // ──────────────────────────────────────────────────────────────────
            $mi = $times[0];
            $ai = $times[$n - 3];
            $mo = $times[$n - 2];
            $ao = $times[$n - 1];
        }

        return [
            'MorningIn'    => $mi,
            'MorningOut'   => $mo,
            'AfternoonIn'  => $ai,
            'AfternoonOut' => $ao,
        ];
    }

    /**
     * Parse year and month from a period string like "2026/02/01 ~ 02/28".
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
     * Convert "HH:MM" to total minutes since midnight.
     */
    private function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time . ':00'));
        return ($h * 60) + $m;
    }
}
