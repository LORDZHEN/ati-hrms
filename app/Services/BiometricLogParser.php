<?php

namespace App\Services;

use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Support\Collection;

/**
 * BiometricLogParser
 *
 * Reads a raw biometric attendance machine export (multi-employee, punch-event format)
 * and converts it into the clean per-employee DTR row format that DtrCalculator expects.
 *
 * Raw input columns expected:
 *   LogID, CredentialID, EmployeeName, Date, Time, Timestamp,
 *   PunchType, DeviceID, VerificationMethod, WorkCode, Status
 *
 * Output per DTR row:
 *   EmployeeID, Name, Date, MorningIn, MorningOut, AfternoonIn, AfternoonOut
 */
class BiometricLogParser
{
    // Session boundary constants — change these if your office hours differ
    private const MORNING_CUTOFF_IN = '12:30'; // latest a Morning IN punch can be
    private const MORNING_CUTOFF_OUT = '12:30'; // latest a Morning OUT punch can be
    private const AFTERNOON_CUTOFF_IN = '14:00'; // latest an Afternoon IN punch can be

    // Statuses we silently discard (duplicate machine entries, etc.)
    private const SKIP_STATUSES = ['Duplicate'];

    /**
     * Parse the raw biometric CSV and return DTR rows for ONE specific employee.
     *
     * @param  string     $filePath      Absolute path to the raw biometric CSV
     * @param  int|string $credentialId  The CredentialID enrolled in the biometric device
     * @return array  Array of DTR rows ready for DtrCalculator::calculateFromArray()
     *
     * @throws \Exception  If file is missing or has no records for this employee
     */
    public function parseForEmployee(string $filePath, int|string $credentialId): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Biometric log file not found: {$filePath}");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $punchesByDay = $this->groupPunchesByDay($csv, (string) $credentialId);

        if ($punchesByDay->isEmpty()) {
            throw new \Exception(
                "No attendance records found for Credential ID: {$credentialId}"
            );
        }

        return $this->buildDtrRows($punchesByDay, (string) $credentialId);
    }

    /**
     * Scan the raw CSV and return a summary of ALL employees found in the file.
     * Use this to populate the "select employee" dropdown in the Filament UI.
     *
     * @param  string $filePath
     * @return Collection  Keyed by CredentialID => ['id', 'name', 'days', 'day_count', 'punch_count']
     */
    public function detectEmployees(string $filePath): Collection
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Biometric log file not found: {$filePath}");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $employees = collect();

        foreach ($csv->getRecords() as $row) {
            $row = $this->sanitizeRow($row);

            if (in_array($row['Status'] ?? '', self::SKIP_STATUSES, true)) {
                continue;
            }

            $id = $row['CredentialID'] ?? '';
            if ($id === '') {
                continue;
            }

            if (!$employees->has($id)) {
                $employees->put($id, [
                    'id' => $id,
                    'name' => trim($row['EmployeeName'] ?? ''),
                    'days' => collect(),
                    'punch_count' => 0,
                ]);
            }

            $entry = $employees->get($id);
            $entry['days']->push($row['Date'] ?? '');
            $entry['punch_count']++;
            $employees->put($id, $entry);
        }

        return $employees->map(function ($e) {
            return [
                'id' => $e['id'],
                'name' => $e['name'],
                'days' => $e['days']->unique()->sort()->values()->toArray(),
                'day_count' => $e['days']->unique()->count(),
                'punch_count' => $e['punch_count'],
            ];
        });
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    /**
     * Read all records for the target employee and group them by date.
     */
    private function groupPunchesByDay(Reader $csv, string $credentialId): Collection
    {
        $byDay = collect();
        $timezone = config('app.timezone', 'Asia/Manila');

        foreach ($csv->getRecords() as $row) {
            $row = $this->sanitizeRow($row);

            // Only this employee
            if (($row['CredentialID'] ?? '') !== $credentialId) {
                continue;
            }

            // Drop flagged duplicates
            if (in_array($row['Status'] ?? '', self::SKIP_STATUSES, true)) {
                continue;
            }

            $dateStr = $row['Date'] ?? '';
            $timestamp = $row['Timestamp'] ?? ($dateStr . ' ' . ($row['Time'] ?? '00:00:00'));

            if ($dateStr === '') {
                continue;
            }

            try {
                $dt = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $timezone);
            } catch (\Exception) {
                continue; // skip rows with unparseable timestamps
            }

            if (!$byDay->has($dateStr)) {
                $byDay->put($dateStr, collect());
            }

            $byDay->get($dateStr)->push([
                'datetime' => $dt,
                'time' => $dt->format('H:i'),
                'punch_type' => strtoupper(trim($row['PunchType'] ?? '')),
                'work_code' => $row['WorkCode'] ?? '',
                'name' => trim($row['EmployeeName'] ?? ''),
            ]);
        }

        // Sort each day chronologically, then sort the days themselves
        return $byDay
            ->map(fn($punches) => $punches->sortBy('datetime')->values())
            ->sortKeys();
    }

    /**
     * Convert grouped punch events into clean DTR rows.
     */
    private function buildDtrRows(Collection $punchesByDay, string $credentialId): array
    {
        $rows = [];
        $employeeName = null;

        foreach ($punchesByDay as $dateStr => $punches) {
            if ($employeeName === null && $punches->isNotEmpty()) {
                $employeeName = $punches->first()['name'];
            }

            $sessions = $this->assignSessions($punches);

            $rows[] = [
                'EmployeeID' => $credentialId,
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
     * Given all punches for a single day (pre-sorted by time),
     * assign each punch to the correct session slot.
     *
     * Rules:
     *  MorningIn    — first IN  at or before 12:30
     *  MorningOut   — last  OUT at or before 12:30
     *  AfternoonIn  — first IN  between 12:30 and 14:00
     *  AfternoonOut — last  OUT after 12:30 (normal EOD or overtime — always take the latest)
     */
    private function assignSessions(Collection $punches): array
    {
        $morningIn = null;
        $morningOut = null;
        $afternoonIn = null;
        $afternoonOut = null;

        $cutoffMorningIn = $this->toMinutes(self::MORNING_CUTOFF_IN);
        $cutoffMorningOut = $this->toMinutes(self::MORNING_CUTOFF_OUT);
        $cutoffAfternoonIn = $this->toMinutes(self::AFTERNOON_CUTOFF_IN);

        foreach ($punches as $punch) {
            $mins = $this->toMinutes($punch['time']);
            $type = $punch['punch_type'];

            if ($type === 'IN') {
                if ($mins <= $cutoffMorningIn && $morningIn === null) {
                    $morningIn = $punch['time'];
                } elseif ($mins > $cutoffMorningIn && $mins <= $cutoffAfternoonIn && $afternoonIn === null) {
                    $afternoonIn = $punch['time'];
                }
            }

            if ($type === 'OUT') {
                if ($mins <= $cutoffMorningOut) {
                    $morningOut = $punch['time']; // lunch break departure
                } else {
                    $afternoonOut = $punch['time']; // EOD or overtime — always update to latest
                }
            }
        }

        return [
            'MorningIn' => $morningIn ?? '',
            'MorningOut' => $morningOut ?? '',
            'AfternoonIn' => $afternoonIn ?? '',
            'AfternoonOut' => $afternoonOut ?? '',
        ];
    }

    /**
     * Convert "HH:MM" time string to total minutes since midnight.
     */
    private function toMinutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time . ':00'));

        return ($h * 60) + $m;
    }

    /**
     * Trim whitespace, carriage returns, BOM, and surrounding quotes from all CSV fields.
     */
    private function sanitizeRow(array $row): array
    {
        return array_map(
            fn($v) => trim(str_replace(["\r", "\n", "\xEF\xBB\xBF"], '', (string) $v), " \t\""),
            $row
        );
    }
}
