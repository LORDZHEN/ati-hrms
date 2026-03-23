<?php

namespace App\Filament\Resources\BiometricEmployeeMappingResource\Pages;

use App\Filament\Resources\BiometricEmployeeMappingResource;
use App\Models\BiometricEmployeeMapping;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateBiometricEmployeeMapping extends CreateRecord
{
    protected static string $resource = BiometricEmployeeMappingResource::class;

    /**
     * Before inserting a new active mapping, deactivate any existing active
     * row that uses the same device_id. This prevents the DB unique constraint
     * from firing and guarantees a clean single-active-mapping-per-device state.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['is_active'] ?? true) && !empty(trim((string) ($data['device_id'] ?? '')))) {
            BiometricEmployeeMapping::deactivateByDeviceId($data['device_id']);

            Log::info('[BiometricMapping] Deactivated old mappings before create', [
                'device_id' => $data['device_id'],
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
