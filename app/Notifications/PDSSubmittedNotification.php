<?php

namespace App\Notifications;

use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class PDSSubmittedNotification extends BaseNotification
{
    use Queueable;

    public $user;
    public $pds;

    public function __construct($user, $pds)
    {
        $this->user = $user;
        $this->pds = $pds;
    }

    public function toFilament(): Notification
    {
        return Notification::make()
            ->title('New PDS Submitted')
            ->body("{$this->user->first_name} {$this->user->last_name} has submitted a PDS for the year {$this->pds->year}.")
            ->success()
            ->action('View', url("/hrms/personal-data-sheet/{$this->pds->id}/view"));
    }
}
