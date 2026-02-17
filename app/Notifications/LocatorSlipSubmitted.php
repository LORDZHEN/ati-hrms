<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LocatorSlipSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LocatorSlip $slip) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Locator Slip Submitted')
            ->body(
                $this->slip->employee_name .
                ' filed a ' .
                ($this->slip->transaction_type === 'official' ? 'Official Business' : 'Personal Transaction') .
                ' locator slip to ' . $this->slip->destination . '.'
            )
            ->icon('heroicon-o-map-pin')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View Slip')
                    ->url(route('filament.hrms.resources.locator-slips.view', $this->slip->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
