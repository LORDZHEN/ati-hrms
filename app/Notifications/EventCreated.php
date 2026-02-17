<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class EventCreated extends Notification
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
            ->title('📅 New Event Scheduled')
            ->body(
                '**' . $this->event->title . '**' . "\n" .
                'Date: ' . $this->event->event_date->format('M d, Y') . ' at ' . $this->event->event_time->format('g:i A') . "\n" .
                'Location: ' . $this->event->location . "\n" .
                'Type: ' . ucfirst($this->event->type)
            )
            ->icon($this->getTypeIcon())
            ->iconColor($this->getTypeColor())
            ->actions([
                Action::make('view')
                    ->label('View Event')
                    ->url(route('filament.hrms.resources.events.edit', $this->event->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get icon based on event type.
     */
    protected function getTypeIcon(): string
    {
        return match ($this->event->type) {
            'event' => 'heroicon-o-star',
            'meeting' => 'heroicon-o-user-group',
            'deadline' => 'heroicon-o-exclamation-circle',
            'training' => 'heroicon-o-academic-cap',
            'holiday' => 'heroicon-o-sun',
            default => 'heroicon-o-calendar-days',
        };
    }

    /**
     * Get color based on event type.
     */
    protected function getTypeColor(): string
    {
        return match ($this->event->type) {
            'event' => 'success',
            'meeting' => 'info',
            'deadline' => 'danger',
            'training' => 'warning',
            'holiday' => 'primary',
            default => 'gray',
        };
    }
}
