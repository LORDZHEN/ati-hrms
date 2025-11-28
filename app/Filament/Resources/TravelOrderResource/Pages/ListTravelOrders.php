<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTravelOrders extends ListRecords
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        // Actions visible to all users
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Add "Generate Report" only for admin users
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('report')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->url(route('travel-order.report'))
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
