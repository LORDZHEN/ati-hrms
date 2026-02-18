<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\User;
use App\Notifications\EventUpdated;
use App\Notifications\EventStatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    /**
     * Skip default Filament policy check so employees aren't bounced with 403.
     * canEdit() on the resource returns true for all authenticated users.
     */
    protected function authorizeAccess(): void
    {
        // intentionally open — form fields are disabled for non-admins
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Server-side guard: silently block saves from non-admins.
        if (! (Auth::user()?->isAdmin() ?? false)) {
            $this->halt();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $users = User::where('id', '!=', Auth::id())->get();
        Notification::send($users, new EventUpdated($this->record));
    }

    protected function getHeaderActions(): array
    {
        $isAdmin = Auth::user()?->isAdmin() ?? false;

        // Employees get only a back button
        if (! $isAdmin) {
            return [
                Actions\Action::make('back')
                    ->label('Back to Events')
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
                    $newStatus = ! $this->record->is_active;
                    $this->record->update(['is_active' => $newStatus]);

                    $users = User::where('id', '!=', Auth::id())->get();
                    Notification::send($users, new EventStatusChanged($this->record, $newStatus));

                    $this->refreshFormData(['is_active']);
                })
                ->requiresConfirmation()
                ->modalHeading(fn() => $this->record->is_active ? 'Deactivate Event' : 'Activate Event')
                ->modalDescription(fn() => $this->record->is_active
                    ? 'This event will no longer appear on the dashboard.'
                    : 'This event will become visible on the dashboard.')
                ->modalSubmitActionLabel(fn() => $this->record->is_active ? 'Deactivate' : 'Activate'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    /**
     * Hide the "Save changes" footer button for employees.
     */
    protected function getFormActions(): array
    {
        if (! (Auth::user()?->isAdmin() ?? false)) {
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
        return 'Event updated successfully';
    }
}
