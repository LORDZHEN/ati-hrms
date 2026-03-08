<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        Commands\AccrueMonthlyLeaveCredits::class,
        Commands\RunAnnualLeaveReset::class,
        Commands\AutoExpireContent::class,
        Commands\BackfillTransactionHistoryNames::class,   // ← ADDED
        Commands\CheckExpiringAnnouncements::class,
        Commands\SendDtrReminders::class,
        Commands\SendEventReminders::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ── Leave: monthly accrual ────────────────────────────────────
        $schedule->command('leave:accrue-monthly')
            ->monthlyOn(1, '00:01')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/leave-accrual.log'));

        // ── Leave: annual reset ───────────────────────────────────────
        $schedule->command('leave:annual-reset')
            ->yearlyOn(1, 1, '00:05')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/leave-annual-reset.log'));

        // ── Content: auto-expire announcements & events ───────────────
        $schedule->command('hrms:auto-expire-content')
            ->hourly()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/auto-expire.log'));

        // ── Announcements: expiry notifications ───────────────────────
        $schedule->command('announcements:check-expiring')
            ->dailyAt('08:00')
            ->withoutOverlapping();

        // ── DTR: monthly upload reminders (1st of month) ─────────────
        $schedule->command('dtr:send-reminders --type=monthly')
            ->monthlyOn(1, '07:00')
            ->withoutOverlapping();

        // ── DTR: weekly upload reminders (every Monday) ───────────────
        $schedule->command('dtr:send-reminders --type=weekly')
            ->weeklyOn(1, '07:00')
            ->withoutOverlapping();

        // ── Events: upcoming event reminders (every 10 minutes) ──────
        $schedule->command('events:send-reminders')
            ->everyTenMinutes()
            ->withoutOverlapping();

        // ── BackfillTransactionHistoryNames is a one-time command ─────
        // Run it manually: php artisan hrms:backfill-transaction-names
        // No schedule entry needed.
    }
}
