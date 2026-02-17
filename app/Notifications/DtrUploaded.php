<?php

namespace App\Notifications;

use App\Models\EmployeeDtr;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class DtrUploaded extends Notification
{
    use Queueable;

    public function __construct(public EmployeeDtr $dtr)
    {
    }

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
        $filePath = $this->dtr->file_path;
        if (is_array($filePath)) {
            $filePath = $filePath[0] ?? '';
        }

        return FilamentNotification::make()
            ->title('📄 New DTR Uploaded')
            ->body(
                'Your Daily Time Record has been uploaded and is now available for review.' . "\n" .
                'File: ' . basename($filePath) . "\n" .
                'Uploaded: ' . $this->dtr->created_at->format('M d, Y g:i A')
            )
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('View DTR')
                    ->url(route('filament.hrms.resources.daily-time-records.index'))
                    ->markAsRead(),
                Action::make('download')
                    ->label('Download CSV')
                    ->url(\Illuminate\Support\Facades\Storage::disk('public')->url($filePath))
                    ->openUrlInNewTab(),
            ])
            ->getDatabaseMessage();
    }
}
