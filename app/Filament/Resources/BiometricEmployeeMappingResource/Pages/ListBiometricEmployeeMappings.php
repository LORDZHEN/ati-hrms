<?php

// ═══════════════════════════════════════════════════════════════════════════
// FILE 1: app/Filament/Resources/BiometricEmployeeMappingResource/Pages/ListBiometricEmployeeMappings.php
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Filament\Resources\BiometricEmployeeMappingResource\Pages;

use App\Filament\Resources\BiometricEmployeeMappingResource;
use App\Models\BiometricEmployeeMapping;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBiometricEmployeeMappings extends ListRecords
{
    protected static string $resource = BiometricEmployeeMappingResource::class;

    protected function getHeaderActions(): array
    {
        // CreateAction is intentionally moved to the table header in
        // BiometricEmployeeMappingResource::table() alongside the Bulk Mapping button.
        // Do not add it here — it would create a duplicate.
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
