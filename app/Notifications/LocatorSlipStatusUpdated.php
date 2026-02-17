<?php

namespace App\Notifications;

use App\Models\LocatorSlip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class LocatorSlipStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LocatorSlip $slip) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isApproved = $this->slip->status === 'approved';

        return FilamentNotification::make()
            ->title('Locator Slip ' . ucfirst($this->slip->status))
            ->body(
                'Your locator slip to ' . $this->slip->destination .
                ' has been ' . strtoupper($this->slip->status) . '.' .
                ($this->slip->admin_remarks
                    ? ' Remarks: ' . $this->slip->admin_remarks
                    : '')
            )
            ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->iconColor($isApproved ? 'success' : 'danger')
            ->actions([
                Action::make('view')
                    ->label('View Slip')
                    ->url(route('filament.hrms.resources.locator-slips.view', $this->slip->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
