<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveApplicationStatusUpdated extends Notification
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
            'title' => 'Leave Application Update',
            'body' => "Your leave application has been " . strtoupper($this->leave->status) . ".",
            'url' => route('filament.hrms.resources.leave-applications.index'),
        ];
    }
}
