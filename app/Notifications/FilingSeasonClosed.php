<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FilingSeasonClosed extends Notification
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
            'title' => '🔒 Filing Season Has Closed',
            'body' => 'The administrator has closed the filing season. SALN and PDS editing is no longer available until the next filing season.',
            'icon' => 'heroicon-o-lock-closed',
            'color' => 'danger',
            'actions' => [
                [
                    'label' => 'View My Documents',
                    'url' => '/hrms/salns',
                ],
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
