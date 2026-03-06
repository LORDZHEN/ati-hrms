<?php

namespace Database\Seeders;

use App\Models\LeaveCredit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeds the leave_credits table (NOT the users table) with opening balances.
 *
 * Run: php artisan db:seed --class=LeaveCreditSeeder
 *
 * This uses updateOrCreate so it is safe to re-run. If a row already exists,
 * it will be overwritten with the correct opening balances.
 */
class LeaveCreditSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();

        $this->command->info("Seeding leave_credits table for {$employees->count()} employee(s)...");

        foreach ($employees as $employee) {
            LeaveCredit::updateOrCreate(
                // ── Find by user_id ──────────────────────────────────
                ['user_id' => $employee->id],

                // ── Set / overwrite with correct opening balances ────
                [
                    'vacation_leave_balance' => 15.000,
                    'sick_leave_balance' => 15.000,
                    'special_leave_balance' => 3.000,
                    'mandatory_leave_balance' => 5.000,

                    'vacation_leave_max' => 30.000,
                    'sick_leave_max' => 30.000,

                    // Set last_accrual_date to THIS month so the monthly
                    // accrual scheduler does NOT double-add on the next run.
                    // The opening 15 days already represents the annual grant.
                    'last_accrual_date' => Carbon::today()->startOfMonth(),
                ]
            );
        }

        $this->command->info('✅ leave_credits table seeded successfully.');
        $this->command->info('');
        $this->command->warn('NOTE: The leave balance columns on the users table (vacation_leave_balance,');
        $this->command->warn('sick_leave_balance, etc.) are no longer used by the widget or service.');
        $this->command->warn('All balances are now managed exclusively in the leave_credits table.');
    }
}
