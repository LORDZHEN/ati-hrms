<?php

namespace App\Console\Commands;

use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * One-time command: backfill employee_name on TransactionHistory rows
 * where employee_name is null, empty, or 'Unknown' / 'Unknown Employee'.
 *
 * Usage:
 *   php artisan hrms:backfill-transaction-names
 *   php artisan hrms:backfill-transaction-names --dry-run
 */
class BackfillTransactionHistoryNames extends Command
{
    protected $signature   = 'hrms:backfill-transaction-names {--dry-run : Preview changes without writing to DB}';
    protected $description = 'Backfill missing employee_name on transaction_histories from the related user record.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        // Target rows: null, empty string, or the two sentinel fallbacks
        $rows = TransactionHistory::whereNull('employee_name')
            ->orWhere('employee_name', '')
            ->orWhereIn('employee_name', ['Unknown', 'Unknown Employee'])
            ->get();

        if ($rows->isEmpty()) {
            $this->info('Nothing to backfill — all rows already have an employee_name.');
            return self::SUCCESS;
        }

        $this->info("Found {$rows->count()} row(s) to backfill.");

        // Pre-load all referenced users in one query to avoid N+1
        $userIds = $rows->pluck('user_id')->filter()->unique()->values();
        $users   = User::whereIn('id', $userIds)->get()->keyBy('id');

        $fixed   = 0;
        $skipped = 0;

        foreach ($rows as $tx) {
            $name = null;

            if ($tx->user_id && $users->has($tx->user_id)) {
                $user = $users->get($tx->user_id);
                $name = $user->full_name ?? $user->name ?? null;
            }

            if (blank($name)) {
                $label = $tx->user_id ? "Employee #{$tx->user_id}" : 'Unknown Employee';
                $this->line("  [SKIP] ID {$tx->id} — no user found, keeping as '{$label}'");
                if (! $dryRun) {
                    $tx->updateQuietly(['employee_name' => $label]);
                }
                $skipped++;
                continue;
            }

            $this->line("  [FIX]  ID {$tx->id} — '{$tx->employee_name}' → '{$name}'");

            if (! $dryRun) {
                $tx->updateQuietly(['employee_name' => $name]);
            }

            $fixed++;
        }

        $this->newLine();
        $this->info("Done. Fixed: {$fixed} | Skipped (no user): {$skipped}");

        if ($dryRun) {
            $this->warn('DRY RUN complete — re-run without --dry-run to apply changes.');
        }

        return self::SUCCESS;
    }
}
