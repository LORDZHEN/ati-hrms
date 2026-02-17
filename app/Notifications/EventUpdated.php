<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class EventUpdated extends Notification
{
    use Queueable;

    public function __construct(public Event $event) {}

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
            ->title('✏️ Event Updated')
            ->body(
                '**' . $this->event->title . '**' . "\n" .
                'Date: ' . $this->event->event_date->format('M d, Y') . ' at ' . $this->event->event_time->format('g:i A') . "\n" .
                'Location: ' . $this->event->location . "\n" .
                'The event details have been updated.'
            )
            ->icon('heroicon-o-pencil-square')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('View Changes')
                    ->url(route('filament.hrms.resources.events.edit', $this->event->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
