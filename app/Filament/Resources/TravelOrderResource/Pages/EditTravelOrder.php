<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Notifications\TravelOrderSubmitted;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditTravelOrder extends EditRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Employees can only delete their own rejected orders from the edit page
            Actions\DeleteAction::make()
                ->visible(fn() =>
                    Auth::user()->role === 'employee' &&
                    $this->record->created_by === Auth::id() &&
                    $this->record->status === 'rejected'
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Resubmit to pending and clear the rejection remark
        $data['status']           = 'pending';
        $data['rejection_remark'] = null;

        return $data;
    }

    protected function afterSave(): void
    {
        $adminUsers = User::where('role', 'admin')->get();

        foreach ($adminUsers as $admin) {
            $admin->notify(new TravelOrderSubmitted($this->record));
        }

        Notification::make()
            ->title('Travel Order Resubmitted')
            ->body('Your travel order has been updated and resubmitted for review.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
