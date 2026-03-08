<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\User;
use App\Notifications\TravelOrderSubmitted;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTravelOrder extends EditRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // WHY: Only show Delete on the edit page for employees who own a
            // rejected order. canDelete() on the Resource enforces the same
            // rule at the model layer (defence-in-depth).
            Actions\DeleteAction::make()
                ->visible(
                    fn() =>
                    Auth::user()->role === 'employee' &&
                    $this->record->created_by === Auth::id() &&
                    $this->record->status === 'rejected'
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // WHY: Editing a rejected order is a resubmission — reset to pending
        // and clear the rejection remark so the admin reviews fresh.
        $data['status'] = 'pending';
        $data['rejection_remark'] = null;

        return $data;
    }

    protected function afterSave(): void
    {
        // Notify all admins that this order has been revised and resubmitted.
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new TravelOrderSubmitted($this->record));
        }

        Notification::make()
            ->title('Travel Order Resubmitted')
            ->body('Your travel order has been updated and resubmitted for review.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Resubmit')
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
