<?php

namespace App\Notifications;

use App\Models\Saln;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class NewSalnFiled extends Notification
{
    use Queueable;

    public function __construct(public Saln $saln)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New SALN Filed')
            ->line("A new SALN has been filed by {$this->saln->user->first_name} {$this->saln->user->last_name}.")
            ->action('View SALN', route('filament.resources.salns.edit', $this->saln->id))
            ->line('Please review it at your earliest convenience.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New SALN Filed',
            'body' => "{$this->saln->user->first_name} {$this->saln->user->last_name} filed a SALN.",
            'url' => route('filament.resources.salns.edit', $this->saln->id),
        ];
    }

    public function toFilament($notifiable)
    {
        return FilamentNotification::make()
            ->title('New SALN Filed')
            ->body("{$this->saln->user->first_name} {$this->saln->user->last_name} filed a SALN.")
            ->success()
            ->action('View', route('filament.resources.salns.edit', $this->saln->id));
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
