<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * ADD these two entries to your existing schedule() method.
     * Do NOT replace your entire Kernel — only add the two lines shown.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ── Leave: monthly accrual ────────────────────────────────────
        // Runs at 00:01 on the 1st of every month.
        $schedule->command('leave:accrue-monthly')
            ->monthlyOn(1, '00:01')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/leave-accrual.log'));

        // ── Leave: annual reset ───────────────────────────────────────
        // Runs at 00:05 on January 1st each year.
        $schedule->command('leave:annual-reset')
            ->yearlyOn(1, 1, '00:05')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/leave-annual-reset.log'));
    }

    /**
     * Register the commands for the application.
     * Add the two new commands to your existing $commands array.
     */
    protected $commands = [
        Commands\AccrueMonthlyLeaveCredits::class,
        Commands\RunAnnualLeaveReset::class,
    ];
}
