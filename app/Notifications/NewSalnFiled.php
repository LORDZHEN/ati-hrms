<?php

namespace App\Notifications;

use App\Models\Saln;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewSalnFiled extends Notification
{
    use Queueable;

    public Saln $saln;

    public function __construct(Saln $saln)
    {
        $this->saln = $saln;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // you can add 'broadcast' if using real-time notifications
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New SALN Filed')
            ->line("A new SALN has been filed by {$this->saln->user->first_name} {$this->saln->user->last_name}.")
            ->action('View SALN', route('filament.resources.salns.edit', $this->saln->id))
            ->line('Please review it at your earliest convenience.');
    }

    public function toArray($notifiable)
    {
        return [
            'saln_id' => $this->saln->id,
            'filed_by' => $this->saln->user->first_name . ' ' . $this->saln->user->last_name,
            'as_of_date' => $this->saln->as_of_date,
        ];
    }
}
