<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class PDSStatusUpdated extends BaseNotification
{
    use Queueable;

    public function __construct(public $pds) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $status = $this->pds->status;

        $config = match ($status) {
            'approved'    => [
                'title'     => 'PDS Approved',
                'body'      => 'Your Personal Data Sheet has been approved.',
                'icon'      => 'heroicon-o-check-circle',
                'iconColor' => 'success',
            ],
            'disapproved' => [
                'title'     => 'PDS Disapproved',
                'body'      => 'Your Personal Data Sheet has been disapproved.' .
                               ($this->pds->remarks ? ' Reason: ' . $this->pds->remarks : ''),
                'icon'      => 'heroicon-o-x-circle',
                'iconColor' => 'danger',
            ],
            default       => [
                'title'     => 'PDS Status Updated',
                'body'      => 'Your Personal Data Sheet status has been reset to submitted.',
                'icon'      => 'heroicon-o-arrow-path',
                'iconColor' => 'info',
            ],
        };

        return FilamentNotification::make()
            ->title($config['title'])
            ->body($config['body'])
            ->icon($config['icon'])
            ->iconColor($config['iconColor'])
            ->actions([
                Action::make('view')
                    ->label('View PDS')
                    ->url(route('filament.hrms.resources.pds.edit', $this->pds->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
