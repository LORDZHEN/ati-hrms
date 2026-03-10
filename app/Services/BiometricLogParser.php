<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

/**
 * BiometricLogParser
 *
 * Reads a raw biometric attendance machine export (multi-employee, punch-event format)
 * and converts it into the clean per-employee DTR row format that DtrCalculator expects.
 *
 * Raw input columns: EmployeeID, EmployeeName, LogDate, LogTime, Timestamp, Device, LogType
 *
 * ── MATCHING STRATEGY ────────────────────────────────────────────────────────
 * CSV EmployeeID  ←→  users.employee_id
 *
 * Both sides are cast to trimmed strings and compared in PHP — NOT in SQL.
 *
 * WHY NOT SQL whereIn/whereRaw?
 *   The users.employee_id column is VARCHAR. When a whereIn() receives a list
 *   that contains numeric-looking strings ("12345", "1", "2"...) MySQL may
 *   silently coerce the VARCHAR column to INT for comparison, causing "12345"
 *   stored as VARCHAR to fail matching against the string "12345" from the CSV.
 *   PHP string === string comparison is always exact and never has this issue.
 * ─────────────────────────────────────────────────────────────────────────────
 */
class BiometricLogParser
{
    private const MORNING_CUTOFF = '12:30';
    private const DOUBLE_TAP_GRACE = '12:45';
    private const AFTERNOON_CUTOFF_IN = '14:00';

    private const COL_ALIASES = [
        'employee_id' => ['EmployeeID', 'employeeid', 'CredentialID', 'credentialid', 'ID', 'id'],
        'employee_name' => ['EmployeeName', 'employeename', 'Name', 'name', 'Employee Name'],
        'log_date' => ['LogDate', 'logdate', 'Date', 'date'],
        'log_time' => ['LogTime', 'logtime', 'Time', 'time'],
        'timestamp' => ['Timestamp', 'timestamp', 'DateTime', 'datetime'],
        'punch_type' => ['LogType', 'logtype', 'PunchType', 'punchtype', 'Type', 'type'],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Scan the CSV and return ONLY employees that exist in the users table.
     *
     * @param  string $filePath  Absolute path to the raw biometric CSV
     * @return Collection  Keyed by users.id
     */
    public function detectEmployees(string $filePath): Collection
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Biometric log file not found: {$filePath}");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $colMap = $this->buildColumnMap($csv->getHeader());

        Log::info('[BiometricLogParser] detectEmployees — column map', ['map' => $colMap]);

        // ── STEP 1: Collect all unique IDs + stats from the CSV ───────────────
        $csvEmployees = collect();

        foreach ($csv->getRecords() as $row) {
            $row = $this->sanitizeRow($row);
            $csvId = $this->col($row, $colMap, 'employee_id');
            $csvName = $this->col($row, $colMap, 'employee_name');
            $date = $this->col($row, $colMap, 'log_date');

            if ($csvId === '' || $date === '') {
                continue;
            }

            if (!$csvEmployees->has($csvId)) {
                $csvEmployees->put($csvId, [
                    'csv_id' => $csvId,
                    'csv_name' => $csvName,
                    'days' => collect(),
                    'punch_count' => 0,
                ]);
            }

            $entry = $csvEmployees->get($csvId);
            $entry['days']->push($date);
            $entry['punch_count']++;
            $csvEmployees->put($csvId, $entry);
        }

        Log::info('[BiometricLogParser] CSV IDs found', [
            'count' => $csvEmployees->count(),
            'csv_ids' => $csvEmployees->keys()->values()->toArray(),
        ]);

        if ($csvEmployees->isEmpty()) {
            Log::warning('[BiometricLogParser] No rows parsed — check column mapping or file encoding.');
            return collect();
        }

        // ── STEP 2: Load ALL registered employees from DB ─────────────────────
        // Load everything into PHP memory, then do string comparison there.
        // This completely bypasses the MySQL VARCHAR vs INT coercion bug.
        $allDbEmployees = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', '')
            ->get();

        Log::info('[BiometricLogParser] DB employees loaded', [
            'total' => $allDbEmployees->count(),
            'db_employee_ids' => $allDbEmployees->pluck('employee_id', 'id')->toArray(),
        ]);

        // Build lookup keyed by trimmed string employee_id
        $dbLookup = $allDbEmployees->keyBy(fn($u) => trim((string) $u->employee_id));

        // ── STEP 3: PHP-side match — no SQL type coercion possible ────────────
        $matched = collect();

        foreach ($csvEmployees as $csvId => $csvData) {
            $trimmedCsvId = trim((string) $csvId);
            $dbUser = $dbLookup->get($trimmedCsvId);

            if (!$dbUser) {
                Log::debug('[BiometricLogParser] No DB match — skipped', [
                    'csv_id' => $csvId,
                    'csv_name' => $csvData['csv_name'],
                ]);
                continue;
            }

            $uniqueDays = $csvData['days']->unique()->sort()->values();

            $matched->put($dbUser->id, [
                'user_id' => $dbUser->id,
                'db_name' => $dbUser->name,
                'employee_id' => trim((string) $dbUser->employee_id),
                'csv_name' => $csvData['csv_name'],
                'days' => $uniqueDays->toArray(),
                'day_count' => $uniqueDays->count(),
                'punch_count' => $csvData['punch_count'],
            ]);

            Log::info('[BiometricLogParser] Matched', [
                'user_id' => $dbUser->id,
                'db_name' => $dbUser->name,
                'employee_id' => $dbUser->employee_id,
                'days' => $uniqueDays->count(),
                'punches' => $csvData['punch_count'],
            ]);
        }

        Log::info('[BiometricLogParser] Match complete', [
            'matched' => $matched->count(),
            'skipped' => $csvEmployees->count() - $matched->count(),
        ]);

        return $matched->sortBy('db_name')->values()->keyBy('user_id');
    }

    /**
     * Parse the raw CSV and return DTR rows for ONE specific employee.
     *
     * @param  string     $filePath   Absolute path to the raw biometric CSV
     * @param  int|string $employeeId The employee_id from users table (= CSV EmployeeID)
     * @return array  DTR rows for DtrCalculator::calculateFromArray()
     * @throws \Exception
     */
    public function parseForEmployee(string $filePath, int|string $employeeId): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Biometric log file not found: {$filePath}");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $colMap = $this->buildColumnMap($csv->getHeader());

        $normalizedId = trim((string) $employeeId);
        $punchesByDay = $this->groupPunchesByDay($csv, $colMap, $normalizedId);

        if ($punchesByDay->isEmpty()) {
            throw new \Exception("No attendance records found for Employee ID: {$employeeId}");
        }

        return $this->buildDtrRows($punchesByDay, $normalizedId);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildColumnMap(array $headers): array
    {
        $map = [];
        $headerLower = array_map('strtolower', array_map('trim', $headers));

        foreach (self::COL_ALIASES as $internalKey => $aliases) {
            foreach ($aliases as $alias) {
                $pos = array_search(strtolower(trim($alias)), $headerLower, true);
                if ($pos !== false) {
                    $map[$internalKey] = $headers[$pos];
                    break;
                }
            }
        }

        return $map;
    }

    private function col(array $row, array $colMap, string $key): string
    {
        $header = $colMap[$key] ?? null;
        if ($header === null) {
            return '';
        }
        return trim($row[$header] ?? '');
    }

    private function groupPunchesByDay(Reader $csv, array $colMap, string $employeeId): Collection
    {
        $byDay = collect();
        $timezone = config('app.timezone', 'Asia/Manila');

        foreach ($csv->getRecords() as $row) {
            $row = $this->sanitizeRow($row);

            if (trim((string) $this->col($row, $colMap, 'employee_id')) !== $employeeId) {
                continue;
            }

            $dateStr = $this->col($row, $colMap, 'log_date');
            $timeStr = $this->col($row, $colMap, 'log_time');
            $tsStr = $this->col($row, $colMap, 'timestamp');
            $punchRaw = $this->col($row, $colMap, 'punch_type');
            $empName = $this->col($row, $colMap, 'employee_name');

            if ($dateStr === '') {
                continue;
            }

            $rawTs = $tsStr !== ''
                ? $tsStr
                : ($dateStr . ' ' . ($timeStr !== '' ? $timeStr : '00:00:00'));

            try {
                $dt = null;
                foreach (['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'] as $fmt) {
                    try {
                        $dt = Carbon::createFromFormat($fmt, $rawTs, $timezone);
                        break;
                    } catch (\Exception) {
                    }
                }
                if ($dt === null) {
                    $dt = Carbon::parse($rawTs, $timezone);
                }
            } catch (\Exception) {
                continue;
            }

            if (!$byDay->has($dateStr)) {
                $byDay->put($dateStr, collect());
            }

            $byDay->get($dateStr)->push([
                'datetime' => $dt,
                'time' => $dt->format('H:i'),
                'punch_type' => strtoupper(trim($punchRaw)),
                'name' => $empName,
            ]);
        }

        return $byDay
            ->map(fn($punches) => $punches->sortBy('datetime')->values())
            ->sortKeys();
    }

    private function buildDtrRows(Collection $punchesByDay, string $employeeId): array
    {
        $rows = [];
        $employeeName = null;

        foreach ($punchesByDay as $dateStr => $punches) {
            if ($employeeName === null && $punches->isNotEmpty()) {
                $employeeName = $punches->first()['name'];
            }

            $sessions = $this->assignSessions($punches);

            $rows[] = [
                'EmployeeID' => $employeeId,
                'Name' => $employeeName ?? '',
                'Date' => $dateStr,
                'MorningIn' => $sessions['MorningIn'],
                'MorningOut' => $sessions['MorningOut'],
                'AfternoonIn' => $sessions['AfternoonIn'],
                'AfternoonOut' => $sessions['AfternoonOut'],
            ];
        }

        return $rows;
    }

    /**
     * Assign punch events to MorningIn/Out and AfternoonIn/Out slots.
     *
     * Infer mode (LogType empty — typical machine export):
     *   Morning  ≤ 12:30 → 1 punch = MorningIn; 2+ = first/last
     *   Afternoon > 12:30:
     *     solo ≤ 12:45 with morning In but no Out → double-tap MorningOut
     *     solo ≤ 14:00                            → AfternoonIn
     *     solo > 14:00                            → AfternoonOut
     *     2+   → first (≤14:00) = AfternoonIn, last = AfternoonOut
     *
     * Explicit mode (LogType = IN/OUT):
     *   Uses the declared punch direction directly.
     */
    private function assignSessions(Collection $punches): array
    {
        $morningIn = null;
        $morningOut = null;
        $afternoonIn = null;
        $afternoonOut = null;

        $cutoffMorning = $this->toMinutes(self::MORNING_CUTOFF);
        $cutoffDoubleTap = $this->toMinutes(self::DOUBLE_TAP_GRACE);
        $cutoffAfternoonIn = $this->toMinutes(self::AFTERNOON_CUTOFF_IN);

        $hasPunchType = $punches->contains(fn($p) => $p['punch_type'] !== '');

        if ($hasPunchType) {
            foreach ($punches as $punch) {
                $mins = $this->toMinutes($punch['time']);
                $type = $punch['punch_type'];

                if ($type === 'IN') {
                    if ($mins <= $cutoffMorning && $morningIn === null) {
                        $morningIn = $punch['time'];
                    } elseif ($mins > $cutoffMorning && $mins <= $cutoffAfternoonIn && $afternoonIn === null) {
                        $afternoonIn = $punch['time'];
                    }
                }
                if ($type === 'OUT') {
                    if ($mins <= $cutoffMorning) {
                        $morningOut = $punch['time'];
                    } else {
                        $afternoonOut = $punch['time'];
                    }
                }
            }
        } else {
            $morningPunches = $punches->filter(fn($p) => $this->toMinutes($p['time']) <= $cutoffMorning)->values();
            $afternoonPunches = $punches->filter(fn($p) => $this->toMinutes($p['time']) > $cutoffMorning)->values();

            if ($morningPunches->count() === 1) {
                $morningIn = $morningPunches->first()['time'];
            } elseif ($morningPunches->count() >= 2) {
                $morningIn = $morningPunches->first()['time'];
                $morningOut = $morningPunches->last()['time'];
            }

            if ($afternoonPunches->count() === 1) {
                $solo = $afternoonPunches->first()['time'];
                $soloMins = $this->toMinutes($solo);

                if ($soloMins <= $cutoffDoubleTap && $morningIn !== null && $morningOut === null) {
                    $morningOut = $solo;
                } elseif ($soloMins <= $cutoffAfternoonIn) {
                    $afternoonIn = $solo;
                } else {
                    $afternoonOut = $solo;
                }
            } elseif ($afternoonPunches->count() >= 2) {
                $first = $afternoonPunches->first();
                if ($this->toMinutes($first['time']) <= $cutoffAfternoonIn) {
                    $afternoonIn = $first['time'];
                }
                $afternoonOut = $afternoonPunches->last()['time'];
            }
        }

        return [
            'MorningIn' => $morningIn ?? '',
            'MorningOut' => $morningOut ?? '',
            'AfternoonIn' => $afternoonIn ?? '',
            'AfternoonOut' => $afternoonOut ?? '',
        ];
    }

    private function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time . ':00'));
        return ($h * 60) + $m;
    }

    private function sanitizeRow(array $row): array
    {
        return array_map(
            fn($v) => trim(str_replace(["\r", "\n", "\xEF\xBB\xBF"], '', (string) $v), " \t\""),
            $row
        );
    }
}
