<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use App\Models\User;
use App\Notifications\AnnouncementCreated;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        $hours = $this->form->getRawState()['duration_hours'] ?? null;

        if ($hours !== null && $hours !== '') {
            $data['expires_at'] = now()->addHours((int) $hours);
        } else {
            $data['expires_at'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $users = User::where('id', '!=', Auth::id())->get();
        Notification::send($users, new AnnouncementCreated($this->record));

        if ($this->record->priority === 'high') {
            $admins = User::where('role', 'admin')->get();
            // extra high-priority logic here if needed
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // WHY: Hide the default footer Create button and expose a header action
    // instead — keeps the UX consistent with the rest of the project where
    // the primary action sits in the page header, not the form footer.
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
            Action::make('saveAnnouncement')
                ->label('Save Announcement')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->action(fn() => $this->create()),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Announcement created successfully';
    }
}
