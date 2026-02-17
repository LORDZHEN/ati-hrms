<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class EventStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Event $event,
        public bool $isActive
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
        $status = $this->isActive ? 'Activated' : 'Deactivated';
        $icon = $this->isActive ? 'heroicon-o-eye' : 'heroicon-o-eye-slash';
        $color = $this->isActive ? 'success' : 'warning';
        $emoji = $this->isActive ? '✅' : '⏸️';

        return FilamentNotification::make()
            ->title($emoji . ' Event ' . $status)
            ->body(
                '**' . $this->event->title . '**' . "\n" .
                'Date: ' . $this->event->event_date->format('M d, Y') . ' at ' . $this->event->event_time->format('g:i A') . "\n" .
                'This event is now ' . strtolower($status) . '.'
            )
            ->icon($icon)
            ->iconColor($color)
            ->actions([
                Action::make('view')
                    ->label('View Event')
                    ->url(route('filament.hrms.resources.events.edit', $this->event->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
