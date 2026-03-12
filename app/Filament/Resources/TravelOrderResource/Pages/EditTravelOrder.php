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
            // Fixed: was 'employee', now uses User::ROLE_REGULAR = 'regular'
            Actions\DeleteAction::make()
                ->visible(
                    fn() =>
                    Auth::user()->role === User::ROLE_REGULAR &&
                    $this->record->created_by === Auth::id() &&
                    $this->record->status === 'rejected'
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 'pending';
        $data['rejection_remark'] = null;

        return $data;
    }

    protected function afterSave(): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

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
