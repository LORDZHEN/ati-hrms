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

    /**
     * Employees can open this page (no 403), but we abort the save
     * before anything is written to the database.
     */
    protected function authorizeAccess(): void
    {
        // Let everyone through — canEdit() on the resource already returns
        // true for all authenticated users. Employees are blocked server-side
        // inside mutateFormDataBeforeSave() below.
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extra server-side guard: silently reject saves from non-admins.
        if (!(Auth::user()?->isAdmin() ?? false)) {
            $this->halt();
        }

        $hours = $this->form->getRawState()['duration_hours'] ?? null;

        if ($hours !== null && $hours !== '') {
            $data['expires_at'] = now()->addHours((int) $hours);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $users = User::where('id', '!=', Auth::id())->get();
        Notification::send($users, new AnnouncementUpdated($this->record));
    }

    protected function getHeaderActions(): array
    {
        $isAdmin = Auth::user()?->isAdmin() ?? false;

        // Employees get only a back button — no save, no delete, no toggle.
        if (!$isAdmin) {
            return [
                Actions\Action::make('back')
                    ->label('Back to Announcements')
                    ->icon('heroicon-o-arrow-left')
                    ->color('gray')
                    ->url($this->getResource()::getUrl('index')),
            ];
        }

        return [
            Actions\Action::make('toggle_active')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->action(function () {
                    $newStatus = !$this->record->is_active;
                    $this->record->update(['is_active' => $newStatus]);

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
                })
                ->requiresConfirmation()
                ->modalHeading('Clear Auto-Expire Timer')
                ->modalDescription('The announcement will stay active until manually deactivated.')
                ->modalSubmitActionLabel('Clear Timer'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getFormActions(): array
    {
        if (!(Auth::user()?->isAdmin() ?? false)) {
            return [];
        }

        return parent::getFormActions();
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
