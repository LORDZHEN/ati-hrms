<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelOrderResource\Pages;
use App\Models\TravelOrder;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TravelOrderStatusUpdated;

class TravelOrderResource extends Resource
{
    protected static ?string $model = TravelOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $slug = 'travel-order';
    protected static ?string $navigationLabel = 'Travel Order';
    protected static ?string $title = 'Travel Orders';
    protected static ?string $modelLabel = 'Travel Order';
    protected static ?string $pluralModelLabel = 'Travel Order';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 6;

    protected static function generateTravelOrderNumber(): string
    {
        // Get current month and year
        $monthYear = now()->format('m-Y'); // e.g., 12-2025

        // Get the last travel order for the current month
        $lastRecord = TravelOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();

        // Determine next number
        $nextNumber = $lastRecord ? ((int) substr($lastRecord->travel_order_no, -3)) + 1 : 1;

        // Pad to 3 digits
        $number = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Return formatted Travel Order Number
        return "{$monthYear}-{$number}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- Travel Order Information Section ---
                Forms\Components\Section::make('Travel Order Information')
                    ->schema([
                        // First row: Travel Order No. | Date
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('travel_order_no')
                                    ->label('Travel Order No.')
                                    ->default(fn() => self::generateTravelOrderNumber())
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now()),
                            ]),

                        // Second row: Status | Travel Type
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Pending Review',
                                    ])
                                    ->default('pending')
                                    ->disabled()
                                    ->required(),

                                Forms\Components\Select::make('travel_type')
                                    ->label('Travel Type')
                                    ->options([
                                        'solo' => 'Solo Travel',
                                        'batch' => 'Batch Travel',
                                    ])
                                    ->default('solo')
                                    ->reactive()
                                    ->required(),
                            ]),

                        // Traveler fields (solo or batch)
                        Forms\Components\TextInput::make('solo_employee')
                            ->label('Traveler')
                            ->default(fn() => Auth::user()->full_name ?? Auth::user()->name)
                            ->disabled()
                            ->visible(fn(callable $get) => $get('travel_type') === 'solo')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('employee_ids')
                            ->label('Select Employee(s)')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn() => User::where('role', 'employee')
                                ->orWhere('id', Auth::id())
                                ->pluck('name', 'id'))
                            ->required(fn(callable $get) => $get('travel_type') === 'batch')
                            ->visible(fn(callable $get) => $get('travel_type') === 'batch')
                            ->placeholder('Search and select employee(s)')
                            ->helperText('Select employees for batch travel')
                            ->columnSpanFull(),

                        // Salary and Station row
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('salary_per_annum')
                                    ->label('Salary (Per Annum)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(fn(callable $get) => $get('travel_type') === 'solo') // only required for solo
                                    ->visible(fn(callable $get) => $get('travel_type') === 'solo') // optional: hide for batch
                                    ->helperText('Annual salary amount (average salary for multiple employees)'),

                                Forms\Components\TextInput::make('station')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Work station/office location'),
                            ]),

                        // Position
                        Forms\Components\TextInput::make('position')
                            ->label('Primary Position')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter the primary position (or common position for batch travel)')
                            ->helperText('For multiple employees, enter the most common position or "Various Positions"')
                            ->columnSpanFull(),

                        // Repeater for batch employee details
                        Forms\Components\Repeater::make('employee_details')
                            ->label('Employee Details')
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Name')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('position')
                                        ->label('Position')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('salary')
                                        ->label('Annual Salary')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->visible(false),
                                ]),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Employee Details')
                            ->helperText('Add individual details for each employee (batch only)')
                            ->minItems(0)
                            ->collapsible()
                            ->visible(fn(callable $get) => $get('travel_type') === 'batch'),

                        // Created By
                        Forms\Components\Select::make('created_by')
                            ->label('Created By')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::id())
                            ->required(),
                    ]),


                // --- Travel Details Section ---
                Forms\Components\Section::make('Travel Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('departure_date')
                                    ->label('Departure Date')
                                    ->required()
                                    ->minDate(now()),

                                Forms\Components\DatePicker::make('return_date')
                                    ->label('Return Date')
                                    ->required()
                                    ->after('departure_date'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('report_to')
                                    ->label('Report To')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('destination')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Textarea::make('purpose_of_trip')
                            ->label('Purpose of Trip')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Please provide detailed information about the purpose of your travel...'),
                    ]),

                // --- Approval Status Section ---
                Forms\Components\Section::make('Approval Status')
                    ->description('This section shows the approval status of your travel order')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('recommended_by_assistant_director')
                                    ->label('Recommended by Assistant Director'),

                                Forms\Components\Toggle::make('approved_by_center_director')
                                    ->label('Approved by Center Director'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('recommended_at')
                                    ->label('Recommended At'),

                                Forms\Components\DateTimePicker::make('approved_at')
                                    ->label('Approved At'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('recommended_by')
                                    ->label('Recommended By')
                                    ->relationship('recommender', 'name'),

                                Forms\Components\Select::make('approved_by')
                                    ->label('Approved By')
                                    ->relationship('approver', 'name'),
                            ]),
                    ])
                    ->visible(fn() => Auth::user()->role === 'admin'), // <-- only admins


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('travel_order_no')
                    ->label('Order No.')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                // Updated Traveler(s) column with full batch employee names
                Tables\Columns\TextColumn::make('name')
                    ->label('Traveler(s)')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        // Use the 'name' column directly
                        if (empty($record->name)) {
                            return 'Not specified';
                        }

                        // Limit display to 3 names + "more"
                        $names = explode(', ', $record->name);
                        $count = count($names);
                        if ($count <= 3) {
                            return $record->name;
                        }

                        $firstThree = array_slice($names, 0, 3);
                        return implode(', ', $firstThree) . " + " . ($count - 3) . " more";
                    }),

                Tables\Columns\BadgeColumn::make('employee_count')
                    ->label('Type')
                    ->state(fn($record) => $record->employee_ids && is_array($record->employee_ids) ? (count($record->employee_ids) > 1 ? 'batch' : 'solo') : 'solo')
                    ->colors([
                        'primary' => 'solo',
                        'success' => 'batch',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'solo' => 'Solo',
                        'batch' => 'Batch',
                        default => 'Solo',
                    }),

                Tables\Columns\TextColumn::make('destination')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('departure_date')
                    ->label('Departure')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Return')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'info' => 'recommended',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'recommended' => 'Recommended',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('recommended_by_assistant_director')
                    ->label('Recommended')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('approved_by_center_director')
                    ->label('Approved')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending Review',
                        'recommended' => 'Recommended',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\SelectFilter::make('employee_count')
                    ->label('Travel Type')
                    ->options([
                        'solo' => 'Solo Travel',
                        'batch' => 'Batch Travel',
                    ])
                    ->query(fn(Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'solo' => $query->whereRaw('JSON_LENGTH(employee_ids) = 1 OR employee_ids IS NULL'),
                        'batch' => $query->whereRaw('JSON_LENGTH(employee_ids) > 1'),
                        default => $query,
                    }),

                // Tables\Filters\Filter::make('my_orders')
                //     ->label('My Orders')
                //     ->query(fn(Builder $query): Builder => $query->where('created_by', Auth::id()))
                //     ->default(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        in_array($record->status, ['draft', 'rejected'])
                    ),

                Tables\Actions\Action::make('submit')
                    ->label('Submit for Review')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('info')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'draft'
                    )
                    ->requiresConfirmation()
                    ->action(fn(TravelOrder $record) => $record->update(['status' => 'pending']))
                    ->after(
                        fn() =>
                        Notification::make()->title('Travel order submitted')->success()->send()
                    ),

                Tables\Actions\Action::make('withdraw')
                    ->label('Withdraw')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('warning')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(fn(TravelOrder $record) => $record->update(['status' => 'draft'])),

                Tables\Actions\DeleteAction::make()
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'draft'
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('travel-order.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn(TravelOrder $record) => $record->status === 'approved'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::check()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelOrders::route('/'),
            'create' => Pages\CreateTravelOrder::route('/create'),
            'edit' => Pages\EditTravelOrder::route('/{record}/edit'),
            'view' => Pages\ViewTravelOrder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = TravelOrder::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }


    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = TravelOrder::where('status', 'pending')->count();

        return $count > 0 ? 'warning' : 'success';
    }


    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('created_by', Auth::id());
    }

}
