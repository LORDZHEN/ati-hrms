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
    protected static ?string $slug = 'saln';
    protected static ?string $navigationGroup = 'Manage';
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
                                            ->directory('saln/declarant-ids')
                                            ->visibility('private')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120),
                                        Forms\Components\FileUpload::make('spouse_id_presented')
                                            ->label('Spouse ID Presented')
                                            ->image()
                                            ->directory('saln/spouse-ids')
                                            ->visibility('private')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120),
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
                        // Admin Remarks: Only editable by Admin
                        Section::make('Admin Remarks')
                            ->schema([
                                Forms\Components\Textarea::make('remarks')
                                    ->label('Admin Remarks')
                                    ->visible(fn() => auth()->user()->hasRole('admin'))
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Admin Remarks')
                    ->wrap()
                    ->limit(50)
                    ->toggleable()
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
                    ->options(fn() => Saln::selectRaw('YEAR(as_of_date) as year')
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year', 'year'))
                    ->query(fn(Builder $query, array $data) => $query->whereYear('as_of_date', $data['value'])),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Employee')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name),
                Tables\Filters\Filter::make('as_of_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('as_of_date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('as_of_date', '<=', $date))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // Print button
                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab(),

                // Admin-only: Add/Edit Remarks
                Tables\Actions\Action::make('remarks')
                    ->label('Add/Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->action(function (Saln $record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);
                    })
                    ->form([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->required(),
                    ])
                    ->visible(fn() => auth()->user()->hasRole('admin')),
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
            'edit' => Pages\EditSaln::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'children', 'realProperties', 'personalProperties', 'liabilities', 'businessInterests', 'relativesInGovernment']);

        if (!(auth()->user()?->is_admin ?? false)) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    // Show badge on navigation for SALNs without admin remarks
    public static function getNavigationBadge(): ?string
    {
        if (!(auth()->user()?->is_admin ?? false)) {
            return null; // No badge for non-admin users
        }
        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? (string) $count : null;
    }

    // Optional: change badge color dynamically
    public static function getNavigationBadgeColor(): ?string
    {
        if (!(auth()->user()?->is_admin ?? false)) {
            return null; // No badge for non-admin users
        }
        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? 'warning' : 'success';
    }


}
