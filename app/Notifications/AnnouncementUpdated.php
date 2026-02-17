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

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
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
                    ->label('View Changes')
                    ->url(route('filament.hrms.resources.announcements.edit', $this->announcement->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
