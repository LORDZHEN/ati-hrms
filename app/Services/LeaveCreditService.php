<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveCreditService
{
    public const VACATION_MONTHLY = 1.250;
    public const SICK_MONTHLY = 1.250;
    public const SPECIAL_YEARLY = 3.000;
    public const MANDATORY_YEARLY = 5.000;

    // Default max caps — used whenever a new LeaveCredit row is created
    private const VACATION_MAX_DEFAULT = 30.000;
    private const SICK_MAX_DEFAULT = 30.000;

    // ────────────────────────────────────────────────────────────────
    // SHARED HELPER — always use this to get-or-create a credit record
    // ────────────────────────────────────────────────────────────────

    /**
     * Get or create a LeaveCredit record with proper default values.
     * FIXED: All firstOrCreate calls now include default max caps so
     * the accrual cap math never silently results in 0 credits added.
     */
    private function getOrCreateCredit(int $userId): LeaveCredit
    {
        return LeaveCredit::firstOrCreate(
            ['user_id' => $userId],
            [
                'vacation_leave_balance' => 0,
                'sick_leave_balance' => 0,
                'special_leave_balance' => 0,
                'mandatory_leave_balance' => 0,
                'vacation_leave_max' => self::VACATION_MAX_DEFAULT,
                'sick_leave_max' => self::SICK_MAX_DEFAULT,
            ]
        );
    }

    // ────────────────────────────────────────────────────────────────
    // ACCRUAL
    // ────────────────────────────────────────────────────────────────

    public function accrueForEmployee(User $employee): void
    {
        DB::transaction(function () use ($employee) {
            $credit = $this->getOrCreateCredit($employee->id);

            $today = Carbon::today();

            if (
                $credit->last_accrual_date &&
                $credit->last_accrual_date->isSameMonth($today)
            ) {
                return;
            }

            $this->addCredit(
                credit: $credit,
                leaveType: 'vacation_leave',
                amount: self::VACATION_MONTHLY,
                transactionType: 'accrual',
                remarks: 'Monthly accrual — ' . $today->format('F Y'),
            );

            $this->addCredit(
                credit: $credit,
                leaveType: 'sick_leave',
                amount: self::SICK_MONTHLY,
                transactionType: 'accrual',
                remarks: 'Monthly accrual — ' . $today->format('F Y'),
            );

            $credit->last_accrual_date = $today;
            $credit->save();
        });
    }

    public function accrueForAll(): void
    {
        User::where('role', User::ROLE_REGULAR)
            ->chunk(100, function ($employees) {
                foreach ($employees as $employee) {
                    try {
                        $this->accrueForEmployee($employee);
                    } catch (\Throwable $e) {
                        Log::error("Leave accrual failed for user {$employee->id}: " . $e->getMessage());
                    }
                }
            });
    }

    // ────────────────────────────────────────────────────────────────
    // DEDUCTION
    // ────────────────────────────────────────────────────────────────

    public function deductForApplication(LeaveApplication $application): void
    {
        $balanceCol = LeaveCredit::balanceColumn($application->type_of_leave);

        if (!$balanceCol) {
            return;
        }

        DB::transaction(function () use ($application, $balanceCol) {
            // FIXED: was firstOrCreate(['user_id' => ...]) with no defaults
            $credit = $this->getOrCreateCredit($application->employee_id);

            $days = (float) $application->number_of_working_days;
            $current = (float) $credit->{$balanceCol};
            $deduct = min($days, $current);

            $credit->{$balanceCol} = max(0, $current - $deduct);
            $credit->save();

            LeaveCreditLog::create([
                'user_id' => $application->employee_id,
                'leave_type' => $application->type_of_leave,
                'transaction_type' => 'deduction',
                'amount' => -$deduct,
                'balance_after' => $credit->{$balanceCol},
                'leave_application_id' => $application->id,
                'remarks' => "Approved — {$days} day(s) deducted",
            ]);
        });
    }

    // ────────────────────────────────────────────────────────────────
    // REVERSAL
    // ────────────────────────────────────────────────────────────────

    public function reverseDeduction(LeaveApplication $application): void
    {
        $balanceCol = LeaveCredit::balanceColumn($application->type_of_leave);

        if (!$balanceCol) {
            return;
        }

        DB::transaction(function () use ($application, $balanceCol) {
            // FIXED: was firstOrCreate(['user_id' => ...]) with no defaults
            $credit = $this->getOrCreateCredit($application->employee_id);
            $days = (float) $application->number_of_working_days;
            $maxCol = LeaveCredit::maxColumn($application->type_of_leave);
            $current = (float) $credit->{$balanceCol};
            $restored = $days;

            if ($maxCol) {
                $max = (float) $credit->{$maxCol};
                $restored = min($days, max(0, $max - $current));
            }

            $credit->{$balanceCol} = $current + $restored;
            $credit->save();

            LeaveCreditLog::create([
                'user_id' => $application->employee_id,
                'leave_type' => $application->type_of_leave,
                'transaction_type' => 'reversal',
                'amount' => +$restored,
                'balance_after' => $credit->{$balanceCol},
                'leave_application_id' => $application->id,
                'remarks' => "Reversal — leave disapproved or cancelled",
            ]);
        });
    }

    // ────────────────────────────────────────────────────────────────
    // ANNUAL RESET
    // ────────────────────────────────────────────────────────────────

    public function runAnnualReset(): void
    {
        $year = Carbon::now()->year;

        User::where('role', User::ROLE_REGULAR)
            ->chunk(100, function ($employees) use ($year) {
                foreach ($employees as $employee) {
                    DB::transaction(function () use ($employee, $year) {
                        // FIXED: was firstOrCreate(['user_id' => ...]) with no defaults
                        $credit = $this->getOrCreateCredit($employee->id);

                        $credit->special_leave_balance = self::SPECIAL_YEARLY;
                        $credit->mandatory_leave_balance = self::MANDATORY_YEARLY;
                        $credit->save();

                        LeaveCreditLog::create([
                            'user_id' => $employee->id,
                            'leave_type' => 'special_privilege_leave',
                            'transaction_type' => 'year_reset',
                            'amount' => self::SPECIAL_YEARLY,
                            'balance_after' => self::SPECIAL_YEARLY,
                            'remarks' => "Annual reset — {$year}",
                        ]);

                        LeaveCreditLog::create([
                            'user_id' => $employee->id,
                            'leave_type' => 'mandatory_forced_leave',
                            'transaction_type' => 'year_reset',
                            'amount' => self::MANDATORY_YEARLY,
                            'balance_after' => self::MANDATORY_YEARLY,
                            'remarks' => "Annual reset — {$year}",
                        ]);
                    });
                }
            });
    }

    // ────────────────────────────────────────────────────────────────
    // MANUAL ADJUSTMENT
    // ────────────────────────────────────────────────────────────────

    public function adjustCredits(User $employee, string $leaveType, float $amount, string $remarks): void
    {
        $balanceCol = LeaveCredit::balanceColumn($leaveType);

        if (!$balanceCol) {
            throw new \InvalidArgumentException("No balance column for leave type: {$leaveType}");
        }

        DB::transaction(function () use ($employee, $leaveType, $balanceCol, $amount, $remarks) {
            // FIXED: was firstOrCreate(['user_id' => ...]) with no defaults
            $credit = $this->getOrCreateCredit($employee->id);
            $newBal = max(0, (float) $credit->{$balanceCol} + $amount);

            $credit->{$balanceCol} = $newBal;
            $credit->save();

            LeaveCreditLog::create([
                'user_id' => $employee->id,
                'leave_type' => $leaveType,
                'transaction_type' => 'adjustment',
                'amount' => $amount,
                'balance_after' => $newBal,
                'remarks' => $remarks,
            ]);
        });
    }

    // ────────────────────────────────────────────────────────────────
    // INTERNAL HELPER
    // ────────────────────────────────────────────────────────────────

    private function addCredit(
        LeaveCredit $credit,
        string $leaveType,
        float $amount,
        string $transactionType,
        string $remarks = '',
    ): void {
        $col = LeaveCredit::balanceColumn($leaveType);
        $maxCol = LeaveCredit::maxColumn($leaveType);

        $current = (float) $credit->{$col};
        $added = $amount;

        if ($maxCol) {
            $max = (float) $credit->{$maxCol};
            $added = min($amount, max(0, $max - $current));
        }

        $newBal = $current + $added;
        $credit->{$col} = $newBal;

        LeaveCreditLog::create([
            'user_id' => $credit->user_id,
            'leave_type' => $leaveType,
            'transaction_type' => $transactionType,
            'amount' => $added,
            'balance_after' => $newBal,
            'remarks' => $remarks,
        ]);
    }
}
