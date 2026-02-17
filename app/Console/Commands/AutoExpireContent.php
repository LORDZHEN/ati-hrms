<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Console\Command;

class AutoExpireContent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hrms:auto-expire-content
                            {--dry-run : Preview what would be expired without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-deactivate announcements whose expires_at has passed and events that ended more than 24 hours ago.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        $this->expireAnnouncements($isDryRun);
        $this->expireEvents($isDryRun);

        $this->info('✅ Auto-expire check complete.');

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function expireAnnouncements(bool $isDryRun): void
    {
        $announcements = Announcement::expiredAndActive()->get();

        if ($announcements->isEmpty()) {
            $this->line('  Announcements: nothing to expire.');
            return;
        }

        $this->info("  Announcements: found {$announcements->count()} to deactivate.");

        foreach ($announcements as $announcement) {
            $this->line("    ↳ [{$announcement->id}] \"{$announcement->title}\" (expired at {$announcement->expires_at})");

            if (! $isDryRun) {
                $announcement->update(['is_active' => false]);
            }
        }
    }

    private function expireEvents(bool $isDryRun): void
    {
        $events = Event::expiredAndActive()->get();

        if ($events->isEmpty()) {
            $this->line('  Events: nothing to expire.');
            return;
        }

        $this->info("  Events: found {$events->count()} to deactivate.");

        foreach ($events as $event) {
            $endsAt = $event->event_date->copy()->addHours(24)->format('Y-m-d H:i');
            $this->line("    ↳ [{$event->id}] \"{$event->title}\" (24h window ended at {$endsAt})");

            if (! $isDryRun) {
                $event->update(['is_active' => false]);
            }
        }
    }
}
