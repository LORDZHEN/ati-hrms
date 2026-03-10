<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListDailyTimeRecords extends ListRecords
{
    protected static string $resource = DailyTimeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return []; // BiometricImportAction lives in the table's ->headerActions([])
    }
}
