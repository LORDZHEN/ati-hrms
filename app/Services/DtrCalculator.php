<?php

namespace App\Services;

use Carbon\Carbon;
use League\Csv\Reader;

/**
 * DtrCalculator — Philippine Civil Service Commission (CSC) rules
 *
 * Schedule : 08:00–12:00 (AM)  |  13:00–17:00 (PM)
 *
 * LATE      = morning arrival AFTER 08:00, AND the punch is a genuine morning
 *             punch (before 12:00).  If the first tap is ≥ 12:00 the employee
 *             was absent in the AM session — BioTime treats that as absent, NOT late.
 *             PM arrival time is NEVER counted as late.
 * UNDERTIME = departure BEFORE schedule end (12:00 noon / 17:00 PM)
 * OVERTIME  = AfternoonOut AFTER 17:00 PM only (pre-8am arrivals are NOT OT per CSC)
 *
 * ── VERIFIED BIOTIME GRACE-PERIOD RULE ────────────────────────────────────
 * BioTime silently subtracts 1 minute from every late/undertime event.
 * This grace is applied identically to both late and undertime.
 * Proof from XLS data:
 *   PJ MAHUMOT  5 events: (28)+(29)+(4)+(57)+(52)         = 170 min  ✓
 *   L CAMARINES 13 events: sum with -1/event               = 370 min  ✓
 *   JENCARNACION undertime: (30-1)+(17-1)+(15-1)           = 59 min   ✓
 *   R RUBIS undertime:      (38-1)+(60-1)                  = 96 min   ✓
 *   R ZAMORA undertime:     (29-1)+(57-1)                  = 84 min   ✓
 */
class DtrCalculator
{
    // Schedule constants (hours / minutes)
    private const AM_START_H = 8;
    private const AM_START_M = 0;
    private const AM_END_H   = 12;
    private const AM_END_M   = 0;
    private const PM_START_H = 13;
    private const PM_START_M = 0;
    private const PM_END_H   = 17;
    private const PM_END_M   = 0;

    // BioTime grace period deducted from every late/undertime event (minutes)
    private const UT_GRACE = 1;

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
        $calculated = [];
        $tz = config('app.timezone', 'Asia/Manila');

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

            // Parse all four punches (null = no punch recorded)
            $amIn  = $this->parseTime($record['MorningIn']    ?? '', $date);
            $amOut = $this->parseTime($record['MorningOut']   ?? '', $date);
            $pmIn  = $this->parseTime($record['AfternoonIn']  ?? '', $date);
            $pmOut = $this->parseTime($record['AfternoonOut'] ?? '', $date);

            // Schedule anchors — setTime(h, m, 0) guarantees zero seconds
            $amStart = $this->anchor($date, self::AM_START_H, self::AM_START_M);
            $amEnd   = $this->anchor($date, self::AM_END_H,   self::AM_END_M);
            $pmStart = $this->anchor($date, self::PM_START_H, self::PM_START_M);
            $pmEnd   = $this->anchor($date, self::PM_END_H,   self::PM_END_M);

            // ── LATE ──────────────────────────────────────────────────────
            //
            // BioTime counts tardiness ONLY for morning arrivals after 08:00
            // that are genuine morning punches (i.e. amIn < 12:00).
            //
            // If amIn ≥ 12:00 the employee was absent in the AM session —
            // BioTime records that as an absence, NOT as tardiness.
            //
            // PM arrival time (pmIn) is NEVER counted as late per BioTime/CSC.
            //
            // The same 1-minute grace (UT_GRACE) that BioTime applies to
            // undertime events is also applied to each late event.
            $lateAmRaw = ($amIn && $amIn->gt($amStart) && $amIn->lt($amEnd))
                ? min($this->mins($amStart, $amIn), 240)
                : 0;
            $lateAm = $lateAmRaw > 0 ? max(0, $lateAmRaw - self::UT_GRACE) : 0;

            $totalLate = $lateAm; // PM arrival is NOT counted as late

            // ── UNDERTIME (early leave) — BioTime grace: subtract 1 min/event ──
            $utAmRaw = ($amOut && $amOut->lt($amEnd))
                ? $this->mins($amOut, $amEnd)
                : 0;
            $utAm = $utAmRaw > 0 ? max(0, $utAmRaw - self::UT_GRACE) : 0;

            $utPmRaw = ($pmOut && $pmOut->lt($pmEnd))
                ? $this->mins($pmOut, $pmEnd)
                : 0;
            $utPm = $utPmRaw > 0 ? max(0, $utPmRaw - self::UT_GRACE) : 0;

            $totalUt = $utAm + $utPm;

            // ── OVERTIME ──────────────────────────────────────────────────
            $totalOt = ($pmOut && $pmOut->gt($pmEnd))
                ? $this->mins($pmEnd, $pmOut)
                : 0;

            // ── WORKED MINUTES ────────────────────────────────────────────
            $workedAm = ($amIn && $amOut && $amOut->gt($amIn))
                ? $this->mins($amIn, $amOut)
                : 0;
            $workedPm = ($pmIn && $pmOut && $pmOut->gt($pmIn))
                ? $this->mins($pmIn, $pmOut)
                : 0;
            $totalWorked = $workedAm + $workedPm;

            // ── ABSENCE FLAGS ─────────────────────────────────────────────
            $hasAm = ($amIn !== null || $amOut !== null);
            $hasPm = ($pmIn !== null || $pmOut !== null);

            $calculated[] = [
                'EmployeeID'       => $record['EmployeeID'],
                'Name'             => $record['Name'],
                'Date'             => $date->format('Y-m-d'),
                'DayOfWeek'        => $date->format('D'),
                'MorningIn'        => $amIn  ? $amIn->format('H:i')  : '',
                'MorningOut'       => $amOut ? $amOut->format('H:i') : '',
                'AfternoonIn'      => $pmIn  ? $pmIn->format('H:i')  : '',
                'AfternoonOut'     => $pmOut ? $pmOut->format('H:i') : '',
                'LateMinutes'      => $totalLate,
                'UndertimeMinutes' => $totalUt,
                'OvertimeMinutes'  => $totalOt,
                'WorkedMinutes'    => $totalWorked,
                'Late'             => $this->fmt($totalLate),
                'Undertime'        => $this->fmt($totalUt),
                'Overtime'         => $this->fmt($totalOt),
                'WorkedHours'      => $this->fmt($totalWorked),
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
     */
    public function calculateSummary(array $rows): array
    {
        $workingDays = $daysPresent = $absentDays = $halfDays  = 0;
        $lateDays    = $lateMins    = $utDays     = $utMins    = 0;
        $otMins      = $workedMins  = 0;

        foreach ($rows as $row) {
            if ($row['IsWeekend'])
                continue;

            $workingDays++;

            if ($row['IsFullAbsent']) {
                $absentDays++;
                continue;
            }

            if ($row['IsHalfAbsent'])
                $halfDays++;
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
            'total_working_days'          => $workingDays,
            'days_present'                => $daysPresent,
            'absent_days'                 => $absentDays,
            'half_days'                   => $halfDays,
            'absent_days_total'           => $absentDays + ($halfDays * 0.5),
            'late_days'                   => $lateDays,
            'late_total_minutes'          => $lateMins,
            'late_hours'                  => (int) floor($lateMins / 60),
            'late_minutes_remainder'      => $lateMins % 60,
            'undertime_days'              => $utDays,
            'undertime_total_minutes'     => $utMins,
            'undertime_hours'             => (int) floor($utMins / 60),
            'undertime_minutes_remainder' => $utMins % 60,
            'overtime_total_minutes'      => $otMins,
            'overtime_hours'              => (int) floor($otMins / 60),
            'overtime_minutes_remainder'  => $otMins % 60,
            'worked_total_minutes'        => $workedMins,
            'worked_hours'                => (int) floor($workedMins / 60),
            'worked_minutes_remainder'    => $workedMins % 60,
        ];
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function parseTime(string $value, Carbon $date): ?Carbon
    {
        $value = trim($value);
        if ($value === '' || $value === '00:00' || $value === '0:00')
            return null;
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $value, $m))
            return null;
        try {
            return $date->copy()->setTime((int) $m[1], (int) $m[2], 0);
        } catch (\Exception) {
            return null;
        }
    }

    private function anchor(Carbon $date, int $h, int $m): Carbon
    {
        return $date->copy()->setTime($h, $m, 0);
    }

    private function mins(Carbon $from, Carbon $to): int
    {
        return (int) (($to->timestamp - $from->timestamp) / 60);
    }

    private function fmt(int $minutes): string
    {
        if ($minutes <= 0)
            return '0:00';
        return floor($minutes / 60) . ':' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }

    private function weekendRow(array $record, Carbon $date): array
    {
        return [
            'EmployeeID'       => $record['EmployeeID'],
            'Name'             => $record['Name'],
            'Date'             => $date->format('Y-m-d'),
            'DayOfWeek'        => $date->format('D'),
            'MorningIn'        => '',
            'MorningOut'       => '',
            'AfternoonIn'      => '',
            'AfternoonOut'     => '',
            'LateMinutes'      => 0,
            'UndertimeMinutes' => 0,
            'OvertimeMinutes'  => 0,
            'WorkedMinutes'    => 0,
            'Late'             => '',
            'Undertime'        => '',
            'Overtime'         => '',
            'WorkedHours'      => '',
            'IsWeekend'        => true,
            'IsFullAbsent'     => false,
            'IsHalfAbsent'     => false,
            'HasAmSession'     => false,
            'HasPmSession'     => false,
        ];
    }
}
