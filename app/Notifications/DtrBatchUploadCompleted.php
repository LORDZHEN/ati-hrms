<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class DtrBatchUploadCompleted extends Notification
{
    use Queueable;

    public function __construct(
        public int $successCount,
        public int $errorCount,
        public array $employeeNames = []
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
        $totalRecords = $this->successCount + $this->errorCount;
        $employeeList = count($this->employeeNames) > 0
            ? "\n\nEmployees: " . implode(', ', array_slice($this->employeeNames, 0, 5))
            : '';

        if (count($this->employeeNames) > 5) {
            $employeeList .= ' and ' . (count($this->employeeNames) - 5) . ' more';
        }

        return FilamentNotification::make()
            ->title('✅ Batch DTR Upload Completed')
            ->body(
                "Successfully uploaded {$this->successCount} out of {$totalRecords} DTR record(s)." .
                ($this->errorCount > 0 ? "\n⚠️ {$this->errorCount} upload(s) failed." : '') .
                $employeeList
            )
            ->icon('heroicon-o-cloud-arrow-up')
            ->iconColor($this->errorCount > 0 ? 'warning' : 'success')
            ->actions([
                Action::make('view_all')
                    ->label('View All DTRs')
                    ->url(route('filament.hrms.resources.daily-time-records.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
