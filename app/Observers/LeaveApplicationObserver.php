<?php

namespace App\Observers;

use App\Models\LeaveApplication;
use App\Models\TransactionHistory;
use App\Models\User;

class LeaveApplicationObserver
{
    public function created(LeaveApplication $leave): void
    {
        // employee_id is the confirmed FK to users.id (see employee() relationship)
        $userId = $leave->employee_id ?? null;
        $user = User::find($userId);

        $employeeName = $user?->full_name
            ?? trim(implode(' ', array_filter([
                $leave->first_name,
                $leave->middle_name,
                $leave->last_name,
            ])))
            ?: 'Unknown Employee';

        TransactionHistory::log([
            'user_id' => $userId,
            'employee_name' => $employeeName,
            'transaction_type' => 'Leave Application',
            'module' => 'Leave',
            'description' => 'Filed a leave application' .
                ($leave->type_of_leave ? " ({$leave->type_of_leave})" : '') .
                ($leave->leave_date_from && $leave->leave_date_to
                    ? " from {$leave->leave_date_from} to {$leave->leave_date_to}"
                    : '') . '.',
            'status' => $leave->status ?? 'pending',
            'icon' => 'heroicon-o-calendar',
            'color' => 'blue',
            'record_id' => $leave->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.leave-applications.view',
                $leave->id
            ),
        ]);
    }

    public function updated(LeaveApplication $leave): void
    {
        if (!$leave->isDirty('status')) {
            return;
        }

        $userId = $leave->employee_id ?? null;
        $user = User::find($userId);

        $employeeName = $user?->full_name
            ?? trim(implode(' ', array_filter([
                $leave->first_name,
                $leave->middle_name,
                $leave->last_name,
            ])))
            ?: 'Unknown Employee';

        $status = strtolower($leave->status);

        $descriptions = [
            'approved' => 'Leave application was approved.',
            'disapproved' => 'Leave application was disapproved.',
            'rejected' => 'Leave application was rejected.',
            'cancelled' => 'Leave application was cancelled.',
        ];

        TransactionHistory::log([
            'user_id' => $userId,
            'employee_name' => $employeeName,
            'transaction_type' => 'Leave Application',
            'module' => 'Leave',
            'description' => $descriptions[$status]
                ?? "Leave application status changed to {$leave->status}.",
            'status' => $leave->status,
            'icon' => 'heroicon-o-calendar',
            'color' => match ($status) {
                'approved' => 'green',
                'rejected', 'disapproved' => 'red',
                default => 'blue',
            },
            'record_id' => $leave->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.leave-applications.view',
                $leave->id
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
