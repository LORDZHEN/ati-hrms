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

    protected function afterSave(): void
    {
        // Send notification to all employees when event is updated
        $users = User::where('id', '!=', Auth::id())->get();

        Notification::send($users, new EventUpdated($this->record));
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
                ->icon('heroicon-o-trash')
                ->after(function () {
                    // Optional: Send cancellation notification when event is deleted
                    // $users = User::where('id', '!=', Auth::id())->get();
                    // Notification::send($users, new EventCancelled($this->record));
                }),
        ];
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
