<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LeaveApplicationRemarksAdded extends Notification
{
    use Queueable;

    public function __construct(public LeaveApplication $leave)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Admin Remarks on Your Leave Application')
            ->body(
                'Remarks were added to your ' .
                str_replace('_', ' ', ucwords($this->leave->type_of_leave, '_')) .
                ': "' . $this->leave->remarks . '"'
            )
            ->icon('heroicon-o-chat-bubble-left-right')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View Application')
                    ->url(route('filament.hrms.resources.leave-applications.view', $this->leave->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
