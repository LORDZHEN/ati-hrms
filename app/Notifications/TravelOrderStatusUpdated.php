<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Filament\Resources\TravelOrderResource;

class TravelOrderStatusUpdated extends Notification
{
    use Queueable;

    public TravelOrder $travelOrder;

    public function __construct(TravelOrder $travelOrder)
    {
        $this->travelOrder = $travelOrder;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // store in DB and send email if mail configured
    }

    public function toMail($notifiable)
{
    $status = ucfirst($this->travelOrder->status);

    return (new MailMessage)
        ->subject("Travel Order {$status}")
        ->greeting("Hello {$notifiable->name},")
        ->line("The travel order ({$this->travelOrder->travel_order_no}) has been {$status}.")
        ->action(
    'View Travel Order', 
    TravelOrderResource::getUrl('edit', ['record' => $this->travelOrder->id])
)

        ->line('Thank you!');
}


    public function toDatabase($notifiable)
    {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'travel_order_no' => $this->travelOrder->travel_order_no,
            'status' => $this->travelOrder->status,
            'message' => "Travel Order #{$this->travelOrder->travel_order_no} has been {$this->travelOrder->status}.",
        ];
    }
}
