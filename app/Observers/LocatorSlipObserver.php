<?php

namespace App\Observers;

use App\Models\LocatorSlip;
use App\Models\TransactionHistory;
use App\Models\User;

class LocatorSlipObserver
{
    public function created(LocatorSlip $slip): void
    {
        // user_id is set automatically in LocatorSlip::booted() before insert
        $user = User::find($slip->user_id);
        $employeeName = $user?->full_name
            ?? $slip->employee_name  // LocatorSlip has its own employee_name column
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id' => $slip->user_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'Locator Slip',
            'module' => 'Locator',
            'description' => 'Locator slip submitted' .
                ($slip->destination ? " to {$slip->destination}" : '') .
                ($slip->purpose ? ": {$slip->purpose}" : '') . '.',
            'status' => $slip->status ?? 'pending',
            'icon' => 'heroicon-o-map-pin',
            'color' => 'purple',
            'record_id' => $slip->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.locator-slips.view',
                $slip->id
            ),
        ]);
    }

    public function updated(LocatorSlip $slip): void
    {
        if (!$slip->isDirty('status')) {
            return;
        }

        $user = User::find($slip->user_id);
        $employeeName = $user?->full_name
            ?? $slip->employee_name
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id' => $slip->user_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'Locator Slip',
            'module' => 'Locator',
            'description' => "Locator slip status updated to {$slip->status}.",
            'status' => $slip->status,
            'icon' => 'heroicon-o-map-pin',
            'color' => match (strtolower($slip->status)) {
                'approved' => 'green',
                'disapproved',
                'rejected' => 'red',
                default => 'purple',
            },
            'record_id' => $slip->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.locator-slips.view',
                $slip->id
            ),
        ]);
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try {
            return route($name, $params);
        } catch (\Exception) {
            return null;
        }
    }
}
