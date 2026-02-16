<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Services\EmployeeRegistrationService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Employee created successfully';
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Employee Created')
            ->body("Employee {$this->record->name} has been added to the system.")
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate full name from name components
        $data['name'] = trim(implode(' ', array_filter([
            $data['first_name'] ?? '',
            $data['middle_name'] ?? '',
            $data['last_name'] ?? '',
        ])));

        return $data;
    }

    protected function afterCreate(): void
    {
        // Process the new employee registration
        app(EmployeeRegistrationService::class)->processNewEmployee($this->record);

        // Send additional notification if status is pending
        if ($this->record->status === 'pending') {
            Notification::make()
                ->info()
                ->title('Pending Approval')
                ->body('This employee needs approval before they can access the system.')
                ->persistent()
                ->send();
        }
    }
}
