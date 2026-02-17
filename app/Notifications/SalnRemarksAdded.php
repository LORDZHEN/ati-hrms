<?php

namespace App\Notifications;

use App\Models\Saln;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class SalnRemarksAdded extends Notification
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
        return FilamentNotification::make()
            ->title('Admin Remarks on Your SALN')
            ->body(
                'Your SALN (as of ' . $this->saln->as_of_date?->format('F d, Y') . ') ' .
                'has received administrative remarks: ' . $this->saln->remarks
            )
            ->icon('heroicon-o-chat-bubble-left-right')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View SALN')
                    ->url(route('filament.hrms.resources.salns.view', $this->saln->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
