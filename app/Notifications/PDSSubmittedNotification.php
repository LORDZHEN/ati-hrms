<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class PDSSubmittedNotification extends BaseNotification
{
    use Queueable;

    public function __construct(public $user, public $pds) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'New PDS Submitted',
            'body' => "{$this->user->first_name} {$this->user->last_name} submitted a PDS for the year {$this->pds->year}.",
            'url' => url("/hrms/personal-data-sheet/{$this->pds->id}/view"),
        ];
    }

    public function notifyUser($user)
    {
        $user->notify($this); // store in DB

        if (class_exists(FilamentNotification::class)) {
            $data = $this->toDatabase($user);
            FilamentNotification::make()
                ->title($data['title'])
                ->body($data['body'])
                ->success()
                ->action('View', $data['url'])
                ->send();
        }
    }
}
