<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use App\Models\User;
use App\Notifications\PDSSubmittedNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreatePersonalDataSheet extends CreateRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    /**
     * 🔑 Attach PDS to logged-in user and set initial status
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'submitted'; // Set initial status

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit PDS')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * 🔔 Notify admins after successful submission
     */
    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();

        LaravelNotification::send(
            $admins,
            new PDSSubmittedNotification(Auth::user(), $this->record)
        );

        Notification::make()
            ->title('PDS Submitted Successfully!')
            ->body('Your Personal Data Sheet has been sent for review.')
            ->success()
            ->send();
    }
}
