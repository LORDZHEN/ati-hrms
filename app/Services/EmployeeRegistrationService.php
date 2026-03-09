<?php

namespace App\Services;

use App\Models\User;
use App\Mail\AccountVerifiedMail;
use App\Mail\PendingRegistrationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeeRegistrationService
{
    /**
     * Called when a new employee is created via Admin (CreateEmployee page).
     * Sends the pending registration email.
     */
    public function processNewEmployee(User $employee): void
    {
        try {
            Mail::to($employee->email)->send(new PendingRegistrationMail($employee));
        } catch (\Throwable $e) {
            Log::error("Failed to send pending registration email to {$employee->email}: " . $e->getMessage());
        }
    }

    /**
     * Approves an employee registration:
     * 1. Generates temp password from birthday in MMDDYYYY format
     *    e.g. December 04, 2002 → "12042002"
     * 2. Updates the user record (active, verified, new password)
     * 3. FIX: Also sets verification_status = 'verified' so the DB
     *    column stays in sync with email_verified_at and status.
     * 4. Sends approval email with the temp password
     *
     * Returns false if birthday is missing or unparseable.
     */
    public function approveEmployee(User $employee): bool
    {
        $temporaryPassword = $this->generatePasswordFromBirthday($employee->birthday);

        if (!$temporaryPassword) {
            Log::warning("Cannot approve employee ID {$employee->id}: birthday is missing or invalid.");
            return false;
        }

        $employee->update([
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'verification_status' => 'verified',   // FIX: was never set, left as 'pending'
        ]);

        try {
            Mail::to($employee->email)->send(
                new AccountVerifiedMail($employee, $temporaryPassword)
            );
        } catch (\Throwable $e) {
            Log::error("Failed to send approval email to {$employee->email}: " . $e->getMessage());
            // Return true anyway — DB was updated successfully
        }

        return true;
    }

    /**
     * Rejects an employee registration.
     * Sets status to inactive.
     */
    public function rejectEmployee(User $employee): void
    {
        $employee->update([
            'status' => 'inactive',
        ]);
    }

    /**
     * Resends login credentials to an already-approved employee.
     * Regenerates password from birthday and resends email.
     * FIX: Also re-confirms verification_status = 'verified' in case
     *      older records were approved before this fix was deployed.
     */
    public function resendCredentials(User $employee): bool
    {
        $temporaryPassword = $this->generatePasswordFromBirthday($employee->birthday);

        if (!$temporaryPassword) {
            return false;
        }

        $employee->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'verification_status' => 'verified',   // FIX: keep in sync for legacy records
        ]);

        try {
            Mail::to($employee->email)->send(
                new AccountVerifiedMail($employee, $temporaryPassword)
            );
        } catch (\Throwable $e) {
            Log::error("Failed to resend credentials to {$employee->email}: " . $e->getMessage());
        }

        return true;
    }

    /**
     * Generates a temporary password from the employee's birthday.
     *
     * Format : MMDDYYYY  (zero-padded month and day)
     * Example: December 04, 2002  →  "12042002"
     * Example: January 9, 1995    →  "01091995"
     *
     * @param  string|null  $birthday
     * @return string|null  Returns null if birthday is missing/invalid.
     */
    public function generatePasswordFromBirthday(?string $birthday): ?string
    {
        if (!$birthday) {
            return null;
        }

        try {
            // format('m') = zero-padded month (01-12)
            // format('d') = zero-padded day   (01-31)
            // format('Y') = 4-digit year
            return Carbon::parse($birthday)->format('mdY');
        } catch (\Throwable $e) {
            Log::error("Birthday parse failed for value '{$birthday}': " . $e->getMessage());
            return null;
        }
    }
}
