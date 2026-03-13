<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FilingSeasonOpened extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '📂 Filing Season is Now Open',
            'body' => 'The administrator has opened the filing season. You may now edit and resubmit your SALN and PDS if they have been unlocked.',
            'icon' => 'heroicon-o-lock-open',
            'color' => 'success',
            'actions' => [
                [
                    'label' => 'View SALN',
                    'url' => '/hrms/salns',
                ],
                [
                    'label' => 'View PDS',
                    'url' => '/hrms/pds',
                ],
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
