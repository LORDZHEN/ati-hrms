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
        return [
            Actions\CreateAction::make(),
        ];
    }
}
