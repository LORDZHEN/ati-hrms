<?php

namespace App\Services;

use App\Models\BiometricEmployeeMapping;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * XlsLogParser — with biometric mapping support
 *
 * ══════════════════════════════════════════════════════════════════════════════
 * CHANGE LOG vs. PREVIOUS VERSION
 * ══════════════════════════════════════════════════════════════════════════════
 *
 * ONLY ONE METHOD WAS MODIFIED: detectEmployees()
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ NEW — Hybrid lookup in detectEmployees()                                │
 * │                                                                         │
 * │ Problem                                                                  │
 * │   The XLS file uses simple device enrollment numbers (1, 2, 3…).       │
 * │   ATI stores government plantilla IDs in users.employee_id              │
 * │   (e.g. "OSEC-DAB-AGR-252-1998"). Direct comparison always fails.       │
 * │                                                                         │
 * │ Solution                                                                 │
 * │   1. PRIMARY: query biometric_employee_mappings (is_active=true)        │
 * │      keyed by device_id. If a mapping exists → use that user.           │
 * │   2. FALLBACK: compare against users.employee_id directly (old logic).  │
 * │      Preserved unchanged so existing setups using numeric employee_id   │
 * │      values continue to work without any admin intervention.            │
 * │   3. UNMATCHED: emit a Log::warning with device_id for debugging.       │
 * │                                                                         │
 * │ What did NOT change                                                      │
 * │   • parseLogsSheet()      — unchanged                                   │
 * │   • buildDtrRows()        — unchanged                                   │
 * │   • assignSessions()      — unchanged (all 3 bug fixes retained)        │
 * │   • parseAllEmployees()   — unchanged                                   │
 * │   • parseForEmployee()    — unchanged                                   │
 * │   • extractPeriod()       — unchanged                                   │
 * │   • All private helpers   — unchanged                                   │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * ── REAL-WORLD DATA CONFIRMED FROM 001_2026_2_MON.xls ────────────────────────
 * The uploaded XLS confirms:
 *   Device No 1  → JENCARNACION  (REGULAR)
 *   Device No 2  → R RUBIS       (REGULAR)
 *   Device No 42 → OB DALAM      (REGULAR)
 *   Device No 4  → M CASTRO      (JOB ORDER)
 *   ... and 51 more employees with numeric device IDs 1–96
 *
 * None of these numeric IDs will ever match a plantilla ID like
 * "OSEC-DAB-AGR-252-1998" — hence this mapping table is required.
 *
 * ── PUNCH-SLOT ASSIGNMENT RULES (unchanged) ──────────────────────────────────
 *  N = 0  → all empty (absence)
 *  N = 1  → MI=tap[0]
 *  N = 2  → time-zone disambiguation (AM/PM/mixed)
 *  N = 3  → positional: MI, MO, AI
 *  N = 4  → positional: MI, MO, AI, AO
 *  N ≥ 5  → MI=tap[0], AI=tap[N-3], MO=tap[N-2], AO=tap[N-1]
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
     * ── LOOKUP ORDER ──────────────────────────────────────────────────────────
     * 1. biometric_employee_mappings (is_active=true) — keyed by device_id
     * 2. users.employee_id direct match (original fallback — DO NOT REMOVE)
     *
     * This hybrid approach means:
     * • Admins who have set up mappings get correct matching immediately.
     * • Existing setups where users.employee_id happens to store the biometric
     *   number (numeric) continue to work without any migration effort.
     * • Unmatched device IDs are logged as warnings — never silently dropped.
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

        // ── PRIMARY: load all active mappings keyed by device_id ──────────────
        // Loaded once, outside the foreach, to avoid N+1 queries.
        $mappingLookup = BiometricEmployeeMapping::active()
            ->with('user')
            ->get()
            ->keyBy(fn($m) => trim((string) $m->device_id));

        // ── FALLBACK: load DB users keyed by employee_id (original logic) ─────
        // Kept intact so existing numeric employee_id setups are unaffected.
        $allDbUsers = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', '')
            ->get();

        Log::info('[XlsLogParser] DB employees loaded', [
            'total'            => $allDbUsers->count(),
            'active_mappings'  => $mappingLookup->count(),
            'db_employee_ids'  => $allDbUsers->pluck('employee_id', 'id')->toArray(),
        ]);

        $dbLookup = $allDbUsers->keyBy(fn($u) => trim((string) $u->employee_id));
        $matched  = collect();

        foreach ($employees as $xlsNo => $xlsData) {
            $trimmedNo = trim((string) $xlsNo);
            $dbUser    = null;
            $source    = null;

            // ── Step 1: Try the mapping table (primary) ───────────────────────
            $mapping = $mappingLookup->get($trimmedNo);

            if ($mapping && $mapping->user) {
                $dbUser = $mapping->user;
                $source = 'mapping_table';

                Log::info('[XlsLogParser] Matched via mapping table', [
                    'device_id'   => $trimmedNo,
                    'xls_name'    => $xlsData['xls_name'],
                    'user_id'     => $dbUser->id,
                    'db_name'     => $dbUser->name,
                    'device_name' => $mapping->device_name,
                ]);
            }

            // ── Step 2: Fallback to users.employee_id (original logic) ────────
            // DO NOT REMOVE — backward compatibility for setups where
            // users.employee_id already stores the biometric device number.
            if (!$dbUser) {
                $dbUser = $dbLookup->get($trimmedNo);
                $source = $dbUser ? 'employee_id_fallback' : null;

                if ($dbUser) {
                    Log::info('[XlsLogParser] Matched via employee_id fallback', [
                        'device_id' => $trimmedNo,
                        'xls_name'  => $xlsData['xls_name'],
                        'user_id'   => $dbUser->id,
                        'db_name'   => $dbUser->name,
                    ]);
                }
            }

            // ── Step 3: No match found — log warning, skip ───────────────────
            if (!$dbUser) {
                Log::warning('[DTR] No employee match for device ID — skipped', [
                    'device_id' => $trimmedNo,
                    'xls_name'  => $xlsData['xls_name'],
                    'hint'      => 'Add a mapping in System → Biometric Mappings, or set users.employee_id to this device ID.',
                ]);
                continue;
            }

            $matched->put($dbUser->id, [
                'user_id'      => $dbUser->id,
                'db_name'      => $dbUser->name,
                'employee_id'  => trim((string) $dbUser->employee_id),
                'xls_name'     => $xlsData['xls_name'],
                'department'   => $xlsData['department'],
                'days'         => $xlsData['days'],
                'day_count'    => count($xlsData['days']),
                'punch_count'  => $xlsData['punch_count'],
                'match_source' => $source, // 'mapping_table' | 'employee_id_fallback'
            ]);
        }

        Log::info('[XlsLogParser] Match complete', [
            'matched'            => $matched->count(),
            'skipped'            => $employees->count() - $matched->count(),
            'via_mapping'        => $matched->where('match_source', 'mapping_table')->count(),
            'via_fallback'       => $matched->where('match_source', 'employee_id_fallback')->count(),
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
    // Private helpers (ALL UNCHANGED from previous version)
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
     * ── COLUMN → DAY MAPPING (BUG 1 FIX — retained) ──────────────────────────
     * PHP array key 0 = column A = day 1 → day_of_month = colIdx + 1
     *
     * Confirmed against 001_2026_2_MON.xls:
     *   Row 3 header: col[0]=1.0, col[1]=2.0, ... col[27]=28.0
     *   Row 4 is employee header (No :, emp_no at col[2], name at col[10])
     *   Row 5 is punch data (newline-separated HH:MM per cell)
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
            // Confirmed layout from real XLS: col[2]=device_no, col[10]=name, col[20]=dept
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

                // BUG 1 FIX: PHP array is 0-based; col index 0 = column A = day 1.
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
     * Assign punch times to DTR slots.
     * Algorithm unchanged — all three bug fixes retained.
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
                $mi = $times[0];
                $mo = $times[1];
            } elseif ($m0 > self::NOON_MINUTES && $m1 > self::NOON_MINUTES) {
                // BUG 3 FIX: both PM → AI + AO, not MO + AO
                $ai = $times[0];
                $ao = $times[1];
            } else {
                $mi = $times[0];
                $ao = $times[1];
            }

        } elseif ($n === 3) {
            $mi = $times[0];
            $mo = $times[1];
            $ai = $times[2];

        } elseif ($n === 4) {
            // BUG 2 FIX: always positional, no adjacent-duplicate exception
            $mi = $times[0];
            $mo = $times[1];
            $ai = $times[2];
            $ao = $times[3];

        } else {
            // N ≥ 5 — BUG 3 FIX
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
     * Parse year and month from "2026/02/01 ~ 02/28".
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
     */
    private function parsePeriodRange(string $period): array
    {
        if (preg_match('/(\d{4})\/(\d{2})\/(\d{2})\s*~\s*(\d{2})\/(\d{2})/', $period, $m)) {
            $year  = (int) $m[1];
            $start = Carbon::createFromDate($year, (int) $m[2], (int) $m[3]);
            $end   = Carbon::createFromDate($year, (int) $m[4], (int) $m[5]);

            // Cross-year period guard
            if ($end->lt($start)) {
                $end->addYear();
            }

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
