<?php

namespace App\Services;

use Carbon\Carbon;
use League\Csv\Reader;

/**
 * DtrCalculator — Philippine Civil Service Commission (CSC) rules
 *
 * Schedule: 08:00–12:00 (AM), 13:00–17:00 (PM)
 *
 * LATE      = arrival after schedule start (08:00 AM / 13:00 PM)
 * UNDERTIME = departure before schedule end (12:00 AM / 17:00 PM)
 * OVERTIME  = departure after 17:00 PM only (pre-8am arrivals are NOT OT)
 *
 * All values stored in MINUTES (integers).
 * Formatted strings ("H:MM") provided separately for display.
 */
class DtrCalculator
{
    private const AM_START = '08:00';
    private const AM_END   = '12:00';
    private const PM_START = '13:00';
    private const PM_END   = '17:00';

    // ── Public entry points ────────────────────────────────────────────────

    public function calculateFromCsv(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("CSV file not found: {$filePath}");
        }
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $csv->addStreamFilter('convert.iconv.UTF-8/UTF-8//IGNORE');

        $rows = [];
        foreach ($csv->getRecords() as $record) {
            $rows[] = array_map(fn($v) => trim((string) $v), $record);
        }
        return $this->calculateFromArray($rows);
    }

    public function calculateFromArray(array $rows): array
    {
        $calculated  = [];
        $tz          = config('app.timezone', 'Asia/Manila');

        foreach ($rows as $offset => $record) {
            $record = array_map(fn($v) => trim((string) $v), $record);

            foreach (['EmployeeID', 'Name', 'Date'] as $col) {
                if (!array_key_exists($col, $record)) {
                    throw new \Exception("Missing column '{$col}' at row {$offset}.");
                }
            }

            $date = Carbon::parse($record['Date'], $tz)->startOfDay();

            if ($date->isWeekend()) {
                $calculated[] = $this->weekendRow($record, $date);
                continue;
            }

            // Parse all four punches — null means no punch recorded
            $amIn  = $this->parseTime($record['MorningIn']    ?? '', $date, $tz);
            $amOut = $this->parseTime($record['MorningOut']   ?? '', $date, $tz);
            $pmIn  = $this->parseTime($record['AfternoonIn']  ?? '', $date, $tz);
            $pmOut = $this->parseTime($record['AfternoonOut'] ?? '', $date, $tz);

            // Official schedule anchors
            $amStart = $date->copy()->setTimeFromTimeString(self::AM_START);
            $amEnd   = $date->copy()->setTimeFromTimeString(self::AM_END);
            $pmStart = $date->copy()->setTimeFromTimeString(self::PM_START);
            $pmEnd   = $date->copy()->setTimeFromTimeString(self::PM_END);

            // ── LATE (minutes arriving after schedule start) ───────────────
            // AM late: only if amIn exists and is after 08:00
            $lateAm = ($amIn && $amIn->gt($amStart))
                ? (int) min($amStart->diffInMinutes($amIn), 240)
                : 0;

            // PM late: only if pmIn exists and is after 13:00
            $latePm = ($pmIn && $pmIn->gt($pmStart))
                ? (int) min($pmStart->diffInMinutes($pmIn), 240)
                : 0;

            $totalLate = $lateAm + $latePm;

            // ── UNDERTIME (minutes leaving before schedule end) ────────────
            // AM undertime: only if amOut exists AND is before 12:00
            // If amOut is missing we do NOT count undertime — employee may have
            // forgotten to punch, not necessarily left early.
            $utAm = ($amOut && $amOut->lt($amEnd))
                ? (int) $amOut->diffInMinutes($amEnd)
                : 0;

            // PM undertime: only if pmOut exists AND is before 17:00
            $utPm = ($pmOut && $pmOut->lt($pmEnd))
                ? (int) $pmOut->diffInMinutes($pmEnd)
                : 0;

            $totalUt = $utAm + $utPm;

            // ── OVERTIME (minutes worked AFTER 17:00 only) ────────────────
            // Early morning arrivals before 08:00 are NOT overtime (CSC rule).
            // Only AfternoonOut after 17:00 counts.
            $totalOt = ($pmOut && $pmOut->gt($pmEnd))
                ? (int) $pmEnd->diffInMinutes($pmOut)
                : 0;

            // ── WORKED MINUTES (actual clock time) ────────────────────────
            $workedAm = ($amIn && $amOut && $amOut->gt($amIn))
                ? (int) $amIn->diffInMinutes($amOut)
                : 0;
            $workedPm = ($pmIn && $pmOut && $pmOut->gt($pmIn))
                ? (int) $pmIn->diffInMinutes($pmOut)
                : 0;
            $totalWorked = $workedAm + $workedPm;

            // ── ABSENCE FLAGS ─────────────────────────────────────────────
            $hasAm = ($amIn !== null || $amOut !== null);
            $hasPm = ($pmIn !== null || $pmOut !== null);

            $calculated[] = [
                // Identity
                'EmployeeID'       => $record['EmployeeID'],
                'Name'             => $record['Name'],
                'Date'             => $date->format('Y-m-d'),
                'DayOfWeek'        => $date->format('D'),

                // Punch times (HH:MM or empty string)
                'MorningIn'        => $amIn  ? $amIn->format('H:i')  : '',
                'MorningOut'       => $amOut ? $amOut->format('H:i') : '',
                'AfternoonIn'      => $pmIn  ? $pmIn->format('H:i')  : '',
                'AfternoonOut'     => $pmOut ? $pmOut->format('H:i') : '',

                // Computed durations in MINUTES (integers) — use these in the blade
                'LateMinutes'      => $totalLate,
                'UndertimeMinutes' => $totalUt,
                'OvertimeMinutes'  => $totalOt,
                'WorkedMinutes'    => $totalWorked,

                // Formatted strings (H:MM) for optional display
                'Late'             => $this->fmt($totalLate),
                'Undertime'        => $this->fmt($totalUt),
                'Overtime'         => $this->fmt($totalOt),
                'WorkedHours'      => $this->fmt($totalWorked),

                // Flags
                'IsWeekend'        => false,
                'IsFullAbsent'     => (!$hasAm && !$hasPm),
                'IsHalfAbsent'     => ($hasAm XOR $hasPm),
                'HasAmSession'     => $hasAm,
                'HasPmSession'     => $hasPm,
            ];
        }

        return $calculated;
    }

    /**
     * Compute period summary from calculateFromArray() / calculateFromCsv() output.
     * Returns all fields needed by the PDF summary row.
     */
    public function calculateSummary(array $rows): array
    {
        $workingDays = $daysPresent = $absentDays = $halfDays  = 0;
        $lateDays    = $lateMins    = $utDays     = $utMins    = 0;
        $otMins      = $workedMins  = 0;

        foreach ($rows as $row) {
            if ($row['IsWeekend']) continue;

            $workingDays++;

            if ($row['IsFullAbsent']) {
                $absentDays++;
                continue;
            }

            if ($row['IsHalfAbsent']) $halfDays++;
            $daysPresent++;

            if ($row['LateMinutes'] > 0) {
                $lateDays++;
                $lateMins += $row['LateMinutes'];
            }
            if ($row['UndertimeMinutes'] > 0) {
                $utDays++;
                $utMins += $row['UndertimeMinutes'];
            }

            $otMins     += $row['OvertimeMinutes'];
            $workedMins += $row['WorkedMinutes'];
        }

        return [
            // Presence / absence
            'total_working_days'          => $workingDays,
            'days_present'                => $daysPresent,
            'absent_days'                 => $absentDays,
            'half_days'                   => $halfDays,
            'absent_days_total'           => $absentDays + ($halfDays * 0.5),  // "AB"

            // Late
            'late_days'                   => $lateDays,          // "L"
            'late_total_minutes'          => $lateMins,
            'late_hours'                  => (int) floor($lateMins / 60),
            'late_minutes_remainder'      => $lateMins % 60,

            // Undertime / Early Leave
            'undertime_days'              => $utDays,
            'undertime_total_minutes'     => $utMins,
            'undertime_hours'             => (int) floor($utMins / 60),
            'undertime_minutes_remainder' => $utMins % 60,

            // Overtime (after 17:00 only)
            'overtime_total_minutes'      => $otMins,
            'overtime_hours'              => (int) floor($otMins / 60),
            'overtime_minutes_remainder'  => $otMins % 60,

            // Worked
            'worked_total_minutes'        => $workedMins,
            'worked_hours'                => (int) floor($workedMins / 60),
            'worked_minutes_remainder'    => $workedMins % 60,
        ];
    }

    // ── Private helpers ────────────────────────────────────────────────────

    /**
     * Parse "HH:MM" into Carbon. Returns null for empty / zero / invalid.
     */
    private function parseTime(string $value, Carbon $date, string $tz): ?Carbon
    {
        $value = trim($value);
        if ($value === '' || $value === '00:00' || $value === '0:00') return null;
        if (!preg_match('/^\d{1,2}:\d{2}$/', $value)) return null;
        try {
            return Carbon::parse($date->format('Y-m-d') . ' ' . $value, $tz);
        } catch (\Exception) {
            return null;
        }
    }

    /** Format integer minutes as "H:MM". */
    private function fmt(int $minutes): string
    {
        if ($minutes <= 0) return '0:00';
        return floor($minutes / 60) . ':' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }

    private function weekendRow(array $record, Carbon $date): array
    {
        return [
            'EmployeeID' => $record['EmployeeID'], 'Name' => $record['Name'],
            'Date'       => $date->format('Y-m-d'), 'DayOfWeek' => $date->format('D'),
            'MorningIn' => '', 'MorningOut' => '', 'AfternoonIn' => '', 'AfternoonOut' => '',
            'LateMinutes' => 0, 'UndertimeMinutes' => 0, 'OvertimeMinutes' => 0, 'WorkedMinutes' => 0,
            'Late' => '', 'Undertime' => '', 'Overtime' => '', 'WorkedHours' => '',
            'IsWeekend' => true, 'IsFullAbsent' => false, 'IsHalfAbsent' => false,
            'HasAmSession' => false, 'HasPmSession' => false,
        ];
    }
}
