<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewEmployeeRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $employee)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Employee Registration')
            ->body(
                $this->employee->name .
                ' (' . $this->employee->employee_id . ')' .
                ' has registered and is awaiting approval.' .
                ' Position: ' . ($this->employee->position ?? 'N/A') .
                ' — ' . ($this->employee->department ?? 'N/A') . '.'
            )
            ->icon('heroicon-o-user-plus')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('Review Registration')
                    ->url(route('filament.hrms.resources.employees.view', $this->employee->id))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
