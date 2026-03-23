<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\WorkflowHelper;
use App\Filament\Resources\PersonalDataSheetResource\Pages;
use App\Models\PersonalDataSheet;
use App\Models\User;
use App\Notifications\PDSStatusUpdated;
use App\Notifications\PDSRemarksAdded;
use App\Notifications\PDSSubmittedNotification;
use App\Services\FilingSeasonService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

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
    use WorkflowHelper;

    protected static ?string $model = PersonalDataSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Personal Data Sheet';
    protected static ?string $pluralModelLabel = 'Personal Data Sheet';
    protected static ?string $slug = 'pds';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Documents';

    // =========================================================================
    //  ACCESS CONTROL
    // =========================================================================

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canCreate(): bool
    {
        if (Auth::user()->role !== User::ROLE_REGULAR) {
            return false;
        }

        return !PersonalDataSheet::where('user_id', Auth::id())->exists();
    }

    /**
     * Admins never use the edit page — they act via view-page header actions.
     * Employees may only edit when the workflow gate passes.
     */
    public static function canEdit($record): bool
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return false;
        }

        if ($record->user_id !== $user->id) {
            return false;
        }

        return static::canEmployeeEdit($record);
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
        $isLocked = static::formDisabledClosure();

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
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('education')
                        ->schema([
                            Select::make('level')->options([
                                'ELEMENTARY' => 'Elementary',
                                'SECONDARY' => 'Secondary',
                                'VOCATIONAL/TRADE COURSE' => 'Vocational/Trade Course',
                                'COLLEGE' => 'College',
                                'GRADUATE STUDIES' => 'Graduate Studies',
                            ])->required(),
                            TextInput::make('school_name')->required(),
                            TextInput::make('degree')->required(),
                            TextInput::make('from_year')->maxLength(4),
                            TextInput::make('to_year')->maxLength(4),
                            TextInput::make('honors'),
                        ])
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('civil_service_eligibility')
                        ->schema([
                            TextInput::make('career_service'),
                            TextInput::make('rating')->numeric(),
                            DatePicker::make('exam_date'),
                            TextInput::make('place'),
                            TextInput::make('license_no'),
                            DatePicker::make('validity'),
                        ])
                        ->defaultItems(0)->disabled($isLocked),

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
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('voluntary_work')
                        ->schema([
                            TextInput::make('organization_name'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('position'),
                        ])
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('learning_development')
                        ->schema([
                            TextInput::make('training_title'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('type'),
                            TextInput::make('conducted_by'),
                        ])
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('special_skills')
                        ->simple(TextInput::make('skill'))
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('non_academic_distinctions')
                        ->simple(TextInput::make('distinction'))
                        ->defaultItems(0)->disabled($isLocked),

                    Repeater::make('membership_association')
                        ->simple(TextInput::make('organization'))
                        ->defaultItems(0)->disabled($isLocked),

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
                        ->defaultItems(3)->minItems(3)->maxItems(3)
                        ->addable(false)->deletable(false)
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

                    // ── Bulk Unlock Editing ───────────────────────────────────
                    Tables\Actions\BulkAction::make('bulkUnlock')
                        ->label('Unlock Editing')
                        ->icon('heroicon-o-lock-open')
                        ->color('info')
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Unlock Editing for Selected Records')
                        ->modalDescription(
                            app(FilingSeasonService::class)->isEnabled()
                            ? 'Allow employees to edit their selected approved PDS records. Filing season is currently OPEN.'
                            : '⚠️ Filing season is currently CLOSED. Employees will still not be able to edit until filing season is enabled.'
                        )
                        ->modalSubmitActionLabel('Yes, Unlock Selected')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'approved' && !$record->editing_unlocked) {
                                    $record->update(['editing_unlocked' => true]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title('Editing Unlocked')
                                ->body("{$count} approved PDS record(s) have been unlocked for editing.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // ── Bulk Lock Editing ─────────────────────────────────────
                    Tables\Actions\BulkAction::make('bulkLock')
                        ->label('Lock Editing')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Lock Editing for Selected Records')
                        ->modalDescription('Prevent employees from editing their selected PDS records. This will re-lock any currently unlocked approved records.')
                        ->modalSubmitActionLabel('Yes, Lock Selected')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'approved' && $record->editing_unlocked) {
                                    $record->update(['editing_unlocked' => false]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title('Editing Locked')
                                ->body("{$count} PDS record(s) have been locked.")
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // ── Bulk Delete ───────────────────────────────────────────
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation(),

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
            // ── 1. Employee (mirrors SALN: bold, primary icon, searchable) ─────
            Tables\Columns\TextColumn::make('full_name')
                ->label('Employee')
                ->getStateUsing(fn($record) => self::getFullName($record))
                ->searchable(['surname', 'first_name', 'middle_name'])
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            // ── 2. Status (mirrors SALN: badge with icons per state) ──────────
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->formatStateUsing(fn(string $state): string => ucfirst($state))
                ->color(fn(string $state) => match ($state) {
                    'approved' => 'success',
                    'disapproved' => 'danger',
                    'submitted' => 'warning',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'approved' => 'heroicon-o-check-circle',
                    'disapproved' => 'heroicon-o-x-circle',
                    'submitted' => 'heroicon-o-clock',
                    default => null,
                }),

            // ── 3. Edit Lock (NEW — mirrors SALN exactly) ─────────────────────
            //    • Only shown to admins (visible($isAdmin))
            //    • Only renders an icon when status === 'approved'; null otherwise
            //      so non-approved rows show a blank cell, not a misleading icon
            //    • trueIcon/falseIcon + trueColor/falseColor replicate SALN style
            //    • tooltip gives quick context on hover
            Tables\Columns\IconColumn::make('editing_unlocked')
                ->label('Edit Lock')
                ->boolean()
                ->trueIcon('heroicon-o-lock-open')
                ->falseIcon('heroicon-o-lock-closed')
                ->trueColor('success')
                ->falseColor('danger')
                ->getStateUsing(
                    fn($record) => $record?->status === 'approved'
                    ? $record->editing_unlocked
                    : null                          // null → blank cell for non-approved rows
                )
                ->tooltip(fn($record) => match (true) {
                    $record?->status !== 'approved' => null,
                    $record->editing_unlocked => 'Editing Unlocked',
                    default => 'Editing Locked',
                })
                ->visible($isAdmin),

            // ── 4. Completion (PDS-specific, kept with SALN badge conventions) ─
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

            // ── 5. Last Submitted (mirrors SALN's "Filed": since + tooltip) ────
            Tables\Columns\TextColumn::make('created_at')
                ->label('Last Submitted')
                ->since()
                ->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            // ── 6. Email (hidden by default, unchanged) ───────────────────────
            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->icon('heroicon-o-envelope')
                ->iconColor('primary')
                ->copyable()
                ->copyMessage('Email copied!')
                ->placeholder('No email')
                ->toggleable(isToggledHiddenByDefault: true),

            // ── 7. Mobile (hidden by default, unchanged) ──────────────────────
            Tables\Columns\TextColumn::make('mobile')
                ->label('Mobile')
                ->icon('heroicon-o-phone')
                ->iconColor('success')
                ->copyable()
                ->copyMessage('Mobile copied!')
                ->placeholder('No mobile')
                ->toggleable(isToggledHiddenByDefault: true),

            // ── 8. Remarks (mirrors SALN: warning color + chat icon when set) ──
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
                        ->searchable()->native(false)->placeholder('All employees'),

                    \Filament\Forms\Components\Select::make('has_remarks')
                        ->label('Admin Remarks')
                        ->native(false)->placeholder('All records')
                        ->options(['with' => 'With remarks', 'without' => 'Without remarks']),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when(
                            $data['employee_name'] ?? null,
                            fn($q, $v) =>
                            $q->where(fn($q2) => $q2->whereRaw("CONCAT(surname, ' ', first_name) LIKE ?", ["%{$v}%"]))
                        )
                        ->when(
                            ($data['has_remarks'] ?? null) === 'with',
                            fn($q) => $q->whereNotNull('remarks')->where('remarks', '!=', '')
                        )
                        ->when(
                            ($data['has_remarks'] ?? null) === 'without',
                            fn($q) => $q->where(fn($q2) => $q2->whereNull('remarks')->orWhere('remarks', ''))
                        )
                )
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['employee_name'] ?? null)
                        $indicators[] = Tables\Filters\Indicator::make('Employee: ' . $data['employee_name'])->removeField('employee_name');
                    if ($data['has_remarks'] ?? null) {
                        $label = $data['has_remarks'] === 'with' ? 'With remarks' : 'Without remarks';
                        $indicators[] = Tables\Filters\Indicator::make('Remarks: ' . $label)->removeField('has_remarks');
                    }
                    return $indicators;
                });
        }

        $filters[] = Tables\Filters\Filter::make('status_and_completion')
            ->label('Status & Completion')
            ->columnSpan(1)
            ->form([
                \Filament\Forms\Components\Select::make('status')
                    ->label('Status')->native(false)->placeholder('All statuses')
                    ->options([
                        'submitted' => '🕐  Submitted',
                        'approved' => '✅  Approved',
                        'disapproved' => '❌  Disapproved',
                    ]),
                \Filament\Forms\Components\Select::make('completion_level')
                    ->label('Completion Level')->native(false)->placeholder('All levels')
                    ->options([
                        'high' => '✅  High (90%+)',
                        'moderate' => '⚠️   Moderate (70–89%)',
                        'low' => '❌  Low (<70%)',
                    ]),
            ])
            ->query(fn(Builder $query, array $data) => $query->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v)))
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['status'] ?? null)
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))->removeField('status');
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

                // ── ADMIN: View ───────────────────────────────────────────────
                Tables\Actions\ViewAction::make()
                    ->label('View PDS')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(fn() => $isAdmin),

                // ── ADMIN: Unlock Editing ─────────────────────────────────────
                Tables\Actions\Action::make('quickUnlock')
                    ->label('Unlock Editing')
                    ->icon('heroicon-o-lock-open')
                    ->color('info')
                    ->visible(fn($record) => $isAdmin && $record->status === 'approved' && !$record->editing_unlocked)
                    ->requiresConfirmation()
                    ->modalDescription(
                        app(FilingSeasonService::class)->isEnabled()
                        ? 'Allow the employee to edit this PDS. Filing season is OPEN.'
                        : '⚠️ Filing season is CLOSED. Employee cannot edit until it is enabled.'
                    )
                    ->action(function ($record) {
                        $record->update(['editing_unlocked' => true]);
                        Notification::make()->title('Editing Unlocked')->success()->send();
                    }),

                // ── ADMIN: Lock Editing ───────────────────────────────────────
                Tables\Actions\Action::make('quickLock')
                    ->label('Lock Editing')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn($record) => $isAdmin && $record->status === 'approved' && $record->editing_unlocked)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['editing_unlocked' => false]);
                        Notification::make()->title('Record Locked')->warning()->send();
                    }),

                // ── ADMIN: Delete ─────────────────────────────────────────────
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => $isAdmin),

                // ── EMPLOYEE: View ────────────────────────────────────────────
                Tables\Actions\ViewAction::make('employeeView')
                    ->label('View PDS')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(
                        fn($record) =>
                        !$isAdmin && $record->user_id === Auth::id()
                    ),

                // ── EMPLOYEE: Edit — only when workflow gate passes ────────────
                Tables\Actions\EditAction::make()
                    ->label('Edit PDS')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        !$isAdmin &&
                        $record->user_id === Auth::id() &&
                        static::canEmployeeEdit($record)
                    ),

                // ── EMPLOYEE: Print (approved only) ───────────────────────────
                Tables\Actions\Action::make('employeePrint')
                    ->label('Print PDS')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        !$isAdmin &&
                        $record->user_id === Auth::id() &&
                        $record->status === 'approved'
                    )
                    ->url(fn($record) => route('pds.print', $record))
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
