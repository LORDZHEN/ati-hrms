<?php

namespace App\Services;

use Carbon\Carbon;
use League\Csv\Reader;

class DtrCalculator
{
    /**
     * Calculate DTR logs from CSV with Morning and Afternoon sessions
     *
     * @param string $filePath Absolute path to CSV
     * @return array
     */
    public function calculateFromCsv(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("CSV file not found at path: $filePath");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $calculated = [];

        foreach ($records as $record) {
            $date = Carbon::parse($record['Date']);

            $morningIn = Carbon::parse($record['MorningIn']);
            $morningOut = Carbon::parse($record['MorningOut']);
            $afternoonIn = Carbon::parse($record['AfternoonIn']);
            $afternoonOut = Carbon::parse($record['AfternoonOut']);

            // Compute Late & Undertime
            $lateMorning = max($morningIn->diffInMinutes(Carbon::parse($date->format('Y-m-d') . ' 08:00')), 0);
            $lateAfternoon = max($afternoonIn->diffInMinutes(Carbon::parse($date->format('Y-m-d') . ' 13:00')), 0);

            $undertimeMorning = max(Carbon::parse($date->format('Y-m-d') . ' 12:00')->diffInMinutes($morningOut), 0);
            $undertimeAfternoon = max(Carbon::parse($date->format('Y-m-d') . ' 17:00')->diffInMinutes($afternoonOut), 0);

            $late = $lateMorning + $lateAfternoon;
            $undertime = $undertimeMorning + $undertimeAfternoon;

            // Compute worked hours
            $workedMinutes = ($morningOut->diffInMinutes($morningIn) + $afternoonOut->diffInMinutes($afternoonIn)) - ($late + $undertime);
            $workedHours = floor($workedMinutes / 60) . ':' . str_pad($workedMinutes % 60, 2, '0', STR_PAD_LEFT);

            $calculated[] = [
                'EmployeeID' => $record['EmployeeID'],
                'Name' => $record['Name'],
                'Date' => $date->format('Y-m-d'),
                'MorningIn' => $morningIn->format('H:i'),
                'MorningOut' => $morningOut->format('H:i'),
                'AfternoonIn' => $afternoonIn->format('H:i'),
                'AfternoonOut' => $afternoonOut->format('H:i'),
                'Late' => $late,
                'Undertime' => $undertime,
                'WorkedHours' => $workedHours,
            ];
        }

        return $calculated;
    }
}
