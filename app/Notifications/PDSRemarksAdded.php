<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class PDSRemarksAdded extends BaseNotification
{
    use Queueable;

    public function __construct(public $pds) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Admin Remarks on Your PDS')
            ->body('Your Personal Data Sheet has received remarks: ' . $this->pds->remarks)
            ->icon('heroicon-o-chat-bubble-left-right')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('View PDS')
                    ->url(route('filament.hrms.resources.pds.edit', $this->pds->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
