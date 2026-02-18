<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class AnnouncementUpdated extends Notification
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
            ->title('✏️ Announcement Updated')
            ->body(
                '**' . $this->announcement->title . '**' . "\n" .
                'The announcement has been updated. Check the latest changes.'
            )
            ->icon('heroicon-o-pencil-square')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('View Announcement')
                    ->url(route('filament.hrms.resources.announcements.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
