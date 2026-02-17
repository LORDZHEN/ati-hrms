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

        // Convert the virtual duration_hours selector to a real expires_at timestamp.
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
        // Send notification to all employees about the new announcement
        $users = User::where('id', '!=', Auth::id())->get();

        Notification::send($users, new AnnouncementCreated($this->record));

        // Optional: Send to admins only for high priority announcements
        if ($this->record->priority === 'high') {
            $admins = User::where('role', 'admin')->get();
            // You can send a different notification or add extra logic here
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
            Action::make('saveAnnouncement')
                ->label('Save Announcement')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(function () {
                    $this->create();
                }),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Announcement created successfully';
    }
}
