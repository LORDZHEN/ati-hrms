<?php

namespace App\Services;

use Carbon\Carbon;
use League\Csv\Reader;

/**
 * DtrCalculator
 *
 * Computes late, undertime, overtime and worked hours from DTR rows.
 *
 * Two entry points:
 *  - calculateFromCsv(string $filePath)  — original: reads a pre-filtered CSV
 *  - calculateFromArray(array $rows)     — NEW: accepts rows already in memory
 *                                          (used after BiometricLogParser runs)
 */
class DtrCalculator
{
    private const MORNING_START = '08:00';
    private const MORNING_END = '12:00';
    private const AFTERNOON_START = '13:00';
    private const AFTERNOON_END = '17:00';

    /**
     * Calculate from an absolute CSV file path.
     * The CSV must already be filtered to one employee.
     */
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

    /**
     * NEW — Calculate directly from an array of DTR rows already in memory.
     *
     * This is what BiometricLogParser feeds into, so no temp CSV file is needed.
     *
     * Expected keys per row:
     *   EmployeeID, Name, Date, MorningIn, MorningOut, AfternoonIn, AfternoonOut
     */
    public function calculateFromArray(array $rows): array
    {
        $calculated = [];
        $appTimezone = config('app.timezone', 'Asia/Manila');

        foreach ($rows as $lineOffset => $record) {
            $record = array_map(fn($v) => trim((string) $v), $record);

            foreach (['EmployeeID', 'Name', 'Date'] as $col) {
                if (!array_key_exists($col, $record)) {
                    throw new \Exception("Missing column '{$col}' at row offset {$lineOffset}.");
                }
            }

            $date = Carbon::parse($record['Date'], $appTimezone)->startOfDay();

            if ($date->isWeekend()) {
                $calculated[] = $this->buildWeekendRow($record, $date);
                continue;
            }

            $morningIn = $this->safeParseTime($record['MorningIn'] ?? '', $date);
            $morningOut = $this->safeParseTime($record['MorningOut'] ?? '', $date);
            $afternoonIn = $this->safeParseTime($record['AfternoonIn'] ?? '', $date);
            $afternoonOut = $this->safeParseTime($record['AfternoonOut'] ?? '', $date);

            $officialMorningStart = $date->copy()->setTimeFromTimeString(self::MORNING_START);
            $officialMorningEnd = $date->copy()->setTimeFromTimeString(self::MORNING_END);
            $officialAfternoonStart = $date->copy()->setTimeFromTimeString(self::AFTERNOON_START);
            $officialAfternoonEnd = $date->copy()->setTimeFromTimeString(self::AFTERNOON_END);

            $lateMorning = ($morningIn && $morningIn->gt($officialMorningStart))
                ? (int) $officialMorningStart->diffInMinutes($morningIn)
                : 0;

            $lateAfternoon = ($afternoonIn && $afternoonIn->gt($officialAfternoonStart))
                ? (int) $officialAfternoonStart->diffInMinutes($afternoonIn)
                : 0;

            $undertimeMorning = ($morningOut && $morningOut->lt($officialMorningEnd))
                ? (int) $morningOut->diffInMinutes($officialMorningEnd)
                : 0;

            $undertimeAfternoon = ($afternoonOut && $afternoonOut->lt($officialAfternoonEnd))
                ? (int) $afternoonOut->diffInMinutes($officialAfternoonEnd)
                : 0;

            $totalLate = $lateMorning + $lateAfternoon;
            $totalUndertime = $undertimeMorning + $undertimeAfternoon;

            $morningWorked = ($morningIn && $morningOut)
                ? max(0, (int) $morningIn->diffInMinutes($morningOut))
                : 0;

            $afternoonWorked = ($afternoonIn && $afternoonOut)
                ? max(0, (int) $afternoonIn->diffInMinutes($afternoonOut))
                : 0;

            $totalWorkedMinutes = $morningWorked + $afternoonWorked;

            $overtimeMinutes = ($afternoonOut && $afternoonOut->gt($officialAfternoonEnd))
                ? (int) $officialAfternoonEnd->diffInMinutes($afternoonOut)
                : 0;

            $calculated[] = [
                'EmployeeID' => $record['EmployeeID'],
                'Name' => $record['Name'],
                'Date' => $date->format('Y-m-d'),
                'MorningIn' => $morningIn ? $morningIn->format('H:i') : '',
                'MorningOut' => $morningOut ? $morningOut->format('H:i') : '',
                'AfternoonIn' => $afternoonIn ? $afternoonIn->format('H:i') : '',
                'AfternoonOut' => $afternoonOut ? $afternoonOut->format('H:i') : '',
                'Late' => $totalLate,
                'Undertime' => $totalUndertime,
                'Overtime' => $overtimeMinutes,
                'WorkedHours' => $this->formatMinutes($totalWorkedMinutes),
                'IsWeekend' => false,
            ];
        }

        return $calculated;
    }

    private function safeParseTime(string $value, Carbon $date): ?Carbon
    {
        $value = trim($value);
        if ($value === '' || $value === '00:00' || $value === '0:00') {
            return null;
        }
        try {
            return Carbon::parse(
                $date->format('Y-m-d') . ' ' . $value,
                config('app.timezone', 'Asia/Manila')
            );
        } catch (\Exception) {
            return null;
        }
    }

    private function buildWeekendRow(array $record, Carbon $date): array
    {
        return [
            'EmployeeID' => $record['EmployeeID'],
            'Name' => $record['Name'],
            'Date' => $date->format('Y-m-d'),
            'MorningIn' => '',
            'MorningOut' => '',
            'AfternoonIn' => '',
            'AfternoonOut' => '',
            'Late' => 0,
            'Undertime' => 0,
            'Overtime' => 0,
            'WorkedHours' => '',
            'IsWeekend' => true,
        ];
    }

    private function formatMinutes(int $minutes): string
    {
        if ($minutes <= 0)
            return '0:00';
        return floor($minutes / 60) . ':' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }
}
