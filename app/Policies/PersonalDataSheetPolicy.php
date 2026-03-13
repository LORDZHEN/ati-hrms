<?php

namespace App\Policies;

use App\Filament\Concerns\WorkflowHelper;
use App\Models\PersonalDataSheet;
use App\Models\User;

class PersonalDataSheetPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->role !== User::ROLE_JOB_ORDER;
    }

    public function view(User $user, PersonalDataSheet $pds): bool
    {
        return $pds->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        if ($user->role !== User::ROLE_REGULAR) {
            return false;
        }

        return ! PersonalDataSheet::where('user_id', $user->id)->exists();
    }

    /**
     * Employees may only update when the workflow allows it.
     */
    public function update(User $user, PersonalDataSheet $pds): bool
    {
        if ($pds->user_id !== $user->id) {
            return false;
        }

        return WorkflowHelper::canEmployeeEdit($pds);
    }

    public function delete(User $user, PersonalDataSheet $pds): bool
    {
        return false;
    }

    public function forceDelete(User $user, PersonalDataSheet $pds): bool
    {
        return false;
    }

    public function restore(User $user, PersonalDataSheet $pds): bool
    {
        return false;
    }
}
