<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelOrder extends EditRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
