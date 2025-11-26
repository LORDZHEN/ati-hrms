<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTravelOrder extends CreateRecord
{
    protected static string $resource = TravelOrderResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
