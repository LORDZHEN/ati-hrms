<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTimeRecordResource\Pages;
use App\Models\EmployeeDtr;
use App\Models\User;
use App\Services\DtrCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
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
use App\Notifications\DtrPdfGenerated;
use App\Notifications\DtrDeleted;

class DailyTimeRecordResource extends Resource
{
    protected static ?string $model = EmployeeDtr::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Daily Time Record';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?string $modelLabel = 'Daily Time Record';
    protected static ?string $pluralModelLabel = 'Daily Time Records';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Modern Multi-Step Employee Selection
            Forms\Components\Wizard::make([
                // Step 1: Select Employees
                Forms\Components\Wizard\Step::make('Select Employees')
                    ->description('Choose up to 10 employees for DTR upload')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Forms\Components\CheckboxList::make('selected_employees')
                            ->label('')
                            ->options(
                                User::where('role', User::ROLE_EMPLOYEE)
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [
                                            $user->id => new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 p-2">
                                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 font-bold">
                                                        ' . strtoupper(substr($user->name, 0, 2)) . '
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900 dark:text-white">' . e($user->name) . '</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">' . e($user->email) . '</div>
                                                    </div>
                                                </div>'
                                            )
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
                                    ->map(function ($employeeId) use ($existingData) {
                                        $existing = $existingData->get($employeeId);
                                        return [
                                            'employee_id' => $employeeId,
                                            'file_path' => $existing['file_path'] ?? null,
                                            'notes' => $existing['notes'] ?? null,
                                        ];
                                    })
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
                                        <p class="text-lg font-bold text-primary-600 dark:text-primary-400">' . $count . ' Employee' . ($count > 1 ? 's' : '') . ' Selected</p>
                                        <p class="text-sm text-primary-500 dark:text-primary-500">' . (10 - $count) . ' slots remaining</p>
                                    </div>'
                                );
                            })
                            ->columnSpanFull(),
                    ]),

                // Step 2: Upload Files
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
                                                            return '-';

                                                        return new \Illuminate\Support\HtmlString(
                                                            '<div class="flex items-center gap-4 p-4 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-950 dark:to-primary-900 rounded-xl border border-primary-200 dark:border-primary-800">
                                                                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-500 dark:bg-primary-600 text-white font-bold text-lg shadow-lg">
                                                                    ' . strtoupper(substr($employee->name, 0, 2)) . '
                                                                </div>
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
                                                    ->helperText('Upload the employee\'s DTR in CSV format')
                                                    ->downloadable()
                                                    ->previewable(false)
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        // Normalize the state to a string immediately
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

                // Step 3: Review & Submit
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
                                        '<div class="text-center p-8">
                                            <p class="text-gray-500">No files to review</p>
                                        </div>'
                                    );
                                }

                                $html = '<div class="space-y-4">';
                                foreach ($rows as $row) {
                                    $employee = User::find($row['employee_id']);
                                    $filePath = $row['file_path'] ?? null;

                                    // Handle file_path being an array or string
                                    if (is_array($filePath)) {
                                        $filePath = $filePath[0] ?? null;
                                    }

                                    $hasFile = !empty($filePath);
                                    $fileName = $hasFile ? basename($filePath) : 'No file uploaded';
                                    $notes = $row['notes'] ?? 'No notes';

                                    $statusColor = $hasFile ? 'green' : 'red';
                                    $statusIcon = $hasFile ? '✓' : '✗';

                                    $html .= '
                                        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-' . $statusColor . '-100 dark:bg-' . $statusColor . '-900 text-' . $statusColor . '-600 dark:text-' . $statusColor . '-400 font-bold text-xl">
                                                ' . $statusIcon . '
                                            </div>
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

            // Instructions Section
            Forms\Components\Section::make('CSV File Requirements')
                ->description('Please ensure your CSV files meet these requirements')
                ->schema([
                    Forms\Components\Placeholder::make('csv_format')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="font-semibold text-blue-900 dark:text-blue-100 mb-2 flex items-center gap-2">
                                        <span>📋</span> Required Columns
                                    </div>
                                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
                                        <li>EmployeeID</li>
                                        <li>Name</li>
                                        <li>Date (YYYY-MM-DD)</li>
                                        <li>MorningIn, MorningOut</li>
                                        <li>AfternoonIn, AfternoonOut</li>
                                    </ul>
                                </div>
                                <div class="p-4 bg-amber-50 dark:bg-amber-950/30 rounded-lg border border-amber-200 dark:border-amber-800">
                                    <div class="font-semibold text-amber-900 dark:text-amber-100 mb-2 flex items-center gap-2">
                                        <span>⏰</span> Time Format
                                    </div>
                                    <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1 list-disc list-inside">
                                        <li>Use 24-hour format (HH:MM)</li>
                                        <li>Example: 08:00, 17:30</li>
                                        <li>Leave empty for no entry</li>
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

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->isAdmin();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->employee->email)
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('primary')
                    ->visible($isAdmin),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('DTR File')
                    ->formatStateUsing(function ($state) {
                        // Handle if file_path is stored as array
                        if (is_array($state)) {
                            $state = $state[0] ?? '';
                        }
                        return basename($state);
                    })
                    ->description(fn($record) => 'Uploaded ' . $record->created_at->diffForHumans())
                    ->icon('heroicon-m-document-text')
                    ->iconColor('success')
                    ->limit(35)
                    ->tooltip(function ($state) {
                        if (is_array($state)) {
                            $state = $state[0] ?? '';
                        }
                        return $state;
                    })
                    ->copyable()
                    ->copyMessage('Path copied!')
                    ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->icon('heroicon-m-chat-bubble-left')
                    ->iconColor('warning')
                    ->toggleable()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Upload Date')
                    ->dateTime('M d, Y')
                    ->description(fn($record) => $record->created_at->format('h:i A'))
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn() => 'Active')
                    ->colors(['success' => 'Active'])
                    ->icon('heroicon-m-check-circle')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Modified')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-arrow-path')
                    ->color('gray'),
            ])
            ->filters(
                self::getEnhancedFilters($isAdmin),
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->filtersFormColumns(2)
            ->filtersFormWidth('2xl')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->label('Download CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function ($record) {
                            $filePath = $record->file_path;
                            if (is_array($filePath)) {
                                $filePath = $filePath[0] ?? '';
                            }
                            return Storage::disk('public')->download($filePath);
                        }),

                    Tables\Actions\Action::make('export_pdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Export DTR Report')
                        ->modalDescription('Generate a calculated DTR report.')
                        ->action(function ($record) {
                            set_time_limit(300);
                            $calculator = app(DtrCalculator::class);

                            $filePath = $record->file_path;
                            if (is_array($filePath)) {
                                $filePath = $filePath[0] ?? '';
                            }

                            $fullPath = Storage::disk('public')->path($filePath);

                            if (!file_exists($fullPath)) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('File Not Found')
                                    ->send();
                                return;
                            }

                            try {
                                $calculated = $calculator->calculateFromCsv($fullPath);
                                $pdf = Pdf::loadView('exports.dtr_pdf', [
                                    'records' => $calculated,
                                    'employee' => $record->employee,
                                ]);

                                $pdfFileName = 'DTR_' . str_replace(' ', '_', $record->employee->name) . '_' . now()->format('Ymd') . '.pdf';

                                // Send notification to employee
                                $record->employee->notify(new DtrPdfGenerated($record, $pdfFileName));

                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('PDF Generated')
                                    ->body('Employee has been notified.')
                                    ->send();

                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    $pdfFileName
                                );
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Failed')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('details')
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn($record) => 'DTR: ' . $record->employee->name)
                        ->modalContent(fn($record) => view('filament.tables.cells.dtr-details', ['record' => $record]))
                        ->modalSubmitAction(false)
                        ->slideOver(),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => $isAdmin)
                        ->before(function ($record) {
                            // Send notification before deletion
                            $filePath = $record->file_path;
                            if (is_array($filePath)) {
                                $filePath = $filePath[0] ?? '';
                            }

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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin)
                        ->before(function ($records) {
                            // Send notification to each employee before bulk deletion
                            foreach ($records as $record) {
                                $filePath = $record->file_path;
                                if (is_array($filePath)) {
                                    $filePath = $filePath[0] ?? '';
                                }

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
            ->emptyStateHeading('No DTR Records')
            ->emptyStateDescription('Upload DTR records to get started.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload DTR')
                    ->icon('heroicon-m-plus')
                    ->visible(fn() => $isAdmin),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        return [
            Tables\Filters\SelectFilter::make('employee')
                ->relationship('employee', 'name')
                ->searchable()
                ->preload()
                ->multiple()
                ->label('Employee')
                ->placeholder('All employees')
                ->native(false)
                ->visible(fn() => $isAdmin),

            Tables\Filters\Filter::make('date_range')
                ->form([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('from')
                            ->label('From')
                            ->native(false),
                        Forms\Components\DatePicker::make('to')
                            ->label('To')
                            ->native(false),
                    ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                }),

            Tables\Filters\TernaryFilter::make('has_notes')
                ->label('Notes')
                ->placeholder('All')
                ->trueLabel('With notes')
                ->falseLabel('No notes')
                ->queries(
                    true: fn($q) => $q->whereNotNull('notes')->where('notes', '!=', ''),
                    false: fn($q) => $q->where(fn($q) => $q->whereNull('notes')->orWhere('notes', '')),
                ),

            Tables\Filters\TernaryFilter::make('recent')
                ->label('Recent')
                ->placeholder('All time')
                ->trueLabel('Last 7 days')
                ->falseLabel('Older')
                ->queries(
                    true: fn($q) => $q->where('created_at', '>=', now()->subDays(7)),
                    false: fn($q) => $q->where('created_at', '<', now()->subDays(7)),
                ),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        return Auth::user()->isAdmin() ? $query : $query->where('employee_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyTimeRecords::route('/'),
            'create' => Pages\CreateDailyTimeRecord::route('/create'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
