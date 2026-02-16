<?php

namespace App\Filament\Resources;

use App\Models\Saln;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SalnResource\Pages;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class SalnResource extends Resource
{
    protected static ?string $model = Saln::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'SALN';
    protected static ?string $slug = 'salns';
    protected static ?string $modelLabel = 'SALN';
    protected static ?string $pluralModelLabel = 'Statement of Assets, Liabilities and Net Worth';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 5;

    /* ============================================================
       FORM DEFINITION - WIZARD-BASED MULTI-STEP FORM
       ============================================================ */

    public static function form(Form $form): Form
    {
        $isAdmin = fn() => Auth::user()?->role === 'admin';

        return $form->schema([
            // Admin Remarks Section (Top priority for admins)
            self::buildAdminRemarksSection($isAdmin),

            // Main SALN Wizard
            Wizard::make([
                self::buildDeclarantInfoStep(),
                self::buildFamilyCompositionStep(),
                self::buildAssetsStep(),
                self::buildLiabilitiesStep(),
                self::buildBusinessRelativesStep(),
                self::buildDeclarationStep($isAdmin),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable($isAdmin()),
        ]);
    }

    /* ============================================================
       FORM STEP BUILDERS
       ============================================================ */

    protected static function buildAdminRemarksSection($isAdmin): Section
    {
        return Section::make('Admin Remarks')
            ->description('Administrative notes and comments on this SALN submission')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->disabled(fn() => !$isAdmin())
                    ->dehydrated(true)
                    ->placeholder($isAdmin() ? 'Add administrative remarks here...' : 'No remarks from administrator')
                    ->columnSpanFull(),
            ])
            ->visible(fn($record) => $isAdmin() || !blank($record?->remarks))
            ->collapsible()
            ->collapsed(fn($record) => blank($record?->remarks))
            ->compact();
    }

    protected static function buildDeclarantInfoStep(): Wizard\Step
    {
        return Wizard\Step::make('Declarant & Spouse')
            ->description('Personal information of declarant and spouse')
            ->icon('heroicon-o-identification')
            ->schema([
                // Filing Type
                Section::make('Filing Type')
                    ->description('Husband and wife who are both public officials may file jointly or separately')
                    ->schema([
                        Forms\Components\DatePicker::make('as_of_date')
                            ->label('As of Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar'),

                        Grid::make(3)->schema([
                            Forms\Components\Checkbox::make('joint_filing')
                                ->label('Joint Filing')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? ($set('separate_filing', false) && $set('not_applicable', false)) : null),
                            Forms\Components\Checkbox::make('separate_filing')
                                ->label('Separate Filing')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? ($set('joint_filing', false) && $set('not_applicable', false)) : null),
                            Forms\Components\Checkbox::make('not_applicable')
                                ->label('Not Applicable')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? ($set('joint_filing', false) && $set('separate_filing', false)) : null),
                        ]),
                    ])
                    ->compact(),

                // Declarant Information
                Section::make('Declarant Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(4)->schema([
                            Forms\Components\TextInput::make('declarant_family_name')
                                ->label('Family Name')
                                ->required()
                                ->default(fn() => Auth::user()?->last_name)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('declarant_first_name')
                                ->label('First Name')
                                ->required()
                                ->default(fn() => Auth::user()?->first_name)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('declarant_middle_initial')
                                ->label('M.I.')
                                ->maxLength(5)
                                ->default(fn() => substr(Auth::user()?->middle_name ?? '', 0, 1))
                                ->columnSpan(1),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('declarant_position')
                                ->label('Position')
                                ->required()
                                ->default(fn() => Auth::user()?->position)
                                ->prefixIcon('heroicon-o-briefcase'),
                            Forms\Components\TextInput::make('declarant_agency_office')
                                ->label('Agency/Office')
                                ->required()
                                ->default(fn() => Auth::user()?->department)
                                ->prefixIcon('heroicon-o-building-office'),
                        ]),

                        Forms\Components\Textarea::make('declarant_office_address')
                            ->label('Office Address')
                            ->required()
                            ->rows(2)
                            ->default(fn() => implode(', ', array_filter([
                                Auth::user()?->purok_street,
                                Auth::user()?->city_municipality,
                                Auth::user()?->province,
                            ])))
                            ->helperText('Enter complete office address'),
                    ])
                    ->compact(),

                // Spouse Information
                Section::make('Spouse Information')
                    ->description('Optional - Fill if applicable')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        Grid::make(4)->schema([
                            Forms\Components\TextInput::make('spouse_family_name')
                                ->label('Family Name')
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('spouse_first_name')
                                ->label('First Name')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('spouse_middle_initial')
                                ->label('M.I.')
                                ->maxLength(5)
                                ->columnSpan(1),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('spouse_position')
                                ->label('Position')
                                ->prefixIcon('heroicon-o-briefcase'),
                            Forms\Components\TextInput::make('spouse_agency_office')
                                ->label('Agency/Office')
                                ->prefixIcon('heroicon-o-building-office'),
                        ]),

                        Forms\Components\Textarea::make('spouse_office_address')
                            ->label('Office Address')
                            ->rows(2)
                            ->helperText('Enter complete office address'),
                    ])
                    ->compact()
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    protected static function buildFamilyCompositionStep(): Wizard\Step
    {
        return Wizard\Step::make('Family')
            ->description('Unmarried children below 18 years living in household')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('Unmarried Children Below Eighteen (18) Years of Age')
                    ->description('Living in Declarant\'s Household')
                    ->schema([
                        Repeater::make('children')
                            ->relationship('children')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Full Name')
                                        ->required()
                                        ->columnSpan(2)
                                        ->prefixIcon('heroicon-o-user'),
                                    Forms\Components\DatePicker::make('date_of_birth')
                                        ->label('Date of Birth')
                                        ->required()
                                        ->native(false)
                                        ->maxDate(now())
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $age = Carbon::parse($state)->age;
                                                $set('age', (int) $age);
                                            } else {
                                                $set('age', null);
                                            }
                                        })
                                        ->prefixIcon('heroicon-o-cake'),
                                ]),
                                Forms\Components\TextInput::make('age')
                                    ->label('Age')
                                    ->integer()
                                    ->disabled()
                                    ->dehydrated()
                                    ->suffix('years old'),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Child')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Child Details')
                            ->defaultItems(0),
                    ])
                    ->compact(),
            ]);
    }

    protected static function buildAssetsStep(): Wizard\Step
    {
        return Wizard\Step::make('Assets')
            ->description('Real and Personal Properties')
            ->icon('heroicon-o-building-office')
            ->schema([
                // Real Properties
                Section::make('Real Properties')
                    ->description('Land, Buildings, and other Real Estate')
                    ->icon('heroicon-o-home')
                    ->schema([
                        Repeater::make('realProperties')
                            ->relationship('realProperties')
                            ->schema([
                                Grid::make(4)->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->label('Description')
                                        ->required()
                                        ->rows(2)
                                        ->columnSpan(2)
                                        ->placeholder('Describe the property...'),
                                    Forms\Components\TextInput::make('kind')
                                        ->label('Kind')
                                        ->required()
                                        ->placeholder('e.g., House & Lot'),
                                    Forms\Components\TextInput::make('exact_location')
                                        ->label('Location')
                                        ->required()
                                        ->placeholder('Barangay, City'),
                                ]),
                                Grid::make(4)->schema([
                                    Forms\Components\TextInput::make('assessed_value')
                                        ->label('Assessed Value')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->required(),
                                    Forms\Components\TextInput::make('current_fair_market_value')
                                        ->label('Fair Market Value')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->required(),
                                    Forms\Components\TextInput::make('acquisition_year')
                                        ->label('Year Acquired')
                                        ->numeric()
                                        ->minValue(1900)
                                        ->maxValue(date('Y'))
                                        ->required()
                                        ->placeholder('YYYY'),
                                    Forms\Components\TextInput::make('mode_of_acquisition')
                                        ->label('How Acquired')
                                        ->required()
                                        ->placeholder('e.g., Purchase'),
                                ]),
                                Forms\Components\TextInput::make('acquisition_cost')
                                    ->label('Acquisition Cost')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Real Property')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')
                            ->defaultItems(0),
                    ])
                    ->compact(),

                // Personal Properties
                Section::make('Personal Properties')
                    ->description('Vehicles, Jewelry, Cash, Bank Deposits, etc.')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Repeater::make('personalProperties')
                            ->relationship('personalProperties')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->label('Description')
                                        ->required()
                                        ->rows(2)
                                        ->columnSpan(2)
                                        ->placeholder('e.g., Toyota Vios 2020, Cash on Hand'),
                                    Forms\Components\TextInput::make('year_acquired')
                                        ->label('Year Acquired')
                                        ->numeric()
                                        ->minValue(1900)
                                        ->maxValue(date('Y'))
                                        ->required()
                                        ->placeholder('YYYY'),
                                ]),
                                Forms\Components\TextInput::make('acquisition_cost')
                                    ->label('Acquisition Cost/Amount')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Personal Property')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')
                            ->defaultItems(0),
                    ])
                    ->compact(),
            ]);
    }

    protected static function buildLiabilitiesStep(): Wizard\Step
    {
        return Wizard\Step::make('Liabilities')
            ->description('Loans, Mortgages, and other Obligations')
            ->icon('heroicon-o-credit-card')
            ->schema([
                Section::make('Liabilities')
                    ->description('List all outstanding debts and financial obligations')
                    ->schema([
                        Repeater::make('liabilities')
                            ->relationship('liabilities')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('nature')
                                        ->label('Nature')
                                        ->required()
                                        ->placeholder('e.g., Housing Loan')
                                        ->prefixIcon('heroicon-o-document-text'),
                                    Forms\Components\TextInput::make('name_of_creditors')
                                        ->label('Creditor')
                                        ->required()
                                        ->placeholder('e.g., Bank Name')
                                        ->prefixIcon('heroicon-o-building-library'),
                                    Forms\Components\TextInput::make('outstanding_balance')
                                        ->label('Outstanding Balance')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->required(),
                                ]),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Liability')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')
                            ->defaultItems(0),
                    ])
                    ->compact(),
            ]);
    }

    protected static function buildBusinessRelativesStep(): Wizard\Step
    {
        return Wizard\Step::make('Business & Relatives')
            ->description('Business Interests and Relatives in Government')
            ->icon('heroicon-o-building-storefront')
            ->schema([
                // Business Interests
                Section::make('Business Interests and Financial Connections')
                    ->description('Ownership/Shareholding (10% or more of total)')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Checkbox::make('has_business_interests')
                                ->label('I/We have business interest or financial connection')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_business_interests', false) : null),
                            Forms\Components\Checkbox::make('no_business_interests')
                                ->label('I/We do not have any business interest or financial connection')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_business_interests', false) : null),
                        ]),

                        Repeater::make('businessInterests')
                            ->relationship('businessInterests')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name_of_entity')
                                        ->label('Business Name')
                                        ->required()
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    Forms\Components\Textarea::make('business_address')
                                        ->label('Business Address')
                                        ->required()
                                        ->rows(2)
                                        ->helperText('Complete business address'),
                                ]),
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('nature_of_business_interest')
                                        ->label('Nature of Interest')
                                        ->required()
                                        ->placeholder('e.g., Owner, Shareholder'),
                                    Forms\Components\DatePicker::make('date_of_acquisition')
                                        ->label('Date of Acquisition')
                                        ->required()
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                ]),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Business Interest')
                            ->reorderable(false)
                            ->collapsible()
                            ->visible(fn($get) => $get('has_business_interests'))
                            ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')
                            ->defaultItems(0),
                    ])
                    ->compact(),

                // Relatives in Government
                Section::make('Relatives in the Government Service')
                    ->description('Within the Fourth Degree of Consanguinity or Affinity')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Checkbox::make('has_relatives_in_government')
                                ->label('I have relatives in the government service')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_relatives_in_government', false) : null),
                            Forms\Components\Checkbox::make('no_relatives_in_government')
                                ->label('I/We do not know of any relative in the government service')
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_relatives_in_government', false) : null),
                        ]),

                        Repeater::make('relativesInGovernment')
                            ->relationship('relativesInGovernment')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name_of_relative')
                                        ->label('Name of Relative')
                                        ->required()
                                        ->prefixIcon('heroicon-o-user'),
                                    Forms\Components\TextInput::make('relationship')
                                        ->label('Relationship')
                                        ->required()
                                        ->placeholder('e.g., Father, Sibling'),
                                ]),
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('position')
                                        ->label('Position')
                                        ->required()
                                        ->prefixIcon('heroicon-o-briefcase'),
                                    Forms\Components\Textarea::make('name_of_agency_office_address')
                                        ->label('Agency/Office and Address')
                                        ->required()
                                        ->rows(2)
                                        ->helperText('Complete agency name and address'),
                                ]),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Relative')
                            ->reorderable(false)
                            ->collapsible()
                            ->visible(fn($get) => $get('has_relatives_in_government'))
                            ->itemLabel(fn(array $state): ?string => $state['name_of_relative'] ?? 'Relative')
                            ->defaultItems(0),
                    ])
                    ->compact(),
            ]);
    }

    protected static function buildDeclarationStep($isAdmin): Wizard\Step
    {
        return Wizard\Step::make('Declaration')
            ->description('Certification and Oath')
            ->icon('heroicon-o-pencil-square')
            ->schema([
                // Summary
                Section::make('Financial Summary')
                    ->description('Automatically calculated totals')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\Placeholder::make('calculated_totals')
                            ->label('')
                            ->content(function ($get, $record) {
                                if ($record) {
                                    $record->load(['realProperties', 'personalProperties', 'liabilities']);
                                    $realPropertiesTotal = $record->realProperties->sum('current_fair_market_value');
                                    $personalPropertiesTotal = $record->personalProperties->sum('acquisition_cost');
                                    $totalAssets = $realPropertiesTotal + $personalPropertiesTotal;
                                    $totalLiabilities = $record->liabilities->sum('outstanding_balance');
                                    $netWorth = $totalAssets - $totalLiabilities;
                                } else {
                                    $realProperties = $get('realProperties') ?? [];
                                    $personalProperties = $get('personalProperties') ?? [];
                                    $liabilities = $get('liabilities') ?? [];
                                    $realPropertiesTotal = collect($realProperties)->sum('current_fair_market_value');
                                    $personalPropertiesTotal = collect($personalProperties)->sum('acquisition_cost');
                                    $totalAssets = $realPropertiesTotal + $personalPropertiesTotal;
                                    $totalLiabilities = collect($liabilities)->sum('outstanding_balance');
                                    $netWorth = $totalAssets - $totalLiabilities;
                                }

                                return view('filament.components.saln-summary', [
                                    'totalAssets' => $totalAssets,
                                    'totalLiabilities' => $totalLiabilities,
                                    'netWorth' => $netWorth,
                                    'netWorthColor' => $netWorth >= 0 ? 'success' : 'danger',
                                ]);
                            })
                            ->columnSpanFull(),

                        // Hidden fields
                        Forms\Components\Hidden::make('total_assets'),
                        Forms\Components\Hidden::make('total_liabilities'),
                        Forms\Components\Hidden::make('net_worth'),
                    ])
                    ->compact(),

                // Signature Information
                Section::make('Signature Information')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('date_signed')
                                ->label('Date Signed')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->prefixIcon('heroicon-o-calendar'),
                            Forms\Components\DatePicker::make('subscribed_sworn_date')
                                ->label('Subscribed and Sworn Date')
                                ->default(now())
                                ->native(false)
                                ->visible(fn() => $isAdmin())
                                ->required(fn() => $isAdmin())
                                ->prefixIcon('heroicon-o-calendar-days'),
                        ]),

                        Forms\Components\TextInput::make('person_administering_oath')
                            ->label('Person Administering Oath')
                            ->placeholder('Name of official administering the oath')
                            ->visible(fn() => $isAdmin())
                            ->required(fn() => $isAdmin())
                            ->disabled(fn() => !$isAdmin())
                            ->dehydrated(fn() => $isAdmin())
                            ->helperText('This field is only fillable by administrators')
                            ->prefixIcon('heroicon-o-shield-check'),

                        Grid::make(2)->schema([
                            Forms\Components\FileUpload::make('declarant_id_presented')
                                ->label('Declarant Government Issued ID')
                                ->image()
                                ->directory('saln/declarant-ids')
                                ->visibility('private')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                ->maxSize(5120)
                                ->helperText('Upload ID (Passport, GSIS, SSS, PRC, etc.)')
                                ->imageEditor(),
                            Forms\Components\FileUpload::make('spouse_id_presented')
                                ->label('Spouse Government Issued ID')
                                ->image()
                                ->directory('saln/spouse-ids')
                                ->visibility('private')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                ->maxSize(5120)
                                ->helperText('Upload spouse ID (if applicable)')
                                ->imageEditor(),
                        ]),
                    ])
                    ->compact(),

                // Certification Text
                Section::make('Certification')
                    ->icon('heroicon-o-document-check')
                    ->schema([
                        Forms\Components\Placeholder::make('certification_text')
                            ->content('I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('authorization_text')
                            ->content('I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.')
                            ->columnSpanFull(),
                    ])
                    ->compact(),
            ]);
    }

    /* ============================================================
       TABLE DEFINITION - FINANCIAL CARD LAYOUT
       ============================================================ */

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getFinancialCardColumns())
            ->filters(self::getEnhancedFilters())
            ->actions(self::getContextualActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No SALN records found')
            ->emptyStateDescription('File your first SALN to get started.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('File SALN')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    /* ============================================================
       FINANCIAL CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getFinancialCardColumns(): array
    {
        return [
            // Header: Employee & Date
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->searchable(['user.first_name', 'user.last_name'])
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary'),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('as_of_date')
                        ->label('As of')
                        ->date('F d, Y')
                        ->icon('heroicon-o-calendar')
                        ->iconColor('gray')
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Filed')
                        ->dateTime('M d, Y')
                        ->icon('heroicon-o-clock')
                        ->iconColor('gray')
                        ->size('sm')
                        ->color('gray'),
                ]),
            ])->space(1),

            // Financial Summary Cards
            Tables\Columns\Layout\Split::make([
                // Left: Assets & Liabilities
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('total_assets')
                        ->label('Total Assets')
                        ->money('PHP')
                        ->weight('medium')
                        ->icon('heroicon-o-building-office')
                        ->iconColor('success')
                        ->color('success'),

                    Tables\Columns\TextColumn::make('total_liabilities')
                        ->label('Total Liabilities')
                        ->money('PHP')
                        ->size('sm')
                        ->icon('heroicon-o-credit-card')
                        ->iconColor('danger')
                        ->color('danger'),
                ])->space(1),

                // Right: Net Worth (Prominent)
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('net_worth')
                        ->label('Net Worth')
                        ->money('PHP')
                        ->weight('bold')
                        ->size('lg')
                        ->icon('heroicon-o-banknotes')
                        ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                        ->badge(),
                ])->space(1)->alignment('end'),
            ])->from('md'),

            // Admin Remarks Panel
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\IconColumn::make('has_remarks')
                        ->label('Remarks')
                        ->boolean()
                        ->getStateUsing(fn($record) => !blank($record->remarks))
                        ->trueIcon('heroicon-o-chat-bubble-left-right')
                        ->falseIcon('heroicon-o-check-circle')
                        ->trueColor('warning')
                        ->falseColor('success')
                        ->size('lg'),

                    Tables\Columns\TextColumn::make('remarks')
                        ->label('Admin Remarks')
                        ->limit(100)
                        ->wrap()
                        ->default('No remarks')
                        ->color(fn($record) => blank($record->remarks) ? 'success' : 'warning')
                        ->icon(fn($record) => blank($record->remarks) ? 'heroicon-o-check' : 'heroicon-o-exclamation-triangle'),
                ]),
            ])
                ->collapsible()
                ->collapsed(fn($record) => blank($record->remarks)),
        ];
    }

    /* ============================================================
       ENHANCED FILTERS
       ============================================================ */

    protected static function getEnhancedFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('year')
                ->label('Year')
                ->options(fn() => Saln::selectRaw('YEAR(as_of_date) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year', 'year'))
                ->query(fn(Builder $query, array $data) =>
                    isset($data['value']) ? $query->whereYear('as_of_date', $data['value']) : $query)
                ->native(false)
                ->indicator('Year'),

            Tables\Filters\SelectFilter::make('user_id')
                ->label('Employee')
                ->relationship('user', 'first_name')
                ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name)
                ->visible(fn() => Auth::user()?->role === 'admin')
                ->searchable()
                ->preload()
                ->native(false)
                ->indicator('Employee'),

            Tables\Filters\Filter::make('as_of_date')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->label('From Date')
                        ->native(false),
                    Forms\Components\DatePicker::make('until')
                        ->label('Until Date')
                        ->native(false),
                ])
                ->query(fn(Builder $query, array $data) => $query
                    ->when($data['from'], fn($q, $date) => $q->whereDate('as_of_date', '>=', $date))
                    ->when($data['until'], fn($q, $date) => $q->whereDate('as_of_date', '<=', $date)))
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['from'] ?? null) {
                        $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                    }
                    if ($data['until'] ?? null) {
                        $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->toFormattedDateString();
                    }
                    return $indicators;
                }),

            Tables\Filters\TernaryFilter::make('has_remarks')
                ->label('Has Admin Remarks')
                ->queries(
                    true: fn(Builder $query) => $query->whereNotNull('remarks'),
                    false: fn(Builder $query) => $query->whereNull('remarks'),
                )
                ->visible(fn() => Auth::user()?->role === 'admin')
                ->indicator('Remarks'),

            Tables\Filters\Filter::make('net_worth')
                ->form([
                    Forms\Components\TextInput::make('min_net_worth')
                        ->label('Minimum Net Worth')
                        ->numeric()
                        ->prefix('₱'),
                    Forms\Components\TextInput::make('max_net_worth')
                        ->label('Maximum Net Worth')
                        ->numeric()
                        ->prefix('₱'),
                ])
                ->query(fn(Builder $query, array $data) => $query
                    ->when($data['min_net_worth'], fn($q, $amount) => $q->where('net_worth', '>=', $amount))
                    ->when($data['max_net_worth'], fn($q, $amount) => $q->where('net_worth', '<=', $amount)))
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['min_net_worth'] ?? null) {
                        $indicators['min'] = 'Min: ₱' . number_format($data['min_net_worth'], 2);
                    }
                    if ($data['max_net_worth'] ?? null) {
                        $indicators['max'] = 'Max: ₱' . number_format($data['max_net_worth'], 2);
                    }
                    return $indicators;
                })
                ->visible(fn() => Auth::user()?->role === 'admin'),
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
                    ->visible(fn() => Auth::user()?->role !== 'admin'),

                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('remarks')
                    ->label('Add/Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(4)
                            ->required()
                            ->placeholder('Enter administrative remarks or comments...'),
                    ])
                    ->action(function (Saln $record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);

                        Notification::make()
                            ->title('Remarks Updated')
                            ->body('Administrative remarks have been saved successfully.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn() => Auth::user()?->role === 'admin'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => Auth::user()?->role === 'admin'),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    /* ============================================================
       RESOURCE CONFIGURATION
       ============================================================ */

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
        $query = parent::getEloquentQuery()
            ->with([
                'user',
                'children',
                'realProperties',
                'personalProperties',
                'liabilities',
                'businessInterests',
                'relativesInGovernment'
            ]);

        if (Auth::user()?->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (Auth::user()?->role !== 'admin') {
            return null;
        }

        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (Auth::user()?->role !== 'admin') {
            return null;
        }

        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? 'warning' : 'success';
    }
}
