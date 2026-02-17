<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use App\Models\EmployeeDtr;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions;
use Illuminate\Support\Facades\DB;

class CreateDailyTimeRecord extends CreateRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === User::ROLE_ADMIN, 403);

        parent::mount();
    }

    /**
     * Override default record creation to handle multiple employees
     */
    protected function handleRecordCreation(array $data): Model
    {
        $createdRecords = [];
        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();

        try {
            foreach ($data['dtr_rows'] ?? [] as $row) {
                try {

                    // ✅ Filament already stored the file.
                    $filePath = $row['file_path'] ?? null;

                    if (!$filePath) {
                        $errorCount++;
                        continue;
                    }

                    $record = EmployeeDtr::create([
                        'employee_id' => $row['employee_id'],
                        'file_path' => $filePath,
                        'notes' => $row['notes'] ?? null,
                    ]);

                    $createdRecords[] = $record;
                    $successCount++;

                    // Send notification
                    $employee = User::find($row['employee_id']);

                    if ($employee) {
                        Notification::make()
                            ->title('New Daily Time Record Uploaded')
                            ->body('Your DTR for the period has been uploaded and is now available for review.')
                            ->icon('heroicon-o-document-text')
                            ->success()
                            ->sendToDatabase($employee);
                    }

                } catch (\Exception $e) {
                    $errorCount++;

                    \Log::error('DTR Upload Error', [
                        'employee_id' => $row['employee_id'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            if ($successCount > 0) {
                Notification::make()
                    ->title('DTR Records Uploaded Successfully')
                    ->body("Successfully uploaded {$successCount} record(s)." .
                        ($errorCount > 0 ? " {$errorCount} failed." : ''))
                    ->success()
                    ->send();
            }

            return $createdRecords[0] ?? new EmployeeDtr();

        } catch (\Exception $e) {

            DB::rollBack();

            Notification::make()
                ->title('Upload Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return new EmployeeDtr();
        }
    }


    /**
     * Customize form actions
     */
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Upload DTR Records')
                ->submit('create')
                ->color('primary')
                ->icon('heroicon-o-cloud-arrow-up')
                ->requiresConfirmation()
                ->modalHeading('Confirm DTR Upload')
                ->modalDescription('Are you sure you want to upload these DTR records? Employees will be notified.')
                ->modalSubmitActionLabel('Yes, Upload'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    /**
     * Redirect after successful creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Customize the page heading
     */
    public function getHeading(): string
    {
        return 'Upload Daily Time Records';
    }

    /**
     * Add subheading
     */
    public function getSubheading(): ?string
    {
        return 'Upload DTR CSV files for multiple employees at once. Maximum 10 employees per upload.';
    }

    /**
     * Validate before creation
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate that we have at least one employee selected
        if (empty($data['dtr_rows'])) {
            Notification::make()
                ->title('No Employees Selected')
                ->body('Please select at least one employee to upload DTR records.')
                ->warning()
                ->send();

            $this->halt();
        }

        // Validate that all selected employees have files uploaded
        foreach ($data['dtr_rows'] as $index => $row) {
            if (empty($row['file_path'])) {
                $employee = User::find($row['employee_id']);
                $employeeName = $employee ? $employee->name : 'Unknown';

                Notification::make()
                    ->title('Missing DTR File')
                    ->body("Please upload a DTR file for {$employeeName}.")
                    ->warning()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }
    
}
