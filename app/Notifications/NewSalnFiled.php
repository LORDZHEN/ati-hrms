<?php

namespace App\Notifications;

use App\Models\Saln;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewSalnFiled extends Notification
{
    use Queueable;

    public function __construct(public Saln $saln)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'saln_id' => $this->saln->id,
            'user_name' => $this->saln->user->first_name . ' ' . $this->saln->user->last_name,
            'user_id' => $this->saln->user_id,
            'as_of_date' => $this->saln->as_of_date?->format('Y-m-d'),
        ];
    }
}
