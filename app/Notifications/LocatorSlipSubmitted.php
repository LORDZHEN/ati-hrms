<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class LocatorSlipSubmitted extends Notification
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
            'title' => 'New Locator Slip Submitted',
            'body' => $this->slip->employee_name . ' filed a locator slip.',
            'url' => route('filament.hrms.resources.locator-slip.index'),
        ];
    }

    public function toFilament($notifiable)
    {
        return FilamentNotification::make()
            ->title('New Locator Slip')
            ->body($this->slip->employee_name . ' filed a locator slip.')
            ->success()
            ->action('View', route('filament.hrms.resources.locator-slip.index'));
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
                ->action('View', $data['url'])
                ->send();
        }
    }
}
