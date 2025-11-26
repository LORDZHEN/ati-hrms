<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditDailyTimeRecord extends EditRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(Auth::user()->role === \App\Models\User::ROLE_ADMIN, 403);

        parent::mount($record);
    }
}
