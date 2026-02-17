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
                    ->url(route('filament.hrms.resources.announcements.edit', $this->announcement->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get color based on announcement priority.
     */
    protected function getPriorityColor(): string
    {
        return match ($this->announcement->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'info',
        };
    }
}
