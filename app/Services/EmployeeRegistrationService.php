<?php

namespace App\Services;

use App\Models\User;
use App\Mail\AdminTemporaryPasswordMail;
use App\Mail\PendingRegistrationMail;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeRegistrationService
{
    /**
     * Process a newly created employee or admin account.
     */
    public function processNewEmployee(User $user): void
    {
        if ($user->role === 'admin') {
            $this->activateAdminAccount($user);
        } else {
            $this->setPendingEmployeeAccount($user);
        }

        $this->notifyAdmins($user);
    }

    /**
     * Activate an admin account with a temporary password.
     */
    private function activateAdminAccount(User $user): void
    {
        $tempPassword = Str::random(12);

        $user->update([
            'status' => 'active',
            'email_verified_at' => now(),
            'must_change_password' => true,
            'password' => Hash::make($tempPassword),
        ]);

        Mail::to($user->email)->send(new AdminTemporaryPasswordMail($user, $tempPassword));
    }

    /**
     * Set employee account to pending status.
     */
    private function setPendingEmployeeAccount(User $user): void
    {
        Mail::to($user->email)->send(new PendingRegistrationMail($user));
    }

    /**
     * Notify all admins about the new registration.
     */
    private function notifyAdmins(User $newUser): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Employee Registered')
                ->body("A new employee account was created: **{$newUser->name}**")
                ->success()
                ->sendToDatabase($admin);
        }
    }

    /**
     * Approve a pending employee registration.
     */
    public function approveEmployee(User $employee): bool
    {
        $birthday = $employee->birthday?->format('mdY');

        if (!$birthday || strlen($birthday) !== 8) {
            return false;
        }

        $employee->update([
            'email_verified_at' => now(),
            'password' => Hash::make($birthday),
            'must_change_password' => true,
            'status' => 'active',
        ]);

        Mail::to($employee->email)->send(new \App\Mail\AccountVerifiedMail($employee, $birthday));

        return true;
    }

    /**
     * Reject a pending employee registration.
     */
    public function rejectEmployee(User $employee): void
    {
        $employee->update(['status' => 'inactive']);
    }
}
