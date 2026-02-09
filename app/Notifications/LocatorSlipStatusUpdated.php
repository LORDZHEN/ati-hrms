<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class LocatorSlipStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LocatorSlip $slip)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Locator Slip Status Update',
            'body' => "Your locator slip has been " . strtoupper($this->slip->status) . ".",
            'url' => route('filament.hrms.resources.locator-slip.index'),
        ];
    }

    public function toFilament($notifiable)
    {
        return FilamentNotification::make()
            ->title('Locator Slip Status Updated')
            ->body("Your locator slip has been " . strtoupper($this->slip->status) . ".")
            ->success()
            ->url(route('filament.hrms.resources.locator-slip.index'));
    }

    public function notifyUser($user)
    {
        $user->notify($this);
        if (class_exists(FilamentNotification::class)) {
            $data = $this->toDatabase($user);
            FilamentNotification::make()
                ->title($data['title'])
                ->body($data['body'])
                ->success()
                ->url($data['url'])
                ->send();
        }
    }
}
