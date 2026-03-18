<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\WorkflowHelper;
use App\Models\Saln;
use App\Models\User;
use App\Services\FilingSeasonService;
use Carbon\Carbon;
use Filament\Forms;
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
use App\Notifications\SalnRemarksAdded;
use App\Filament\Resources\SalnResource\Pages;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View as ViewComponent;

class SalnResource extends Resource
{
    use WorkflowHelper;

    protected static ?string $model = Saln::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'SALN';
    protected static ?string $slug = 'salns';
    protected static ?string $modelLabel = 'SALN';
    protected static ?string $pluralModelLabel = 'Statement of Assets, Liabilities and Net Worth';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 5;

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

        return !Saln::where('user_id', Auth::id())->exists();
    }

    /**
     * Admins cannot use the edit page — they act via the View page actions.
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
        $isAdmin = fn() => Auth::user()?->role === 'admin';

        return $form->schema([

            self::buildAdminRemarksSection($isAdmin),

            ViewComponent::make('filament.resources.saln-resource.saln-form')
                ->columnSpanFull(),

            Section::make('Form Fields (For Validation Only)')
                ->description('⚠️ Please fill out the official SALN form above. These fields are for data binding.')
                ->schema([

                    Section::make('Compliance Type')->schema([
                        Grid::make(3)->schema([
                            Forms\Components\Checkbox::make('compliance_assumption')
                                ->label('Assumption of Office')->live()->disabled($isLocked)
                                ->afterStateUpdated(fn($state, callable $set) =>
                                    $state ? ($set('compliance_annual', false) && $set('compliance_exit', false)) : null),
                            Forms\Components\Checkbox::make('compliance_annual')
                                ->label('Annual Filing')->live()->disabled($isLocked)
                                ->afterStateUpdated(fn($state, callable $set) =>
                                    $state ? ($set('compliance_assumption', false) && $set('compliance_exit', false)) : null),
                            Forms\Components\Checkbox::make('compliance_exit')
                                ->label('Exit')->live()->disabled($isLocked)
                                ->afterStateUpdated(fn($state, callable $set) =>
                                    $state ? ($set('compliance_assumption', false) && $set('compliance_annual', false)) : null),
                        ]),
                        Forms\Components\DatePicker::make('as_of_date')
                            ->label('As of Date')->required()
                            ->default(fn() => now()->toDateString())->native(false)
                            ->disabled($isLocked),
                    ])->compact(),

                    Grid::make(3)->schema([
                        Forms\Components\Checkbox::make('joint_filing')->label('Joint Filing')->live()->disabled($isLocked)
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('separate_filing', false) && $set('not_applicable', false)) : null),
                        Forms\Components\Checkbox::make('separate_filing')->label('Separate Filing')->live()->disabled($isLocked)
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('joint_filing', false) && $set('not_applicable', false)) : null),
                        Forms\Components\Checkbox::make('not_applicable')->label('Not Applicable')->live()->disabled($isLocked)
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('joint_filing', false) && $set('separate_filing', false)) : null),
                    ]),

                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('declarant_family_name')->label('Family Name')->required()->disabled($isLocked),
                        Forms\Components\TextInput::make('declarant_first_name')->label('First Name')->required()->columnSpan(2)->disabled($isLocked),
                        Forms\Components\TextInput::make('declarant_middle_initial')->label('M.I.')->maxLength(5)->disabled($isLocked),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('declarant_position')->label('Position')->required()->disabled($isLocked),
                        Forms\Components\TextInput::make('declarant_agency_office')->label('Agency/Office')->required()->disabled($isLocked),
                    ]),
                    Forms\Components\Textarea::make('declarant_office_address')->label('Office Address')->required()->rows(2)->disabled($isLocked),

                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('spouse_family_name')->label('Spouse Family Name')->disabled($isLocked),
                        Forms\Components\TextInput::make('spouse_first_name')->label('Spouse First Name')->columnSpan(2)->disabled($isLocked),
                        Forms\Components\TextInput::make('spouse_middle_initial')->label('M.I.')->maxLength(5)->disabled($isLocked),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('spouse_position')->label('Spouse Position')->disabled($isLocked),
                        Forms\Components\TextInput::make('spouse_agency_office')->label('Spouse Agency/Office')->disabled($isLocked),
                    ]),
                    Forms\Components\Textarea::make('spouse_office_address')->label('Spouse Office Address')->rows(2)->disabled($isLocked),

                    Forms\Components\Textarea::make('multiple_marriages_names')
                        ->label('Multiple Marriages — Name(s) of Spouse(s)')->rows(2)->disabled($isLocked),
                    Forms\Components\Checkbox::make('multiple_marriages_not_applicable')
                        ->label('Not Applicable (Multiple Marriages)')->disabled($isLocked),

                    // ── ANNEX A — CHILDREN ────────────────────────────────────
                    Repeater::make('children')->relationship('children')->label('Unmarried Children Below 18')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name')->label('Full Name')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('age')->label('Age')->integer()->suffix('years old')->required()->disabled($isLocked),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Child')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Child')->defaultItems(0)
                        ->disabled($isLocked),

                    // ── ANNEX A — REAL PROPERTIES ─────────────────────────────
                    Repeater::make('realProperties')->relationship('realProperties')->label('Real Properties (Annex A)')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\Textarea::make('description')->label('Description')->required()->rows(2)->columnSpan(2)->disabled($isLocked),
                                Forms\Components\TextInput::make('kind')->label('Kind')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('exact_location')->label('Location')->required()->disabled($isLocked),
                            ]),
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('assessed_value')->label('Assessed Value')->numeric()->prefix('₱')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('current_fair_market_value')->label('Fair Market Value')->numeric()->prefix('₱')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('acquisition_year')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('mode_of_acquisition')->label('How Acquired')->required()->disabled($isLocked),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->required()->disabled($isLocked),
                        ])
                        ->columns(1)->addActionLabel('Add Real Property')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                        ->disabled($isLocked),

                    // ── ANNEX A — PERSONAL PROPERTIES ────────────────────────
                    Repeater::make('personalProperties')->relationship('personalProperties')->label('Personal Properties (Annex A)')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Textarea::make('description')->label('Description')->required()->rows(2)->columnSpan(2)->disabled($isLocked),
                                Forms\Components\TextInput::make('year_acquired')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->required()->disabled($isLocked),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost/Amount')->numeric()->prefix('₱')->required()->disabled($isLocked),
                        ])
                        ->columns(1)->addActionLabel('Add Personal Property')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                        ->disabled($isLocked),

                    // ── ANNEX A — LIABILITIES ─────────────────────────────────
                    Repeater::make('liabilities')->relationship('liabilities')->label('Liabilities (Annex A)')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('nature')->label('Nature')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('name_of_creditors')->label('Creditor')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('outstanding_balance')->label('Outstanding Balance')->numeric()->prefix('₱')->required()->disabled($isLocked),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Liability')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')->defaultItems(0)
                        ->disabled($isLocked),

                    // ── ANNEX A — BUSINESS INTERESTS ──────────────────────────
                    Forms\Components\Checkbox::make('no_business_interests')
                        ->label('I/We do not have any business interest or financial connection')->disabled($isLocked),
                    Repeater::make('businessInterests')->relationship('businessInterests')->label('Business Interests (Annex A)')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_entity')->label('Business Name')->required()->disabled($isLocked),
                                Forms\Components\Textarea::make('business_address')->label('Business Address')->required()->rows(2)->disabled($isLocked),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('nature_of_business_interest')->label('Nature of Interest')->required()->disabled($isLocked),
                                Forms\Components\DatePicker::make('date_of_acquisition')->label('Date of Acquisition')->required()->native(false)->disabled($isLocked),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Business Interest')->reorderable(false)->collapsible()
                        ->visible(fn($get) => !$get('no_business_interests'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')->defaultItems(0)
                        ->disabled($isLocked),

                    // ── ANNEX A — RELATIVES IN GOVERNMENT ────────────────────
                    Forms\Components\Checkbox::make('no_relatives_in_government')
                        ->label('I/We do not know of any relative/s in the government service')->disabled($isLocked),
                    Repeater::make('relativesInGovernment')->relationship('relativesInGovernment')->label('Relatives in Government (Annex A)')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_relative')->label('Name of Relative')->required()->disabled($isLocked),
                                Forms\Components\TextInput::make('relationship')->label('Relationship')->required()->disabled($isLocked),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('position')->label('Position')->required()->disabled($isLocked),
                                Forms\Components\Textarea::make('name_of_agency_office_address')->label('Agency/Office and Address')->required()->rows(2)->disabled($isLocked),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Relative')->reorderable(false)->collapsible()
                        ->visible(fn($get) => !$get('no_relatives_in_government'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_relative'] ?? 'Relative')->defaultItems(0)
                        ->disabled($isLocked),

                    // ─────────────────────────────────────────────────────────
                    //  ANNEX B — Declarant's Exclusive Properties
                    // ─────────────────────────────────────────────────────────
                    Section::make('Annex B — Additional Sheet (Declarant)')
                        ->description("Declarant's exclusive properties not listed in Annex A.")
                        ->collapsed()->collapsible()
                        ->schema([

                            Repeater::make('annexBRealPropertiesRepeater')
                                ->label('Real Properties (Annex B)')
                                ->relationship('annexBRealProperties')
                                ->schema([
                                    Grid::make(4)->schema([
                                        Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpan(2)->disabled($isLocked),
                                        Forms\Components\TextInput::make('kind')->label('Kind')->disabled($isLocked),
                                        Forms\Components\TextInput::make('exact_location')->label('Location')->disabled($isLocked),
                                    ]),
                                    Grid::make(4)->schema([
                                        Forms\Components\TextInput::make('assessed_value')->label('Assessed Value')->numeric()->prefix('₱')->disabled($isLocked),
                                        Forms\Components\TextInput::make('current_fair_market_value')->label('Fair Market Value')->numeric()->prefix('₱')->disabled($isLocked),
                                        Forms\Components\TextInput::make('acquisition_year')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->disabled($isLocked),
                                        Forms\Components\TextInput::make('mode_of_acquisition')->label('How Acquired')->disabled($isLocked),
                                    ]),
                                    Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->disabled($isLocked),
                                ])
                                ->columns(1)->addActionLabel('Add Real Property')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexBPersonalPropertiesRepeater')
                                ->label('Personal Properties (Annex B)')
                                ->relationship('annexBPersonalProperties')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpan(2)->disabled($isLocked),
                                        Forms\Components\TextInput::make('year_acquired')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->disabled($isLocked),
                                    ]),
                                    Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->disabled($isLocked),
                                ])
                                ->columns(1)->addActionLabel('Add Personal Property')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexBLiabilitiesRepeater')
                                ->label('Liabilities (Annex B)')
                                ->relationship('annexBLiabilities')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('nature')->label('Nature')->disabled($isLocked),
                                        Forms\Components\TextInput::make('name_of_creditors')->label('Creditor')->disabled($isLocked),
                                        Forms\Components\TextInput::make('outstanding_balance')->label('Outstanding Balance')->numeric()->prefix('₱')->disabled($isLocked),
                                    ]),
                                ])
                                ->columns(1)->addActionLabel('Add Liability')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexBBusinessInterestsRepeater')
                                ->label('Business Interests (Annex B)')
                                ->relationship('annexBBusinessInterests')
                                ->schema([
                                    Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('name_of_entity')->label('Business Name')->disabled($isLocked),
                                        Forms\Components\Textarea::make('business_address')->label('Business Address')->rows(2)->disabled($isLocked),
                                    ]),
                                    Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('nature_of_business_interest')->label('Nature of Interest')->disabled($isLocked),
                                        Forms\Components\DatePicker::make('date_of_acquisition')->label('Date of Acquisition')->native(false)->disabled($isLocked),
                                    ]),
                                ])
                                ->columns(1)->addActionLabel('Add Business Interest')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')->defaultItems(0)
                                ->disabled($isLocked),
                        ]),

                    // ─────────────────────────────────────────────────────────
                    //  ANNEX C — Spouse & Children's Exclusive Properties
                    // ─────────────────────────────────────────────────────────
                    Section::make('Annex C — Additional Sheet (Spouse & Children)')
                        ->description("Exclusive properties of the declarant's spouse and unmarried children below 18.")
                        ->collapsed()->collapsible()
                        ->schema([

                            Repeater::make('annexCRealPropertiesRepeater')
                                ->label('Real Properties (Annex C)')
                                ->relationship('annexCRealProperties')
                                ->schema([
                                    Grid::make(4)->schema([
                                        Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpan(2)->disabled($isLocked),
                                        Forms\Components\TextInput::make('kind')->label('Kind')->disabled($isLocked),
                                        Forms\Components\TextInput::make('exact_location')->label('Location')->disabled($isLocked),
                                    ]),
                                    Grid::make(4)->schema([
                                        Forms\Components\TextInput::make('assessed_value')->label('Assessed Value')->numeric()->prefix('₱')->disabled($isLocked),
                                        Forms\Components\TextInput::make('current_fair_market_value')->label('Fair Market Value')->numeric()->prefix('₱')->disabled($isLocked),
                                        Forms\Components\TextInput::make('acquisition_year')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->disabled($isLocked),
                                        Forms\Components\TextInput::make('mode_of_acquisition')->label('How Acquired')->disabled($isLocked),
                                    ]),
                                    Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->disabled($isLocked),
                                ])
                                ->columns(1)->addActionLabel('Add Real Property')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexCPersonalPropertiesRepeater')
                                ->label('Personal Properties (Annex C)')
                                ->relationship('annexCPersonalProperties')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\Textarea::make('description')->label('Description')->rows(2)->columnSpan(2)->disabled($isLocked),
                                        Forms\Components\TextInput::make('year_acquired')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->disabled($isLocked),
                                    ]),
                                    Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->disabled($isLocked),
                                ])
                                ->columns(1)->addActionLabel('Add Personal Property')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexCLiabilitiesRepeater')
                                ->label('Liabilities (Annex C)')
                                ->relationship('annexCLiabilities')
                                ->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('nature')->label('Nature')->disabled($isLocked),
                                        Forms\Components\TextInput::make('name_of_creditors')->label('Creditor')->disabled($isLocked),
                                        Forms\Components\TextInput::make('outstanding_balance')->label('Outstanding Balance')->numeric()->prefix('₱')->disabled($isLocked),
                                    ]),
                                ])
                                ->columns(1)->addActionLabel('Add Liability')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')->defaultItems(0)
                                ->disabled($isLocked),

                            Repeater::make('annexCBusinessInterestsRepeater')
                                ->label('Business Interests (Annex C)')
                                ->relationship('annexCBusinessInterests')
                                ->schema([
                                    Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('name_of_entity')->label('Business Name')->disabled($isLocked),
                                        Forms\Components\Textarea::make('business_address')->label('Business Address')->rows(2)->disabled($isLocked),
                                    ]),
                                    Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('nature_of_business_interest')->label('Nature of Interest')->disabled($isLocked),
                                        Forms\Components\DatePicker::make('date_of_acquisition')->label('Date of Acquisition')->native(false)->disabled($isLocked),
                                    ]),
                                ])
                                ->columns(1)->addActionLabel('Add Business Interest')->reorderable(false)->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')->defaultItems(0)
                                ->disabled($isLocked),
                        ]),

                    // ── DATES & OATH ──────────────────────────────────────────
                    Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('date_signed')
                            ->label('Date Signed')->default(now())->native(false)->disabled($isLocked),
                        Forms\Components\DatePicker::make('subscribed_sworn_date')
                            ->label('Subscribed and Sworn Date')->default(now())->native(false)
                            ->visible(fn() => $isAdmin())->required(fn() => $isAdmin()),
                    ]),
                    Forms\Components\TextInput::make('person_administering_oath')
                        ->label('Person Administering Oath')
                        ->visible(fn() => $isAdmin())->required(fn() => $isAdmin())
                        ->disabled(fn() => !$isAdmin())->dehydrated(fn() => $isAdmin()),

                    Forms\Components\FileUpload::make('declarant_id_presented')
                        ->label('Declarant Government Issued ID')
                        ->image()->directory('saln/declarant-ids')->visibility('private')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                        ->maxSize(5120)->imageEditor()->disabled($isLocked),

                    Forms\Components\Hidden::make('total_assets'),
                    Forms\Components\Hidden::make('total_liabilities'),
                    Forms\Components\Hidden::make('net_worth'),
                    Forms\Components\Hidden::make('annex_b_total_assets'),
                    Forms\Components\Hidden::make('annex_b_total_liabilities'),
                    Forms\Components\Hidden::make('annex_b_net_worth'),
                    Forms\Components\Hidden::make('annex_c_total_assets'),
                    Forms\Components\Hidden::make('annex_c_total_liabilities'),
                    Forms\Components\Hidden::make('annex_c_net_worth'),
                ])
                ->collapsed()->collapsible()->columnSpanFull(),

        ])->columns(1);
    }

    // =========================================================================
    //  ADMIN REMARKS SECTION
    // =========================================================================

    protected static function buildAdminRemarksSection($isAdmin): Section
    {
        return Section::make('Admin Remarks')
            ->description('Administrative notes and comments on this SALN submission')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')->rows(3)
                    ->disabled(fn() => !$isAdmin())->dehydrated(true)
                    ->placeholder($isAdmin() ? 'Add administrative remarks here...' : 'No remarks from administrator')
                    ->columnSpanFull(),
            ])
            ->visible(fn($record) => $isAdmin() || !blank($record?->remarks))
            ->collapsible()
            ->collapsed(fn($record) => blank($record?->remarks))
            ->compact();
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $table
            ->columns(self::getTableColumns($isAdmin))
            ->filters(self::getEnhancedFilters($isAdmin), layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns($isAdmin ? 4 : 2)
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
                            ? 'Allow employees to edit their selected approved SALN records. Filing season is currently OPEN.'
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
                                ->body("{$count} approved SALN record(s) have been unlocked for editing.")
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
                        ->modalDescription('Prevent employees from editing their selected SALN records. This will re-lock any currently unlocked approved records.')
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
                                ->body("{$count} SALN record(s) have been locked.")
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // ── Bulk Delete ───────────────────────────────────────────
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin),

                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No SALN records found')
            ->emptyStateDescription(
                Auth::user()->role === User::ROLE_REGULAR && !Saln::where('user_id', Auth::id())->exists()
                ? 'File your first SALN to get started.'
                : 'No SALN records match your filters.'
            )
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('File SALN')->icon('heroicon-o-plus')
                    ->visible(
                        fn() =>
                        Auth::user()->role === User::ROLE_REGULAR &&
                        !Saln::where('user_id', Auth::id())->exists()
                    ),
            ]);
    }

    // =========================================================================
    //  TABLE COLUMNS
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Employee')
                ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                ->searchable(['user.first_name', 'user.last_name'])
                ->sortable()->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')->iconColor('primary')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')->badge()
                ->formatStateUsing(fn(string $state) => ucfirst($state))
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

            Tables\Columns\IconColumn::make('editing_unlocked')
                ->label('Edit Lock')
                ->boolean()
                ->trueIcon('heroicon-o-lock-open')
                ->falseIcon('heroicon-o-lock-closed')
                ->trueColor('success')
                ->falseColor('danger')
                ->formatStateUsing(fn($state, $record) => $record?->status === 'approved' ? $state : null)
                ->tooltip(fn($record) => match (true) {
                    $record?->status !== 'approved' => null,
                    $record->editing_unlocked => 'Editing Unlocked',
                    default => 'Editing Locked',
                })
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('compliance_type_label')
                ->label('Filing Type')
                ->getStateUsing(fn($record) => match (true) {
                    (bool) $record->compliance_assumption => 'Assumption',
                    (bool) $record->compliance_annual => 'Annual',
                    (bool) $record->compliance_exit => 'Exit',
                    default => '—',
                })
                ->badge()
                ->color(fn($state) => match ($state) {
                    'Assumption' => 'info',
                    'Annual' => 'primary',
                    'Exit' => 'warning',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('as_of_date')
                ->label('As of')->date('F d, Y')->sortable()
                ->icon('heroicon-o-calendar-days')->iconColor('info'),

            Tables\Columns\TextColumn::make('total_assets')
                ->label('Total Assets')->money('PHP')->sortable()
                ->color('success')->icon('heroicon-o-building-office')->iconColor('success'),

            Tables\Columns\TextColumn::make('total_liabilities')
                ->label('Liabilities')->money('PHP')->sortable()
                ->color('danger')->icon('heroicon-o-credit-card')->iconColor('danger'),

            Tables\Columns\TextColumn::make('net_worth')
                ->label('Net Worth')->money('PHP')->sortable()->badge()
                ->color(fn($state) => ($state ?? 0) >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Filed')->since()->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')->icon('heroicon-o-paper-airplane')->iconColor('gray'),

            Tables\Columns\TextColumn::make('resubmitted_at')
                ->label('Resubmitted')->sortable()
                ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->diffForHumans() : null)
                ->placeholder('—')
                ->tooltip(fn($record) => $record->resubmitted_at?->format('M d, Y h:i A'))
                ->badge()
                ->color(fn($state) => $state ? 'warning' : 'gray')
                ->icon(fn($state) => $state ? 'heroicon-o-arrow-path' : null)
                ->iconColor('warning')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('remarks')
                ->label('Remarks')->limit(50)->wrap()->placeholder('—')
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
                ->label('Employee & Remarks')->columnSpan(1)
                ->form([
                    \Filament\Forms\Components\Select::make('user_id')->label('Employee')
                        ->options(fn() => User::where('role', User::ROLE_REGULAR)->orderBy('first_name')->get()
                            ->mapWithKeys(fn($u) => [$u->id => $u->first_name . ' ' . $u->last_name])->toArray())
                        ->searchable()->native(false)->placeholder('All employees'),
                    \Filament\Forms\Components\Select::make('has_remarks')->label('Admin Remarks')
                        ->native(false)->placeholder('All records')
                        ->options(['with' => 'With remarks', 'without' => 'Without remarks']),
                    \Filament\Forms\Components\Select::make('resubmitted')->label('Resubmission')
                        ->native(false)->placeholder('All records')
                        ->options(['yes' => '🔄 Resubmitted', 'no' => '📄 Original only']),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when($data['user_id'] ?? null, fn($q, $v) => $q->where('user_id', $v))
                        ->when(($data['has_remarks'] ?? null) === 'with', fn($q) => $q->whereNotNull('remarks')->where('remarks', '!=', ''))
                        ->when(($data['has_remarks'] ?? null) === 'without', fn($q) => $q->where(fn($q2) => $q2->whereNull('remarks')->orWhere('remarks', '')))
                        ->when(($data['resubmitted'] ?? null) === 'yes', fn($q) => $q->whereNotNull('resubmitted_at'))
                        ->when(($data['resubmitted'] ?? null) === 'no', fn($q) => $q->whereNull('resubmitted_at'))
                )
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['user_id'] ?? null) {
                        $u = User::find($data['user_id']);
                        $indicators[] = Tables\Filters\Indicator::make('Employee: ' . ($u ? $u->first_name . ' ' . $u->last_name : 'Unknown'))->removeField('user_id');
                    }
                    if ($data['has_remarks'] ?? null)
                        $indicators[] = Tables\Filters\Indicator::make('Remarks: ' . ($data['has_remarks'] === 'with' ? 'With remarks' : 'Without remarks'))->removeField('has_remarks');
                    if ($data['resubmitted'] ?? null)
                        $indicators[] = Tables\Filters\Indicator::make('Resubmission: ' . ($data['resubmitted'] === 'yes' ? 'Resubmitted' : 'Original only'))->removeField('resubmitted');
                    return $indicators;
                });
        }

        $filters[] = Tables\Filters\SelectFilter::make('compliance_type')
            ->label('Filing Type')
            ->options(['assumption' => 'Assumption of Office', 'annual' => 'Annual Filing', 'exit' => 'Exit'])
            ->placeholder('All Filing Types')
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when(($data['value'] ?? null) === 'assumption', fn($q) => $q->where('compliance_assumption', true))
                    ->when(($data['value'] ?? null) === 'annual', fn($q) => $q->where('compliance_annual', true))
                    ->when(($data['value'] ?? null) === 'exit', fn($q) => $q->where('compliance_exit', true))
            );

        $filters[] = Tables\Filters\SelectFilter::make('status')
            ->label('Status')
            ->options(['submitted' => 'Submitted', 'approved' => 'Approved', 'disapproved' => 'Disapproved'])
            ->placeholder('All Statuses');

        return $filters;
    }

    // =========================================================================
    //  CONTEXTUAL ACTIONS
    // =========================================================================

    protected static function getContextualActions(bool $isAdmin): array
    {
        if ($isAdmin) {
            return [
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make()
                        ->label('View SALN')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\Action::make('quickApprove')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Saln $record) => in_array($record->status, ['submitted', 'disapproved']))
                        ->requiresConfirmation()
                        ->action(function (Saln $record) {
                            $record->update(['status' => 'approved', 'editing_unlocked' => false]);
                            $record->user?->notify(new \App\Notifications\SalnStatusUpdated($record));
                            Notification::make()->title('SALN Approved')->success()->send();
                        }),

                    Tables\Actions\Action::make('quickUnlock')
                        ->label('Unlock Editing')
                        ->icon('heroicon-o-lock-open')
                        ->color('info')
                        ->visible(fn(Saln $record) => $record->status === 'approved' && !$record->editing_unlocked)
                        ->requiresConfirmation()
                        ->modalDescription(
                            app(FilingSeasonService::class)->isEnabled()
                            ? 'Allow the employee to edit this SALN. Filing season is OPEN.'
                            : '⚠️ Filing season is CLOSED. The employee will not be able to edit until it is enabled.'
                        )
                        ->action(function (Saln $record) {
                            $record->update(['editing_unlocked' => true]);
                            Notification::make()->title('Editing Unlocked')->success()->send();
                        }),

                    Tables\Actions\Action::make('quickLock')
                        ->label('Lock Editing')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->visible(fn(Saln $record) => $record->status === 'approved' && $record->editing_unlocked)
                        ->requiresConfirmation()
                        ->action(function (Saln $record) {
                            $record->update(['editing_unlocked' => false]);
                            Notification::make()->title('Record Locked')->warning()->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),

                ])
                    ->label('Actions')->icon('heroicon-o-ellipsis-vertical')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->color('gray')->button(),
            ];
        }

        return [
            Tables\Actions\ActionGroup::make([

                Tables\Actions\ViewAction::make()
                    ->label('View SALN')
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->label(fn(Saln $record) => $record->status === 'disapproved' ? 'Revise & Resubmit' : 'Edit SALN')
                    ->icon(fn(Saln $record) => $record->status === 'disapproved' ? 'heroicon-o-arrow-path' : 'heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn(Saln $record) =>
                        $record->user_id === Auth::id() &&
                        static::canEmployeeEdit($record)
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab()
                    ->visible(
                        fn(Saln $record) =>
                        $record->user_id === Auth::id() &&
                        $record->status === 'approved'
                    ),

            ])
                ->label('Actions')->icon('heroicon-o-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')->button(),
        ];
    }

    // =========================================================================
    //  PAGES / QUERY / NAV BADGE
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalns::route('/'),
            'create' => Pages\CreateSaln::route('/create'),
            'view' => Pages\ViewSaln::route('/{record}'),
            'edit' => Pages\EditSaln::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with([
            'user',
            'children',
            'realProperties',
            'personalProperties',
            'liabilities',
            'businessInterests',
            'relativesInGovernment',
            'annexBRealProperties',
            'annexBPersonalProperties',
            'annexBLiabilities',
            'annexBBusinessInterests',
            'annexCRealProperties',
            'annexCPersonalProperties',
            'annexCLiabilities',
            'annexCBusinessInterests',
        ]);

        if (Auth::user()?->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (Auth::user()?->role !== 'admin')
            return null;
        $count = Saln::where('status', 'submitted')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (Auth::user()?->role !== 'admin')
            return null;
        return Saln::where('status', 'submitted')->count() > 0 ? 'warning' : 'success';
    }
}
