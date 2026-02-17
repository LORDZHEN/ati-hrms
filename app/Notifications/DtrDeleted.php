<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class DtrDeleted extends Notification
{
    use Queueable;

    public function __construct(
        public string $employeeName,
        public string $fileName,
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
        $body = 'Your DTR record has been removed from the system.' . "\n" .
                'File: ' . $this->fileName;

        if ($this->reason) {
            $body .= "\n\nReason: " . $this->reason;
        }

        return FilamentNotification::make()
            ->title('🗑️ DTR Record Deleted')
            ->body($body)
            ->icon('heroicon-o-trash')
            ->iconColor('danger')
            ->actions([
                Action::make('contact_hr')
                    ->label('Contact HR')
                    ->url(route('filament.hrms.resources.daily-time-records.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
