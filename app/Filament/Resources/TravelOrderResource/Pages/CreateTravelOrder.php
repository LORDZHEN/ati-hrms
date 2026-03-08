<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderSubmitted;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTravelOrder extends CreateRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate travel order number: MM-YYYY-NNN (padded sequence).
        // Uses the user-selected date field, not created_at, matching the
        // format visible in the screenshots: 03-2026-001, 03-2026-002, etc.
        $date = \Carbon\Carbon::parse($data['date'] ?? now());
        $month = $date->format('m');
        $year = $date->format('Y');

        $count = \App\Models\TravelOrder::where('travel_order_no', 'like', "{$month}-{$year}-%")
            ->count() + 1;

        $data['travel_order_no'] = $month . '-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $data['status'] = 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        // Notify all admins once the record is confirmed persisted.
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new TravelOrderSubmitted($this->record));
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Send')
                ->submit('create')
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
