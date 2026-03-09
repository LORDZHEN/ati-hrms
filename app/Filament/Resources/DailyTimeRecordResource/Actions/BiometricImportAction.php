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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

/**
 * BiometricImportAction
 *
 * ── HOW LIVEWIRE FILE UPLOAD WORKS ON WINDOWS / XAMPP ──────────────────────
 *
 * Upload request (Request A):
 *   Browser → POST /livewire/upload-file
 *   PHP receives file → AppData\Local\Temp\phpXXXX.tmp   (standard PHP upload)
 *   Livewire reads phpXXXX.tmp, copies it to:
 *     storage/app/private/livewire-tmp/<UUID>-meta<base64name>.csv
 *   PHP request ends → phpXXXX.tmp is DELETED BY PHP automatically
 *   Livewire returns the UUID filename to the browser as the field value
 *
 * afterStateUpdated request (Request B — a NEW request):
 *   Browser → POST /livewire/update  (with field value = UUID filename)
 *   BUT: Filament/Livewire serialises TemporaryUploadedFile as:
 *     { "Livewire\...\TemporaryUploadedFile": "C:\...\phpXXXX.tmp" }
 *   When deserialised, $state becomes that old phpXXXX.tmp path string.
 *   That file no longer exists. The real file is the UUID one in livewire-tmp.
 *
 * THE ONLY RELIABLE APPROACH:
 *   Ignore $state entirely. Pick the NEWEST .csv in livewire-tmp by mtime.
 *   afterStateUpdated fires within milliseconds of upload completing, so the
 *   newest file in livewire-tmp is always the one just uploaded.
 *   Copy it immediately to biometric_imports/ (our stable location).
 * ───────────────────────────────────────────────────────────────────────────
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
            ->modalHeading('Import Raw Biometric Attendance Log')
            ->modalDescription(
                'Upload a raw biometric machine export CSV. ' .
                'The system will scan it and only show employees registered in the system.'
            )
            ->modalWidth('2xl')
            ->form([
                Forms\Components\FileUpload::make('biometric_csv')
                    ->label('Raw Biometric CSV File')
                    ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                    ->maxSize(10240)
                    ->required()
                    ->live()
                    ->helperText('Export from ZKTeco / Suprema / Hikvision / Anviz. Only employees registered in the system will be shown.')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) {
                            return;
                        }

                        // Purge stale files from livewire-tmp first (files older
                        // than 10 min from previous failed/abandoned sessions).
                        // This ensures getNewestLivewireTmpFile() never picks up
                        // a stale leftover from a previous session.
                        $this->purgeOldLivewireTmpFiles(600);

                        $livewirePath = $this->getNewestLivewireTmpFile();

                        if (!$livewirePath) {
                            Log::error('[BiometricImport] afterStateUpdated: no CSV found in livewire-tmp', [
                                'state'      => is_string($state) ? $state : gettype($state),
                                'v3_dir'     => storage_path('app/private/livewire-tmp'),
                                'v3_exists'  => is_dir(storage_path('app/private/livewire-tmp')),
                                'v3_files'   => is_dir(storage_path('app/private/livewire-tmp'))
                                    ? scandir(storage_path('app/private/livewire-tmp'))
                                    : 'DIR MISSING',
                            ]);
                            Notification::make()
                                ->danger()
                                ->title('Could not locate uploaded file')
                                ->body('Please remove the file and try uploading again.')
                                ->persistent()
                                ->send();
                            return;
                        }

                        // Copy to our stable location immediately — before Livewire GC can remove it
                        $stableDir      = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'biometric_imports');
                        $stableFilename = 'bio_' . now()->format('YmdHis') . '_' . uniqid() . '.csv';
                        $stablePath     = $stableDir . DIRECTORY_SEPARATOR . $stableFilename;

                        if (!is_dir($stableDir)) {
                            mkdir($stableDir, 0755, true);
                        }

                        if (!copy($livewirePath, $stablePath)) {
                            Log::error('[BiometricImport] afterStateUpdated: copy() failed', [
                                'from' => $livewirePath,
                                'to'   => $stablePath,
                            ]);
                            Notification::make()
                                ->danger()
                                ->title('File copy failed')
                                ->body('Please try uploading the file again.')
                                ->persistent()
                                ->send();
                            return;
                        }

                        Log::info('[BiometricImport] afterStateUpdated: SUCCESS', [
                            'from'   => $livewirePath,
                            'to'     => $stablePath,
                            'exists' => file_exists($stablePath),
                            'size'   => filesize($stablePath),
                        ]);

                        $set('biometric_csv_path', $stablePath);
                        $set('detected_employees', null);
                        $set('employee_meta', null);
                        $set('selected_user_ids', []);

                        Notification::make()
                            ->success()
                            ->title('File received')
                            ->body('Click "Scan File" below to detect registered employees.')
                            ->send();
                    }),

                Forms\Components\Hidden::make('biometric_csv_path'),
                Forms\Components\Hidden::make('detected_employees'),
                Forms\Components\Hidden::make('employee_meta'),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('scan_file')
                        ->label('Scan File for Employees')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->visible(fn(callable $get) => filled($get('biometric_csv_path')))
                        ->action(function (callable $get, callable $set) {
                            $fullPath = $get('biometric_csv_path');

                            Log::info('[BiometricImport] scan_file: path check', [
                                'path'   => $fullPath,
                                'exists' => $fullPath ? file_exists($fullPath) : false,
                            ]);

                            if (!$fullPath || !file_exists($fullPath)) {
                                Notification::make()
                                    ->danger()
                                    ->title('File not found — please re-upload')
                                    ->body('The temporary file could not be located. Please remove it and upload again.')
                                    ->persistent()
                                    ->send();
                                return;
                            }

                            try {
                                $parser    = app(BiometricLogParser::class);
                                $employees = $parser->detectEmployees($fullPath);

                                if ($employees->isEmpty()) {
                                    Notification::make()
                                        ->warning()
                                        ->title('No registered employees found in this file')
                                        ->body(
                                            'No Employee IDs in the CSV matched any registered user. ' .
                                            'Ensure each user\'s "Employee ID" in the Employees module ' .
                                            'matches their biometric device ID. ' .
                                            'Check storage/logs/laravel.log for the full scanned ID list.'
                                        )
                                        ->persistent()
                                        ->send();
                                    return;
                                }

                                $options = $employees->mapWithKeys(function ($emp) {
                                    $label = "{$emp['db_name']} — {$emp['day_count']} day(s), {$emp['punch_count']} punches"
                                        . " [Device ID: {$emp['employee_id']}]";
                                    return [$emp['user_id'] => $label];
                                })->toArray();

                                $set('detected_employees', $options);
                                $set('employee_meta', $employees->toArray());

                                Notification::make()
                                    ->success()
                                    ->title($employees->count() . ' registered employee(s) found')
                                    ->body('Select employees below, then click Submit.')
                                    ->send();

                            } catch (\Exception $e) {
                                Log::error('[BiometricImport] scan_file: exception', [
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

                Forms\Components\CheckboxList::make('selected_user_ids')
                    ->label('Registered Employees Found in File')
                    ->helperText('Only employees whose Device ID matches a registered user are shown. Unrecognised IDs are excluded.')
                    ->options(fn(callable $get) => $get('detected_employees') ?? [])
                    ->columns(1)
                    ->bulkToggleable()
                    ->required()
                    ->visible(fn(callable $get) => filled($get('detected_employees'))),

                Forms\Components\Placeholder::make('unregistered_notice')
                    ->label('')
                    ->content(function (callable $get) {
                        $meta = $get('employee_meta');
                        if (!$meta) return '';
                        return new \Illuminate\Support\HtmlString(
                            '<div class="text-xs text-gray-500 dark:text-gray-400 mt-1 p-2 bg-gray-50 dark:bg-gray-900 rounded">'
                            . '✅ <strong>' . count($meta) . '</strong> registered employee(s) found and ready to import. '
                            . 'Records belonging to unregistered device IDs have been excluded.'
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

            ->action(function (array $data) {
                $fullPath    = $data['biometric_csv_path'] ?? null;
                $selectedIds = $data['selected_user_ids']  ?? [];
                $notes       = $data['notes']               ?? null;

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

                $parser        = app(BiometricLogParser::class);
                $calculator    = app(DtrCalculator::class);
                $successCount  = 0;
                $errorCount    = 0;
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
                        $dtrRows    = $parser->parseForEmployee($fullPath, $deviceId);
                        $calculated = $calculator->calculateFromArray($dtrRows);

                        $safeName = preg_replace('/[^A-Za-z0-9_]/', '_', $user->name);
                        $filename = "dtr_files/DTR_{$safeName}_{$deviceId}_" . now()->format('Ymd_His') . '.csv';

                        $this->writeDtrCsv($filename, $calculated);

                        $record = EmployeeDtr::create([
                            'employee_id' => $user->id,
                            'file_path'   => $filename,
                            'notes'       => $notes,
                        ]);

                        $user->notify(new DtrUploaded($record));
                        $successCount++;

                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMessages[] = "❌ {$user->name} (ID: {$deviceId}): " . $e->getMessage();
                        Log::error('[BiometricImport] action: employee error', [
                            'user_id' => $userId, 'error' => $e->getMessage(),
                        ]);
                    }
                }

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
     * Returns the absolute path of the most recently modified CSV file in
     * Livewire's tmp directory, or null if none found within the last 5 min.
     *
     * On Windows/XAMPP the $state value from afterStateUpdated is always a
     * stale phpXXXX.tmp path that no longer exists. The real uploaded file
     * is a UUID-named .csv in livewire-tmp. We find it by mtime because
     * afterStateUpdated fires within seconds of the upload completing.
     */
    private function getNewestLivewireTmpFile(): ?string
    {
        $dirs = [
            storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'livewire-tmp'),
            storage_path('app' . DIRECTORY_SEPARATOR . 'livewire-tmp'),
        ];

        $newest     = null;
        $newestTime = 0;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot() || $fileInfo->isDir()) {
                    continue;
                }
                if (strtolower($fileInfo->getExtension()) !== 'csv') {
                    continue;
                }
                $mtime = $fileInfo->getMTime();
                if ($mtime > $newestTime) {
                    $newestTime = $mtime;
                    $newest     = $fileInfo->getRealPath();
                }
            }
        }

        $secondsAgo = $newest ? (time() - $newestTime) : null;

        Log::info('[BiometricImport] getNewestLivewireTmpFile', [
            'result'      => $newest,
            'seconds_ago' => $secondsAgo,
        ]);

        // Guard: reject if older than 5 minutes — that's a stale leftover
        if ($newest !== null && $secondsAgo > 300) {
            Log::warning('[BiometricImport] getNewestLivewireTmpFile: file too old, rejecting', [
                'path'        => $newest,
                'seconds_ago' => $secondsAgo,
            ]);
            return null;
        }

        return $newest;
    }

    /**
     * Delete all files from livewire-tmp that are older than $maxAgeSeconds.
     * Prevents stale files from a previous session from being mistakenly
     * picked up by getNewestLivewireTmpFile().
     */
    private function purgeOldLivewireTmpFiles(int $maxAgeSeconds = 600): void
    {
        $dirs = [
            storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'livewire-tmp'),
            storage_path('app' . DIRECTORY_SEPARATOR . 'livewire-tmp'),
        ];

        $now     = time();
        $deleted = [];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot() || $fileInfo->isDir()) {
                    continue;
                }
                if (strtolower($fileInfo->getExtension()) !== 'csv') {
                    continue;
                }
                if (($now - $fileInfo->getMTime()) > $maxAgeSeconds) {
                    $path = $fileInfo->getRealPath();
                    @unlink($path);
                    $deleted[] = basename($path);
                }
            }
        }

        if (!empty($deleted)) {
            Log::info('[BiometricImport] purgeOldLivewireTmpFiles: purged stale files', [
                'count' => count($deleted),
                'files' => $deleted,
            ]);
        }
    }

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