<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class TravelOrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public TravelOrder $travelOrder) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $status = $this->travelOrder->status;

        $config = match ($status) {
            'approved' => [
                'title'     => 'Travel Order Approved',
                'body'      => 'Your travel order (' . $this->travelOrder->travel_order_no .
                               ') to ' . $this->travelOrder->destination . ' has been approved.',
                'icon'      => 'heroicon-o-check-circle',
                'iconColor' => 'success',
            ],
            'rejected' => [
                'title'     => 'Travel Order Rejected',
                'body'      => 'Your travel order (' . $this->travelOrder->travel_order_no .
                               ') to ' . $this->travelOrder->destination . ' has been rejected.',
                'icon'      => 'heroicon-o-x-circle',
                'iconColor' => 'danger',
            ],
            'recommended' => [
                'title'     => 'Travel Order Recommended',
                'body'      => 'Your travel order (' . $this->travelOrder->travel_order_no .
                               ') has been recommended and is awaiting final approval.',
                'icon'      => 'heroicon-o-hand-thumb-up',
                'iconColor' => 'info',
            ],
            default => [
                'title'     => 'Travel Order Updated',
                'body'      => 'Your travel order (' . $this->travelOrder->travel_order_no .
                               ') status has been updated to ' . ucfirst($status) . '.',
                'icon'      => 'heroicon-o-arrow-path',
                'iconColor' => 'warning',
            ],
        };

        return FilamentNotification::make()
            ->title($config['title'])
            ->body($config['body'])
            ->icon($config['icon'])
            ->iconColor($config['iconColor'])
            ->actions([
                Action::make('view')
                    ->label('View Travel Order')
                    ->url(route('filament.hrms.resources.travel-orders.view', $this->travelOrder->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
