<?php

namespace App\Console\Commands;

use App\Services\LeaveCreditService;
use Illuminate\Console\Command;

/**
 * Manually trigger or test monthly leave credit accrual.
 *
 * Usage:
 *   php artisan leave:accrue-monthly
 */
class AccrueMonthlyLeaveCredits extends Command
{
    protected $signature   = 'leave:accrue-monthly';
    protected $description = 'Accrue monthly Vacation and Sick Leave credits for all employees';

    public function handle(LeaveCreditService $service): int
    {
        $this->info('Running monthly leave credit accrual...');

        $service->accrueForAll();

        $this->info('✅ Monthly accrual completed.');

        return self::SUCCESS;
    }
}
