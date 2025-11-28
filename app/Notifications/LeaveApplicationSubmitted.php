<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class LeaveApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LeaveApplication $leave)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Leave Application Submitted',
            'body' => $this->leave->first_name . ' ' . $this->leave->last_name . ' submitted a leave application.',
            'url' => route('filament.hrms.resources.leave-applications.index'),
        ];
    }
}
