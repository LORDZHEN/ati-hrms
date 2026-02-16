<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDailyTimeRecords extends ListRecords
{
    protected static string $resource = DailyTimeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return Auth::user()->isAdmin()
            ? [
                Actions\CreateAction::make()
                    ->label('Upload DTR Records')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('primary'),
            ]
            : [];
    }
}
