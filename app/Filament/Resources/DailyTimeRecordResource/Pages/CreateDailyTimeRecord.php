<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDailyTimeRecord extends CreateRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === \App\Models\User::ROLE_ADMIN, 403);
    }
}
