<?php

namespace App\Observers;

use App\Models\User;
use App\Models\TransactionHistory;

/**
 * Observer: UserObserver
 *
 * Logs a transaction when a new employee account is registered.
 *
 * Register: User::observe(UserObserver::class);
 *
 * NOTE: This only logs when role === 'employee' to avoid
 *       spamming the log with admin account creations.
 */
class UserObserver
{
    public function created(User $user): void
    {
        // Only log employee registrations
        if (($user->role ?? '') !== 'employee') {
            return;
        }

        TransactionHistory::log([
            'user_id'          => $user->id,
            'employee_name'    => $user->full_name ?? $user->name,
            'transaction_type' => 'Employee Registration',
            'module'           => 'Employee',
            'description'      => "New employee account registered" .
                                  ($user->department ? " under {$user->department}" : '') . '.',
            'status'           => $user->status ?? 'registered',
            'icon'             => 'heroicon-o-user-plus',
            'color'            => 'green',
            'record_id'        => $user->id,
            'record_url'       => self::safeRoute(
                'filament.hrms.resources.employees.view', $user->id
            ),
        ]);
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try { return route($name, $params); } catch (\Exception) { return null; }
    }
}
