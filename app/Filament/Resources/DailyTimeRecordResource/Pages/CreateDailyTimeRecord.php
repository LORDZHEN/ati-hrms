<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use App\Models\EmployeeDtr;
use App\Models\User;
use App\Notifications\DtrUploaded;
use App\Notifications\DtrBatchUploadCompleted;
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
     * Override default record creation to handle multiple employees.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $createdRecords  = [];
        $successCount    = 0;
        $errorCount      = 0;
        $employeeNames   = [];

        // FIX 12: Pre-load all employees in a single query instead of
        //         calling User::find() inside the loop (N+1 problem).
        $employeeIds = collect($data['dtr_rows'] ?? [])->pluck('employee_id')->filter()->unique();
        $employees   = User::whereIn('id', $employeeIds)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            foreach ($data['dtr_rows'] ?? [] as $row) {
                try {
                    // FIX 10: Unwrap file_path here at write time — once — so the
                    //          stored value is always a plain string, never an array.
                    $filePath = $row['file_path'] ?? null;
                    if (is_array($filePath)) {
                        $filePath = $filePath[0] ?? null;
                    }

                    if (!$filePath) {
                        $errorCount++;
                        continue;
                    }

                    $record = EmployeeDtr::create([
                        'employee_id' => $row['employee_id'],
                        'file_path'   => $filePath,          // always a string now
                        'notes'       => $row['notes'] ?? null,
                    ]);

                    $createdRecords[] = $record;
                    $successCount++;

                    // FIX 12: Use pre-loaded collection — zero extra queries
                    $employee = $employees->get($row['employee_id']);

                    if ($employee) {
                        $employeeNames[] = $employee->name;
                        // Notification is collected but sent AFTER commit — see below.
                        // Attach employee to record so we can notify post-commit.
                        $record->setRelation('employee', $employee);
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('DTR Upload Error', [
                        'employee_id' => $row['employee_id'] ?? null,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }

            // FIX 9: Only commit if at least one record was created.
            if ($successCount === 0) {
                DB::rollBack();

                Notification::make()
                    ->title('Upload Failed')
                    ->body('No records were saved. Check that all files are valid.')
                    ->danger()
                    ->send();

                // FIX 9: Return a new (unsaved) model so Filament doesn't crash
                //         trying to call ->getKey() on null.  We halt the redirect
                //         via the notification — the page stays open.
                $this->halt();

                return new EmployeeDtr(); // unreachable but satisfies return type
            }

            DB::commit();

            // FIX 11: Send notifications AFTER the transaction commits so employees
            //          are only notified about records that are actually persisted.
            foreach ($createdRecords as $savedRecord) {
                $savedRecord->employee?->notify(new DtrUploaded($savedRecord));
            }

            if ($successCount > 0) {
                Auth::user()->notify(
                    new DtrBatchUploadCompleted($successCount, $errorCount, $employeeNames)
                );

                Notification::make()
                    ->title('DTR Records Uploaded Successfully')
                    ->body("Successfully uploaded {$successCount} record(s)." .
                        ($errorCount > 0 ? " {$errorCount} failed." : ''))
                    ->success()
                    ->send();
            }

            // Filament requires a persisted Model returned here.
            return $createdRecords[0];

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Upload Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();

            return new EmployeeDtr(); // unreachable after halt()
        }
    }

    /**
     * Validate before creation.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['dtr_rows'])) {
            Notification::make()
                ->title('No Employees Selected')
                ->body('Please select at least one employee to upload DTR records.')
                ->warning()
                ->send();

            $this->halt();
        }

        // FIX 10: Normalize file_path to string here as well so validation sees
        //          the right type (not a wrapped array).
        foreach ($data['dtr_rows'] as $index => $row) {
            $filePath = $row['file_path'] ?? null;
            if (is_array($filePath)) {
                $filePath = $filePath[0] ?? null;
            }
            $data['dtr_rows'][$index]['file_path'] = $filePath;

            if (empty($filePath)) {
                $employees = User::find($row['employee_id']);
                $name = $employees?->name ?? 'Unknown';

                Notification::make()
                    ->title('Missing DTR File')
                    ->body("Please upload a DTR file for {$name}.")
                    ->warning()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    /**
     * Customize form actions.
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getHeading(): string
    {
        return 'Upload Daily Time Records';
    }

    public function getSubheading(): ?string
    {
        return 'Upload DTR CSV files for multiple employees at once. Maximum 10 employees per upload.';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null; // We send our own notification in handleRecordCreation
    }
}
