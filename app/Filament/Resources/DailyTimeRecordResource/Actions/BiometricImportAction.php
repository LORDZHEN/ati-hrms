<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Actions;

use App\Livewire\EmployeeCheckboxList;
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
use Illuminate\Support\Str;
use League\Csv\Writer;

/**
 * BiometricImportAction — XLS Edition
 *
 * ── HOW SELECTION WORKS ───────────────────────────────────────────────────────
 * Selection state lives in App\Livewire\EmployeeCheckboxList and is written
 * to the Laravel session on every toggle. BiometricImportAction reads the
 * session at submit time via EmployeeCheckboxList::getSessionSelection().
 * No JS bridge, no Livewire::find(), no mountedActionsData path guessing.
 *
 * ── HOW ALREADY-IMPORTED FILTERING WORKS ─────────────────────────────────────
 * The `employee_dtrs` table has a `period_label` column (e.g. "2026/02/01 ~ 02/28")
 * that is stored on every DTR record at creation time. On re-scan, the filter:
 *
 *   EmployeeDtr::where('period_label', $period)->pluck('employee_id')
 *
 * This is an exact string match against the XLS-extracted period string —
 * completely independent of created_at, so it works correctly even when the
 * admin imports February data in March (or any other month).
 *
 * THE BUG THAT WAS HERE:
 * The old code used ->whereYear('created_at', $year)->whereMonth('created_at', $month).
 * XLS period = "2026/02/01 ~ 02/28" → year=2026, month=02 (February).
 * But the admin runs the import in March 2026 → created_at = March.
 * So the filter found NOTHING, and all 25 already-imported employees reappeared
 * on every re-scan. Also, `period_label` was never written at create time,
 * so even a correct query against it would have found nothing.
 *
 * ── IMPORT_BATCH ──────────────────────────────────────────────────────────────
 * Each Submit click generates a UUID stored in `import_batch` on every record
 * created in that run. Useful for auditing which employees were imported together.
 *
 * ── BATCH LIMIT ───────────────────────────────────────────────────────────────
 * 25 employees per submit. Enforced client-side (UI disables checkboxes at limit)
 * and server-side (array_slice guard).
 */
class BiometricImportAction extends Action
{
    private const BATCH_LIMIT = 25;

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
                'The system will scan the Logs sheet and only show employees registered in the system. ' .
                'Maximum ' . self::BATCH_LIMIT . ' employees per batch — re-scan after each batch to import the rest.'
            )
            ->modalWidth('2xl')
            ->form([

                // ── Upload widget ─────────────────────────────────────────────
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
        \$refs.fileInput.value = '';
    }
}" class="w-full">

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
        <p class="text-xs text-gray-500 mt-1">ZKTeco / BioTime attendance export (.xls, .xlsx)</p>
        <input x-ref="fileInput" type="file" accept=".xls,.xlsx" class="hidden" @change="uploadFile(\$event)">
    </div>

    <!-- Uploading -->
    <div x-show="uploading" class="border-2 border-primary-300 dark:border-primary-600 rounded-lg p-4 bg-primary-50 dark:bg-primary-950">
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

    <!-- Uploaded -->
    <div x-show="uploaded" class="border-2 border-green-300 dark:border-green-600 rounded-lg p-4 bg-green-50 dark:bg-green-950">
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
            <button type="button" @click="clearFile()" class="text-green-400 hover:text-green-600 dark:hover:text-green-200 ml-3">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Error -->
    <div x-show="error" class="mt-2 p-3 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-700 rounded-lg">
        <p class="text-sm text-red-600 dark:text-red-400" x-text="error"></p>
        <button type="button" @click="\$refs.fileInput.click()" class="mt-1 text-xs text-red-500 hover:text-red-700 underline">Try again</button>
    </div>

</div>
HTML);
                    }),

                // ── Hidden fields ─────────────────────────────────────────────
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

                            $fullPath = $get('biometric_xls_path') ?? null;

                            if (!$fullPath || !file_exists($fullPath)) {
                                $fullPath = $this->findNewestXlsImport();
                            }

                            if (!$fullPath || !file_exists($fullPath)) {
                                Notification::make()->danger()
                                    ->title('No file found')
                                    ->body('Please upload an XLS file first, then click Scan.')
                                    ->persistent()->send();
                                return;
                            }

                            $set('biometric_xls_path', $fullPath);

                            // Clear any stale session selection from a previous scan
                            EmployeeCheckboxList::clearSessionSelection();

                            try {
                                $parser = app(XlsLogParser::class);
                                $employees = $parser->detectEmployees($fullPath);
                                $period = $parser->extractPeriod($fullPath);

                                $set('xls_period', $period);

                                if ($employees->isEmpty()) {
                                    Notification::make()->warning()
                                        ->title('No registered employees found in this file')
                                        ->body(
                                            'No employee numbers in the XLS Logs sheet matched any registered user. ' .
                                            'Ensure each user\'s "Employee ID" matches their biometric device number.'
                                        )
                                        ->persistent()->send();
                                    return;
                                }

                                // ── Filter out employees already imported for this period ──────
                                //
                                // Query `period_label` with an exact match against the XLS period
                                // string. This is reliable regardless of when the import runs —
                                // February data imported in March still matches "2026/02/01 ~ 02/28".
                                //
                                // The pluck returns employee_id (users.id) as integers from MySQL.
                                // We cast to int and compare with (int) $emp['user_id'] to prevent
                                // a silent type-mismatch false negative.
                                $alreadyImportedUserIds = collect();

                                if ($period) {
                                    $alreadyImportedUserIds = EmployeeDtr::query()
                                        ->whereIn('employee_id', $employees->keys()->values()->toArray())
                                        ->where('period_label', $period)
                                        ->pluck('employee_id')
                                        ->unique()
                                        ->map(fn($id) => (int) $id);
                                }

                                $pending = $employees->reject(
                                    fn($emp) => $alreadyImportedUserIds->contains((int) $emp['user_id'])
                                );
                                $doneCount = $employees->count() - $pending->count();

                                if ($pending->isEmpty()) {
                                    Notification::make()->info()
                                        ->title('All employees already have DTR for this period')
                                        ->body(
                                            "All {$employees->count()} matched employee(s) already received a DTR " .
                                            "for period: {$period}. No new imports needed."
                                        )
                                        ->persistent()->send();

                                    $set('detected_employees', null);
                                    $set('employee_meta', null);
                                    return;
                                }

                                $options = $pending->mapWithKeys(function ($emp) {
                                    return [
                                        $emp['user_id'] => "{$emp['db_name']} — {$emp['day_count']} day(s), {$emp['punch_count']} punches [Device No: {$emp['employee_id']}]",
                                    ];
                                })->toArray();

                                $set('detected_employees', $options);
                                $set('employee_meta', $pending->toArray());

                                $pendingCount = $pending->count();
                                $batches = (int) ceil($pendingCount / self::BATCH_LIMIT);
                                $bodyParts = ['Period: ' . ($period ?: 'unknown')];

                                if ($doneCount > 0) {
                                    $bodyParts[] = "{$doneCount} already imported (excluded)";
                                }

                                $bodyParts[] = $pendingCount > self::BATCH_LIMIT
                                    ? "⚠️ {$pendingCount} remaining — select up to " . self::BATCH_LIMIT . " per batch (~{$batches} batches needed)"
                                    : 'Select employees below, then click Submit.';

                                // Inform admin that re-scanning always resets prior checkbox selections
                                $bodyParts[] = 'Note: Any previous selection has been cleared.';

                                Notification::make()->success()
                                    ->title("{$pendingCount} employee(s) pending DTR import")
                                    ->body(implode(' | ', $bodyParts))
                                    ->send();

                            } catch (\Exception $e) {
                                Log::error('[BiometricImport] scan_file exception', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);
                                Notification::make()->danger()
                                    ->title('Could not scan file')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),

                // ── Employee list (Livewire component) ────────────────────────
                Forms\Components\Placeholder::make('employee_list_component')
                    ->label('')
                    ->content(function (callable $get) {
                        $detected = $get('detected_employees') ?? [];
                        $period = $get('xls_period') ?? '';
                        $limit = self::BATCH_LIMIT;

                        if (empty($detected)) {
                            return '';
                        }

                        return new \Illuminate\Support\HtmlString(
                            \Blade::render(
                                "@livewire('employee-checkbox-list', ['employees' => \$employees, 'limit' => \$limit, 'period' => \$period], key(\$key))",
                                [
                                    'employees' => $detected,
                                    'limit' => $limit,
                                    'period' => $period,
                                    'key' => 'ecl-' . md5(json_encode(array_keys($detected)) . $period),
                                ]
                            )
                        );
                    })
                    ->visible(fn(callable $get) => filled($get('detected_employees'))),

                // ── Notes ─────────────────────────────────────────────────────
                Forms\Components\Textarea::make('notes')
                    ->label('Notes (applied to all created DTR records)')
                    ->placeholder('e.g. February 2026 biometric import')
                    ->rows(2)
                    ->maxLength(500),
            ])

            // ── Submit ────────────────────────────────────────────────────────
            ->action(function (array $data) {

                ini_set('memory_limit', '512M');
                set_time_limit(300);

                $fullPath = $data['biometric_xls_path'] ?? null;
                $period = $data['xls_period'] ?? '';
                $notes = $data['notes'] ?? null;

                // One UUID ties all records from this single Submit click together.
                // Stored in `import_batch` for auditing.
                $importBatch = (string) Str::uuid();

                // Read selection from session
                $selectedIds = EmployeeCheckboxList::getSessionSelection();

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

                // Server-side batch cap
                if (count($selectedIds) > self::BATCH_LIMIT) {
                    $selectedIds = array_slice($selectedIds, 0, self::BATCH_LIMIT);
                }

                $parser = app(XlsLogParser::class);
                $calculator = app(DtrCalculator::class);
                $successCount = 0;
                $errorCount = 0;
                $errorMessages = [];

                // Parse spreadsheet ONCE
                try {
                    $allEmployeeRows = $parser->parseAllEmployees($fullPath, $period);
                } catch (\Exception $e) {
                    Notification::make()->danger()
                        ->title('Failed to parse XLS file')
                        ->body($e->getMessage())
                        ->send();
                    @unlink($fullPath);
                    EmployeeCheckboxList::clearSessionSelection();
                    return;
                }

                foreach ($selectedIds as $userId) {
                    $user = User::find($userId);

                    if (!$user) {
                        $errorMessages[] = "⚠️ User ID {$userId}: not found.";
                        $errorCount++;
                        continue;
                    }

                    $deviceId = trim((string) $user->employee_id);

                    if ($deviceId === '') {
                        $errorMessages[] = "⚠️ {$user->name}: no employee_id set.";
                        $errorCount++;
                        continue;
                    }

                    try {
                        $dtrRows = $allEmployeeRows[$deviceId] ?? null;

                        if ($dtrRows === null) {
                            throw new \RuntimeException("No attendance data found for Device ID: {$deviceId}");
                        }

                        $calculated = $calculator->calculateFromArray($dtrRows);
                        $safeName = preg_replace('/[^A-Za-z0-9_]/', '_', $user->name);
                        $filename = 'dtr_files/DTR_' . $safeName . '_' . $deviceId . '_' . now()->format('Ymd_His') . '.csv';

                        $this->writeDtrCsv($filename, $calculated);

                        $record = EmployeeDtr::create([
                            'employee_id' => $user->id,
                            'file_path' => $filename,
                            'notes' => $notes,
                            // CRITICAL: store the XLS period string so re-scan filtering works.
                            // scan_file queries where('period_label', $period) to exclude
                            // already-imported employees. Without this value being stored,
                            // the column stays null and the filter never finds any matches.
                            'period_label' => $period ?: null,
                            // Group all records from this Submit click under one UUID.
                            'import_batch' => $importBatch,
                        ]);

                        $user->notify(new DtrUploaded($record));
                        $successCount++;

                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMessages[] = "❌ {$user->name} (Device ID: {$deviceId}): " . $e->getMessage();
                        Log::error('[BiometricImport] employee error', [
                            'user_id' => $userId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Always clean up
                if ($errorCount === 0) {
                    @unlink($fullPath);
                } else {
                    Log::warning('[BiometricImport] File retained due to errors — admin may retry', [
                        'path' => $fullPath,
                        'error_count' => $errorCount,
                    ]);
                }
                EmployeeCheckboxList::clearSessionSelection();

                if ($successCount > 0 && $errorCount === 0) {
                    Notification::make()->success()
                        ->title("✅ Batch Complete — {$successCount} employee(s) processed")
                        ->body('DTR records created and employees notified. Re-upload and scan again for the next batch.')
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

    // ── Private helpers ───────────────────────────────────────────────────────

    private function findNewestXlsImport(): ?string
    {
        $dir = Storage::disk('local')->path('biometric_imports');

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
            Log::warning('[BiometricImport] findNewestXlsImport: file too old', ['file' => $newest]);
            return null;
        }

        return $newest;
    }

    private function writeDtrCsv(string $storagePath, array $rows): void
    {
        $csv = Writer::createFromString();
        $csv->insertOne(['EmployeeID', 'Name', 'Date', 'MorningIn', 'MorningOut', 'AfternoonIn', 'AfternoonOut']);

        foreach ($rows as $row) {
            if ($row['IsWeekend'] ?? false)
                continue;

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
