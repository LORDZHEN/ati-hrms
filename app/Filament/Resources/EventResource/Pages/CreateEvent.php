<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\User;
use App\Notifications\EventCreated;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send notification to all employees about the new event
        $users = User::where('id', '!=', Auth::id())->get();

        Notification::send($users, new EventCreated($this->record));

        // Optional: Send to specific users for deadline or meeting types
        if (in_array($this->record->type, ['deadline', 'meeting'])) {
            // You can add specific logic here for urgent event types
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->hidden();
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancel');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveEvent')
                ->label('Save Event')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(function () {
                    $this->create();
                }),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Event created successfully';
    }
}
