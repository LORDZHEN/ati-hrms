<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class EventCancelled extends Notification
{
    use Queueable;

    public function __construct(
        public Event $event,
        public ?string $reason = null
    ) {}

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
        $body = '**' . $this->event->title . '**' . "\n" .
                'Scheduled: ' . $this->event->event_date->format('M d, Y') . ' at ' . $this->event->event_time->format('g:i A') . "\n" .
                'Location: ' . $this->event->location;

        if ($this->reason) {
            $body .= "\n\nReason: " . $this->reason;
        }

        return FilamentNotification::make()
            ->title('❌ Event Cancelled')
            ->body($body)
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->actions([
                Action::make('acknowledge')
                    ->label('Acknowledge')
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
