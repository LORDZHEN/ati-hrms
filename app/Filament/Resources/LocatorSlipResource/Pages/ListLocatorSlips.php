<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocatorSlips extends ListRecords
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getHeaderActions(): array
    {
        // Actions everyone can see
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Only show "Generate Report" for admin users
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('report')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->url(route('locator-slip.report'))
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
