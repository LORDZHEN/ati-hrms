<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Notifications\LeaveApplicationStatusUpdated;
use App\Notifications\LeaveApplicationRemarksAdded;
use App\Notifications\LeaveApplicationSubmitted;
use Carbon\Carbon;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $slug = 'leave-applications';
    protected static ?string $navigationLabel = 'Leave Application';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 2;

    // =========================================================================
    //  ACCESS CONTROL — Hide entirely from Job Order users
    // =========================================================================

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === User::ROLE_REGULAR;
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return false;
        return $record->employee_id === $user->id && $record->status === 'pending';
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return true;
        return $record->employee_id === $user->id;
    }

    // =========================================================================
    //  LEAVE TYPE OPTIONS — centralised so every dropdown stays in sync
    //  [WELLNESS LEAVE ADDED — 2026 CSC format update]
    // =========================================================================

    public static function leaveTypeOptions(): array
    {
        return [
            'vacation_leave'                   => 'Vacation Leave',
            'mandatory_forced_leave'           => 'Mandatory/Forced Leave',
            'sick_leave'                       => 'Sick Leave',
            'maternity_leave'                  => 'Maternity Leave',
            'paternity_leave'                  => 'Paternity Leave',
            'special_privilege_leave'          => 'Special Privilege Leave',
            'solo_parent_leave'                => 'Solo Parent Leave',
            'study_leave'                      => 'Study Leave',
            '10_day_vawc_leave'                => '10-Day VAWC Leave',
            'rehabilitation_privilege'         => 'Rehabilitation Privilege',
            'special_leave_benefits_for_women' => 'Special Leave Benefits for Women',
            'special_emergency_leave'          => 'Special Emergency Leave',
            'adoption_leave'                   => 'Adoption Leave',
            'wellness_leave'                   => 'Wellness Leave',   // NEW — 2026 CSC format
            'others'                           => 'Others',
        ];
    }

    // =========================================================================
    //  FORM — restores original custom blade view + hidden binding fields
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([

            // Admin remarks — shown to employee when present (read-only)
            Forms\Components\Textarea::make('remarks')
                ->label('Remarks from Admin')
                ->rows(4)
                ->columnSpanFull()
                ->disabled()
                ->hidden(fn($record) => blank($record?->remarks)),

            // ── Your existing custom CSC Form 6 blade view ────────────────────
            Forms\Components\View::make('filament.resources.leave-application-resource.leave-form')
                ->columnSpanFull(),

            // ── Hidden binding fields (original form logic preserved) ─────────
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

                    // [CHANGE] Uses centralised leaveTypeOptions() — Wellness Leave included
                    Forms\Components\Select::make('type_of_leave')
                        ->label('Type of Leave')
                        ->options(self::leaveTypeOptions())
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
                            'abroad'             => 'Abroad',
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
                            'masters_degree'  => "Completion of Master's Degree",
                            'bar_board_review' => 'BAR/Board Examination Review',
                        ])
                        ->inline(),

                    Forms\Components\Radio::make('other_purpose')
                        ->label('Other Purpose')
                        ->options([
                            'monetization'  => 'Monetization of Leave Credits',
                            'terminal_leave' => 'Terminal Leave',
                        ])
                        ->inline(),

                    Forms\Components\Radio::make('commutation')
                        ->label('Commutation')
                        ->options([
                            'not_requested' => 'Not Requested',
                            'requested'     => 'Requested',
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
        $isAdmin = auth()->user()->role === User::ROLE_ADMIN;

        return $table
            ->columns(self::getTableColumns($isAdmin))
            ->filters(
                self::getEnhancedFilters($isAdmin),
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->filtersFormColumns($isAdmin ? 3 : 2)
            ->filtersFormWidth(\Filament\Support\Enums\MaxWidth::FourExtraLarge)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->actions(self::getContextualActions($isAdmin))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkApprove')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Approve Multiple Leave Applications')
                        ->modalDescription('Are you sure you want to approve all selected leave applications?')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'approved') {
                                    $record->update(['status' => 'approved']);
                                    $record->employee?->notify(new LeaveApplicationStatusUpdated($record));
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->success()
                                ->title('Bulk Approval Complete')
                                ->body("{$count} leave application(s) approved.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

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
    //  TABLE COLUMNS — use the actual model field names
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\TextColumn::make('employee.name')
                ->label('Employee')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('position')
                ->label('Position')
                ->color('gray')
                ->formatStateUsing(
                    fn($record) => $record->position . ' • ' . ($record->office_department ?? 'N/A')
                )
                ->toggleable(isToggledHiddenByDefault: true),

            // [CHANGE] Added 'wellness_leave' badge colour + icon
            Tables\Columns\TextColumn::make('type_of_leave')
                ->label('Leave Type')
                ->badge()
                ->searchable()
                ->formatStateUsing(fn($state) => str_replace('_', ' ', ucwords($state, '_')))
                ->color(fn(string $state) => match ($state) {
                    'vacation_leave'                   => 'success',
                    'sick_leave'                       => 'danger',
                    'mandatory_forced_leave'           => 'warning',
                    'maternity_leave', 'paternity_leave' => 'info',
                    'special_privilege_leave', 'study_leave' => 'primary',
                    'wellness_leave'                   => 'success',  // NEW
                    default                            => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'vacation_leave'                   => 'heroicon-o-sun',
                    'sick_leave'                       => 'heroicon-o-heart',
                    'maternity_leave', 'paternity_leave' => 'heroicon-o-user-group',
                    'study_leave'                      => 'heroicon-o-academic-cap',
                    'wellness_leave'                   => 'heroicon-o-sparkles',  // NEW
                    default                            => 'heroicon-o-document-text',
                }),

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

            Tables\Columns\TextColumn::make('number_of_working_days')
                ->label('Duration')
                ->badge()
                ->color('info')
                ->icon('heroicon-o-clock')
                ->formatStateUsing(
                    fn($state) => $state . ' working ' . ($state == 1 ? 'day' : 'days')
                ),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state) => match ($state) {
                    'pending'      => 'warning',
                    'approved'     => 'success',
                    'disapproved'  => 'danger',
                    default        => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'pending'     => 'heroicon-o-clock',
                    'approved'    => 'heroicon-o-check-circle',
                    'disapproved' => 'heroicon-o-x-circle',
                    default       => null,
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),

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

            Tables\Columns\TextColumn::make('authorized_officer')
                ->label('Processed By')
                ->color('gray')
                ->placeholder('Awaiting Review')
                ->icon('heroicon-o-shield-check')
                ->iconColor('gray')
                ->limit(22)
                ->tooltip(fn($record) => $record->authorized_officer),

            Tables\Columns\TextColumn::make('remarks')
                ->label('Remarks')
                ->limit(40)
                ->wrap()
                ->placeholder('—')
                ->color(fn($record) => filled($record?->remarks) ? 'warning' : 'gray')
                ->icon(fn($record) => filled($record?->remarks) ? 'heroicon-o-chat-bubble-left-ellipsis' : null)
                ->iconColor('warning')
                ->tooltip(fn($record) => $record?->remarks)
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make('commutation')
                ->label('Commutation')
                ->boolean()
                ->trueIcon('heroicon-o-currency-dollar')
                ->falseIcon('heroicon-o-x-mark')
                ->trueColor('success')
                ->falseColor('gray')
                ->getStateUsing(fn($record) => $record->commutation === 'requested')
                ->toggleable(isToggledHiddenByDefault: true),

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
    //  [CHANGE] Added 'wellness_leave' to the Leave Type filter options
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        $filters = [];

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

        $filters[] = Tables\Filters\Filter::make('status_and_type')
            ->label('Status & Leave Type')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->placeholder('All statuses')
                    ->options([
                        'pending'     => '🕐  Pending',
                        'approved'    => '✅  Approved',
                        'disapproved' => '❌  Disapproved',
                    ]),

                // [CHANGE] Wellness Leave added to filter dropdown
                Forms\Components\Select::make('type_of_leave')
                    ->label('Leave Type')
                    ->native(false)
                    ->placeholder('All types')
                    ->options([
                        'vacation_leave'                   => 'Vacation Leave',
                        'sick_leave'                       => 'Sick Leave',
                        'maternity_leave'                  => 'Maternity Leave',
                        'paternity_leave'                  => 'Paternity Leave',
                        'special_privilege_leave'          => 'Special Privilege Leave',
                        'mandatory_forced_leave'           => 'Mandatory/Forced Leave',
                        'study_leave'                      => 'Study Leave',
                        'solo_parent_leave'                => 'Solo Parent Leave',
                        'wellness_leave'                   => 'Wellness Leave',   // NEW
                        'others'                           => 'Others',
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
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))->removeField('status');
                }
                if ($data['type_of_leave'] ?? null) {
                    $label = str_replace('_', ' ', ucwords($data['type_of_leave'], '_'));
                    $indicators[] = Tables\Filters\Indicator::make('Type: ' . $label)->removeField('type_of_leave');
                }
                return $indicators;
            });

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
                        'this_week'  => '📅  This Week',
                        'this_year'  => '📅  This Year',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                            'this_week'  => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                            'this_year'  => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                            default      => [null, null],
                        };
                        $set('from', $from);
                        $set('to', $to);
                    }),

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
                $presetLabels = [
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_week'  => 'This Week',
                    'this_year'  => 'This Year',
                ];
                $indicators = [];
                $preset = $data['preset'] ?? null;
                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Period: ' . $presetLabels[$preset])->removeField('preset');
                    } else {
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From: ' . Carbon::parse($data['from'])->format('M d, Y'))->removeField('from');
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('To: ' . Carbon::parse($data['to'])->format('M d, Y'))->removeField('to');
                        }
                    }
                }
                return $indicators;
            });

        return $filters;
    }

    // =========================================================================
    //  CONTEXTUAL ACTIONS
    // =========================================================================

    protected static function getContextualActions(bool $isAdmin): array
    {
        return [
            Tables\Actions\ActionGroup::make([

                Tables\Actions\ViewAction::make()
                    ->label('View Application')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(fn() => $isAdmin),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => $isAdmin),

                Tables\Actions\ViewAction::make('employeeView')
                    ->label('View Application')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(
                        fn($record) => !$isAdmin && $record->employee_id === Auth::id()
                    ),

                Tables\Actions\EditAction::make()
                    ->label('Edit Application')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        !$isAdmin &&
                        $record->employee_id === Auth::id() &&
                        $record->status === 'pending'
                    ),

                Tables\Actions\Action::make('employeePrint')
                    ->label('Print Leave Form')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        !$isAdmin &&
                        $record->employee_id === Auth::id() &&
                        $record->status === 'approved'
                    )
                    ->url(fn($record) => route('leave_application.print', $record))
                    ->openUrlInNewTab(),

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
            if ($result->isWeekday())
                $added++;
        }
        return $result;
    }

    protected static function calculateWorkingDays($from, $to, Forms\Set $set): void
    {
        if (!$from || !$to)
            return;
        try {
            $fromDate = Carbon::parse($from);
            $toDate   = Carbon::parse($to);
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
        if (auth()->user()?->role !== User::ROLE_ADMIN)
            return null;
        $count = LeaveApplication::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return auth()->user()?->role === User::ROLE_ADMIN &&
            LeaveApplication::where('status', 'pending')->count() > 0
            ? 'warning'
            : null;
    }

    // =========================================================================
    //  PAGES
    // =========================================================================

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit'   => Pages\EditLeaveApplication::route('/{record}/edit'),
            'view'   => Pages\ViewLeaveApplication::route('/{record}'),
        ];
    }
}
