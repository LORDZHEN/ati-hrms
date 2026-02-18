<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class AnnouncementExpired extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('⛔ Announcement Has Expired')
            ->body(
                '**' . $this->announcement->title . '**' . "\n" .
                'This announcement has expired and is no longer visible to employees.'
            )
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->actions([
                Action::make('view')
                    ->label('View Announcements')
                    ->url(route('filament.hrms.resources.announcements.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
