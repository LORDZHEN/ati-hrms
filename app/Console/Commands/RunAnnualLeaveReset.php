<?php

namespace App\Console\Commands;

use App\Services\LeaveCreditService;
use Illuminate\Console\Command;

/**
 * Reset Special Privilege and Mandatory Leave credits at the start of a new year.
 *
 * Usage:
 *   php artisan leave:annual-reset
 */
class RunAnnualLeaveReset extends Command
{
    protected $signature = 'leave:annual-reset';
    protected $description = 'Reset Special Privilege (3 days) and Mandatory (5 days) leave credits for the new year';

    public function handle(LeaveCreditService $service): int
    {
        $this->info('Running annual leave credit reset...');

        $service->runAnnualReset();

        $this->info('✅ Annual reset completed.');

        return self::SUCCESS;
    }
}
