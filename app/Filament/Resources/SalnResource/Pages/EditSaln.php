<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaln extends EditRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('saln.print', $this->record))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->role === 'admin'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent non-admins from changing person_administering_oath
        if (auth()->user()?->role !== 'admin') {
            unset($data['person_administering_oath']);
            unset($data['subscribed_sworn_date']);
        }

        // Recalculate totals from form data
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

    protected function afterSave(): void
    {
        // Final recalculation to ensure accuracy
        $this->record->calculateTotals();

        \Filament\Notifications\Notification::make()
            ->title('SALN Updated Successfully')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
