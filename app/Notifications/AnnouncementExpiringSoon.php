<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class AnnouncementExpiringSoon extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $expiresAt = $this->announcement->expires_at ?? $this->announcement->expiry_date;

        return FilamentNotification::make()
            ->title('⏰ Announcement Expiring Soon')
            ->body(
                '**' . $this->announcement->title . '**' . "\n" .
                'This announcement will expire ' . $expiresAt->diffForHumans() . '.' . "\n" .
                'Consider extending or renewing it if needed.'
            )
            ->icon('heroicon-o-clock')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View Announcements')
                    ->url(route('filament.hrms.resources.announcements.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
