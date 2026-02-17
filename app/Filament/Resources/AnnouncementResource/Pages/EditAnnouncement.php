<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use App\Models\User;
use App\Notifications\AnnouncementUpdated;
use App\Notifications\AnnouncementStatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert the virtual duration_hours selector to a new expires_at timestamp.
        $hours = $this->form->getRawState()['duration_hours'] ?? null;

        if ($hours !== null && $hours !== '') {
            $data['expires_at'] = now()->addHours((int) $hours);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Send notification to all employees when announcement is updated
        $users = User::where('id', '!=', Auth::id())->get();

        Notification::send($users, new AnnouncementUpdated($this->record));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggle_active')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->action(function () {
                    $newStatus = !$this->record->is_active;
                    $this->record->update(['is_active' => $newStatus]);

                    // Send status change notification
                    $users = User::where('id', '!=', Auth::id())->get();
                    Notification::send($users, new AnnouncementStatusChanged($this->record, $newStatus));

                    $this->refreshFormData(['is_active']);
                })
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Announcement' : 'Activate Announcement')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This announcement will no longer be visible to employees.'
                    : 'This announcement will become visible to employees.')
                ->modalSubmitActionLabel(fn() => $this->record->is_active ? 'Deactivate' : 'Activate'),

            Actions\Action::make('clear_auto_expire')
                ->label('Clear Auto-Expire')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn() => $this->record->expires_at !== null)
                ->action(function () {
                    $this->record->update(['expires_at' => null]);
                    $this->refreshFormData(['expires_at']);
                    $this->notify('success', 'Auto-expire timer cleared.');
                })
                ->requiresConfirmation()
                ->modalHeading('Clear Auto-Expire Timer')
                ->modalDescription('The announcement will stay active until manually deactivated.')
                ->modalSubmitActionLabel('Clear Timer'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Announcement updated successfully';
    }
}
