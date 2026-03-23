<?php

namespace App\Filament\Resources\BiometricEmployeeMappingResource\Pages;

use App\Filament\Resources\BiometricEmployeeMappingResource;
use App\Models\BiometricEmployeeMapping;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditBiometricEmployeeMapping extends EditRecord
{
    protected static string $resource = BiometricEmployeeMappingResource::class;

    /**
     * When editing a mapping to active=true and the device_id changed or
     * the record is being re-activated, deactivate competing rows first.
     */
    // EditBiometricEmployeeMapping.php
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Use loose truthiness — handles both bool(true) and int(1)
        // which Filament may produce depending on form state origin
        if (!empty($data['is_active'])) {
            BiometricEmployeeMapping::deactivateByDeviceId($data['device_id'], excludeId: $this->record->id);

            Log::info('[BiometricMapping] Deactivated competing mappings before edit', [
                'device_id' => $data['device_id'],
                'editing_id' => $this->record->id,
                'by' => Auth::id(),
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
