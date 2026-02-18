<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class AnnouncementCreated extends Notification
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
            ->title('📢 New Announcement Posted')
            ->body(
                'Priority: ' . ucfirst($this->announcement->priority) . "\n" .
                '**' . $this->announcement->title . '**' . "\n" .
                substr($this->announcement->message, 0, 100) . '...'
            )
            ->icon($this->announcement->icon ?? 'heroicon-o-megaphone')
            ->iconColor($this->getPriorityColor())
            ->actions([
                Action::make('view')
                    ->label('View Announcement')
                    ->url(route('filament.hrms.resources.announcements.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    protected function getPriorityColor(): string
    {
        return match ($this->announcement->priority) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'success',
            default  => 'info',
        };
    }
}
