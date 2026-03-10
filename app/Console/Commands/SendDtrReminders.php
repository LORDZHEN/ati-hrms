<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\EmployeeDtr;
use App\Notifications\DtrUploadReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendDtrReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dtr:send-reminders {--type=monthly : Type of reminder (monthly|weekly)}';

    /**
     * The console command description.
     */
    protected $description = 'Send DTR upload reminders to employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $this->info("Sending {$type} DTR reminders...");

        // Get all employees
        $employees = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])->get();

        $remindedCount = 0;

        foreach ($employees as $employee) {
            // Check if employee needs reminder based on type
            if ($this->needsReminder($employee, $type)) {
                $dueDate = $this->getDueDate($type);

                $employee->notify(new DtrUploadReminder($type, $dueDate));

                $this->info("Sent {$type} reminder to: {$employee->name}");
                $remindedCount++;
            }
        }

        $this->info("Finished sending reminders. Total sent: {$remindedCount}");

        return Command::SUCCESS;
    }

    /**
     * Check if employee needs a reminder
     */
    protected function needsReminder(User $employee, string $type): bool
    {
        $now = Carbon::now();

        if ($type === 'monthly') {
            // Send reminder on the 1st of each month if no DTR uploaded for previous month
            if ($now->day === 1) {
                $lastMonth = $now->copy()->subMonth();
                $hasLastMonthDtr = EmployeeDtr::where('employee_id', $employee->id)
                    ->whereYear('created_at', $lastMonth->year)
                    ->whereMonth('created_at', $lastMonth->month)
                    ->exists();

                return !$hasLastMonthDtr;
            }
        }

        if ($type === 'weekly') {
            // Send reminder every Monday if no DTR uploaded last week
            if ($now->isMonday()) {
                $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
                $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

                $hasLastWeekDtr = EmployeeDtr::where('employee_id', $employee->id)
                    ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                    ->exists();

                return !$hasLastWeekDtr;
            }
        }

        return false;
    }

    /**
     * Get the due date message
     */
    protected function getDueDate(string $type): string
    {
        $now = Carbon::now();

        if ($type === 'monthly') {
            // Due on the 5th of the month
            return $now->day(5)->format('F d, Y');
        }

        if ($type === 'weekly') {
            // Due on Friday
            return $now->next(Carbon::FRIDAY)->format('F d, Y');
        }

        return 'As soon as possible';
    }
}
