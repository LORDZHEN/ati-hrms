<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $slug = 'leave-applications';
    protected static ?string $navigationLabel = 'Leave Application';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'employee';
    }

    // =========================================================================
    //  FORM  (unchanged — preserving your full form logic)
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\View::make('filament.resources.leave-application-resource.leave-form')
                ->columnSpanFull(),

            Forms\Components\Section::make('Form Fields (For Validation Only)')
                ->description('⚠️ Fill out the official CSC Form 6 above. These fields are for data binding.')
                ->schema([

                    Forms\Components\Hidden::make('employee_id')
                        ->default(fn() => auth()->id()),
                    Forms\Components\Hidden::make('first_name')
                        ->default(fn() => auth()->user()->first_name),
                    Forms\Components\Hidden::make('middle_name')
                        ->default(fn() => auth()->user()->middle_name),
                    Forms\Components\Hidden::make('last_name')
                        ->default(fn() => auth()->user()->last_name),
                    Forms\Components\Hidden::make('office_department')
                        ->default(fn() => auth()->user()->department),
                    Forms\Components\Hidden::make('position')
                        ->default(fn() => auth()->user()->position),
                    Forms\Components\Hidden::make('date_of_filing')
                        ->default(fn() => now()),
                    Forms\Components\Hidden::make('status')
                        ->default('pending'),

                    Forms\Components\Select::make('type_of_leave')
                        ->label('Type of Leave')
                        ->options([
                            'vacation_leave' => 'Vacation Leave',
                            'mandatory_forced_leave' => 'Mandatory/Forced Leave',
                            'sick_leave' => 'Sick Leave',
                            'maternity_leave' => 'Maternity Leave',
                            'paternity_leave' => 'Paternity Leave',
                            'special_privilege_leave' => 'Special Privilege Leave',
                            'solo_parent_leave' => 'Solo Parent Leave',
                            'study_leave' => 'Study Leave',
                            '10_day_vawc_leave' => '10-Day VAWC Leave',
                            'rehabilitation_privilege' => 'Rehabilitation Privilege',
                            'special_leave_benefits_for_women' => 'Special Leave Benefits for Women',
                            'special_emergency_leave' => 'Special Emergency Leave',
                            'adoption_leave' => 'Adoption Leave',
                            'others' => 'Others',
                        ])
                        ->required()
                        ->live()
                        ->native(false),

                    Forms\Components\TextInput::make('other_leave_type')
                        ->label('Specify Other Leave Type')
                        ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'others')
                        ->required(fn(Forms\Get $get) => $get('type_of_leave') === 'others')
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('leave_date_from')
                        ->label('Leave Date From')
                        ->required()
                        ->live()
                        ->native(false)
                        ->minDate(function (Forms\Get $get) {
                            $leaveType = $get('type_of_leave');
                            if ($leaveType === 'vacation_leave') {
                                return self::addWorkingDays(now(), 5);
                            }
                            if ($leaveType === 'sick_leave') {
                                return null;
                            }
                            return now()->startOfDay();
                        })
                        ->maxDate(function (Forms\Get $get) {
                            if ($get('type_of_leave') === 'sick_leave') {
                                return now()->endOfDay();
                            }
                            return null;
                        })
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            self::calculateWorkingDays($state, $get('leave_date_to'), $set);
                            $to = $get('leave_date_to');
                            if ($to && Carbon::parse($state)->gt(Carbon::parse($to))) {
                                $set('leave_date_to', null);
                            }
                        }),

                    Forms\Components\DatePicker::make('leave_date_to')
                        ->label('Leave Date To')
                        ->required()
                        ->live()
                        ->native(false)
                        ->minDate(function (Forms\Get $get) {
                            $leaveType = $get('type_of_leave');
                            $from = $get('leave_date_from');
                            if ($leaveType === 'vacation_leave') {
                                return $from ? Carbon::parse($from) : self::addWorkingDays(now(), 5);
                            }
                            if ($leaveType === 'sick_leave') {
                                return $from ? Carbon::parse($from) : null;
                            }
                            return $from ? Carbon::parse($from) : now()->startOfDay();
                        })
                        ->maxDate(function (Forms\Get $get) {
                            if ($get('type_of_leave') === 'sick_leave') {
                                return now()->endOfDay();
                            }
                            return null;
                        })
                        ->afterStateUpdated(
                            fn($state, Forms\Set $set, Forms\Get $get) =>
                            self::calculateWorkingDays($get('leave_date_from'), $state, $set)
                        ),

                    Forms\Components\TextInput::make('number_of_working_days')
                        ->label('Number of Working Days')
                        ->numeric()
                        ->required()
                        ->minValue(0.5)
                        ->step(0.5)
                        ->readOnly(),

                    Forms\Components\FileUpload::make('supporting_document')
                        ->label('Supporting Document')
                        ->visible(
                            fn(Forms\Get $get) =>
                            $get('type_of_leave') === 'sick_leave' &&
                            ($get('number_of_working_days') ?? 0) >= 3
                        )
                        ->required(
                            fn(Forms\Get $get) =>
                            $get('type_of_leave') === 'sick_leave' &&
                            ($get('number_of_working_days') ?? 0) >= 3
                        )
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->directory('leave-documents')
                        ->maxSize(5120)
                        ->image()
                        ->imageEditor(),

                    Forms\Components\Radio::make('vacation_location')
                        ->label('Vacation Location')
                        ->options([
                            'within_philippines' => 'Within the Philippines',
                            'abroad' => 'Abroad',
                        ])
                        ->inline()
                        ->live(),

                    Forms\Components\TextInput::make('abroad_specify')
                        ->label('Specify Country/Location Abroad')
                        ->visible(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                        ->required(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                        ->maxLength(255),

                    Forms\Components\Radio::make('sick_leave_location')
                        ->label('Sick Leave Location')
                        ->options([
                            'in_hospital' => 'In Hospital (Confined)',
                            'out_patient' => 'Out Patient',
                        ])
                        ->inline()
                        ->live(),

                    Forms\Components\TextInput::make('hospital_illness_specify')
                        ->label('Hospital Illness')
                        ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                        ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('outpatient_illness_specify')
                        ->label('Outpatient Illness')
                        ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                        ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('women_illness_specify')
                        ->label("Women's Illness")
                        ->maxLength(255),

                    Forms\Components\Radio::make('study_leave_purpose')
                        ->label('Study Leave Purpose')
                        ->options([
                            'masters_degree' => "Completion of Master's Degree",
                            'bar_board_review' => 'BAR/Board Examination Review',
                        ])
                        ->inline(),

                    Forms\Components\Radio::make('other_purpose')
                        ->label('Other Purpose')
                        ->options([
                            'monetization' => 'Monetization of Leave Credits',
                            'terminal_leave' => 'Terminal Leave',
                        ])
                        ->inline(),

                    Forms\Components\Radio::make('commutation')
                        ->label('Commutation')
                        ->options([
                            'not_requested' => 'Not Requested',
                            'requested' => 'Requested',
                        ])
                        ->default('not_requested')
                        ->inline()
                        ->required(),
                ])
                ->collapsed()
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        // Compute once — prevents repeated auth lookups in every closure.
        $isAdmin = auth()->user()->role === 'admin';

        return $table
            ->columns(self::getTableColumns($isAdmin))
            ->filters(
                self::getEnhancedFilters($isAdmin),
                layout: FiltersLayout::AboveContentCollapsible
            )
            // WHY: 2 columns for employees (Status/Type + Period).
            //      3 columns for admins (+ Employee selector).
            //      Dynamic column count prevents wasted whitespace on the employee view.
            ->filtersFormColumns($isAdmin ? 3 : 2)
            ->filtersFormWidth(\Filament\Support\Enums\MaxWidth::FourExtraLarge)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->actions(self::getContextualActions($isAdmin))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin),
                ]),
            ])
            ->defaultSort('date_of_filing', 'desc')
            ->modifyQueryUsing(
                fn(Builder $query) => $isAdmin
                ? $query->with('employee')
                : $query->with('employee')->where('employee_id', auth()->id())
            )
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No leave applications found')
            ->emptyStateDescription('Submit your first leave application to get started.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Leave Application')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => !$isAdmin),
            ]);
    }

    // =========================================================================
    //  TABLE COLUMNS
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            // ── Employee — admin only ─────────────────────────────────────────
            Tables\Columns\TextColumn::make('employee.name')
                ->label('Employee')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            // ── Position • Department ─────────────────────────────────────────
            Tables\Columns\TextColumn::make('position')
                ->label('Position')
                ->color('gray')
                ->formatStateUsing(
                    fn($record) => $record->position . ' • ' . ($record->office_department ?? 'N/A')
                )
                ->toggleable(isToggledHiddenByDefault: true),

            // ── Leave Type badge ──────────────────────────────────────────────
            Tables\Columns\TextColumn::make('type_of_leave')
                ->label('Leave Type')
                ->badge()
                ->searchable()
                ->formatStateUsing(fn($state) => str_replace('_', ' ', ucwords($state, '_')))
                ->color(fn(string $state) => match ($state) {
                    'vacation_leave' => 'success',
                    'sick_leave' => 'danger',
                    'mandatory_forced_leave' => 'warning',
                    'maternity_leave',
                    'paternity_leave' => 'info',
                    'special_privilege_leave',
                    'study_leave' => 'primary',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'vacation_leave' => 'heroicon-o-sun',
                    'sick_leave' => 'heroicon-o-heart',
                    'maternity_leave',
                    'paternity_leave' => 'heroicon-o-user-group',
                    'study_leave' => 'heroicon-o-academic-cap',
                    default => 'heroicon-o-document-text',
                }),

            // ── Leave Period (From → To) ───────────────────────────────────────
            Tables\Columns\TextColumn::make('leave_date_from')
                ->label('Leave Period')
                ->sortable()
                ->icon('heroicon-o-calendar-days')
                ->iconColor('warning')
                ->formatStateUsing(
                    fn($record) =>
                    Carbon::parse($record->leave_date_from)->format('M d, Y')
                    . ' → '
                    . Carbon::parse($record->leave_date_to)->format('M d, Y')
                ),

            // ── Duration badge ────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('number_of_working_days')
                ->label('Duration')
                ->badge()
                ->color('info')
                ->icon('heroicon-o-clock')
                ->formatStateUsing(
                    fn($state) => $state . ' working ' . ($state == 1 ? 'day' : 'days')
                ),

            // ── Status badge ──────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state) => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'disapproved' => 'danger',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'pending' => 'heroicon-o-clock',
                    'approved' => 'heroicon-o-check-circle',
                    'disapproved' => 'heroicon-o-x-circle',
                    default => null,
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),

            // ── Filed ─────────────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('date_of_filing')
                ->label('Filed')
                ->since()
                ->sortable()
                ->tooltip(
                    fn($record) => $record->date_of_filing
                    ? Carbon::parse($record->date_of_filing)->format('M d, Y')
                    : null
                )
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            // ── Processed By ──────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('authorized_officer')
                ->label('Processed By')
                ->color('gray')
                ->placeholder('Awaiting Review')
                ->icon('heroicon-o-shield-check')
                ->iconColor('gray')
                ->limit(22)
                ->tooltip(fn($record) => $record->authorized_officer),

            // ── Commutation ───────────────────────────────────────────────────
            Tables\Columns\IconColumn::make('commutation')
                ->label('Commutation')
                ->boolean()
                ->trueIcon('heroicon-o-currency-dollar')
                ->falseIcon('heroicon-o-x-mark')
                ->trueColor('success')
                ->falseColor('gray')
                ->getStateUsing(fn($record) => $record->commutation === 'requested')
                ->toggleable(isToggledHiddenByDefault: true),

            // ── Processed On ──────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('date_approved_disapproved')
                ->label('Processed On')
                ->dateTime('M d, Y h:i A')
                ->color('gray')
                ->placeholder('—')
                ->icon('heroicon-o-check-badge')
                ->iconColor('success')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    // =========================================================================
    //  FILTERS
    //
    //  Design principle: fewer filters = less confusion.
    //
    //  EMPLOYEE view → 2 filters:
    //    1. "Status & Type"  — the two most-used criteria
    //    2. "Period"         — one Quick Select that auto-fills From/To
    //
    //  ADMIN view → 3 filters (same 2 + Employee selector as column 1):
    //    1. "Employee"       — searchable employee picker
    //    2. "Status & Type"  — same as employee view
    //    3. "Period"         — same as employee view
    //
    //  Commutation and Medical Certificate filters are removed from the
    //  visible panel — they were rarely used and added visual noise.
    //  Admins can still filter by those via the Generate Report modal.
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        $filters = [];

        // ── ADMIN ONLY — Column 1: Employee picker ────────────────────────────
        if ($isAdmin) {
            $filters[] = Tables\Filters\Filter::make('employee_filter')
                ->label('Employee')
                ->columnSpan(1)
                ->form([
                    Forms\Components\Select::make('employee_id')
                        ->label('Employee')
                        ->relationship('employee', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->placeholder('All employees'),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when($data['employee_id'] ?? null, fn($q, $v) => $q->where('employee_id', $v))
                )
                ->indicateUsing(function (array $data): array {
                    if (!($data['employee_id'] ?? null))
                        return [];
                    $name = \App\Models\User::find($data['employee_id'])?->name;
                    return $name
                        ? [Tables\Filters\Indicator::make('Employee: ' . $name)->removeField('employee_id')]
                        : [];
                });
        }

        // ── Column 1 (employee) / Column 2 (admin): Status & Leave Type ──────
        // WHY: These are the two filters employees use 95% of the time.
        // Grouped into one card so the panel stays compact and uncluttered.
        $filters[] = Tables\Filters\Filter::make('status_and_type')
            ->label('Status & Leave Type')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->placeholder('All statuses')
                    ->options([
                        'pending' => '🕐  Pending',
                        'approved' => '✅  Approved',
                        'disapproved' => '❌  Disapproved',
                    ]),

                Forms\Components\Select::make('type_of_leave')
                    ->label('Leave Type')
                    ->native(false)
                    ->placeholder('All types')
                    ->options([
                        'vacation_leave' => 'Vacation Leave',
                        'sick_leave' => 'Sick Leave',
                        'maternity_leave' => 'Maternity Leave',
                        'paternity_leave' => 'Paternity Leave',
                        'special_privilege_leave' => 'Special Privilege Leave',
                        'mandatory_forced_leave' => 'Mandatory/Forced Leave',
                        'study_leave' => 'Study Leave',
                        'solo_parent_leave' => 'Solo Parent Leave',
                        'others' => 'Others',
                    ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v))
                    ->when($data['type_of_leave'] ?? null, fn($q, $v) => $q->where('type_of_leave', $v))
            )
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['status'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))
                        ->removeField('status');
                }
                if ($data['type_of_leave'] ?? null) {
                    $label = str_replace('_', ' ', ucwords($data['type_of_leave'], '_'));
                    $indicators[] = Tables\Filters\Indicator::make('Type: ' . $label)
                        ->removeField('type_of_leave');
                }
                return $indicators;
            });

        // ── Column 2 (employee) / Column 3 (admin): Period picker ────────────
        // WHY: A single Quick Select dropdown covers "this month", "last month"
        // etc. in one click. The From/To pickers are auto-filled but editable
        // for custom ranges. This replaces the old 4-field date range mess.
        $filters[] = Tables\Filters\Filter::make('period')
            ->label('Filing Period')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('preset')
                    ->label('Quick Select')
                    ->placeholder('— pick a period —')
                    ->native(false)
                    ->options([
                        'this_month' => '📅  This Month',
                        'last_month' => '📅  Last Month',
                        'this_week' => '📅  This Week',
                        'this_year' => '📅  This Year',
                        'custom' => '✏️   Custom range…',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                            'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                            default => [null, null], // 'custom' — let user fill pickers
                        };
                        $set('from', $from);
                        $set('to', $to);
                    }),

                // From/To shown below — auto-filled by preset, editable for custom
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
                    ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('date_of_filing', '>=', $d))
                    ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('date_of_filing', '<=', $d))
            )
            ->indicateUsing(function (array $data): array {
                // Show the preset label as a single chip when pickers were auto-filled
                $presetLabels = [
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_week' => 'This Week',
                    'this_year' => 'This Year',
                ];

                $indicators = [];

                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    // If a named preset was chosen and the dates weren't manually edited,
                    // show the preset name. Otherwise show the raw date range.
                    $preset = $data['preset'] ?? null;
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Period: ' . $presetLabels[$preset])
                            ->removeField('preset');
                    } else {
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                'From: ' . Carbon::parse($data['from'])->format('M d, Y')
                            )->removeField('from');
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                'To: ' . Carbon::parse($data['to'])->format('M d, Y')
                            )->removeField('to');
                        }
                    }
                }

                return $indicators;
            });

        return $filters;
    }

    // =========================================================================
    //  ACTIONS
    // =========================================================================

    protected static function getContextualActions(bool $isAdmin): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        !$isAdmin && $record->status === 'pending'
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print Form')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('leave_application.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'approved'),

                Tables\Actions\Action::make('view_document')
                    ->label('View Medical Cert')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('primary')
                    ->url(fn($record) => asset('storage/' . $record->supporting_document))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => !empty($record->supporting_document)),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(
                        fn($record) =>
                        !$isAdmin && $record->status === 'pending'
                    ),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')
                ->button(),
        ];
    }

    // =========================================================================
    //  HELPERS
    // =========================================================================

    protected static function addWorkingDays(Carbon $date, int $days): Carbon
    {
        $result = $date->copy()->startOfDay();
        $added = 0;
        while ($added < $days) {
            $result->addDay();
            if ($result->isWeekday()) {
                $added++;
            }
        }
        return $result;
    }

    protected static function calculateWorkingDays($from, $to, Forms\Set $set): void
    {
        if (!$from || !$to)
            return;

        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);

            if ($toDate->lessThan($fromDate)) {
                $set('number_of_working_days', 0);
                return;
            }

            $set('number_of_working_days', $fromDate->diffInWeekdays($toDate) + 1);
        } catch (\Exception) {
            $set('number_of_working_days', 0);
        }
    }

    // =========================================================================
    //  NAVIGATION BADGE
    // =========================================================================

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        $count = LeaveApplication::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return auth()->user()?->role === 'admin' &&
            LeaveApplication::where('status', 'pending')->count() > 0
            ? 'warning'
            : null;
    }

    // =========================================================================
    //  RELATIONS / PAGES
    // =========================================================================

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit' => Pages\EditLeaveApplication::route('/{record}/edit'),
            'view' => Pages\ViewLeaveApplication::route('/{record}'),
        ];
    }
}
