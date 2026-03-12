<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Actions;

use App\Models\EmployeeDtr;
use App\Models\User;
use App\Notifications\DtrUploaded;
use App\Services\XlsLogParser;
use App\Services\DtrCalculator;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

/**
 * BiometricImportAction — XLS Edition
 *
 * Accepts a ZKTeco / BioTime attendance export (.xls or .xlsx).
 * Parses the "Logs" sheet to detect all employees with punch data,
 * matches them against registered users (by employee_id), lets the
 * admin select which ones to import, then produces DTR CSV records.
 *
 * Upload flow:
 *   1. Admin clicks "Import Biometric Log" → modal opens.
 *   2. Admin uploads the .xls / .xlsx file (AJAX upload to /biometric/upload-xls).
 *   3. Admin clicks "Scan File for Employees" → server parses the Logs sheet,
 *      matched employees are shown as a checkbox list.
 *   4. Admin selects employees → clicks Submit → DTR records created.
 */
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
            ->modalHeading('Import Biometric Attendance Log (XLS)')
            ->modalDescription(
                'Upload a ZKTeco / BioTime attendance export (.xls or .xlsx). ' .
                'The system will scan the Logs sheet and only show employees registered in the system.'
            )
            ->modalWidth('2xl')
            ->form([

                // ── Custom upload widget (Alpine + hidden DOM input) ───────────
                Forms\Components\Placeholder::make('xls_upload_widget')
                    ->label('Biometric Attendance XLS File')
                    ->content(function () {
                        $uploadUrl = route('biometric.upload-xls');
                        $csrfToken = csrf_token();

                        return new \Illuminate\Support\HtmlString(<<<HTML
<div x-data="{
    fileName: '',
    fileSize: '',
    uploading: false,
    uploaded: false,
    xlsPath: '',
    error: '',

    uploadFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.fileName  = file.name;
        this.fileSize  = (file.size / 1024).toFixed(1) + ' KB';
        this.uploading = true;
        this.uploaded  = false;
        this.error     = '';
        this.xlsPath   = '';

        const fd = new FormData();
        fd.append('biometric_xls', file);
        fd.append('_token', '{$csrfToken}');

        fetch('{$uploadUrl}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                this.uploading = false;
                if (data.error) {
                    this.error   = data.error;
                    this.xlsPath = '';
                } else {
                    this.uploaded = true;
                    this.xlsPath  = data.path;
                }
                document.getElementById('bio-xls-path').value = this.xlsPath;
                window.dispatchEvent(new CustomEvent('bio-xls-uploaded', { detail: { path: this.xlsPath } }));
            })
            .catch(err => {
                this.uploading = false;
                this.error     = 'Upload failed: ' + err.message;
                this.xlsPath   = '';
                document.getElementById('bio-xls-path').value = '';
            });
    },

    clearFile() {
        this.fileName  = '';
        this.fileSize  = '';
        this.uploaded  = false;
        this.error     = '';
        this.uploading = false;
        this.xlsPath   = '';
        document.getElementById('bio-xls-path').value = '';
        window.dispatchEvent(new CustomEvent('bio-xls-uploaded', { detail: { path: '' } }));
        \$refs.fileInput.value = '';
    }
}" class="w-full">

    <!-- Hidden input that Filament reads as biometric_xls_path field value -->
    <input type="hidden" id="bio-xls-path" name="biometric_xls_path" value="">

    <!-- Upload area -->
    <div x-show="!uploaded && !uploading"
         class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-500 transition-colors"
         @click="\$refs.fileInput.click()">
        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium text-primary-600 dark:text-primary-400">Click to upload</span> or drag and drop
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">ZKTeco / BioTime attendance export (.xls, .xlsx)</p>
        <input x-ref="fileInput" type="file" accept=".xls,.xlsx" class="hidden" @change="uploadFile(\$event)">
    </div>

    <!-- Uploading state -->
    <div x-show="uploading"
         class="border-2 border-primary-300 dark:border-primary-600 rounded-lg p-4 bg-primary-50 dark:bg-primary-950">
        <div class="flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-primary-700 dark:text-primary-300" x-text="'Uploading ' + fileName + '...'"></p>
                <p class="text-xs text-primary-500" x-text="fileSize"></p>
            </div>
        </div>
    </div>

    <!-- Uploaded success state -->
    <div x-show="uploaded"
         class="border-2 border-green-300 dark:border-green-600 rounded-lg p-4 bg-green-50 dark:bg-green-950">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300" x-text="fileName"></p>
                    <p class="text-xs text-green-500 dark:text-green-400" x-text="fileSize + ' — Upload complete. Click Scan File below.'"></p>
                </div>
            </div>
            <button type="button" @click="clearFile()"
                    class="text-green-400 hover:text-green-600 dark:hover:text-green-200 transition-colors ml-3">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Error state -->
    <div x-show="error"
         class="mt-2 p-3 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-700 rounded-lg">
        <p class="text-sm text-red-600 dark:text-red-400" x-text="error"></p>
        <button type="button" @click="\$refs.fileInput.click()"
                class="mt-1 text-xs text-red-500 hover:text-red-700 underline">Try again</button>
    </div>

</div>
HTML);
                    }),

                // Hidden fields — values set by Scan action
                Forms\Components\Hidden::make('biometric_xls_path'),
                Forms\Components\Hidden::make('xls_period'),
                Forms\Components\Hidden::make('detected_employees'),
                Forms\Components\Hidden::make('employee_meta'),

                // ── Scan button ───────────────────────────────────────────────
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('scan_file')
                        ->label('Scan File for Employees')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->action(function (callable $get, callable $set) {
                            // Try form state first, then fallback to newest upload
                            $fullPath = $get('biometric_xls_path') ?? null;

                            if (!$fullPath || !file_exists($fullPath)) {
                                $fullPath = $this->findNewestXlsImport();
                            }

                            Log::info('[BiometricImport] scan_file (XLS): path check', [
                                'path' => $fullPath,
                                'exists' => $fullPath ? file_exists($fullPath) : false,
                            ]);

                            if (!$fullPath || !file_exists($fullPath)) {
                                Notification::make()
                                    ->danger()
                                    ->title('No file found')
                                    ->body('Please upload an XLS file first, then click Scan.')
                                    ->persistent()
                                    ->send();
                                return;
                            }

                            $set('biometric_xls_path', $fullPath);

                            try {
                                $parser = app(XlsLogParser::class);
                                $employees = $parser->detectEmployees($fullPath);
                                $period = $parser->extractPeriod($fullPath);

                                $set('xls_period', $period);

                                if ($employees->isEmpty()) {
                                    Notification::make()
                                        ->warning()
                                        ->title('No registered employees found in this file')
                                        ->body(
                                            'No employee numbers in the XLS Logs sheet matched any registered user. ' .
                                            'Ensure each user\'s "Employee ID" matches their biometric device number.'
                                        )
                                        ->persistent()
                                        ->send();
                                    return;
                                }

                                $options = $employees->mapWithKeys(function ($emp) {
                                    $label = "{$emp['db_name']} — {$emp['day_count']} day(s), {$emp['punch_count']} punches"
                                        . " [Device No: {$emp['employee_id']}]";
                                    return [$emp['user_id'] => $label];
                                })->toArray();

                                $set('detected_employees', $options);
                                $set('employee_meta', $employees->toArray());

                                Notification::make()
                                    ->success()
                                    ->title($employees->count() . ' registered employee(s) found')
                                    ->body('Period: ' . ($period ?: 'unknown') . ' — Select employees below, then click Submit.')
                                    ->send();

                            } catch (\Exception $e) {
                                Log::error('[BiometricImport] scan_file (XLS): exception', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);
                                Notification::make()
                                    ->danger()
                                    ->title('Could not scan file')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),

                // ── Employee checkbox list (visible after scan) ────────────────
                Forms\Components\CheckboxList::make('selected_user_ids')
                    ->label('Registered Employees Found in File')
                    ->helperText('Only employees whose Device Number matches a registered user are shown.')
                    ->options(fn(callable $get) => $get('detected_employees') ?? [])
                    ->columns(1)
                    ->bulkToggleable()
                    ->required()
                    ->visible(fn(callable $get) => filled($get('detected_employees'))),

                Forms\Components\Placeholder::make('scan_summary')
                    ->label('')
                    ->content(function (callable $get) {
                        $meta = $get('employee_meta');
                        $period = $get('xls_period');

                        if (!$meta)
                            return '';

                        $periodStr = $period ? " | Period: <strong>{$period}</strong>" : '';

                        return new \Illuminate\Support\HtmlString(
                            '<div class="text-xs text-gray-500 dark:text-gray-400 mt-1 p-2 bg-gray-50 dark:bg-gray-900 rounded">'
                            . '✅ <strong>' . count($meta) . '</strong> registered employee(s) found and ready to import.'
                            . $periodStr
                            . '</div>'
                        );
                    })
                    ->visible(fn(callable $get) => filled($get('detected_employees'))),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes (applied to all created DTR records)')
                    ->placeholder('e.g. February 2026 biometric import')
                    ->rows(2)
                    ->maxLength(500),
            ])

            // ── Submit action ─────────────────────────────────────────────────
            ->action(function (array $data) {
                $fullPath = $data['biometric_xls_path'] ?? null;
                $period = $data['xls_period'] ?? '';
                $selectedIds = $data['selected_user_ids'] ?? [];
                $notes = $data['notes'] ?? null;

                // Fallback to newest file if path was lost between scan and submit
                if (!$fullPath || !file_exists($fullPath)) {
                    $fullPath = $this->findNewestXlsImport();
                }

                if (!$fullPath || !file_exists($fullPath)) {
                    Notification::make()->danger()
                        ->title('File not found')
                        ->body('Please re-upload and scan the file before submitting.')
                        ->send();
                    return;
                }

                if (empty($selectedIds)) {
                    Notification::make()->warning()
                        ->title('No employees selected')
                        ->body('Please scan the file and select at least one employee.')
                        ->send();
                    return;
                }

                $parser = app(XlsLogParser::class);
                $calculator = app(DtrCalculator::class);
                $successCount = 0;
                $errorCount = 0;
                $errorMessages = [];

                foreach ($selectedIds as $userId) {
                    $user = User::find($userId);

                    if (!$user) {
                        $errorMessages[] = "⚠️  User ID {$userId}: not found.";
                        $errorCount++;
                        continue;
                    }

                    $deviceId = trim((string) $user->employee_id);

                    if ($deviceId === '') {
                        $errorMessages[] = "⚠️  {$user->name}: no employee_id set.";
                        $errorCount++;
                        continue;
                    }

                    try {
                        $dtrRows = $parser->parseForEmployee($fullPath, $deviceId, $period);
                        $calculated = $calculator->calculateFromArray($dtrRows);

                        $safeName = preg_replace('/[^A-Za-z0-9_]/', '_', $user->name);
                        $filename = "dtr_files/DTR_{$safeName}_{$deviceId}_" . now()->format('Ymd_His') . '.csv';

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
                        $errorMessages[] = "❌ {$user->name} (Device ID: {$deviceId}): " . $e->getMessage();

                        Log::error('[BiometricImport] XLS action: employee error', [
                            'user_id' => $userId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Clean up the temporary XLS file
                @unlink($fullPath);

                if ($successCount > 0 && $errorCount === 0) {
                    Notification::make()->success()
                        ->title("✅ Import Complete — {$successCount} employee(s) processed")
                        ->body('All DTR records created and employees notified.')
                        ->send();
                } elseif ($successCount > 0) {
                    Notification::make()->warning()
                        ->title("{$successCount} succeeded, {$errorCount} failed")
                        ->body(implode("\n", $errorMessages))
                        ->send();
                } else {
                    Notification::make()->danger()
                        ->title('Import Failed — 0 records created')
                        ->body(implode("\n", $errorMessages))
                        ->send();
                }
            });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Find the most recently uploaded XLS/XLSX in the biometric_imports directory.
     * Used as fallback when Livewire form state loses the path between actions.
     * Rejects files older than 30 minutes.
     */
    private function findNewestXlsImport(): ?string
    {
        $dir = Storage::disk('local')->path('biometric_imports');

        Log::info('[BiometricImport] findNewestXlsImport: scanning', ['dir' => $dir]);

        if (!is_dir($dir))
            return null;

        $newest = null;
        $newestTime = 0;

        foreach (glob($dir . '/bio_*.{xls,xlsx}', GLOB_BRACE) as $file) {
            $mtime = filemtime($file);
            if ($mtime > $newestTime) {
                $newestTime = $mtime;
                $newest = $file;
            }
        }

        if ($newest && (time() - $newestTime) > 1800) {
            Log::warning('[BiometricImport] findNewestXlsImport: file too old', [
                'file' => $newest,
                'age' => (time() - $newestTime) . 's',
            ]);
            return null;
        }

        Log::info('[BiometricImport] findNewestXlsImport', [
            'result' => $newest,
            'age' => $newest ? (time() - $newestTime) . 's' : null,
        ]);

        return $newest;
    }

    /**
     * Write a DTR CSV to public storage (used by existing download/PDF flow).
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
