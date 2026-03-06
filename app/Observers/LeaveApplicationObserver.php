<?php

namespace App\Observers;

use App\Models\LeaveApplication;
use App\Models\TransactionHistory;

/**
 * Observer: LeaveApplicationObserver
 *
 * Automatically logs a TransactionHistory entry whenever a
 * LeaveApplication is created or its status changes.
 *
 * Register in AppServiceProvider::boot():
 *   LeaveApplication::observe(LeaveApplicationObserver::class);
 */
class LeaveApplicationObserver
{
    /**
     * Called when a new leave application is created.
     */
    public function created(LeaveApplication $leave): void
    {
        $employeeName = $leave->employee?->full_name
            ?? $leave->full_name
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id' => $leave->employee_id ?? $leave->user_id ?? null,
            'employee_name' => $employeeName,
            'transaction_type' => 'Leave Application',
            'module' => 'Leave',
            'description' => "Filed a leave application" .
                ($leave->leave_type ? " ({$leave->leave_type})" : '') .
                ($leave->start_date && $leave->end_date
                    ? " from {$leave->start_date} to {$leave->end_date}"
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

    /**
     * Called when a leave application is updated (e.g. approved/rejected).
     */
    public function updated(LeaveApplication $leave): void
    {
        // Only log when the status column actually changed
        if (!$leave->isDirty('status')) {
            return;
        }

        $employeeName = $leave->employee?->full_name
            ?? $leave->full_name
            ?? 'Unknown Employee';

        $status = strtolower($leave->status);

        $descriptions = [
            'approved' => "Leave application was approved.",
            'rejected' => "Leave application was rejected.",
            'cancelled' => "Leave application was cancelled.",
        ];

        TransactionHistory::log([
            'user_id' => $leave->employee_id ?? $leave->user_id ?? null,
            'employee_name' => $employeeName,
            'transaction_type' => 'Leave Application',
            'module' => 'Leave',
            'description' => $descriptions[$status] ?? "Leave application status changed to {$leave->status}.",
            'status' => $leave->status,
            'icon' => 'heroicon-o-calendar',
            'color' => match ($status) {
                'approved' => 'green',
                'rejected' => 'red',
                default => 'blue',
            },
            'record_id' => $leave->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.leave-applications.view',
                $leave->id
            ),
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try {
            return route($name, $params);
        } catch (\Exception) {
            return null;
        }
    }
}
