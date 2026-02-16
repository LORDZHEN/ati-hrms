<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye')
                ->color('info'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Employee')
                ->modalDescription('Are you sure you want to delete this employee? This action cannot be undone.')
                ->successNotificationTitle('Employee deleted successfully'),

            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset Employee Password')
                ->modalDescription(fn() => "Reset password for {$this->record->name}? A new temporary password will be sent to their email.")
                ->action(function () {
                    // Implement password reset logic
                    $this->record->update([
                        'must_change_password' => true,
                    ]);

                    Notification::make()
                        ->title('Password Reset')
                        ->body('A temporary password has been sent to the employee\'s email.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('toggle_status')
                ->label(fn() => $this->record->status === 'active' ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->status === 'active' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->status === 'active' ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn() => ($this->record->status === 'active' ? 'Deactivate' : 'Activate') . ' Employee')
                ->modalDescription(fn() =>
                    $this->record->status === 'active'
                        ? "Deactivate {$this->record->name}? They will lose access to the system."
                        : "Activate {$this->record->name}? They will regain access to the system."
                )
                ->action(function () {
                    $newStatus = $this->record->status === 'active' ? 'inactive' : 'active';
                    $this->record->update(['status' => $newStatus]);

                    Notification::make()
                        ->title('Status Updated')
                        ->body("Employee status changed to {$newStatus}")
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Employee updated successfully';
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Changes Saved')
            ->body("Employee {$this->record->name} has been updated.")
            ->duration(3000);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Regenerate full name when any name field changes
        $data['name'] = trim(implode(' ', array_filter([
            $data['first_name'] ?? '',
            $data['middle_name'] ?? '',
            $data['last_name'] ?? '',
        ])));

        return $data;
    }

    protected function afterSave(): void
    {
        // Log the update or trigger any necessary events
        activity()
            ->performedOn($this->record)
            ->log('Employee information updated');
    }
}
