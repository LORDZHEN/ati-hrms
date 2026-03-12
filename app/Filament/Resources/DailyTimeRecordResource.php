<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTimeRecordResource\Pages;
use App\Models\EmployeeDtr;
use App\Models\User;
use App\Notifications\DtrPdfGenerated;
use App\Notifications\DtrDeleted;
use App\Filament\Resources\DailyTimeRecordResource\Actions\BiometricImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class DailyTimeRecordResource extends Resource
{
    protected static ?string $model = EmployeeDtr::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Daily Time Record';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?string $modelLabel = 'Daily Time Record';
    protected static ?string $pluralModelLabel = 'Daily Time Records';
    protected static ?int $navigationSort = 1;

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([

                // ── Step 1: Select Employees ──────────────────────────────────
                Forms\Components\Wizard\Step::make('Select Employees')
                    ->description('Choose up to 10 employees for DTR upload')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Forms\Components\CheckboxList::make('selected_employees')
                            ->label('')
                            ->options(
                                User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [
                                            $user->id => new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 p-2">
                                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 font-bold">'
                                                . strtoupper(substr($user->name, 0, 2)) .
                                                '</div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900 dark:text-white">' . e($user->name) . '</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">' . e($user->email) . '</div>
                                                    </div>
                                                </div>'
                                            ),
                                        ];
                                    })
                                    ->toArray()
                            )
                            ->columns(2)
                            ->gridDirection('row')
                            ->searchable()
                            ->bulkToggleable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (count($state ?? []) > 10) {
                                    $state = array_slice($state, 0, 10);
                                    $set('selected_employees', $state);
                                    \Filament\Notifications\Notification::make()
                                        ->warning()
                                        ->title('Maximum 10 Employees')
                                        ->body('You can select up to 10 employees at a time.')
                                        ->send();
                                }

                                $existingRows = $get('dtr_rows') ?? [];
                                $existingData = collect($existingRows)->keyBy('employee_id');

                                $rows = collect($state ?? [])
                                    ->map(fn($employeeId) => [
                                        'employee_id' => $employeeId,
                                        'file_path' => $existingData->get($employeeId)['file_path'] ?? null,
                                        'notes' => $existingData->get($employeeId)['notes'] ?? null,
                                    ])
                                    ->values()
                                    ->toArray();

                                $set('dtr_rows', $rows);
                            }),

                        Forms\Components\Placeholder::make('selection_count')
                            ->label('')
                            ->content(function ($get) {
                                $count = count($get('selected_employees') ?? []);

                                if ($count === 0) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                                            <p class="text-gray-500 dark:text-gray-400">No employees selected</p>
                                        </div>'
                                    );
                                }

                                return new \Illuminate\Support\HtmlString(
                                    '<div class="text-center p-4 bg-primary-50 dark:bg-primary-950 rounded-lg border-2 border-primary-200 dark:border-primary-800">
                                        <p class="text-lg font-bold text-primary-600 dark:text-primary-400">'
                                    . $count . ' Employee' . ($count > 1 ? 's' : '') . ' Selected</p>
                                        <p class="text-sm text-primary-500 dark:text-primary-500">'
                                    . (10 - $count) . ' slots remaining</p>
                                    </div>'
                                );
                            })
                            ->columnSpanFull(),
                    ]),

                // ── Step 2: Upload Files ──────────────────────────────────────
                Forms\Components\Wizard\Step::make('Upload DTR Files')
                    ->description('Upload CSV files for selected employees')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->schema([
                        Forms\Components\Repeater::make('dtr_rows')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('employee_id'),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Placeholder::make('employee_info')
                                                    ->label('')
                                                    ->content(function ($get) {
                                                        $employee = User::find($get('employee_id'));
                                                        if (!$employee)
                                                            return '—';

                                                        return new \Illuminate\Support\HtmlString(
                                                            '<div class="flex items-center gap-4 p-4 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-950 dark:to-primary-900 rounded-xl border border-primary-200 dark:border-primary-800">
                                                                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-500 dark:bg-primary-600 text-white font-bold text-lg shadow-lg">'
                                                            . strtoupper(substr($employee->name, 0, 2)) .
                                                            '</div>
                                                                <div>
                                                                    <div class="text-lg font-bold text-gray-900 dark:text-white">' . e($employee->name) . '</div>
                                                                    <div class="text-sm text-gray-600 dark:text-gray-300">' . e($employee->email) . '</div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ' . e($employee->id) . '</div>
                                                                </div>
                                                            </div>'
                                                        );
                                                    })
                                                    ->columnSpanFull(),

                                                Forms\Components\FileUpload::make('file_path')
                                                    ->label('DTR CSV File')
                                                    ->acceptedFileTypes(['text/csv', 'application/csv'])
                                                    ->maxSize(2048)
                                                    ->directory('dtr_files')
                                                    ->disk('public')
                                                    ->required()
                                                    ->helperText("Upload the employee's DTR in CSV format")
                                                    ->downloadable()
                                                    ->previewable(false)
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if (is_array($state) && !empty($state)) {
                                                            $set('file_path', $state[0] ?? null);
                                                        }
                                                    })
                                                    ->columnSpan(1),

                                                Forms\Components\Textarea::make('notes')
                                                    ->label('Notes & Remarks')
                                                    ->placeholder('Add notes or remarks (optional)...')
                                                    ->rows(4)
                                                    ->maxLength(500)
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($get) => filled($get('dtr_rows'))),

                // ── Step 3: Review & Submit ───────────────────────────────────
                Forms\Components\Wizard\Step::make('Review & Submit')
                    ->description('Review your upload before submitting')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Placeholder::make('review')
                            ->label('')
                            ->content(function ($get) {
                                $rows = $get('dtr_rows') ?? [];

                                if (empty($rows)) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="text-center p-8"><p class="text-gray-500">No files to review</p></div>'
                                    );
                                }

                                $html = '<div class="space-y-4">';

                                foreach ($rows as $row) {
                                    $employee = User::find($row['employee_id']);
                                    $filePath = is_array($row['file_path'] ?? null)
                                        ? ($row['file_path'][0] ?? null)
                                        : ($row['file_path'] ?? null);
                                    $hasFile = !empty($filePath);
                                    $fileName = $hasFile ? basename($filePath) : 'No file uploaded';
                                    $notes = $row['notes'] ?? 'No notes';
                                    $statusColor = $hasFile ? 'green' : 'red';
                                    $statusIcon = $hasFile ? '✓' : '✗';

                                    $html .= '
                                        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-' . $statusColor . '-100 dark:bg-' . $statusColor . '-900 text-' . $statusColor . '-600 dark:text-' . $statusColor . '-400 font-bold text-xl">' . $statusIcon . '</div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900 dark:text-white">' . e($employee->name) . '</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">📄 ' . e($fileName) . '</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">💬 ' . e($notes) . '</div>
                                            </div>
                                        </div>';
                                }

                                $html .= '</div>';

                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->visible(fn() => Auth::user()->isAdmin()),

            // ── CSV format hint ───────────────────────────────────────────────
            Forms\Components\Section::make('CSV File Requirements')
                ->description('Please ensure your CSV files meet these requirements')
                ->schema([
                    Forms\Components\Placeholder::make('csv_format')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="font-semibold text-blue-900 dark:text-blue-100 mb-2 flex items-center gap-2"><span>📋</span> Required Columns</div>
                                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
                                        <li>EmployeeID</li><li>Name</li><li>Date (YYYY-MM-DD)</li>
                                        <li>MorningIn, MorningOut</li><li>AfternoonIn, AfternoonOut</li>
                                    </ul>
                                </div>
                                <div class="p-4 bg-amber-50 dark:bg-amber-950/30 rounded-lg border border-amber-200 dark:border-amber-800">
                                    <div class="font-semibold text-amber-900 dark:text-amber-100 mb-2 flex items-center gap-2"><span>⏰</span> Time Format</div>
                                    <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1 list-disc list-inside">
                                        <li>Use 24-hour format (HH:MM)</li><li>Example: 08:00, 17:30</li><li>Leave empty for no entry</li>
                                    </ul>
                                </div>
                            </div>
                        ')),
                ])
                ->visible(fn() => Auth::user()->isAdmin())
                ->collapsible()
                ->collapsed(true),
        ]);
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        // ── Compute once — reused in every closure below ──────────────────────
        // WHY: Calling Auth::user()->isAdmin() inside every closure causes
        //      repeated session/DB lookups. Capturing it once is free.
        $isAdmin = Auth::user()->isAdmin();

        return $table
            ->columns([

                // ── Employee name + email ─────────────────────────────────────
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    // Eager-loaded in getEloquentQuery() — no N+1 here.
                    ->description(fn($record) => $record->employee?->email ?? '—')
                    ->visible($isAdmin),

                // ── DTR File ──────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('file_path')
                    ->label('DTR File')
                    ->formatStateUsing(fn($state) => basename(
                        is_array($state) ? ($state[0] ?? '') : (string) $state
                    ))
                    ->description(fn($record) => 'Uploaded ' . $record->created_at->diffForHumans())
                    ->icon('heroicon-m-document-text')
                    ->iconColor('success')
                    ->limit(35)
                    ->tooltip(fn($state) => is_array($state) ? ($state[0] ?? '') : $state)
                    ->copyable()
                    ->copyMessage('Path copied!')
                    ->searchable(),

                // ── Notes ─────────────────────────────────────────────────────
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->icon('heroicon-m-chat-bubble-left')
                    ->iconColor('warning')
                    ->toggleable()
                    ->searchable()
                    ->wrap(),

                // ── Uploaded (relative time) ──────────────────────────────────
                // WHY: ->since() shows "3 hours ago" which is more readable for
                //      recent uploads. Full datetime is shown in the tooltip.
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->toggleable(),

                // ── Status ────────────────────────────────────────────────────
                // WHY: BadgeColumn is removed in Filament v3. TextColumn + ->badge()
                //      is the correct v3 API. Status now checks physical file
                //      existence so "Missing File" surfaces real data issues.
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => Storage::disk('public')->exists(
                        self::resolveFilePath($record->file_path)
                    ) ? 'Active' : 'Missing File')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'Active' => 'success',
                        'Missing File' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state) => match ($state) {
                        'Active' => 'heroicon-m-check-circle',
                        'Missing File' => 'heroicon-m-exclamation-circle',
                        default => null,
                    })
                    ->toggleable(),

                // ── Last Modified (hidden by default) ─────────────────────────
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Modified')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-arrow-path')
                    ->color('gray'),
            ])

            // ── Filters ───────────────────────────────────────────────────────
            ->filters(
                self::getEnhancedFilters($isAdmin),
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->filtersFormColumns(3)   // 3-column grid: Employee | Date Range | Toggles
            ->filtersFormWidth(\Filament\Support\Enums\MaxWidth::FourExtraLarge)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->defaultSort('created_at', 'desc')

            // ── Header actions ────────────────────────────────────────────────
            // BiometricImportAction extends Tables\Actions\Action so it must
            // live here — NOT in ListDailyTimeRecords::getHeaderActions() which
            // only accepts Filament\Actions\Action (page actions).
            ->headerActions([
                BiometricImportAction::make(),
            ])

            // ── Row actions ───────────────────────────────────────────────────
            ->actions([
                Tables\Actions\ActionGroup::make([

                    // ── Download CSV ──────────────────────────────────────────
                    Tables\Actions\Action::make('download')
                        ->label('Download CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function ($record) {
                            $filePath = self::resolveFilePath($record->file_path);
                            return Storage::disk('public')->download($filePath);
                        }),

                    // ── Download PDF ──────────────────────────────────────────
                    Tables\Actions\Action::make('export_pdf')
                        ->label('Download PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Download DTR PDF')
                        ->modalDescription('Generate and download the DTR report as a PDF.')
                        ->action(fn($record) => self::generatePdfResponse($record, download: true)),

                    // ── Preview PDF (inline modal iframe) ─────────────────────
                    // WHY: onload fade-in gives visual feedback while the
                    //      browser renders the base64 PDF — avoids blank flash.
                    Tables\Actions\Action::make('preview_pdf')
                        ->label('Preview PDF')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn($record) => 'DTR Preview — ' . $record->employee->name)
                        ->modalWidth(\Filament\Support\Enums\MaxWidth::SevenExtraLarge)
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalContent(function ($record) {
                            try {
                                $pdf = self::buildPdf($record);
                                $base64 = base64_encode($pdf->output());
                                $dataUrl = 'data:application/pdf;base64,' . $base64;

                                return new \Illuminate\Support\HtmlString('
                                    <div style="position:relative; height:78vh; width:100%;">
                                        <iframe
                                            src="' . $dataUrl . '"
                                            style="width:100%; height:100%; border:none; border-radius:6px;
                                                   opacity:0; transition:opacity 0.3s;"
                                            onload="this.style.opacity=1"
                                            type="application/pdf">
                                        </iframe>
                                    </div>
                                ');
                            } catch (\Exception $e) {
                                return new \Illuminate\Support\HtmlString('
                                    <div class="p-8 text-center text-red-500">
                                        <p class="font-bold text-lg">PDF Generation Failed</p>
                                        <p class="text-sm mt-2">' . e($e->getMessage()) . '</p>
                                    </div>
                                ');
                            }
                        }),

                    // ── View Details (slide-over) ─────────────────────────────
                    Tables\Actions\Action::make('details')
                        ->label('View Details')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('gray')
                        ->modalHeading(fn($record) => 'DTR: ' . $record->employee->name)
                        ->modalContent(fn($record) => view('filament.tables.cells.dtr-details', ['record' => $record]))
                        ->modalSubmitAction(false)
                        ->slideOver(),

                    // ── Delete ────────────────────────────────────────────────
                    // WHY: Moved notification to ->after() so employees are only
                    //      notified when the deletion actually succeeds.
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => $isAdmin)
                        ->after(function ($record) {
                            $filePath = self::resolveFilePath($record->file_path);
                            $record->employee->notify(new DtrDeleted(
                                $record->employee->name,
                                basename($filePath),
                                'Record removed by administrator'
                            ));
                        })
                        ->successNotificationTitle('DTR record deleted'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button()
                    ->color('gray'),
            ])

            // ── Bulk actions ──────────────────────────────────────────────────
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin)
                        ->after(function ($records) {
                            // WHY: Same fix as single delete — notify after success.
                            foreach ($records as $record) {
                                $filePath = self::resolveFilePath($record->file_path);
                                $record->employee->notify(new DtrDeleted(
                                    $record->employee->name,
                                    basename($filePath),
                                    'Record removed by administrator (bulk action)'
                                ));
                            }
                        })
                        ->successNotificationTitle(fn($records) => count($records) . ' DTR record(s) deleted'),
                ]),
            ])

            // ── Empty state ───────────────────────────────────────────────────
            ->emptyStateHeading('No DTR Records Found')
            ->emptyStateDescription('No records match your current filters, or none have been uploaded yet.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateActions([
            ])

            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    // =========================================================================
    //  FILTERS
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        return [
            // ── COLUMN 1: Employee ────────────────────────────────────────────
            // Multi-select with search. Spans full height of its column.
            // Admin only — employees always see only their own records via query scope.
            Tables\Filters\SelectFilter::make('employee')
                ->relationship('employee', 'name')
                ->searchable()
                ->preload()
                ->multiple()
                ->label('Employee')
                ->placeholder('All employees')
                ->native(false)
                ->visible(fn() => $isAdmin)
                ->columnSpan(1),

            // ── COLUMN 2: Upload Period ───────────────────────────────────────
            // Unified date filter: Quick Preset dropdown + optional custom From/To.
            // Replaces the old separate "Quick Period" + "Recency" + "date_range"
            // trio which caused the messy 5-filter layout.
            //
            // Logic: Quick Preset is applied first. If the user also sets a custom
            // From/To date, those take precedence (more specific wins).
            Tables\Filters\Filter::make('upload_period')
                ->label('Upload Period')
                ->columnSpan(1)
                ->form([
                    // Quick preset dropdown — single click for common HR queries
                    Forms\Components\Select::make('preset')
                        ->label('Quick Select')
                        ->placeholder('— pick a period —')
                        ->native(false)
                        ->options([
                            'today' => '📅  Today',
                            'yesterday' => '📅  Yesterday',
                            'this_week' => '📅  This Week',
                            'last_week' => '📅  Last Week',
                            'this_month' => '📅  This Month',
                            'last_month' => '📅  Last Month',
                        ])
                        ->live()
                        // Auto-fill the From/To pickers when a preset is chosen
                        // so the user can see and further refine the date range.
                        ->afterStateUpdated(function ($state, callable $set) {
                            [$from, $to] = match ($state) {
                                'today' => [today()->toDateString(), today()->toDateString()],
                                'yesterday' => [today()->subDay()->toDateString(), today()->subDay()->toDateString()],
                                'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                                'last_week' => [now()->subWeek()->startOfWeek()->toDateString(), now()->subWeek()->endOfWeek()->toDateString()],
                                'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                                'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                                default => [null, null],
                            };
                            $set('from', $from);
                            $set('to', $to);
                        }),

                    // Custom From / To — shown below the preset, same column
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('from')
                            ->label('From')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->maxDate(fn(callable $get) => $get('to') ?? now()),
                        Forms\Components\DatePicker::make('to')
                            ->label('To')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->minDate(fn(callable $get) => $get('from'))
                            ->maxDate(now()),
                    ]),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                )
                // Active filter chips — one per active boundary, individually removable
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (($data['preset'] ?? null) && !($data['from'] ?? null) && !($data['to'] ?? null)) {
                        $labels = [
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'this_week' => 'This Week',
                            'last_week' => 'Last Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                        ];
                        $indicators[] = Tables\Filters\Indicator::make('Period: ' . ($labels[$data['preset']] ?? $data['preset']))
                            ->removeField('preset');
                    }

                    if ($data['from'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make(
                            'From: ' . \Carbon\Carbon::parse($data['from'])->format('M d, Y')
                        )->removeField('from');
                    }

                    if ($data['to'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make(
                            'To: ' . \Carbon\Carbon::parse($data['to'])->format('M d, Y')
                        )->removeField('to');
                    }

                    return $indicators;
                }),

            // ── COLUMN 3: Record Attributes ───────────────────────────────────
            // Two small toggles stacked in column 3 — notes presence and
            // file integrity. Replaces the old standalone Recency ternary.
            Tables\Filters\Filter::make('attributes')
                ->label('Record Attributes')
                ->columnSpan(1)
                ->form([
                    Forms\Components\Select::make('has_notes')
                        ->label('Notes')
                        ->native(false)
                        ->placeholder('Any')
                        ->options([
                            'yes' => '💬  Has notes',
                            'no' => '—   No notes',
                        ]),

                    Forms\Components\Select::make('file_status')
                        ->label('File Status')
                        ->native(false)
                        ->placeholder('Any')
                        ->options([
                            'recent' => '🕐  Uploaded last 7 days',
                            'older' => '📁  Older than 7 days',
                        ]),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['has_notes'] === 'yes',
                            fn($q) =>
                            $q->whereNotNull('notes')->where('notes', '!=', '')
                        )
                        ->when(
                            $data['has_notes'] === 'no',
                            fn($q) =>
                            $q->where(fn($q) => $q->whereNull('notes')->orWhere('notes', ''))
                        )
                        ->when(
                            $data['file_status'] === 'recent',
                            fn($q) =>
                            $q->where('created_at', '>=', now()->subDays(7))
                        )
                        ->when(
                            $data['file_status'] === 'older',
                            fn($q) =>
                            $q->where('created_at', '<', now()->subDays(7))
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['has_notes'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make(
                            $data['has_notes'] === 'yes' ? 'Has notes' : 'No notes'
                        )->removeField('has_notes');
                    }

                    if ($data['file_status'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make(
                            $data['file_status'] === 'recent' ? 'Last 7 days' : 'Older than 7 days'
                        )->removeField('file_status');
                    }

                    return $indicators;
                }),
        ];
    }

    // =========================================================================
    //  PDF HELPERS
    //  ─────────────────────────────────────────────────────────────────────────
    //  buildPdf()            → pure PDF builder, throws on failure.
    //                          Used by both preview_pdf and export_pdf actions.
    //  generatePdfResponse() → wraps buildPdf(), handles download vs inline,
    //                          sends Filament notifications on success/failure.
    //
    //  WHERE TO PLACE: These two methods live here, just before resolveFilePath()
    //  and getEloquentQuery(), as protected static helpers of this Resource class.
    // =========================================================================

    /**
     * Build a DomPDF instance from a DTR record.
     *
     * Extracted from generatePdfResponse() so the PDF build logic is
     * independently reusable (e.g. by preview_pdf modal content closure)
     * without duplicating the try/catch and notification boilerplate.
     *
     * @throws \RuntimeException if the source CSV is missing from disk.
     */
    protected static function buildPdf(EmployeeDtr $record): \Barryvdh\DomPDF\PDF
    {
        $filePath = self::resolveFilePath($record->file_path);
        $fullPath = Storage::disk('public')->path($filePath);

        throw_unless(
            file_exists($fullPath),
            \RuntimeException::class,
            'CSV source file not found on disk.'
        );

        /** @var \App\Services\DtrCalculator $calculator */
        $calculator = app(\App\Services\DtrCalculator::class);
        $calculated = $calculator->calculateFromCsv($fullPath);
        $summary = $calculator->calculateSummary($calculated);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.dtr_pdf', [
            'records' => $calculated,
            'summary' => $summary,
            'employee' => $record->employee,
        ])->setOptions([
                    'isRemoteEnabled' => true,
                    'isLocalFileEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'enable_php' => false,
                    'chroot' => public_path(),
                    'dpi' => 96,
                    'defaultFont' => 'Arial',
                ]);
    }

    /**
     * Generate a PDF download response or return raw output bytes.
     *
     * WHY split from buildPdf(): generatePdfResponse() handles the
     * HTTP/notification layer while buildPdf() is a pure builder.
     * This makes both methods individually testable.
     */
    protected static function generatePdfResponse(EmployeeDtr $record, bool $download = false): mixed
    {
        try {
            $pdf = self::buildPdf($record);

            $pdfFileName = 'DTR_'
                . str_replace(' ', '_', $record->employee->name)
                . '_' . now()->format('Ymd')
                . '.pdf';

            if ($download) {
                $record->employee->notify(new DtrPdfGenerated($record, $pdfFileName));

                \Filament\Notifications\Notification::make()
                    ->success()
                    ->title('PDF Generated')
                    ->body('Employee has been notified.')
                    ->send();

                return response()->streamDownload(
                    fn() => print ($pdf->output()),
                    $pdfFileName
                );
            }

            return $pdf->output();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('PDF Generation Failed')
                ->body($e->getMessage())
                ->send();

            return null;
        }
    }

    // =========================================================================
    //  QUERY / ROUTING HELPERS
    // =========================================================================

    /**
     * Normalise file_path — stored values may be a bare string or a
     * single-element array depending on how Filament's FileUpload
     * serialises the state. Always resolve to a plain string here.
     */
    protected static function resolveFilePath(mixed $filePath): string
    {
        if (is_array($filePath)) {
            $filePath = $filePath[0] ?? '';
        }
        return (string) $filePath;
    }

    /**
     * Scope the query so employees only see their own records.
     * Eager-load 'employee' once to prevent N+1 in column closures.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('employee');

        return Auth::user()->isAdmin()
            ? $query
            : $query->where('employee_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyTimeRecords::route('/'),
        ];
    }
}
