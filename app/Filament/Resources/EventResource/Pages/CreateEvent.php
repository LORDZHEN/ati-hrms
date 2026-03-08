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
        $users = User::where('id', '!=', Auth::id())->get();
        Notification::send($users, new EventCreated($this->record));

        if (in_array($this->record->type, ['deadline', 'meeting'])) {
            // extra logic for urgent event types here if needed
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // WHY: Hide the default footer Create button and expose a header action
    // instead — keeps the UX consistent with the rest of the project.
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
        return parent::getCancelFormAction()
            ->label('Cancel')
            ->color('gray');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveEvent')
                ->label('Save Event')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->action(fn () => $this->create()),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Event created successfully';
    }
}
