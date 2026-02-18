<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class AnnouncementStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Announcement $announcement,
        public bool $isActive
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $status = $this->isActive ? 'Activated' : 'Deactivated';
        $icon   = $this->isActive ? 'heroicon-o-eye' : 'heroicon-o-eye-slash';
        $color  = $this->isActive ? 'success' : 'warning';
        $emoji  = $this->isActive ? '✅' : '⏸️';

        return FilamentNotification::make()
            ->title($emoji . ' Announcement ' . $status)
            ->body(
                '**' . $this->announcement->title . '**' . "\n" .
                'This announcement is now ' . strtolower($status) . '.'
            )
            ->icon($icon)
            ->iconColor($color)
            ->actions([
                Action::make('view')
                    ->label('View Announcement')
                    ->url(route('filament.hrms.resources.announcements.index'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
