<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;


class LeaveApplicationStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LeaveApplication $leave) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Leave Application Update',
            'body' => "Your leave application has been " . strtoupper($this->leave->status) . ".",
            'url' => route('filament.hrms.resources.leave-applications.index'),
        ];
    }

    public function notifyUser($user)
{
    $user->notify($this);

    if (class_exists(FilamentNotification::class)) {
        $data = $this->toDatabase($user);

        FilamentNotification::make()
            ->title($data['title'])
            ->body($data['body'])
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url($data['url'])
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
}
