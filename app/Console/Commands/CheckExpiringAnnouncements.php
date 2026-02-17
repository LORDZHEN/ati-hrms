<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementExpiringSoon;
use App\Notifications\AnnouncementExpired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckExpiringAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'announcements:check-expiring';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring announcements and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring announcements...');

        // Check for announcements expiring in 24 hours (auto-expire)
        $expiringSoon = Announcement::where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addHours(24)])
            ->get();

        foreach ($expiringSoon as $announcement) {
            $this->info("Found announcement expiring soon: {$announcement->title}");

            // Send notification to admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AnnouncementExpiringSoon($announcement));
        }

        // Check for announcements expiring in 7 days (manual expiry_date)
        $expiringInWeek = Announcement::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereNull('expires_at') // Don't duplicate notifications for auto-expire
            ->whereBetween('expiry_date', [now(), now()->addDays(7)])
            ->get();

        foreach ($expiringInWeek as $announcement) {
            $this->info("Found announcement expiring in a week: {$announcement->title}");

            // Send notification to admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AnnouncementExpiringSoon($announcement));
        }

        // Check for expired announcements that are still active
        $expired = Announcement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now())
                    ->orWhere(function ($q) {
                        $q->whereNotNull('expiry_date')
                            ->where('expiry_date', '<', now());
                    });
            })
            ->get();

        foreach ($expired as $announcement) {
            $this->info("Found expired announcement: {$announcement->title}");

            // Deactivate the announcement
            $announcement->update(['is_active' => false]);

            // Send notification to admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AnnouncementExpired($announcement));
        }

        $this->info('Finished checking announcements.');
        $this->info("Expiring soon: {$expiringSoon->count()}");
        $this->info("Expiring in week: {$expiringInWeek->count()}");
        $this->info("Expired and deactivated: {$expired->count()}");

        return Command::SUCCESS;
    }
}
