<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LocatorSlipSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LocatorSlip $slip)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Locator Slip Submitted',
            'body' => $this->slip->employee_name . ' filed a locator slip.',
            'url' => route('filament.hrms.resources.locator-slip.index'),
        ];
    }
}
