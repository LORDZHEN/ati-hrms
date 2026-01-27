<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Resources\TravelOrderResource;

class TravelOrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public TravelOrder $travelOrder) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $status = ucfirst($this->travelOrder->status);

        return (new MailMessage)
            ->subject("Travel Order {$status}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The travel order ({$this->travelOrder->travel_order_no}) has been {$status}.")
            ->action('View Travel Order', TravelOrderResource::getUrl('edit', ['record' => $this->travelOrder->id]))
            ->line('Thank you!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => "Travel Order #{$this->travelOrder->travel_order_no} {$this->travelOrder->status}",
            'body' => "Your travel order #{$this->travelOrder->travel_order_no} has been {$this->travelOrder->status}.",
            'url' => TravelOrderResource::getUrl('edit', ['record' => $this->travelOrder->id]),
        ];
    }

    public function notifyUser($user)
    {
        $user->notify($this); // DB + email

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
