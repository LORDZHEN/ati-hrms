<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Models\User;
use App\Notifications\LeaveApplicationSubmitted;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateLeaveApplication extends CreateRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Send')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new LeaveApplicationSubmitted($this->record));
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
