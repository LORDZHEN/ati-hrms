<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSaln extends CreateRecord
{
    protected static string $resource = SalnResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
