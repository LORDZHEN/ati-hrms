<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListDailyTimeRecords extends ListRecords
{
    protected static string $resource = DailyTimeRecordResource::class;

    /**
     * No page-level header actions needed.
     *
     * BiometricImportAction extends Filament\Tables\Actions\Action (a TABLE
     * action) — it cannot be placed here because getHeaderActions() requires
     * Filament\Actions\Action (a PAGE action). They are different namespaces
     * and are NOT interchangeable in Filament v3.
     *
     * BiometricImportAction is correctly registered in
     * DailyTimeRecordResource::table() via ->headerActions([...]),
     * which is the right place for table-scoped actions.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
