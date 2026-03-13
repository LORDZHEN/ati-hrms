<?php

namespace App\Policies;

use App\Filament\Concerns\WorkflowHelper;
use App\Models\Saln;
use App\Models\User;

class SalnPolicy
{
    // Every admin can do anything.
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null; // fall through to individual methods for employees
    }

    public function viewAny(User $user): bool
    {
        return $user->role !== User::ROLE_JOB_ORDER;
    }

    public function view(User $user, Saln $saln): bool
    {
        return $saln->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        if ($user->role !== User::ROLE_REGULAR) {
            return false;
        }

        return !Saln::where('user_id', $user->id)->exists();
    }

    /**
     * Employees may only update (edit) when the workflow allows it.
     */
    public function update(User $user, Saln $saln): bool
    {
        if ($saln->user_id !== $user->id) {
            return false;
        }

        return WorkflowHelper::canEmployeeEdit($saln);
    }

    public function delete(User $user, Saln $saln): bool
    {
        // Employees cannot delete
        return false;
    }

    public function forceDelete(User $user, Saln $saln): bool
    {
        return false;
    }

    public function restore(User $user, Saln $saln): bool
    {
        return false;
    }
}
