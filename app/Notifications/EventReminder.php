<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class EventReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Event $event,
        public string $reminderType = 'tomorrow' // tomorrow, today, or one_hour
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
        $title = match ($this->reminderType) {
            'tomorrow' => '📅 Event Tomorrow',
            'today' => '🔔 Event Today',
            'one_hour' => '⏰ Event Starting Soon',
            default => '📅 Event Reminder',
        };

        $timeText = match ($this->reminderType) {
            'tomorrow' => 'Tomorrow at ' . $this->event->event_time->format('g:i A'),
            'today' => 'Today at ' . $this->event->event_time->format('g:i A'),
            'one_hour' => 'Starts in 1 hour at ' . $this->event->event_time->format('g:i A'),
            default => $this->event->event_date->format('M d, Y') . ' at ' . $this->event->event_time->format('g:i A'),
        };

        return FilamentNotification::make()
            ->title($title)
            ->body(
                '**' . $this->event->title . '**' . "\n" .
                $timeText . "\n" .
                'Location: ' . $this->event->location
            )
            ->icon('heroicon-o-bell-alert')
            ->iconColor($this->reminderType === 'one_hour' ? 'danger' : 'warning')
            ->actions([
                Action::make('view')
                    ->label('View Details')
                    ->url(route('filament.hrms.resources.events.edit', $this->event->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
