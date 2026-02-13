<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\NewSalnFiled;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Filament\Actions;

class CreateSaln extends CreateRecord
{
    protected static string $resource = SalnResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit SALN')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Calculate totals from form data before creating
        $realProperties = $data['realProperties'] ?? [];
        $personalProperties = $data['personalProperties'] ?? [];
        $liabilities = $data['liabilities'] ?? [];

        $realPropertiesTotal = collect($realProperties)->sum('current_fair_market_value');
        $personalPropertiesTotal = collect($personalProperties)->sum('acquisition_cost');
        $data['total_assets'] = $realPropertiesTotal + $personalPropertiesTotal;

        $data['total_liabilities'] = collect($liabilities)->sum('outstanding_balance');
        $data['net_worth'] = $data['total_assets'] - $data['total_liabilities'];

        return $data;
    }

    protected function afterCreate(): void
    {
        // Recalculate to ensure accuracy after all relationships are saved
        $this->record->calculateTotals();

        // Notify all admins
        $admins = User::where('role', 'admin')->get();

        if ($admins->count() > 0) {
            Notification::send($admins, new NewSalnFiled($this->record));
        }

        \Filament\Notifications\Notification::make()
            ->title('SALN Submitted Successfully')
            ->body('Your Statement of Assets, Liabilities and Net Worth has been filed.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
