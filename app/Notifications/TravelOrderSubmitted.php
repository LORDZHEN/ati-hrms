<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class TravelOrderSubmitted extends Notification
{
    use Queueable;

    public function __construct(public TravelOrder $travelOrder) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isBatch = $this->travelOrder->travel_type === 'batch';

        return FilamentNotification::make()
            ->title('New Travel Order Submitted')
            ->body(
                $this->travelOrder->creator->name .
                ' submitted a ' . ($isBatch ? 'batch' : 'solo') .
                ' travel order to ' . $this->travelOrder->destination .
                ' (' . $this->travelOrder->travel_order_no . ').'
            )
            ->icon('heroicon-o-map')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View Travel Order')
                    ->url(route('filament.hrms.resources.travel-orders.view', $this->travelOrder->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
