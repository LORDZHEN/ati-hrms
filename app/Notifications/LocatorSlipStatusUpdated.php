<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LocatorSlipStatusUpdated extends Notification
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
            'title' => 'Locator Slip Status Update',
            'body' => "Your locator slip has been " . strtoupper($this->slip->status) . ".",
            'url' => route('filament.hrms.resources.locator-slip.index'),
        ];
    }
}
