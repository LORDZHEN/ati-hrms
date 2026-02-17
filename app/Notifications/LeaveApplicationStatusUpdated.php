<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LeaveApplicationStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LeaveApplication $leave) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isApproved = $this->leave->status === 'approved';

        return FilamentNotification::make()
            ->title('Leave Application ' . ucfirst($this->leave->status))
            ->body(
                'Your ' .
                str_replace('_', ' ', ucwords($this->leave->type_of_leave, '_')) .
                ' has been ' . strtoupper($this->leave->status) . '.' .
                ($this->leave->disapproval_reason
                    ? ' Reason: ' . $this->leave->disapproval_reason
                    : '')
            )
            ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->iconColor($isApproved ? 'success' : 'danger')
            ->actions([
                Action::make('view')
                    ->label('View Application')
                    ->url(route('filament.hrms.resources.leave-applications.view', $this->leave->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
