<?php

namespace App\Notifications;

use App\Models\EmployeeDtr;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class DtrPdfGenerated extends Notification
{
    use Queueable;

    public function __construct(
        public EmployeeDtr $dtr,
        public string $pdfFileName
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
        return FilamentNotification::make()
            ->title('📑 DTR Report Generated')
            ->body(
                'Your DTR report has been successfully generated and is ready for download.' . "\n" .
                'Report: ' . $this->pdfFileName . "\n" .
                'Generated: ' . now()->format('M d, Y g:i A')
            )
            ->icon('heroicon-o-document-arrow-down')
            ->iconColor('success')
            ->actions([
                Action::make('view_dtr')
                    ->label('View DTR')
                    ->url(route('filament.hrms.resources.daily-time-records.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
