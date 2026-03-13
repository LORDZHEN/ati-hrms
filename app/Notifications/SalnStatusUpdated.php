<?php

namespace App\Notifications;

use App\Models\Saln;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SalnStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Saln $saln)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->saln->status);

        return (new MailMessage)
            ->subject("Your SALN has been {$status}")
            ->greeting("Hello, {$notifiable->first_name}!")
            ->line("Your Statement of Assets, Liabilities and Net Worth (SALN) has been **{$status}**.")
            ->when(
                $this->saln->status === 'approved',
                fn($mail) => $mail->line('Your SALN is now approved and locked for editing.'),
                fn($mail) => $mail->line('Please review any remarks and resubmit as needed.')
            )
            ->when(
                filled($this->saln->remarks),
                fn($mail) => $mail->line("**Admin Remarks:** {$this->saln->remarks}")
            )
            ->action('View SALN', url(route('filament.hrms.resources.salns.view', $this->saln)))
            ->line('Thank you for using the ATI-HRMS.');
    }

    public function toDatabase(object $notifiable): array
    {
        $status = ucfirst($this->saln->status);

        return [
            'title' => "SALN {$status}",
            'body' => "Your SALN submission has been {$status}" .
                (filled($this->saln->remarks) ? ": {$this->saln->remarks}" : '.'),
            'icon' => match ($this->saln->status) {
                'approved' => 'heroicon-o-check-circle',
                'disapproved' => 'heroicon-o-x-circle',
                default => 'heroicon-o-document-text',
            },
            'color' => match ($this->saln->status) {
                'approved' => 'success',
                'disapproved' => 'danger',
                default => 'warning',
            },
            'actions' => [
                [
                    'label' => 'View SALN',
                    'url' => route('filament.hrms.resources.salns.view', $this->saln),
                ],
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
