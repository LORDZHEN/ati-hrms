<?php

namespace App\Services;

use App\Models\LeaveApplication;
use App\Models\LeaveCredit;
use App\Models\LeaveCreditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LeaveCreditService
 *
 * Central service for all leave credit operations:
 *   - Monthly accrual (called by scheduler)
 *   - Deduction when a leave application is APPROVED
 *   - Reversal when an approved application is DISAPPROVED or DELETED
 *   - Manual HR adjustment
 *   - Annual reset
 *
 * Philippine CSC accrual rules:
 *   Vacation Leave  → 1.25 days / month  (15 days / year)
 *   Sick Leave      → 1.25 days / month  (15 days / year)
 *   Special / Mandatory are fixed yearly grants, not monthly accruals.
 */
class LeaveCreditService
{
    // ── Accrual rates per month ──────────────────────────────────────
    public const VACATION_MONTHLY  = 1.250;
    public const SICK_MONTHLY      = 1.250;

    // ── Annual fixed grants (credited once on Jan 1 or on hire) ─────
    public const SPECIAL_YEARLY    = 3.000;
    public const MANDATORY_YEARLY  = 5.000;

    // ────────────────────────────────────────────────────────────────
    // ACCRUAL
    // ────────────────────────────────────────────────────────────────

    /**
     * Accrue monthly leave credits for ONE employee.
     * Safe to call multiple times — skips if already run this month.
     */
    public function accrueForEmployee(User $employee): void
    {
        DB::transaction(function () use ($employee) {
            /** @var LeaveCredit $credit */
            $credit = LeaveCredit::firstOrCreate(
                ['user_id' => $employee->id],
                [
                    'vacation_leave_balance'  => 0,
                    'sick_leave_balance'      => 0,
                    'special_leave_balance'   => 0,
                    'mandatory_leave_balance' => 0,
                ]
            );

            $today = Carbon::today();

            // Skip if already accrued this month
            if (
                $credit->last_accrual_date &&
                $credit->last_accrual_date->isSameMonth($today)
            ) {
                return;
            }

            // ── Vacation Leave ──────────────────────────────────────
            $this->addCredit(
                credit: $credit,
                leaveType: 'vacation_leave',
                amount: self::VACATION_MONTHLY,
                transactionType: 'accrual',
                remarks: 'Monthly accrual — ' . $today->format('F Y'),
            );

            // ── Sick Leave ──────────────────────────────────────────
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

    /**
     * Run monthly accrual for ALL active employees.
     * Called by the scheduler on the 1st of every month.
     */
    public function accrueForAll(): void
    {
        User::where('role', 'employee')
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
    // DEDUCTION  (called when admin APPROVES a leave application)
    // ────────────────────────────────────────────────────────────────

    /**
     * Deduct leave credits when an application is approved.
     *
     * Leave types with no dedicated balance column (e.g. maternity, paternity)
     * are silently skipped — they don't consume from the tracked pools.
     */
    public function deductForApplication(LeaveApplication $application): void
    {
        $balanceCol = LeaveCredit::balanceColumn($application->type_of_leave);

        if (! $balanceCol) {
            return; // leave type not tracked
        }

        DB::transaction(function () use ($application, $balanceCol) {
            $credit = LeaveCredit::firstOrCreate(['user_id' => $application->employee_id]);

            $days = (float) $application->number_of_working_days;

            // Clamp: balance cannot go below 0
            $current = (float) $credit->{$balanceCol};
            $deduct  = min($days, $current);

            $credit->{$balanceCol} = max(0, $current - $deduct);
            $credit->save();

            LeaveCreditLog::create([
                'user_id'              => $application->employee_id,
                'leave_type'           => $application->type_of_leave,
                'transaction_type'     => 'deduction',
                'amount'               => -$deduct,
                'balance_after'        => $credit->{$balanceCol},
                'leave_application_id' => $application->id,
                'remarks'              => "Approved — {$days} day(s) deducted",
            ]);
        });
    }

    // ────────────────────────────────────────────────────────────────
    // REVERSAL  (called when an approved application is disapproved / deleted)
    // ────────────────────────────────────────────────────────────────

    /**
     * Reverse a previous deduction and restore the credits.
     */
    public function reverseDeduction(LeaveApplication $application): void
    {
        $balanceCol = LeaveCredit::balanceColumn($application->type_of_leave);

        if (! $balanceCol) {
            return;
        }

        DB::transaction(function () use ($application, $balanceCol) {
            $credit = LeaveCredit::firstOrCreate(['user_id' => $application->employee_id]);

            $days    = (float) $application->number_of_working_days;
            $maxCol  = LeaveCredit::maxColumn($application->type_of_leave);
            $current = (float) $credit->{$balanceCol};
            $restored = $days;

            // Respect the soft cap if one exists
            if ($maxCol) {
                $max      = (float) $credit->{$maxCol};
                $restored = min($days, max(0, $max - $current));
            }

            $credit->{$balanceCol} = $current + $restored;
            $credit->save();

            LeaveCreditLog::create([
                'user_id'              => $application->employee_id,
                'leave_type'           => $application->type_of_leave,
                'transaction_type'     => 'reversal',
                'amount'               => +$restored,
                'balance_after'        => $credit->{$balanceCol},
                'leave_application_id' => $application->id,
                'remarks'              => "Reversal — leave disapproved or cancelled",
            ]);
        });
    }

    // ────────────────────────────────────────────────────────────────
    // ANNUAL RESET  (called on Jan 1 by the scheduler)
    // ────────────────────────────────────────────────────────────────

    /**
     * Re-grant Special Privilege Leave (3 days) and Mandatory Leave (5 days)
     * at the start of each year. These are not month-accrued.
     *
     * Vacation and Sick leave carry over (up to the max cap) per CSC rules.
     */
    public function runAnnualReset(): void
    {
        $year = Carbon::now()->year;

        User::where('role', 'employee')
            ->chunk(100, function ($employees) use ($year) {
                foreach ($employees as $employee) {
                    DB::transaction(function () use ($employee, $year) {
                        $credit = LeaveCredit::firstOrCreate(['user_id' => $employee->id]);

                        // ── Special Privilege Leave reset ───────────────
                        $credit->special_leave_balance = self::SPECIAL_YEARLY;

                        LeaveCreditLog::create([
                            'user_id'          => $employee->id,
                            'leave_type'       => 'special_privilege_leave',
                            'transaction_type' => 'year_reset',
                            'amount'           => self::SPECIAL_YEARLY,
                            'balance_after'    => self::SPECIAL_YEARLY,
                            'remarks'          => "Annual reset — {$year}",
                        ]);

                        // ── Mandatory Leave reset ───────────────────────
                        $credit->mandatory_leave_balance = self::MANDATORY_YEARLY;

                        LeaveCreditLog::create([
                            'user_id'          => $employee->id,
                            'leave_type'       => 'mandatory_forced_leave',
                            'transaction_type' => 'year_reset',
                            'amount'           => self::MANDATORY_YEARLY,
                            'balance_after'    => self::MANDATORY_YEARLY,
                            'remarks'          => "Annual reset — {$year}",
                        ]);

                        $credit->save();
                    });
                }
            });
    }

    // ────────────────────────────────────────────────────────────────
    // MANUAL ADJUSTMENT  (HR admin override)
    // ────────────────────────────────────────────────────────────────

    /**
     * @param string $leaveType  One of the type_of_leave values
     * @param float  $amount     Positive to add credits, negative to remove
     * @param string $remarks    Required explanation for the audit log
     */
    public function adjustCredits(User $employee, string $leaveType, float $amount, string $remarks): void
    {
        $balanceCol = LeaveCredit::balanceColumn($leaveType);

        if (! $balanceCol) {
            throw new \InvalidArgumentException("No balance column for leave type: {$leaveType}");
        }

        DB::transaction(function () use ($employee, $leaveType, $balanceCol, $amount, $remarks) {
            $credit   = LeaveCredit::firstOrCreate(['user_id' => $employee->id]);
            $newBal   = max(0, (float) $credit->{$balanceCol} + $amount);

            $credit->{$balanceCol} = $newBal;
            $credit->save();

            LeaveCreditLog::create([
                'user_id'          => $employee->id,
                'leave_type'       => $leaveType,
                'transaction_type' => 'adjustment',
                'amount'           => $amount,
                'balance_after'    => $newBal,
                'remarks'          => $remarks,
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
        $col    = LeaveCredit::balanceColumn($leaveType);
        $maxCol = LeaveCredit::maxColumn($leaveType);

        $current = (float) $credit->{$col};
        $added   = $amount;

        // Respect the soft cap (if any)
        if ($maxCol) {
            $max   = (float) $credit->{$maxCol};
            $added = min($amount, max(0, $max - $current));
        }

        $newBal = $current + $added;
        $credit->{$col} = $newBal;
        // Note: caller must call $credit->save()

        LeaveCreditLog::create([
            'user_id'          => $credit->user_id,
            'leave_type'       => $leaveType,
            'transaction_type' => $transactionType,
            'amount'           => $added,
            'balance_after'    => $newBal,
            'remarks'          => $remarks,
        ]);
    }
}
