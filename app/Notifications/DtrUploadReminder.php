<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class DtrUploadReminder extends Notification
{
    use Queueable;

    public function __construct(
        public string $period = 'monthly', // monthly, weekly
        public ?string $dueDate = null
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
        $title = match($this->period) {
            'monthly' => '📅 Monthly DTR Upload Due',
            'weekly' => '📅 Weekly DTR Upload Due',
            default => '📅 DTR Upload Reminder',
        };

        $body = "This is a reminder to upload your {$this->period} DTR.";

        if ($this->dueDate) {
            $body .= "\n\n⏰ Due Date: " . $this->dueDate;
        }

        $body .= "\n\nPlease ensure your DTR is submitted on time.";

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-bell-alert')
            ->iconColor('warning')
            ->actions([
                Action::make('view_dtrs')
                    ->label('View My DTRs')
                    ->url(route('filament.hrms.resources.daily-time-records.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
