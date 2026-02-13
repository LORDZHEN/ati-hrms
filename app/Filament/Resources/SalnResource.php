<?php

namespace App\Filament\Resources;

use App\Models\Saln;
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

class SalnResource extends Resource
{
    protected static ?string $model = Saln::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'SALN';
    protected static ?string $slug = 'saln';
    protected static ?string $modelLabel = 'SALN';
    protected static ?string $pluralModelLabel = 'Statement of Assets, Liabilities and Net Worth';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $isAdmin = fn() => Auth::user()?->role === 'admin';

        return $form->schema([

            // Admin Remarks Section (Visible only if remarks exist or user is admin)
            Section::make('Admin Remarks')
                ->schema([
                    Forms\Components\Textarea::make('remarks')
                        ->label('Remarks')
                        ->rows(3)
                        ->disabled(fn() => !$isAdmin())
                        ->columnSpanFull(),
                ])
                ->visible(fn($record) => $isAdmin() || !blank($record?->remarks))
                ->collapsible()
                ->collapsed(fn($record) => blank($record?->remarks)),

            Wizard::make([

                // STEP 1: DECLARANT & SPOUSE INFORMATION
                Wizard\Step::make('Declarant & Spouse Information')
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
                                    ->native(false),

                                Grid::make(3)->schema([
                                    Forms\Components\Checkbox::make('joint_filing')
                                        ->label('Joint Filing')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('separate_filing', false) && $set('not_applicable', false) : null),
                                    Forms\Components\Checkbox::make('separate_filing')
                                        ->label('Separate Filing')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('joint_filing', false) && $set('not_applicable', false) : null),
                                    Forms\Components\Checkbox::make('not_applicable')
                                        ->label('Not Applicable')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('joint_filing', false) && $set('separate_filing', false) : null),
                                ]),
                            ])
                            ->compact(),

                        // Declarant Information
                        Section::make('Declarant Information')
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
                                        ->default(fn() => Auth::user()?->position),
                                    Forms\Components\TextInput::make('declarant_agency_office')
                                        ->label('Agency/Office')
                                        ->required()
                                        ->default(fn() => Auth::user()?->department),
                                ]),

                                Forms\Components\Textarea::make('declarant_office_address')
                                    ->label('Office Address')
                                    ->required()
                                    ->rows(2)
                                    ->default(fn() => implode(', ', array_filter([
                                        Auth::user()?->purok_street,
                                        Auth::user()?->city_municipality,
                                        Auth::user()?->province,
                                    ]))),
                            ])
                            ->compact(),

                        // Spouse Information
                        Section::make('Spouse Information')
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
                                        ->label('Position'),
                                    Forms\Components\TextInput::make('spouse_agency_office')
                                        ->label('Agency/Office'),
                                ]),

                                Forms\Components\Textarea::make('spouse_office_address')
                                    ->label('Office Address')
                                    ->rows(2),
                            ])
                            ->compact(),
                    ]),

                // STEP 2: FAMILY COMPOSITION
                Wizard\Step::make('Family Composition')
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
                                                ->label('Name')
                                                ->required()
                                                ->columnSpan(2),
                                            Forms\Components\DatePicker::make('date_of_birth')
                                                ->label('Date of Birth')
                                                ->required()
                                                ->native(false)
                                                ->maxDate(now()) // Prevent selecting future dates
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $birthDate = \Carbon\Carbon::parse($state);
                                                        $age = $birthDate->diffInYears(now()); // Calculate from birth to now
                                                        $set('age', (int) $age); // Cast to integer to remove decimals
                                                    } else {
                                                        $set('age', null);
                                                    }
                                                }),
                                        ]),
                                        Forms\Components\TextInput::make('age')
                                            ->label('Age')
                                            ->integer() // Changed from numeric() to integer()
                                            ->disabled()
                                            ->dehydrated(),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Child')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                            ])
                            ->compact(),
                    ]),

                // STEP 3: ASSETS
                Wizard\Step::make('Assets')
                    ->description('Real and Personal Properties')
                    ->icon('heroicon-o-building-office')
                    ->schema([

                        // Real Properties
                        Section::make('Real Properties')
                            ->description('Land, Buildings, and other Real Estate')
                            ->schema([
                                Repeater::make('realProperties')
                                    ->relationship('realProperties')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            Forms\Components\Textarea::make('description')
                                                ->label('Description')
                                                ->required()
                                                ->rows(2)
                                                ->columnSpan(2),
                                            Forms\Components\TextInput::make('kind')
                                                ->label('Kind')
                                                ->required()
                                                ->placeholder('e.g., House & Lot, Agricultural Land'),
                                            Forms\Components\TextInput::make('exact_location')
                                                ->label('Exact Location')
                                                ->required()
                                                ->placeholder('Barangay, City/Municipality'),
                                        ]),
                                        Grid::make(4)->schema([
                                            Forms\Components\TextInput::make('assessed_value')
                                                ->label('Assessed Value')
                                                ->numeric()
                                                ->prefix('₱')
                                                ->required(),
                                            Forms\Components\TextInput::make('current_fair_market_value')
                                                ->label('Current Fair Market Value')
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
                                                ->label('Mode of Acquisition')
                                                ->required()
                                                ->placeholder('e.g., Purchase, Inheritance'),
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
                                    ->itemLabel(fn(array $state): ?string => $state['description'] ?? null),
                            ])
                            ->compact(),

                        // Personal Properties
                        Section::make('Personal Properties')
                            ->description('Vehicles, Jewelry, Cash, Bank Deposits, etc.')
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
                                                ->placeholder('e.g., Toyota Vios 2020, Cash on Hand, Jewelry'),
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
                                    ->itemLabel(fn(array $state): ?string => $state['description'] ?? null),
                            ])
                            ->compact(),
                    ]),

                // STEP 4: LIABILITIES
                Wizard\Step::make('Liabilities')
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
                                                ->placeholder('e.g., Housing Loan, Car Loan, Credit Card'),
                                            Forms\Components\TextInput::make('name_of_creditors')
                                                ->label('Name of Creditors')
                                                ->required()
                                                ->placeholder('e.g., Bank Name, Credit Company'),
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
                                    ->itemLabel(fn(array $state): ?string => $state['nature'] ?? null),
                            ])
                            ->compact(),
                    ]),

                // STEP 5: BUSINESS INTERESTS & RELATIVES
                Wizard\Step::make('Business & Relatives')
                    ->description('Business Interests and Relatives in Government')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([

                        // Business Interests
                        Section::make('Business Interests and Financial Connections')
                            ->description('Ownership/Shareholding (10% or more of total)')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\Checkbox::make('has_business_interests')
                                        ->label('I/We have business interest or financial connection')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_business_interests', false) : null),
                                    Forms\Components\Checkbox::make('no_business_interests')
                                        ->label('I/We do not have any business interest or financial connection')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_business_interests', false) : null),
                                ]),

                                Repeater::make('businessInterests')
                                    ->relationship('businessInterests')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('name_of_entity')
                                                ->label('Name of Entity/Business Enterprise')
                                                ->required(),
                                            Forms\Components\Textarea::make('business_address')
                                                ->label('Business Address')
                                                ->required()
                                                ->rows(2),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('nature_of_business_interest')
                                                ->label('Nature of Business Interest/Financial Connection')
                                                ->required()
                                                ->placeholder('e.g., Owner, Shareholder, Director'),
                                            Forms\Components\DatePicker::make('date_of_acquisition')
                                                ->label('Date of Acquisition of Interest/Connection')
                                                ->required()
                                                ->native(false),
                                        ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Business Interest')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->visible(fn($get) => $get('has_business_interests'))
                                    ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? null),
                            ])
                            ->compact(),

                        // Relatives in Government
                        Section::make('Relatives in the Government Service')
                            ->description('Within the Fourth Degree of Consanguinity or Affinity')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\Checkbox::make('has_relatives_in_government')
                                        ->label('I have relatives in the government service')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_relatives_in_government', false) : null),
                                    Forms\Components\Checkbox::make('no_relatives_in_government')
                                        ->label('I/We do not know of any relative in the government service')
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_relatives_in_government', false) : null),
                                ]),

                                Repeater::make('relativesInGovernment')
                                    ->relationship('relativesInGovernment')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('name_of_relative')
                                                ->label('Name of Relative')
                                                ->required(),
                                            Forms\Components\TextInput::make('relationship')
                                                ->label('Relationship')
                                                ->required()
                                                ->placeholder('e.g., Father, Sibling, Cousin'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('position')
                                                ->label('Position')
                                                ->required(),
                                            Forms\Components\Textarea::make('name_of_agency_office_address')
                                                ->label('Name of Agency/Office and Address')
                                                ->required()
                                                ->rows(2),
                                        ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Relative')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->visible(fn($get) => $get('has_relatives_in_government'))
                                    ->itemLabel(fn(array $state): ?string => $state['name_of_relative'] ?? null),
                            ])
                            ->compact(),
                    ]),

                // STEP 6: DECLARATION & SIGNATURE
                Wizard\Step::make('Declaration & Signature')
                    ->description('Certification and Oath')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([

                        // Summary
                        // Summary
                        Section::make('Summary')
                            ->description('Automatically calculated totals')
                            ->schema([
                                Forms\Components\Placeholder::make('calculated_totals')
                                    ->label('')
                                    ->content(function ($get, $record) {
                                        // For edit mode, use saved relationships
                                        if ($record) {
                                            $record->load(['realProperties', 'personalProperties', 'liabilities']);

                                            $realPropertiesTotal = $record->realProperties->sum('current_fair_market_value');
                                            $personalPropertiesTotal = $record->personalProperties->sum('acquisition_cost');
                                            $totalAssets = $realPropertiesTotal + $personalPropertiesTotal;

                                            $totalLiabilities = $record->liabilities->sum('outstanding_balance');
                                            $netWorth = $totalAssets - $totalLiabilities;
                                        } else {
                                            // For create mode, calculate from form state
                                            $realProperties = $get('realProperties') ?? [];
                                            $personalProperties = $get('personalProperties') ?? [];
                                            $liabilities = $get('liabilities') ?? [];

                                            $realPropertiesTotal = collect($realProperties)->sum('current_fair_market_value');
                                            $personalPropertiesTotal = collect($personalProperties)->sum('acquisition_cost');
                                            $totalAssets = $realPropertiesTotal + $personalPropertiesTotal;

                                            $totalLiabilities = collect($liabilities)->sum('outstanding_balance');
                                            $netWorth = $totalAssets - $totalLiabilities;
                                        }

                                        $netWorthColor = $netWorth >= 0 ? 'success' : 'danger';

                                        return view('filament.components.saln-summary', [
                                            'totalAssets' => $totalAssets,
                                            'totalLiabilities' => $totalLiabilities,
                                            'netWorth' => $netWorth,
                                            'netWorthColor' => $netWorthColor,
                                        ]);
                                    })
                                    ->columnSpanFull(),

                                // Hidden fields to store the values for saving
                                Forms\Components\Hidden::make('total_assets'),
                                Forms\Components\Hidden::make('total_liabilities'),
                                Forms\Components\Hidden::make('net_worth'),
                            ])
                            ->compact(),

                        // Signature Information
                        Section::make('Signature Information')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\DatePicker::make('date_signed')
                                        ->label('Date Signed')
                                        ->required()
                                        ->default(now())
                                        ->native(false),
                                    Forms\Components\DatePicker::make('subscribed_sworn_date')
                                        ->label('Subscribed and Sworn Date')
                                        ->default(now())
                                        ->native(false)
                                        ->visible(fn() => $isAdmin())
                                        ->required(fn() => $isAdmin()),
                                ]),

                                // Person Administering Oath - ADMIN ONLY
                                Forms\Components\TextInput::make('person_administering_oath')
                                    ->label('Person Administering Oath')
                                    ->placeholder('Name of official administering the oath')
                                    ->visible(fn() => $isAdmin())
                                    ->required(fn() => $isAdmin())
                                    ->disabled(fn() => !$isAdmin())
                                    ->helperText('This field is only fillable by administrators'),

                                Grid::make(2)->schema([
                                    Forms\Components\FileUpload::make('declarant_id_presented')
                                        ->label('Declarant Government Issued ID')
                                        ->image()
                                        ->directory('saln/declarant-ids')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                        ->maxSize(5120)
                                        ->helperText('Upload ID (Passport, GSIS, SSS, PRC, Driver\'s License, etc.)'),
                                    Forms\Components\FileUpload::make('spouse_id_presented')
                                        ->label('Spouse Government Issued ID')
                                        ->image()
                                        ->directory('saln/spouse-ids')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                        ->maxSize(5120)
                                        ->helperText('Upload spouse ID (if applicable)'),
                                ]),
                            ])
                            ->compact(),

                        // Certification Text
                        Section::make('Certification')
                            ->schema([
                                Forms\Components\Placeholder::make('certification_text')
                                    ->content('I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.')
                                    ->columnSpanFull(),

                                Forms\Components\Placeholder::make('authorization_text')
                                    ->content('I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.')
                                    ->columnSpanFull(),
                            ])
                            ->compact(),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable($isAdmin()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->searchable(['user.first_name', 'user.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('as_of_date')
                    ->label('As of Date')
                    ->date('F d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('declarant_family_name')
                    ->label('Declarant')
                    ->formatStateUsing(fn($record) => $record->declarant_family_name . ', ' . $record->declarant_first_name)
                    ->searchable(['declarant_family_name', 'declarant_first_name']),

                Tables\Columns\TextColumn::make('total_assets')
                    ->label('Total Assets')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_liabilities')
                    ->label('Total Liabilities')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_worth')
                    ->label('Net Worth')
                    ->money('PHP')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),

                Tables\Columns\IconColumn::make('has_remarks')
                    ->label('Remarks')
                    ->boolean()
                    ->getStateUsing(fn($record) => !blank($record->remarks))
                    ->trueIcon('heroicon-o-chat-bubble-left-right')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Filed Date')
                    ->dateTime('M d, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(fn() => Saln::selectRaw('YEAR(as_of_date) as year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year'))
                    ->query(
                        fn(Builder $query, array $data) =>
                        isset($data['value']) ? $query->whereYear('as_of_date', $data['value']) : $query
                    ),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Employee')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name)
                    ->visible(fn() => Auth::user()?->role === 'admin'),

                Tables\Filters\Filter::make('as_of_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->native(false),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('as_of_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('as_of_date', '<=', $date))
                    ),

                Tables\Filters\TernaryFilter::make('has_remarks')
                    ->label('Has Admin Remarks')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('remarks'),
                        false: fn(Builder $query) => $query->whereNull('remarks'),
                    )
                    ->visible(fn() => Auth::user()?->role === 'admin'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make()
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
                                ->required(),
                        ])
                        ->action(function (Saln $record, array $data) {
                            $record->update(['remarks' => $data['remarks']]);

                            \Filament\Notifications\Notification::make()
                                ->title('Remarks Updated')
                                ->success()
                                ->send();
                        })
                        ->visible(fn() => Auth::user()?->role === 'admin'),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()?->role === 'admin'),
                ])
                    ->button()
                    ->label('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

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
