<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    /* ============================================================
       AUTHORIZATION
       ============================================================ */

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'employee';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::getLeaveTypeSection(),
            self::getLeaveDetailsSection(),
            self::getHiddenFields(),
        ]);
    }

    protected static function getLeaveTypeSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Leave Type Selection')
            ->description('Choose the type of leave you want to apply for')
            ->icon('heroicon-o-clipboard-document-list')
            ->schema([
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
            ])
            ->columns(1);
    }

    protected static function getLeaveDetailsSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Leave Details & Duration')
            ->description('Specify the duration and specific details of your leave')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    // Left Column - Main Leave Details
                    Forms\Components\Group::make([
                        Forms\Components\DatePicker::make('leave_date_from')
                            ->label('Leave Date From')
                            ->required()
                            ->live()
                            ->native(false)
                            ->minDate(function (Forms\Get $get) {
                                $leaveType = $get('type_of_leave');

                                if ($leaveType === 'vacation_leave') {
                                    return now()->addDays(5)->startOfDay();
                                }

                                if ($leaveType === 'sick_leave') {
                                    return null;
                                }

                                return now()->startOfDay();
                            })
                            ->maxDate(function (Forms\Get $get) {
                                $leaveType = $get('type_of_leave');

                                if ($leaveType === 'sick_leave') {
                                    return now()->endOfDay();
                                }

                                return null;
                            })
                            ->afterStateUpdated(
                                fn($state, Forms\Set $set, Forms\Get $get) =>
                                self::calculateWorkingDays($state, $get('leave_date_to'), $set)
                            ),

                        Forms\Components\DatePicker::make('leave_date_to')
                            ->label('Leave Date To')
                            ->required()
                            ->live()
                            ->native(false)
                            ->minDate(fn(Forms\Get $get) => $get('leave_date_from') ?: now())
                            ->maxDate(function (Forms\Get $get) {
                                $leaveType = $get('type_of_leave');

                                if ($leaveType === 'sick_leave') {
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
                            ->suffix('days')
                            ->readOnly()
                            ->helperText('Automatically calculated based on selected dates'),

                        Forms\Components\FileUpload::make('supporting_document')
                            ->label('Supporting Document')
                            ->helperText('Required for sick leave of 3 days or more')
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

                        Forms\Components\Radio::make('commutation')
                            ->label('Commutation')
                            ->options([
                                'not_requested' => 'Not Requested',
                                'requested' => 'Requested',
                            ])
                            ->default('not_requested')
                            ->inline()
                            ->required(),
                    ]),

                    // Right Column - Specific Leave Type Details
                    Forms\Components\Group::make([
                        // Vacation/Special Privilege Leave Details
                        Forms\Components\Fieldset::make('Location Details')
                            ->schema([
                                Forms\Components\Radio::make('vacation_location')
                                    ->label('Where will you spend your leave?')
                                    ->options([
                                        'within_philippines' => 'Within the Philippines',
                                        'abroad' => 'Abroad',
                                    ])
                                    ->inline()
                                    ->live()
                                    ->required(
                                        fn(Forms\Get $get) =>
                                        in_array($get('type_of_leave'), ['vacation_leave', 'special_privilege_leave'])
                                    ),

                                Forms\Components\TextInput::make('abroad_specify')
                                    ->label('Specify Country/Location Abroad')
                                    ->visible(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                                    ->required(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                                    ->maxLength(255),
                            ])
                            ->visible(
                                fn(Forms\Get $get) =>
                                in_array($get('type_of_leave'), ['vacation_leave', 'special_privilege_leave'])
                            ),

                        // Sick Leave Details
                        Forms\Components\Fieldset::make('Treatment Details')
                            ->schema([
                                Forms\Components\Radio::make('sick_leave_location')
                                    ->label('Type of Treatment')
                                    ->options([
                                        'in_hospital' => 'In Hospital (Confined)',
                                        'out_patient' => 'Out Patient',
                                    ])
                                    ->inline()
                                    ->live()
                                    ->required(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave'),

                                Forms\Components\TextInput::make('hospital_illness_specify')
                                    ->label('Specify Illness/Reason')
                                    ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                                    ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('outpatient_illness_specify')
                                    ->label('Specify Illness/Reason')
                                    ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                                    ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                                    ->maxLength(255),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave'),
                    ]),
                ]),
            ])
            ->columns(1);
    }

    protected static function getHiddenFields(): Forms\Components\Group
    {
        return Forms\Components\Group::make([
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
        ]);
    }

    protected static function calculateWorkingDays($from, $to, Forms\Set $set): void
    {
        if (!$from || !$to) {
            return;
        }

        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);

            if ($toDate->lessThan($fromDate)) {
                $set('number_of_working_days', 0);
                return;
            }

            $workingDays = $fromDate->diffInWeekdays($toDate) + 1;
            $set('number_of_working_days', $workingDays);
        } catch (\Exception $e) {
            $set('number_of_working_days', 0);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getModernLeaveTableColumns())
            ->filters(self::getEnhancedFilters())
            ->actions(self::getContextualActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->role === 'admin'),
                ]),
            ])
            ->defaultSort('date_of_filing', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                return auth()->user()->role === 'admin'
                    ? $query
                    : $query->where('employee_id', auth()->id());
            })
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No leave applications found')
            ->emptyStateDescription('Submit your first leave application to get started.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Leave Application')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => auth()->user()->role === 'employee'),
            ]);
    }

    /* ============================================================
       MODERN LEAVE TABLE COLUMNS - CARD-STYLE LAYOUT
       ============================================================ */

    protected static function getModernLeaveTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                // Left Side - Employee & Leave Type Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('employee.name')
                        ->label('Employee')
                        ->searchable()
                        ->sortable()
                        ->weight('bold')
                        ->size('md')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary'),

                    Tables\Columns\TextColumn::make('position')
                        ->label('Position')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-briefcase')
                        ->iconColor('gray')
                        ->formatStateUsing(
                            fn($record) =>
                            $record->position . ' • ' . ($record->office_department ?? 'N/A')
                        ),

                    Tables\Columns\BadgeColumn::make('type_of_leave')
                        ->label('Leave Type')
                        ->formatStateUsing(fn($state) => str_replace('_', ' ', ucwords($state, '_')))
                        ->colors([
                            'success' => 'vacation_leave',
                            'danger' => 'sick_leave',
                            'warning' => 'mandatory_forced_leave',
                            'info' => fn($state) => in_array($state, ['maternity_leave', 'paternity_leave']),
                            'primary' => fn($state) => in_array($state, ['special_privilege_leave', 'study_leave']),
                            'secondary' => 'others',
                        ])
                        ->icon(fn($state) => match ($state) {
                            'vacation_leave' => 'heroicon-o-sun',
                            'sick_leave' => 'heroicon-o-heart',
                            'maternity_leave', 'paternity_leave' => 'heroicon-o-user-group',
                            'study_leave' => 'heroicon-o-academic-cap',
                            default => 'heroicon-o-document-text',
                        }),
                ])->space(2),

                // Middle - Date Range & Duration
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('leave_dates')
                        ->label('Leave Period')
                        ->weight('medium')
                        ->icon('heroicon-o-calendar-days')
                        ->iconColor('warning')
                        ->formatStateUsing(
                            fn($record) =>
                            Carbon::parse($record->leave_date_from)->format('M d, Y') .
                            ' → ' .
                            Carbon::parse($record->leave_date_to)->format('M d, Y')
                        ),

                    Tables\Columns\TextColumn::make('number_of_working_days')
                        ->label('Duration')
                        ->size('sm')
                        ->formatStateUsing(fn($state) => $state . ' working ' . ($state == 1 ? 'day' : 'days'))
                        ->badge()
                        ->color('info')
                        ->icon('heroicon-o-clock'),

                    Tables\Columns\IconColumn::make('commutation')
                        ->label('Commutation')
                        ->boolean()
                        ->trueIcon('heroicon-o-currency-dollar')
                        ->falseIcon('heroicon-o-x-mark')
                        ->trueColor('success')
                        ->falseColor('gray')
                        ->size('sm')
                        ->getStateUsing(fn($record) => $record->commutation === 'requested'),
                ])->space(1),

                // Right Side - Status & Processing Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'warning' => 'pending',
                            'success' => 'approved',
                            'danger' => 'disapproved',
                        ])
                        ->icons([
                            'heroicon-o-clock' => 'pending',
                            'heroicon-o-check-circle' => 'approved',
                            'heroicon-o-x-circle' => 'disapproved',
                        ])
                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                        ->size('md'),

                    Tables\Columns\TextColumn::make('date_of_filing')
                        ->label('Filed')
                        ->date('M d, Y')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-paper-airplane')
                        ->iconColor('gray'),

                    Tables\Columns\TextColumn::make('authorized_officer')
                        ->label('Processed By')
                        ->size('sm')
                        ->color('gray')
                        ->default('Awaiting Review')
                        ->icon('heroicon-o-user')
                        ->iconColor('gray')
                        ->limit(20)
                        ->tooltip(fn($record) => $record->authorized_officer),
                ])->space(2)->alignment('end'),
            ])->from('md'),

            // Additional Details Panel (Collapsible)
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('leave_details')
                        ->label('Additional Details')
                        ->formatStateUsing(function ($record) {
                            $details = [];

                            if ($record->vacation_location) {
                                $location = $record->vacation_location === 'abroad'
                                    ? 'Abroad: ' . ($record->abroad_specify ?? 'Not specified')
                                    : 'Within Philippines';
                                $details[] = '📍 ' . $location;
                            }

                            if ($record->sick_leave_location) {
                                $treatment = $record->sick_leave_location === 'in_hospital'
                                    ? 'Hospital: ' . ($record->hospital_illness_specify ?? 'Not specified')
                                    : 'Outpatient: ' . ($record->outpatient_illness_specify ?? 'Not specified');
                                $details[] = '🏥 ' . $treatment;
                            }

                            if ($record->supporting_document) {
                                $details[] = '📎 Medical certificate attached';
                            }

                            return !empty($details) ? implode(' • ', $details) : 'No additional details';
                        })
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('date_approved_disapproved')
                        ->label('Processed On')
                        ->dateTime('M d, Y h:i A')
                        ->size('sm')
                        ->color('gray')
                        ->placeholder('Not yet processed')
                        ->icon('heroicon-o-check-badge')
                        ->iconColor('success'),
                ]),
            ])->collapsible(),
        ];
    }

    /* ============================================================
       ENHANCED FILTERS
       ============================================================ */

    protected static function getEnhancedFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'disapproved' => 'Disapproved',
                ])
                ->native(false)
                ->indicator('Status'),

            Tables\Filters\SelectFilter::make('type_of_leave')
                ->label('Leave Type')
                ->options([
                    'vacation_leave' => 'Vacation Leave',
                    'sick_leave' => 'Sick Leave',
                    'maternity_leave' => 'Maternity Leave',
                    'paternity_leave' => 'Paternity Leave',
                    'special_privilege_leave' => 'Special Privilege Leave',
                    'mandatory_forced_leave' => 'Mandatory/Forced Leave',
                ])
                ->native(false)
                ->indicator('Type'),

            Tables\Filters\SelectFilter::make('commutation')
                ->label('Commutation')
                ->options([
                    'requested' => 'Requested',
                    'not_requested' => 'Not Requested',
                ])
                ->native(false)
                ->indicator('Commutation'),

            Tables\Filters\Filter::make('date_range')
                ->form([
                    Forms\Components\DatePicker::make('filed_from')
                        ->label('Filed From')
                        ->native(false),
                    Forms\Components\DatePicker::make('filed_until')
                        ->label('Filed Until')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['filed_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_of_filing', '>=', $date),
                        )
                        ->when(
                            $data['filed_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_of_filing', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['filed_from'] ?? null) {
                        $indicators['from'] = 'Filed from ' . Carbon::parse($data['filed_from'])->toFormattedDateString();
                    }

                    if ($data['filed_until'] ?? null) {
                        $indicators['until'] = 'Filed until ' . Carbon::parse($data['filed_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),

            Tables\Filters\Filter::make('leave_period')
                ->form([
                    Forms\Components\DatePicker::make('leave_from')
                        ->label('Leave Period From')
                        ->native(false),
                    Forms\Components\DatePicker::make('leave_until')
                        ->label('Leave Period Until')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['leave_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('leave_date_from', '>=', $date),
                        )
                        ->when(
                            $data['leave_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('leave_date_to', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['leave_from'] ?? null) {
                        $indicators['leave_from'] = 'Leave from ' . Carbon::parse($data['leave_from'])->toFormattedDateString();
                    }

                    if ($data['leave_until'] ?? null) {
                        $indicators['leave_until'] = 'Leave until ' . Carbon::parse($data['leave_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),
        ];
    }

    /* ============================================================
       CONTEXTUAL ACTIONS
       ============================================================ */

    protected static function getContextualActions(): array
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
                        auth()->user()->role === 'employee' &&
                        $record->status === 'pending'
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print')
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
                        auth()->user()->role === 'employee' &&
                        $record->status === 'pending'
                    ),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

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
