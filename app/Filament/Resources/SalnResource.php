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
use Filament\Support\Enums\MaxWidth;

class SalnResource extends Resource
{
    protected static ?string $model = Saln::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'SALN';
    protected static ?string $title = 'SALNs';
    protected static ?string $slug = 'Saln';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Basic Information')
                    ->schema([
                        Section::make('Filing Type')
                            ->schema([
                                Forms\Components\DatePicker::make('as_of_date')
                                    ->label('As of Date')
                                    ->required(),
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\Checkbox::make('joint_filing')
                                            ->label('Joint Filing'),
                                        Forms\Components\Checkbox::make('separate_filing')
                                            ->label('Separate Filing'),
                                        Forms\Components\Checkbox::make('not_applicable')
                                            ->label('Not Applicable'),
                                    ]),
                            ]),

                        Section::make('Declarant Information')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('declarant_family_name')
                                            ->label('Family Name')
                                            ->required()
                                            ->default(fn() => auth()->user()?->last_name),
                                        Forms\Components\TextInput::make('declarant_first_name')
                                            ->label('First Name')
                                            ->required()
                                            ->default(fn() => auth()->user()?->first_name),
                                        Forms\Components\TextInput::make('declarant_middle_initial')
                                            ->label('M.I.')
                                            ->maxLength(5)
                                            ->default(fn() => substr(auth()->user()?->middle_name ?? '', 0, 1)),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('declarant_position')
                                            ->label('Position')
                                            ->required()
                                            ->default(fn() => auth()->user()?->position),
                                        Forms\Components\TextInput::make('declarant_agency_office')
                                            ->label('Agency/Office')
                                            ->required()
                                            ->default(fn() => auth()->user()?->department),
                                    ]),
                                Forms\Components\Textarea::make('declarant_office_address')
                                    ->label('Office Address')
                                    ->required()
                                    ->rows(2)
                                    ->default(fn() => implode(', ', array_filter([
                                        auth()->user()?->purok_street,
                                        auth()->user()?->city_municipality,
                                        auth()->user()?->province,
                                    ]))),
                            ]),

                        Section::make('Spouse Information')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('spouse_family_name')
                                            ->label('Family Name'),
                                        Forms\Components\TextInput::make('spouse_first_name')
                                            ->label('First Name'),
                                        Forms\Components\TextInput::make('spouse_middle_initial')
                                            ->label('M.I.')
                                            ->maxLength(5),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('spouse_position')
                                            ->label('Position'),
                                        Forms\Components\TextInput::make('spouse_agency_office')
                                            ->label('Agency/Office'),
                                    ]),
                                Forms\Components\Textarea::make('spouse_office_address')
                                    ->label('Office Address')
                                    ->rows(2),
                            ]),
                    ]),

                Wizard\Step::make('Family Composition')
                    ->schema([
                        Section::make('Unmarried Children Below Eighteen (18) Years of Age Living in Declarant\'s Household')
                            ->schema([
                                Repeater::make('children')
                                    ->relationship('children')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Name')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(
                                                        fn($state, callable $set) =>
                                                        $set('age', $state ? now()->diffInYears($state) : null)
                                                    ),
                                                Forms\Components\TextInput::make('age')
                                                    ->label('Age')
                                                    ->numeric()
                                                    ->disabled(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Child')
                                    ->collapsed(),
                            ]),
                    ]),

                Wizard\Step::make('Assets')
                    ->schema([
                        Section::make('Real Properties')
                            ->schema([
                                Repeater::make('realProperties')
                                    ->relationship('realProperties')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Description')
                                                    ->required()
                                                    ->rows(2),
                                                Forms\Components\TextInput::make('kind')
                                                    ->label('Kind')
                                                    ->required(),
                                                Forms\Components\TextInput::make('exact_location')
                                                    ->label('Exact Location')
                                                    ->required(),
                                                Forms\Components\TextInput::make('assessed_value')
                                                    ->label('Assessed Value')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->required(),
                                            ]),
                                        Grid::make(4)
                                            ->schema([
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
                                                    ->required(),
                                                Forms\Components\TextInput::make('mode_of_acquisition')
                                                    ->label('Mode of Acquisition')
                                                    ->required(),
                                                Forms\Components\TextInput::make('acquisition_cost')
                                                    ->label('Acquisition Cost')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Real Property')
                                    ->collapsed(),
                            ]),

                        Section::make('Personal Properties')
                            ->schema([
                                Repeater::make('personalProperties')
                                    ->relationship('personalProperties')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Description')
                                                    ->required()
                                                    ->rows(2)
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('year_acquired')
                                                    ->label('Year Acquired')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(date('Y'))
                                                    ->required(),
                                            ]),
                                        Forms\Components\TextInput::make('acquisition_cost')
                                            ->label('Acquisition Cost/Amount')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Personal Property')
                                    ->collapsed(),
                            ]),
                    ]),

                Wizard\Step::make('Liabilities')
                    ->schema([
                        Section::make('Liabilities')
                            ->schema([
                                Repeater::make('liabilities')
                                    ->relationship('liabilities')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('nature')
                                                    ->label('Nature')
                                                    ->required(),
                                                Forms\Components\TextInput::make('name_of_creditors')
                                                    ->label('Name of Creditors')
                                                    ->required(),
                                                Forms\Components\TextInput::make('outstanding_balance')
                                                    ->label('Outstanding Balance')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Liability')
                                    ->collapsed(),
                            ]),
                    ]),

                Wizard\Step::make('Business Interests & Relatives')
                    ->schema([
                        Section::make('Business Interests and Financial Connections')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Checkbox::make('has_business_interests')
                                            ->label('I/We have business interest or financial connection'),
                                        Forms\Components\Checkbox::make('no_business_interests')
                                            ->label('I/We do not have any business interest or financial connection'),
                                    ]),
                                Repeater::make('businessInterests')
                                    ->relationship('businessInterests')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name_of_entity')
                                                    ->label('Name of Entity/Business Enterprise')
                                                    ->required(),
                                                Forms\Components\Textarea::make('business_address')
                                                    ->label('Business Address')
                                                    ->required()
                                                    ->rows(2),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('nature_of_business_interest')
                                                    ->label('Nature of Business Interest &/or Financial Connection')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('date_of_acquisition')
                                                    ->label('Date of Acquisition of Interest or Connection')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->addActionLabel('Add Business Interest')
                                    ->collapsed(),
                            ]),

                        Section::make('Relatives in the Government Service')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Checkbox::make('has_relatives_in_government')
                                            ->label('I have relatives in the government service'),
                                        Forms\Components\Checkbox::make('no_relatives_in_government')
                                            ->label('I/We do not know of any relative\'s in the government service'),
                                    ]),
                                Repeater::make('relativesInGovernment')
                                    ->relationship('relativesInGovernment')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name_of_relative')
                                                    ->label('Name of Relative')
                                                    ->required(),
                                                Forms\Components\TextInput::make('relationship')
                                                    ->label('Relationship')
                                                    ->required(),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
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
                                    ->collapsed(),
                            ]),
                    ]),

                Wizard\Step::make('Signature & Verification')
                    ->schema([
                        Section::make('Signature Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_signed')
                                            ->label('Date')
                                            ->required(),
                                        Forms\Components\DatePicker::make('subscribed_sworn_date')
                                            ->label('Subscribed and Sworn Date')
                                            ->required(),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\FileUpload::make('declarant_id_presented')
                                            ->label('Declarant ID Presented')
                                            ->image()
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('1080')
                                            ->directory('saln/declarant-ids')
                                            ->visibility('private')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120), // 5MB max
                                        Forms\Components\FileUpload::make('spouse_id_presented')
                                            ->label('Spouse ID Presented')
                                            ->image()
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('1080')
                                            ->directory('saln/spouse-ids')
                                            ->visibility('private')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120), // 5MB max
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('declarant_signature')
                                            ->label('Declarant Signature')
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                // Auto-populate with declarant's full name if field is empty
                                                if (empty($state)) {
                                                    $firstName = $get('../../declarant_first_name') ?? $get('declarant_first_name');
                                                    $middleInitial = $get('../../declarant_middle_initial') ?? $get('declarant_middle_initial');
                                                    $lastName = $get('../../declarant_family_name') ?? $get('declarant_family_name');

                                                    if ($firstName && $lastName) {
                                                        $fullName = trim(
                                                            $firstName . ' ' .
                                                            ($middleInitial ? $middleInitial . ' ' : '') .
                                                            $lastName
                                                        );
                                                        $set('declarant_signature', $fullName);
                                                    }
                                                }
                                            })
                                            ->default(function (callable $get) {
                                                // Set default value when the field is first loaded
                                                $firstName = $get('../../declarant_first_name') ?? $get('declarant_first_name');
                                                $middleInitial = $get('../../declarant_middle_initial') ?? $get('declarant_middle_initial');
                                                $lastName = $get('../../declarant_family_name') ?? $get('declarant_family_name');

                                                if ($firstName && $lastName) {
                                                    return trim(
                                                        $firstName . ' ' .
                                                        ($middleInitial ? $middleInitial . ' ' : '') .
                                                        $lastName
                                                    );
                                                }
                                                return null;
                                            }),
                                        Forms\Components\TextInput::make('spouse_signature')
                                            ->label('Spouse Signature')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                // Auto-populate with spouse's full name if field is empty
                                                if (empty($state)) {
                                                    $firstName = $get('../../spouse_first_name') ?? $get('spouse_first_name');
                                                    $middleInitial = $get('../../spouse_middle_initial') ?? $get('spouse_middle_initial');
                                                    $lastName = $get('../../spouse_family_name') ?? $get('spouse_family_name');

                                                    if ($firstName && $lastName) {
                                                        $fullName = trim(
                                                            $firstName . ' ' .
                                                            ($middleInitial ? $middleInitial . ' ' : '') .
                                                            $lastName
                                                        );
                                                        $set('spouse_signature', $fullName);
                                                    }
                                                }
                                            })
                                            ->default(function (callable $get) {
                                                // Set default value when the field is first loaded
                                                $firstName = $get('../../spouse_first_name') ?? $get('spouse_first_name');
                                                $middleInitial = $get('../../spouse_middle_initial') ?? $get('spouse_middle_initial');
                                                $lastName = $get('../../spouse_family_name') ?? $get('spouse_family_name');

                                                if ($firstName && $lastName) {
                                                    return trim(
                                                        $firstName . ' ' .
                                                        ($middleInitial ? $middleInitial . ' ' : '') .
                                                        $lastName
                                                    );
                                                }
                                                return null;
                                            })
                                            ->visible(
                                                fn(callable $get) =>
                                                $get('spouse_first_name') && $get('spouse_family_name')
                                            ),
                                    ]),
                                Forms\Components\TextInput::make('person_administering_oath')
                                    ->label('Person Administering Oath')
                                    ->required(),
                            ]),

                        Section::make('Summary')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('total_assets')
                                            ->label('Total Assets')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('total_liabilities')
                                            ->label('Total Liabilities')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('net_worth')
                                            ->label('Net Worth')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->disabled(),
                                    ]),
                            ]),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // FIXED: Changed from 'user.name' to combine first_name and last_name
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->searchable(['user.first_name', 'user.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('as_of_date')
                    ->label('As of Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('declarant_family_name')
                    ->label('Declarant')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->declarant_family_name . ', ' . $record->declarant_first_name
                    )
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Filed Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(
                        fn() =>
                        Saln::selectRaw('YEAR(as_of_date) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year', 'year')
                    )
                    ->query(
                        fn(Builder $query, array $data) =>
                        $query->whereYear('as_of_date', $data['value'])
                    ),


                // FIXED: Changed the relationship display to use first_name and last_name
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Employee')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name),
                Tables\Filters\Filter::make('as_of_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('as_of_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('as_of_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Add Print Action
                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalns::route('/'),
            'create' => Pages\CreateSaln::route('/create'),
            // 'view' => Pages\ViewSaln::route('/{record}'),
            'edit' => Pages\EditSaln::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'children', 'realProperties', 'personalProperties', 'liabilities', 'businessInterests', 'relativesInGovernment']);
    }
}
