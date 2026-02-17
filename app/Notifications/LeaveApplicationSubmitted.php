<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LeaveApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LeaveApplication $leave) {}

    /**
     * Only 'database' — stores to notifications table,
     * which Filament's bell reads automatically.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * getDatabaseMessage() converts the Filament builder into
     * the exact array shape Filament's bell renderer expects
     * (title, body, color, icon, actions, etc.)
     */
    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Leave Application Submitted')
            ->body(
                $this->leave->first_name . ' ' . $this->leave->last_name .
                ' submitted a ' .
                str_replace('_', ' ', ucwords($this->leave->type_of_leave, '_')) .
                ' application.'
            )
            ->icon('heroicon-o-document-text')
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
