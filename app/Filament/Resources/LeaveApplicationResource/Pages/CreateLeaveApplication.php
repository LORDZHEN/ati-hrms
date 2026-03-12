<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Filament\Widgets\LeaveCreditWidget;
use App\Models\User;
use App\Notifications\LeaveApplicationSubmitted;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateLeaveApplication extends CreateRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LeaveCreditWidget::class,
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit Leave Application')
                ->submit('create')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function afterCreate(): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $admin) {
            $admin->notify(new LeaveApplicationSubmitted($this->record));
        }

        \Filament\Notifications\Notification::make()
            ->title('Leave Application Submitted')
            ->body('Your leave application has been sent for review.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
