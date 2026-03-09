<?php

namespace App\Observers;

use App\Models\User;
use App\Models\TransactionHistory;

class UserObserver
{
    public function created(User $user): void
    {
        // Only log regular employee or job order registrations
        if (!in_array($user->role ?? '', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])) {
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
