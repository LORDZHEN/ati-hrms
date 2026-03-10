<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyTimeRecord extends CreateRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    public function mount(): void
    {
        abort(403, 'DTR records are created via Biometric Import only.');
    }
}
