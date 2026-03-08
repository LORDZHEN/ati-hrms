<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Actions;

use App\Models\EmployeeDtr;
use App\Models\User;
use App\Notifications\DtrUploaded;
use App\Services\BiometricLogParser;
use App\Services\DtrCalculator;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class BiometricImportAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import_biometric';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Biometric Log')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->visible(fn() => Auth::user()->isAdmin())
            ->modalHeading('Import Raw Biometric Attendance Log')
            ->modalDescription(
                'Upload a raw biometric machine export CSV. ' .
                'The system will scan it and let you select which employees to process.'
            )
            ->modalWidth('2xl')

            ->form([
                Forms\Components\FileUpload::make('biometric_csv')
                    ->label('Raw Biometric CSV File')
                    ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                    ->maxSize(10240)
                    ->disk('biometric_upload')
                    ->directory('/')
                    ->required()
                    ->live()
                    ->helperText('Export from ZKTeco / Suprema / Hikvision / Anviz. Multi-employee format supported.')
                    // ✅ KEY CHANGE: afterStateUpdated no longer tries to read the file.
                    // On Windows/XAMPP, Filament fires this callback BEFORE the file is
                    // moved from PHP's temp dir into the upload directory, so the file
                    // never exists at this point. We only store the state and show a
                    // "ready" message. All actual file parsing happens in ->action()
                    // (on Submit), by which time the file is guaranteed to be on disk.
                    ->afterStateUpdated(function ($state, callable $set) {

                        if (is_array($state)) {
                            $state = $state[0] ?? null;
                        }

                        if (!$state) {
                            return;
                        }

                        // Just store the raw state — we'll resolve it on submit
                        $set('biometric_csv_path', $state);

                        // Reset any previously detected employees
                        $set('detected_employees', null);
                        $set('selected_credential_ids', []);

                        Notification::make()
                            ->success()
                            ->title('File received')
                            ->body('Click "Scan File" below to detect employees, or go straight to Submit.')
                            ->send();
                    }),

                Forms\Components\Hidden::make('biometric_csv_path'),
                Forms\Components\Hidden::make('detected_employees'),

                // ✅ "Scan File" button — manual trigger that runs AFTER the file
                // is fully on disk, giving the user the employee checklist to pick from.
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('scan_file')
                        ->label('Scan File for Employees')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->visible(fn(callable $get) => filled($get('biometric_csv_path')))
                        ->action(function (callable $get, callable $set) {
                            $csvPath = $get('biometric_csv_path');

                            if (is_array($csvPath)) {
                                $csvPath = $csvPath[0] ?? null;
                            }

                            if (!$csvPath) {
                                Notification::make()
                                    ->warning()
                                    ->title('No file uploaded yet')
                                    ->body('Please upload a CSV file first.')
                                    ->send();
                                return;
                            }

                            $fullPath = $this->resolveBiometricPath($csvPath);

                            // At this point the file should be on disk — if not,
                            // try the newest file in the folder as a last resort.
                            if (!file_exists($fullPath)) {
                                $fallback = $this->findLatestUploadedFile();
                                if ($fallback) {
                                    $csvPath = $fallback;
                                    $fullPath = $this->resolveBiometricPath($csvPath);
                                    $set('biometric_csv_path', $csvPath);
                                }
                            }

                            if (!file_exists($fullPath)) {
                                Notification::make()
                                    ->danger()
                                    ->title('File not found on disk')
                                    ->body("Resolved path: {$fullPath}\n\nPlease re-upload the file.")
                                    ->send();
                                return;
                            }

                            try {
                                $parser = app(BiometricLogParser::class);
                                $employees = $parser->detectEmployees($fullPath);

                                if ($employees->isEmpty()) {
                                    Notification::make()
                                        ->warning()
                                        ->title('No employees detected')
                                        ->body('No valid attendance records found. Check the CSV format.')
                                        ->send();
                                    return;
                                }

                                $options = $employees->mapWithKeys(function ($emp) {
                                    $label = "{$emp['name']} — {$emp['day_count']} day(s), {$emp['punch_count']} punches"
                                        . " [Credential ID: {$emp['id']}]";
                                    return [$emp['id'] => $label];
                                })->toArray();

                                $set('detected_employees', $options);

                                Notification::make()
                                    ->success()
                                    ->title(count($options) . ' employee(s) detected')
                                    ->body('Select employees below, then click Submit.')
                                    ->send();

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Could not scan file')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),

                Forms\Components\CheckboxList::make('selected_credential_ids')
                    ->label('Employees Found in File')
                    ->helperText(
                        'Select which employees to extract and calculate DTR for. ' .
                        'Each Credential ID will be matched to the employee_id column in your users table.'
                    )
                    ->options(fn(callable $get) => $get('detected_employees') ?? [])
                    ->columns(1)
                    ->bulkToggleable()
                    ->required()
                    ->visible(fn(callable $get) => filled($get('detected_employees'))),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes (applied to all created DTR records)')
                    ->placeholder('e.g. June 2025 biometric import')
                    ->rows(2)
                    ->maxLength(500),
            ])

            ->action(function (array $data) {
                $csvPath = $data['biometric_csv_path'] ?? null;
                $selectedIds = $data['selected_credential_ids'] ?? [];
                $notes = $data['notes'] ?? null;

                if (!$csvPath) {
                    Notification::make()
                        ->danger()
                        ->title('No file path found')
                        ->body('The uploaded file path was lost. Please close this modal and try again.')
                        ->send();
                    return;
                }

                if (empty($selectedIds)) {
                    Notification::make()
                        ->warning()
                        ->title('No employees selected')
                        ->body('Please click "Scan File" and select at least one employee before submitting.')
                        ->send();
                    return;
                }

                if (is_array($csvPath)) {
                    $csvPath = $csvPath[0] ?? null;
                }

                $fullPath = $this->resolveBiometricPath($csvPath);

                if (!file_exists($fullPath)) {
                    // Last resort: find newest file in the folder
                    $fallback = $this->findLatestUploadedFile();
                    if ($fallback) {
                        $csvPath = $fallback;
                        $fullPath = $this->resolveBiometricPath($csvPath);
                    }
                }

                if (!file_exists($fullPath)) {
                    Notification::make()
                        ->danger()
                        ->title('File no longer found')
                        ->body('The uploaded file may have been cleaned up. Please re-upload and try again.')
                        ->send();
                    return;
                }

                $parser = app(BiometricLogParser::class);
                $calculator = app(DtrCalculator::class);
                $successCount = 0;
                $errorCount = 0;
                $errorMessages = [];

                foreach ($selectedIds as $credentialId) {
                    try {
                        $dtrRows = $parser->parseForEmployee($fullPath, $credentialId);
                        $calculated = $calculator->calculateFromArray($dtrRows);
                        $employeeName = $dtrRows[0]['Name'] ?? "employee_{$credentialId}";

                        $user = User::where('employee_id', $credentialId)
                            ->where('role', User::ROLE_EMPLOYEE)
                            ->first();

                        if (!$user) {
                            $errorMessages[] = "⚠️  Credential ID {$credentialId} ({$employeeName}): "
                                . "No user found with employee_id = '{$credentialId}'. "
                                . "Verify the employee_id column in the users table.";
                            $errorCount++;
                            continue;
                        }

                        $safeName = preg_replace('/[^A-Za-z0-9_]/', '_', $employeeName);
                        $filename = "dtr_files/DTR_{$safeName}_{$credentialId}_" . now()->format('Ymd_His') . '.csv';
                        $this->writeDtrCsv($filename, $calculated);

                        $record = EmployeeDtr::create([
                            'employee_id' => $user->id,
                            'file_path' => $filename,
                            'notes' => $notes,
                        ]);

                        $user->notify(new DtrUploaded($record));
                        $successCount++;

                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMessages[] = "❌ Credential ID {$credentialId}: " . $e->getMessage();
                        \Log::error('BiometricImport Error', [
                            'credential_id' => $credentialId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Clean up temp file
                Storage::disk('biometric_upload')->delete($csvPath);

                if ($successCount > 0 && $errorCount === 0) {
                    Notification::make()
                        ->success()
                        ->title("✅ Import Complete — {$successCount} employee(s) processed")
                        ->body('All DTR records created and employees notified.')
                        ->send();

                } elseif ($successCount > 0 && $errorCount > 0) {
                    Notification::make()
                        ->warning()
                        ->title("{$successCount} succeeded, {$errorCount} failed")
                        ->body(implode("\n", $errorMessages))
                        ->send();

                } else {
                    Notification::make()
                        ->danger()
                        ->title('Import Failed — 0 records created')
                        ->body(implode("\n", $errorMessages))
                        ->send();
                }
            });
    }

    /**
     * Resolve a filename to its absolute path using the biometric_upload disk.
     * Strips any directory prefix — we only store bare filenames on this disk.
     */
    private function resolveBiometricPath(string $filename): string
    {
        return Storage::disk('biometric_upload')->path(basename($filename));
    }

    /**
     * Return the bare filename of the most recently modified file in the
     * biometric_upload disk root. Fallback for race conditions on Windows.
     */
    private function findLatestUploadedFile(): ?string
    {
        $files = Storage::disk('biometric_upload')->files('/');

        if (empty($files)) {
            return null;
        }

        usort($files, fn($a, $b) =>
            Storage::disk('biometric_upload')->lastModified($b) -
            Storage::disk('biometric_upload')->lastModified($a)
        );

        return basename($files[0]);
    }

    /**
     * Write calculated DTR rows as a clean CSV to public storage.
     */
    private function writeDtrCsv(string $storagePath, array $rows): void
    {
        $csv = Writer::createFromString();
        $csv->insertOne(['EmployeeID', 'Name', 'Date', 'MorningIn', 'MorningOut', 'AfternoonIn', 'AfternoonOut']);

        foreach ($rows as $row) {
            if ($row['IsWeekend'] ?? false) {
                continue;
            }
            $csv->insertOne([
                $row['EmployeeID'],
                $row['Name'],
                $row['Date'],
                $row['MorningIn'],
                $row['MorningOut'],
                $row['AfternoonIn'],
                $row['AfternoonOut'],
            ]);
        }

        Storage::disk('public')->put($storagePath, $csv->toString());
    }
}
