<?php

namespace App\Observers;

use App\Models\EmployeeDtr;
use App\Models\TransactionHistory;
use App\Models\User;

/**
 * Observer: DtrObserver
 *
 * Logs DTR upload events to the transaction_histories table.
 *
 * NOTE: We resolve the employee name via User::find($dtr->employee_id)
 * rather than $dtr->employee because the relationship is not eager-loaded
 * on the model instance Eloquent passes to the observer at fire time.
 *
 * Register in AppServiceProvider::boot():
 *   EmployeeDtr::observe(DtrObserver::class);
 */
class DtrObserver
{
    public function created(EmployeeDtr $dtr): void
    {
        $user = User::find($dtr->employee_id);
        $employeeName = $this->resolveName($dtr, $user);

        $description = 'DTR file uploaded via biometric import'
            . (filled($dtr->notes) ? " ({$dtr->notes})" : '')
            . '.';

        TransactionHistory::log([
            'user_id' => $dtr->employee_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'Daily Time Record',
            'module' => 'DTR',
            'description' => $description,
            'status' => 'uploaded',
            'icon' => 'heroicon-o-clock',
            'color' => 'cyan',
            'record_id' => $dtr->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.daily-time-records.view',
                $dtr->id
            ),
        ]);
    }

    public function deleted(EmployeeDtr $dtr): void
    {
        $user = User::find($dtr->employee_id);
        $employeeName = $this->resolveName($dtr, $user);

        TransactionHistory::log([
            'user_id' => $dtr->employee_id,
            'employee_name' => $employeeName,
            'transaction_type' => 'Daily Time Record',
            'module' => 'DTR',
            'description' => 'DTR record was deleted by an administrator.',
            'status' => 'deleted',
            'icon' => 'heroicon-o-trash',
            'color' => 'red',
            'record_id' => $dtr->id,
            'record_url' => null, // record no longer exists
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the employee display name safely.
     *
     * Priority:
     *   1. User model full_name (already persisted, most reliable)
     *   2. User model name fallback
     *   3. Hard fallback with the raw employee_id for traceability
     */
    private function resolveName(EmployeeDtr $dtr, ?User $user): string
    {
        return $user?->full_name
            ?? $user?->name
            ?? "Employee #{$dtr->employee_id}";
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
