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

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Select Employees')
                ->description('Choose employees to upload DTR records (Maximum 10 employees at a time)')
                ->schema([
                    Forms\Components\CheckboxList::make('selected_employees')
                        ->label('Select Employees')
                        ->options(
                            User::where('role', User::ROLE_EMPLOYEE)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->columns(3)
                        ->searchable()
                        ->bulkToggleable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Limit to 10 employees
                            if (count($state ?? []) > 10) {
                                $state = array_slice($state, 0, 10);
                                $set('selected_employees', $state);

                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Maximum Limit Reached')
                                    ->body('You can only select up to 10 employees at a time.')
                                    ->send();
                            }

                            // Build DTR rows based on selected employees
                            $existingRows = $get('dtr_rows') ?? [];
                            $existingData = collect($existingRows)->keyBy('employee_id');

                            $rows = collect($state ?? [])
                                ->map(function ($employeeId) use ($existingData) {
                                    // Preserve existing file upload and notes if re-selecting
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
                        })
                        ->helperText('Select employees to upload their DTR records'),
                ])
                ->visible(fn() => Auth::user()->isAdmin())
                ->collapsible()
                ->collapsed(false),

            Forms\Components\Section::make('Upload DTR Records')
                ->description('Upload CSV files and add notes for each selected employee')
                ->schema([
                    Forms\Components\Repeater::make('dtr_rows')
                        ->label('')
                        ->schema([
                            Forms\Components\Hidden::make('employee_id'),

                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\Placeholder::make('employee_info')
                                        ->label('Employee')
                                        ->content(function ($get) {
                                            $employee = User::find($get('employee_id'));
                                            if (!$employee)
                                                return '-';

                                            return new \Illuminate\Support\HtmlString(
                                                '<div class="space-y-1">
                                                    <div class="font-semibold text-gray-900 dark:text-white">' .
                                                e($employee->name) .
                                                '</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">' .
                                                e($employee->email) .
                                                '</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        ID: ' . e($employee->id) .
                                                '</div>
                                                </div>'
                                            );
                                        }),

                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('Upload DTR CSV')
                                        ->acceptedFileTypes(['text/csv', 'application/csv'])
                                        ->maxSize(2048) // 2MB
                                        ->directory('dtr_files')
                                        ->disk('public')
                                        ->required()
                                        ->helperText('Upload the raw DTR log file in CSV format')
                                        ->downloadable()
                                        ->previewable(false),

                                    Forms\Components\Textarea::make('notes')
                                        ->label('Notes (Optional)')
                                        ->placeholder('Add any notes or remarks about this DTR...')
                                        ->rows(3)
                                        ->maxLength(500),
                                ]),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->defaultItems(0)
                        ->columnSpanFull()
                        ->extraAttributes([
                            'class' => 'dtr-repeater-table'
                        ]),
                ])
                ->visible(fn($get) => filled($get('dtr_rows')))
                ->collapsible()
                ->collapsed(false),

            // Info Section
            Forms\Components\Section::make('Instructions')
                ->description('Important information about DTR upload')
                ->schema([
                    Forms\Components\Placeholder::make('instructions')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-primary-600">📋</span>
                                    <span>CSV file must contain the following columns: <strong>EmployeeID, Name, Date, MorningIn, MorningOut, AfternoonIn, AfternoonOut</strong></span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-primary-600">⏰</span>
                                    <span>Time format should be in <strong>HH:MM</strong> (24-hour format)</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-primary-600">📅</span>
                                    <span>Date format should be <strong>YYYY-MM-DD</strong></span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-primary-600">👥</span>
                                    <span>You can upload DTR records for up to <strong>10 employees</strong> at once</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-semibold text-primary-600">🔔</span>
                                    <span>Each employee will receive a notification when their DTR is uploaded</span>
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->visible(fn() => Auth::user()->isAdmin()),

                Tables\Columns\TextColumn::make('employee.email')
                    ->label('Email')
                    ->searchable()
                    ->visible(fn() => Auth::user()->isAdmin())
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn($state) => basename($state))
                    ->limit(30)
                    ->tooltip(fn($state) => $state),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded On')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated On')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn() => Auth::user()->isAdmin()),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Uploaded From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Uploaded Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                // Download raw DTR file (Admins & Owner)
                Tables\Actions\Action::make('download_raw')
                    ->label('Download Raw')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn($record) => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(
                        fn($record) =>
                        Auth::user()->isAdmin() || Auth::id() === $record->employee_id
                    ),

                // Export calculated PDF (Admins and Employee themselves)
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(function ($record) {
                        set_time_limit(300);
                        $calculator = app(DtrCalculator::class);

                        // Fetch file from public disk
                        $filePath = Storage::disk('public')->path($record->file_path);

                        if (!file_exists($filePath)) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('File Not Found')
                                ->body('The DTR file could not be found.')
                                ->send();
                            return;
                        }

                        try {
                            // Calculate DTR
                            $calculated = $calculator->calculateFromCsv($filePath);

                            $pdf = Pdf::loadView('exports.dtr_pdf', [
                                'records' => $calculated,
                                'employee' => $record->employee,
                            ]);

                            return response()->streamDownload(
                                fn() => print ($pdf->output()),
                                'DTR_Report_' . str_replace(' ', '_', $record->employee->name) . '_' . now()->format('Y-m-d') . '.pdf'
                            );
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('PDF Generation Failed')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn($record) => Auth::user()->isAdmin() || Auth::id() === $record->employee_id),

                // // View details
                // Tables\Actions\ViewAction::make()
                //     ->modalContent(fn($record) => view('filament.resources.dtr.view-details', [
                //         'record' => $record,
                //     ]))
                //     ->visible(fn($record) => Auth::user()->isAdmin() || Auth::id() === $record->employee_id),

                // Delete action
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => Auth::user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->isAdmin()),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn() => Auth::user()->isAdmin()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Employees only see their own DTRs
        return Auth::user()->isAdmin()
            ? $query
            : $query->where('employee_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyTimeRecords::route('/'),
            'create' => Pages\CreateDailyTimeRecord::route('/create'),
            // 'edit' => Pages\EditDailyTimeRecord::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false; // Disable editing for data integrity
    }
}
