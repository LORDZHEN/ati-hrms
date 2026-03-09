<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalDataSheetResource\Pages;
use App\Models\PersonalDataSheet;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\PDSStatusUpdated;
use App\Notifications\PDSRemarksAdded;
use App\Notifications\PDSSubmittedNotification;

use Filament\Forms\Components\{
    View as ViewComponent,
    Section,
    Grid,
    TextInput,
    DatePicker,
    Select,
    Radio,
    Checkbox,
    Repeater,
    Textarea
};

class PersonalDataSheetResource extends Resource
{
    protected static ?string $model = PersonalDataSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Personal Data Sheet';
    protected static ?string $pluralModelLabel = 'Personal Data Sheet';
    protected static ?string $slug = 'pds';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Documents';

    // =========================================================================
    //  ACCESS CONTROL — Hide entirely from Job Order users
    // =========================================================================

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    // =========================================================================
    //  AUTHORIZATION
    // =========================================================================

    public static function canCreate(): bool
    {
        // Only regular employees can create a PDS, and only if they don't have one yet.
        if (Auth::user()->role !== User::ROLE_REGULAR) {
            return false;
        }

        return !PersonalDataSheet::where('user_id', Auth::id())->exists();
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        // Admins CANNOT edit — they use table actions (approve/disapprove/remarks).
        if ($user->role === 'admin') {
            return false;
        }

        // Regular employees can access their own PDS edit page.
        return $record->user_id === $user->id;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return true;
        }
        return $record->user_id === $user->id;
    }

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        $isLocked = fn($record) =>
            Auth::user()->role === User::ROLE_REGULAR &&
            $record?->status === 'approved';

        return $form->schema([

            Textarea::make('remarks')
                ->label('Remarks from Admin')
                ->rows(4)
                ->columnSpanFull()
                ->disabled()
                ->hidden(fn($record) => blank($record?->remarks)),

            ViewComponent::make('filament.resources.personal-data-sheet.pds-form')
                ->columnSpanFull(),

            Section::make('Form Fields (Do Not Edit Directly - Use Form Above)')
                ->description('⚠️ These fields are automatically filled when you use the CSC format form above.')
                ->schema([
                    TextInput::make('surname')->required()->disabled($isLocked),
                    TextInput::make('first_name')->required()->disabled($isLocked),
                    TextInput::make('middle_name')->disabled($isLocked),
                    TextInput::make('name_extension')->disabled($isLocked),
                    DatePicker::make('date_of_birth')->required()->disabled($isLocked),
                    TextInput::make('place_of_birth')->disabled($isLocked),
                    Select::make('sex')->options(['Male' => 'Male', 'Female' => 'Female'])->disabled($isLocked),
                    Select::make('civil_status')->options([
                        'Single' => 'Single',
                        'Married' => 'Married',
                        'Widowed' => 'Widowed',
                        'Separated' => 'Separated',
                    ])->disabled($isLocked),
                    TextInput::make('height')->numeric()->step(0.01)->disabled($isLocked),
                    TextInput::make('weight')->numeric()->step(0.1)->disabled($isLocked),
                    TextInput::make('blood_type')->disabled($isLocked),
                    TextInput::make('gsis_id_no')->disabled($isLocked),
                    TextInput::make('pag_ibig_id_no')->disabled($isLocked),
                    TextInput::make('philhealth_no')->disabled($isLocked),
                    TextInput::make('sss_no')->disabled($isLocked),
                    TextInput::make('tin_no')->disabled($isLocked),
                    TextInput::make('agency_employee_no')->disabled($isLocked),
                    Checkbox::make('filipino')->disabled($isLocked),
                    Checkbox::make('dual_citizenship')->reactive()->disabled($isLocked),
                    TextInput::make('dual_citizenship_country')
                        ->visible(fn($get) => $get('dual_citizenship'))
                        ->disabled($isLocked),

                    TextInput::make('res_house_block_lot_no')->disabled($isLocked),
                    TextInput::make('res_street')->disabled($isLocked),
                    TextInput::make('res_subdivision_village')->disabled($isLocked),
                    TextInput::make('res_barangay')->disabled($isLocked),
                    TextInput::make('res_city_municipality')->disabled($isLocked),
                    TextInput::make('res_province')->disabled($isLocked),
                    TextInput::make('res_zip_code')->disabled($isLocked),

                    Checkbox::make('same_as_residential')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if ($state) {
                                $set('perm_house_block_lot_no', $get('res_house_block_lot_no'));
                                $set('perm_street', $get('res_street'));
                                $set('perm_subdivision_village', $get('res_subdivision_village'));
                                $set('perm_barangay', $get('res_barangay'));
                                $set('perm_city_municipality', $get('res_city_municipality'));
                                $set('perm_province', $get('res_province'));
                                $set('perm_zip_code', $get('res_zip_code'));
                            }
                        })
                        ->disabled($isLocked),

                    TextInput::make('perm_house_block_lot_no')->disabled($isLocked),
                    TextInput::make('perm_street')->disabled($isLocked),
                    TextInput::make('perm_subdivision_village')->disabled($isLocked),
                    TextInput::make('perm_barangay')->disabled($isLocked),
                    TextInput::make('perm_city_municipality')->disabled($isLocked),
                    TextInput::make('perm_province')->disabled($isLocked),
                    TextInput::make('perm_zip_code')->disabled($isLocked),
                    TextInput::make('telephone_no')->disabled($isLocked),
                    TextInput::make('mobile')->disabled($isLocked),
                    TextInput::make('email')->email()->disabled($isLocked),

                    TextInput::make('spouse_surname')->disabled($isLocked),
                    TextInput::make('spouse_first_name')->disabled($isLocked),
                    TextInput::make('spouse_middle_name')->disabled($isLocked),
                    TextInput::make('spouse_name_extension')->disabled($isLocked),
                    TextInput::make('spouse_occupation')->disabled($isLocked),
                    TextInput::make('spouse_employer_business_name')->disabled($isLocked),
                    TextInput::make('spouse_business_address')->disabled($isLocked),
                    TextInput::make('spouse_telephone_no')->disabled($isLocked),

                    TextInput::make('father_surname')->disabled($isLocked),
                    TextInput::make('father_first_name')->disabled($isLocked),
                    TextInput::make('father_middle_name')->disabled($isLocked),
                    TextInput::make('father_name_extension')->disabled($isLocked),

                    TextInput::make('mother_surname')->disabled($isLocked),
                    TextInput::make('mother_first_name')->disabled($isLocked),
                    TextInput::make('mother_middle_name')->disabled($isLocked),

                    Repeater::make('children')
                        ->schema([
                            TextInput::make('name')->required(),
                            DatePicker::make('birthdate')->required(),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('education')
                        ->schema([
                            Select::make('level')
                                ->options([
                                    'ELEMENTARY' => 'Elementary',
                                    'SECONDARY' => 'Secondary',
                                    'VOCATIONAL/TRADE COURSE' => 'Vocational/Trade Course',
                                    'COLLEGE' => 'College',
                                    'GRADUATE STUDIES' => 'Graduate Studies',
                                ])
                                ->required(),
                            TextInput::make('school_name')->required(),
                            TextInput::make('degree')->required(),
                            TextInput::make('from_year')->maxLength(4),
                            TextInput::make('to_year')->maxLength(4),
                            TextInput::make('honors'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('civil_service_eligibility')
                        ->schema([
                            TextInput::make('career_service'),
                            TextInput::make('rating')->numeric(),
                            DatePicker::make('exam_date'),
                            TextInput::make('place'),
                            TextInput::make('license_no'),
                            DatePicker::make('validity'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('work_experience')
                        ->schema([
                            DatePicker::make('from')->required(),
                            DatePicker::make('to')->required(),
                            TextInput::make('position')->required(),
                            TextInput::make('agency')->required(),
                            TextInput::make('salary')->numeric(),
                            TextInput::make('salary_grade'),
                            TextInput::make('status'),
                            Radio::make('is_government')->boolean()->inline(),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('voluntary_work')
                        ->schema([
                            TextInput::make('organization_name'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('position'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('learning_development')
                        ->schema([
                            TextInput::make('training_title'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('type'),
                            TextInput::make('conducted_by'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('special_skills')
                        ->simple(TextInput::make('skill'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('non_academic_distinctions')
                        ->simple(TextInput::make('distinction'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('membership_association')
                        ->simple(TextInput::make('organization'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Radio::make('related_third_degree')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('related_third_degree_details')
                        ->visible(fn($get) => $get('related_third_degree') === true)
                        ->disabled($isLocked),

                    Radio::make('related_fourth_degree')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('related_fourth_degree_details')
                        ->visible(fn($get) => $get('related_fourth_degree') === true)
                        ->disabled($isLocked),

                    Radio::make('has_admin_case')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('admin_case_details')
                        ->visible(fn($get) => $get('has_admin_case') === true)
                        ->disabled($isLocked),

                    Radio::make('has_criminal_case')->boolean()->inline()->reactive()->disabled($isLocked),
                    DatePicker::make('criminal_case_date_filed')
                        ->visible(fn($get) => $get('has_criminal_case') === true)
                        ->disabled($isLocked),
                    TextInput::make('criminal_case_status')
                        ->visible(fn($get) => $get('has_criminal_case') === true)
                        ->disabled($isLocked),

                    Radio::make('has_conviction')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('conviction_details')
                        ->visible(fn($get) => $get('has_conviction') === true)
                        ->disabled($isLocked),

                    Radio::make('has_been_separated')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('separation_details')
                        ->visible(fn($get) => $get('has_been_separated') === true)
                        ->disabled($isLocked),

                    Radio::make('has_election_candidacy')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('election_candidacy_details')
                        ->visible(fn($get) => $get('has_election_candidacy') === true)
                        ->disabled($isLocked),

                    Checkbox::make('is_indigenous')->reactive()->disabled($isLocked),
                    TextInput::make('indigenous_details')
                        ->visible(fn($get) => $get('is_indigenous'))
                        ->disabled($isLocked),

                    Checkbox::make('has_disability')->reactive()->disabled($isLocked),
                    TextInput::make('disability_details')
                        ->visible(fn($get) => $get('has_disability'))
                        ->disabled($isLocked),

                    Checkbox::make('is_solo_parent')->reactive()->disabled($isLocked),
                    TextInput::make('solo_parent_details')
                        ->visible(fn($get) => $get('is_solo_parent'))
                        ->disabled($isLocked),

                    Repeater::make('references')
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('address')->required(),
                            TextInput::make('tel'),
                        ])
                        ->defaultItems(3)
                        ->minItems(3)
                        ->maxItems(3)
                        ->addable(false)
                        ->deletable(false)
                        ->disabled($isLocked),

                    TextInput::make('gov_id_type')->disabled($isLocked),
                    TextInput::make('gov_id_no')->disabled($isLocked),
                    TextInput::make('gov_id_issued')->disabled($isLocked),
                    DatePicker::make('date_accomplished')->default(now())->required()->disabled($isLocked),
                ])
                ->collapsed()
                ->collapsible()
                ->columnSpanFull(),

        ])->columns(1);
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->role === 'admin';

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
                        ->modalHeading('Approve Multiple PDS')
                        ->modalDescription('Are you sure you want to approve all selected Personal Data Sheets?')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'approved') {
                                    $record->update(['status' => 'approved']);
                                    $record->user?->notify(new PDSStatusUpdated($record));
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->success()
                                ->title('Bulk Approval Complete')
                                ->body("{$count} PDS record(s) have been approved.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulkDisapprove')
                        ->label('Disapprove Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn() => $isAdmin)
                        ->form([
                            Textarea::make('remarks')
                                ->label('Reason for Bulk Disapproval')
                                ->required()
                                ->rows(4)
                                ->placeholder('This reason will be applied to all selected records...'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'submitted') {
                                    $record->update([
                                        'status' => 'disapproved',
                                        'remarks' => $data['remarks'],
                                    ]);
                                    $record->user?->notify(new PDSStatusUpdated($record));
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->danger()
                                ->title('Bulk Disapproval Complete')
                                ->body("{$count} PDS record(s) have been disapproved.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected PDS')
                        ->modalDescription('Are you sure you want to delete these records? This action cannot be undone.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No Personal Data Sheets yet')
            ->emptyStateDescription('Submit your Personal Data Sheet to get started.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create PDS')
                    ->icon('heroicon-o-plus')
                    ->visible(
                        fn() =>
                        Auth::user()->role === User::ROLE_REGULAR &&
                        !PersonalDataSheet::where('user_id', Auth::id())->exists()
                    ),
            ]);
    }

    // =========================================================================
    //  TABLE COLUMNS
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\TextColumn::make('full_name')
                ->label('Employee')
                ->getStateUsing(fn($record) => self::getFullName($record))
                ->searchable(['surname', 'first_name', 'middle_name'])
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary'),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->icon('heroicon-o-envelope')
                ->iconColor('primary')
                ->copyable()
                ->copyMessage('Email copied!')
                ->placeholder('No email')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('mobile')
                ->label('Mobile')
                ->icon('heroicon-o-phone')
                ->iconColor('success')
                ->copyable()
                ->copyMessage('Mobile copied!')
                ->placeholder('No mobile')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('place_of_birth')
                ->label('Place of Birth')
                ->icon('heroicon-o-map-pin')
                ->iconColor('gray')
                ->color('gray')
                ->limit(30)
                ->placeholder('Not specified')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('completion_rate')
                ->label('Completion')
                ->getStateUsing(fn($record) => self::calculateCompletionRate($record))
                ->formatStateUsing(fn($state) => $state . '%')
                ->badge()
                ->color(fn($state) => match (true) {
                    $state >= 90 => 'success',
                    $state >= 70 => 'warning',
                    default => 'danger',
                })
                ->icon(fn($state) => match (true) {
                    $state >= 90 => 'heroicon-o-check-circle',
                    $state >= 70 => 'heroicon-o-exclamation-circle',
                    default => 'heroicon-o-x-circle',
                }),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state) => match ($state) {
                    'submitted' => 'warning',
                    'approved' => 'success',
                    'disapproved' => 'danger',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'submitted' => 'heroicon-m-clock',
                    'approved' => 'heroicon-m-check-circle',
                    'disapproved' => 'heroicon-m-x-circle',
                    default => null,
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Last Submitted')
                ->since()
                ->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            Tables\Columns\TextColumn::make('remarks')
                ->label('Remarks')
                ->limit(50)
                ->wrap()
                ->placeholder('—')
                ->color(fn($record) => filled($record?->remarks) ? 'warning' : 'gray')
                ->icon(fn($record) => filled($record?->remarks) ? 'heroicon-o-chat-bubble-left-ellipsis' : null)
                ->iconColor('warning')
                ->tooltip(fn($record) => $record?->remarks)
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    // =========================================================================
    //  FILTERS
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        $filters = [];

        if ($isAdmin) {
            $filters[] = Tables\Filters\Filter::make('employee_and_remarks')
                ->label('Employee & Remarks')
                ->columnSpan(1)
                ->form([
                    \Filament\Forms\Components\Select::make('employee_name')
                        ->label('Employee')
                        ->options(
                            fn() => \App\Models\User::where('role', 'employee')
                                ->orderBy('name')
                                ->pluck('name', 'name')
                                ->toArray()
                        )
                        ->searchable()
                        ->native(false)
                        ->placeholder('All employees'),

                    \Filament\Forms\Components\Select::make('has_remarks')
                        ->label('Admin Remarks')
                        ->native(false)
                        ->placeholder('All records')
                        ->options([
                            'with' => 'With remarks',
                            'without' => 'Without remarks',
                        ]),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when(
                            $data['employee_name'] ?? null,
                            fn($q, $v) =>
                            $q->where(
                                fn($q2) =>
                                $q2->whereRaw("CONCAT(surname, ' ', first_name) LIKE ?", ["%{$v}%"])
                            )
                        )
                        ->when(
                            ($data['has_remarks'] ?? null) === 'with',
                            fn($q) =>
                            $q->whereNotNull('remarks')->where('remarks', '!=', '')
                        )
                        ->when(
                            ($data['has_remarks'] ?? null) === 'without',
                            fn($q) =>
                            $q->where(fn($q2) => $q2->whereNull('remarks')->orWhere('remarks', ''))
                        )
                )
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['employee_name'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make('Employee: ' . $data['employee_name'])
                            ->removeField('employee_name');
                    }
                    if ($data['has_remarks'] ?? null) {
                        $label = $data['has_remarks'] === 'with' ? 'With remarks' : 'Without remarks';
                        $indicators[] = Tables\Filters\Indicator::make('Remarks: ' . $label)
                            ->removeField('has_remarks');
                    }
                    return $indicators;
                });
        }

        $filters[] = Tables\Filters\Filter::make('status_and_completion')
            ->label('Status & Completion')
            ->columnSpan(1)
            ->form([
                \Filament\Forms\Components\Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->placeholder('All statuses')
                    ->options([
                        'submitted' => '🕐  Submitted',
                        'approved' => '✅  Approved',
                        'disapproved' => '❌  Disapproved',
                    ]),

                \Filament\Forms\Components\Select::make('completion_level')
                    ->label('Completion Level')
                    ->native(false)
                    ->placeholder('All levels')
                    ->options([
                        'high' => '✅  High (90%+)',
                        'moderate' => '⚠️   Moderate (70–89%)',
                        'low' => '❌  Low (<70%)',
                    ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            )
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['status'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))
                        ->removeField('status');
                }
                if ($data['completion_level'] ?? null) {
                    $labels = ['high' => 'High (90%+)', 'moderate' => 'Moderate (70–89%)', 'low' => 'Low (<70%)'];
                    $indicators[] = Tables\Filters\Indicator::make('Completion: ' . ($labels[$data['completion_level']] ?? ''))
                        ->removeField('completion_level');
                }
                return $indicators;
            });

        $filters[] = Tables\Filters\Filter::make('submitted_period')
            ->label('Submitted Period')
            ->columnSpan(1)
            ->form([
                \Filament\Forms\Components\Select::make('preset')
                    ->label('Quick Select')
                    ->placeholder('— pick a period —')
                    ->native(false)
                    ->options([
                        'this_week' => '📅  This Week',
                        'this_month' => '📅  This Month',
                        'last_month' => '📅  Last Month',
                        'this_year' => '📅  This Year',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                            default => [null, null],
                        };
                        $set('from', $from);
                        $set('to', $to);
                    }),

                \Filament\Forms\Components\Grid::make(2)->schema([
                    \Filament\Forms\Components\DatePicker::make('from')
                        ->label('From')
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->maxDate(fn(callable $get) => $get('to') ?? now()),
                    \Filament\Forms\Components\DatePicker::make('to')
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
            ->indicateUsing(function (array $data): array {
                $presetLabels = [
                    'this_week' => 'This Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_year' => 'This Year',
                ];
                $indicators = [];
                $preset = $data['preset'] ?? null;

                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Submitted: ' . $presetLabels[$preset])
                            ->removeField('preset');
                    } else {
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From: ' . \Carbon\Carbon::parse($data['from'])->format('M d, Y'))
                                ->removeField('from');
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('To: ' . \Carbon\Carbon::parse($data['to'])->format('M d, Y'))
                                ->removeField('to');
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
                    ->label('View PDS')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(fn() => $isAdmin),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $isAdmin && $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve PDS')
                    ->modalDescription(fn($record) => 'Are you sure you want to approve the PDS for ' . self::getFullName($record) . '?')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        $record->user?->notify(new PDSStatusUpdated($record));
                        Notification::make()
                            ->success()
                            ->title('PDS Approved')
                            ->body('The Personal Data Sheet has been approved successfully.')
                            ->send();
                    }),

                Tables\Actions\Action::make('disapprove')
                    ->label('Disapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $isAdmin && $record->status === 'submitted')
                    ->form([
                        Textarea::make('remarks')
                            ->label('Reason for Disapproval')
                            ->required()
                            ->rows(4)
                            ->placeholder('Please provide a clear reason for disapproval...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'disapproved',
                            'remarks' => $data['remarks'],
                        ]);
                        $record->user?->notify(new PDSStatusUpdated($record));
                        $record->user?->notify(new PDSRemarksAdded($record));
                        Notification::make()
                            ->success()
                            ->title('PDS Disapproved')
                            ->body('The employee has been notified with your remarks.')
                            ->send();
                    }),

                Tables\Actions\Action::make('remarks')
                    ->label(fn($record) => blank($record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn() => $isAdmin)
                    ->fillForm(fn($record) => ['remarks' => $record->remarks])
                    ->form([
                        Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(5)
                            ->required()
                            ->placeholder('Add notes or feedback for the employee...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);
                        $record->user?->notify(new PDSRemarksAdded($record));
                        Notification::make()
                            ->success()
                            ->title('Remarks Updated')
                            ->body('Admin remarks have been saved and the employee has been notified.')
                            ->send();
                    }),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->url(fn($record) => route('pds.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label(
                        fn($record) => $record->status === 'approved'
                        ? 'View PDS'
                        : 'Edit / Resubmit'
                    )
                    ->icon(
                        fn($record) => $record->status === 'approved'
                        ? 'heroicon-m-eye'
                        : 'heroicon-m-pencil-square'
                    )
                    ->color(
                        fn($record) => $record->status === 'approved'
                        ? 'info'
                        : 'warning'
                    )
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === User::ROLE_REGULAR &&
                        $record->user_id === Auth::id()
                    ),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => $isAdmin),

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

    protected static function getFullName($record): string
    {
        $parts = array_filter([
            $record->surname,
            $record->first_name,
            $record->middle_name,
            $record->name_extension,
        ]);
        return implode(' ', $parts);
    }

    protected static function calculateCompletionRate($record): int
    {
        $totalFields = 0;
        $filledFields = 0;

        $basicFields = ['surname', 'first_name', 'date_of_birth', 'place_of_birth', 'sex', 'civil_status', 'height', 'weight', 'blood_type', 'mobile', 'email'];
        foreach ($basicFields as $field) {
            $totalFields++;
            if (!blank($record->$field))
                $filledFields++;
        }

        $addressFields = ['res_house_block_lot_no', 'res_street', 'res_barangay', 'res_city_municipality', 'res_province', 'res_zip_code'];
        foreach ($addressFields as $field) {
            $totalFields++;
            if (!blank($record->$field))
                $filledFields++;
        }

        $jsonFields = ['children', 'education', 'work_experience', 'references'];
        foreach ($jsonFields as $field) {
            $totalFields++;
            $value = $record->$field;
            $data = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []);
            if (is_array($data) && count($data) > 0)
                $filledFields++;
        }

        if (!blank($record->gov_id_type) && !blank($record->gov_id_no))
            $filledFields++;
        $totalFields++;

        return $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
    }

    // =========================================================================
    //  QUERY / NAVIGATION
    // =========================================================================

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (Auth::user()->role === User::ROLE_REGULAR) {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        $count = PersonalDataSheet::where('status', 'submitted')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        return PersonalDataSheet::where('status', 'submitted')->count() > 0 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalDataSheets::route('/'),
            'create' => Pages\CreatePersonalDataSheet::route('/create'),
            'edit' => Pages\EditPersonalDataSheet::route('/{record}/edit'),
            'view' => Pages\ViewPersonalDataSheet::route('/{record}'),
        ];
    }
}
