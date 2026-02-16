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
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TravelOrderResource extends Resource
{
    protected static ?string $model = TravelOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $slug = 'travel-orders';
    protected static ?string $navigationLabel = 'Travel Orders';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 6;

    /* ============================================================
       FORM DEFINITION
       ============================================================ */

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::buildTravelOrderInfoSection(),
            self::buildTravelDetailsSection(),
            self::buildApprovalSection(),
        ]);
    }

    /* ============================================================
       FORM SECTIONS
       ============================================================ */

    protected static function buildTravelOrderInfoSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Travel Order Information')
            ->description('Basic information about this travel order')
            ->icon('heroicon-o-document-text')
            ->schema([
                // Header Row: Order Number, Date, Status
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('travel_order_no')
                        ->label('Travel Order No.')
                        ->default('Auto-generated')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefixIcon('heroicon-o-hashtag')
                        ->helperText('Will be generated automatically upon creation')
                        ->columnSpan(1),

                    Forms\Components\DatePicker::make('date')
                        ->label('Date Issued')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->prefixIcon('heroicon-o-calendar')
                        ->columnSpan(1),

                    Forms\Components\Select::make('status')
                        ->label('Current Status')
                        ->options([
                            'draft' => 'Draft',
                            'pending' => 'Pending Review',
                        ])
                        ->default('pending')
                        ->disabled()
                        ->required()
                        ->prefixIcon('heroicon-o-information-circle')
                        ->columnSpan(1),
                ]),

                // Visual Divider
                Forms\Components\Placeholder::make('')
                    ->content('')
                    ->columnSpanFull(),

                // Travel Type Selection - Prominent
                Forms\Components\Radio::make('travel_type')
                    ->label('Travel Type')
                    ->options([
                        'solo' => 'Solo Travel - Single Employee',
                        'batch' => 'Batch Travel - Multiple Employees',
                    ])
                    ->descriptions([
                        'solo' => 'Travel order for one employee only',
                        'batch' => 'Travel order for multiple employees',
                    ])
                    ->default('solo')
                    ->live()
                    ->required()
                    ->inline()
                    ->columnSpanFull(),

                // ============================================================
                // SOLO TRAVEL SECTION
                // ============================================================
                Forms\Components\Group::make([
                    // Traveler Information Card
                    Forms\Components\Section::make('Traveler Information')
                        ->description('Your personal travel details')
                        ->icon('heroicon-o-user-circle')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Placeholder::make('solo_name')
                                    ->label('Employee Name')
                                    ->content(fn() => Auth::user()->name ?? 'N/A')
                                    ->columnSpan(2),

                                Forms\Components\Placeholder::make('solo_position')
                                    ->label('Position')
                                    ->content(fn() => Auth::user()->position ?? 'N/A')
                                    ->columnSpan(1),
                            ]),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('station')
                                    ->label('Work Station/Office')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Main Office, Branch Office')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('salary_per_annum')
                                    ->label('Annual Salary')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required()
                                    ->placeholder('0.00')
                                    ->helperText('Enter your annual salary')
                                    ->columnSpan(1),
                            ]),
                        ])
                        ->compact()
                        ->columnSpanFull(),
                ])
                    ->visible(fn(Forms\Get $get) => $get('travel_type') === 'solo'),

                // ============================================================
                // BATCH TRAVEL SECTION
                // ============================================================
                Forms\Components\Group::make([
                    // Employee Selection
                    Forms\Components\Section::make('Select Employees')
                        ->description('Choose employees for this batch travel')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Forms\Components\Select::make('employee_ids')
                                ->label('Search & Select Employees')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->options(fn() => User::where('role', 'employee')
                                    ->orWhere('id', Auth::id())
                                    ->pluck('name', 'id'))
                                ->required()
                                ->placeholder('Type to search for employees...')
                                ->helperText('You can select multiple employees for this travel order. They will be able to view this travel order in their "Tagged Travel Orders" tab.')
                                ->native(false)
                                ->columnSpanFull(),
                        ])
                        ->compact()
                        ->columnSpanFull(),

                    // Common Information
                    Forms\Components\Section::make('Common Information')
                        ->description('Details applicable to all travelers')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('station')
                                    ->label('Work Station/Office')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Main Office, Branch Office')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->helperText('Common station for all travelers')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('position')
                                    ->label('Position/Designation')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Administrative Officer II')
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->helperText('Enter common position or "Various Positions"')
                                    ->columnSpan(1),
                            ]),
                        ])
                        ->compact()
                        ->columnSpanFull(),
                ])
                    ->visible(fn(Forms\Get $get) => $get('travel_type') === 'batch'),

                // Hidden Fields
                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => Auth::id()),
            ])
            ->collapsible()
            ->collapsed(false)
            ->columns(1);
    }

    protected static function buildTravelDetailsSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Travel Itinerary & Purpose')
            ->description('Specify travel dates, destination, and purpose')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('departure_date')
                        ->label('Departure Date')
                        ->required()
                        ->native(false)
                        ->minDate(now())
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            if ($state && $get('return_date')) {
                                $departure = Carbon::parse($state);
                                $return = Carbon::parse($get('return_date'));
                                if ($return->lessThan($departure)) {
                                    $set('return_date', null);
                                }
                            }
                        }),

                    Forms\Components\DatePicker::make('return_date')
                        ->label('Return Date')
                        ->required()
                        ->native(false)
                        ->minDate(fn(Forms\Get $get) => $get('departure_date') ?: now())
                        ->live(),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('destination')
                        ->label('Destination')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Manila City, Cebu Province')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('report_to')
                        ->label('Report To (Authority/Office)')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Regional Director, Main Office')
                        ->columnSpan(1),
                ]),

                Forms\Components\Textarea::make('purpose_of_trip')
                    ->label('Purpose of Travel')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->placeholder('Provide detailed information about the purpose, activities, and expected outcomes of this travel...')
                    ->helperText('Be specific and comprehensive in describing the travel purpose'),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    protected static function buildApprovalSection(): Forms\Components\Section
    {
        $isAdmin = Auth::user()->role === 'admin';

        return Forms\Components\Section::make('Approval & Authorization')
            ->description('Administrative approval and authorization details')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('recommended_by_assistant_director')
                        ->label('Recommended by Assistant Director')
                        ->inline(false)
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('approved_by_center_director')
                        ->label('Approved by Center Director')
                        ->inline(false)
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->columnSpan(1),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('recommended_by')
                        ->label('Recommended By (Name)')
                        ->relationship('recommender', 'name')
                        ->searchable()
                        ->preload()
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->placeholder('Select recommender')
                        ->columnSpan(1),

                    Forms\Components\Select::make('approved_by')
                        ->label('Approved By (Name)')
                        ->relationship('approver', 'name')
                        ->searchable()
                        ->preload()
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->placeholder('Select approver')
                        ->columnSpan(1),
                ]),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('recommended_at')
                        ->label('Recommendation Date & Time')
                        ->native(false)
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->columnSpan(1),

                    Forms\Components\DateTimePicker::make('approved_at')
                        ->label('Approval Date & Time')
                        ->native(false)
                        ->disabled(!$isAdmin)
                        ->dehydrated($isAdmin)
                        ->columnSpan(1),
                ]),
            ])
            ->visible(fn() => Auth::user()->role === 'admin')
            ->collapsible()
            ->collapsed(true); // Changed to false so it's open by default for admins
    }

    /* ============================================================
       TABLE DEFINITION - WITH TABS FOR MY ORDERS & TAGGED ORDERS
       ============================================================ */

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTimelineTableColumns())
            ->filters(self::getEnhancedFilters())
            ->actions(self::getContextualActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No travel orders found')
            ->emptyStateDescription('Create your first travel order to get started.')
            ->emptyStateIcon('heroicon-o-map')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Travel Order')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    /* ============================================================
       TIMELINE-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getTimelineTableColumns(): array
    {
        return [
            // Order Number & Type Badge
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\TextColumn::make('travel_order_no')
                    ->label('Order No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-o-hashtag')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Order number copied!')
                    ->copyMessageDuration(1500),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\BadgeColumn::make('travel_type_badge')
                        ->label('Type')
                        ->state(fn($record) => $record->employee_ids && is_array($record->employee_ids) ?
                            (count($record->employee_ids) > 1 ? 'batch' : 'solo') : 'solo')
                        ->colors([
                            'primary' => 'solo',
                            'success' => 'batch',
                        ])
                        ->icons([
                            'heroicon-o-user' => 'solo',
                            'heroicon-o-user-group' => 'batch',
                        ])
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'solo' => 'Solo',
                            'batch' => 'Batch',
                            default => 'Solo',
                        }),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Issued')
                        ->date('M d, Y')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-document-text')
                        ->iconColor('gray'),
                ]),
            ])->space(1),

            // Main Content - Traveler & Destination
            Tables\Columns\Layout\Split::make([
                // Left: Traveler Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Traveler(s)')
                        ->searchable()
                        ->weight('medium')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('info')
                        ->formatStateUsing(function ($state, $record) {
                            if (empty($record->name)) {
                                return 'Not specified';
                            }

                            $names = explode(', ', $record->name);
                            $count = count($names);

                            if ($count <= 2) {
                                return $record->name;
                            }

                            $firstTwo = array_slice($names, 0, 2);
                            return implode(', ', $firstTwo) . " +" . ($count - 2) . " more";
                        })
                        ->tooltip(fn($record) => $record->name)
                        ->limit(50),

                    Tables\Columns\TextColumn::make('position')
                        ->label('Position')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-briefcase')
                        ->iconColor('gray')
                        ->limit(40),

                    Tables\Columns\TextColumn::make('station')
                        ->label('Station')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-building-office')
                        ->iconColor('gray')
                        ->limit(30),
                ])->space(1),

                // Right: Travel Details
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('destination')
                        ->label('Destination')
                        ->searchable()
                        ->weight('medium')
                        ->icon('heroicon-o-map-pin')
                        ->iconColor('danger')
                        ->limit(40)
                        ->tooltip(fn($record) => $record->destination),

                    Tables\Columns\TextColumn::make('travel_period')
                        ->label('Travel Period')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-calendar-days')
                        ->iconColor('warning')
                        ->formatStateUsing(
                            fn($record) =>
                            Carbon::parse($record->departure_date)->format('M d') .
                            ' - ' .
                            Carbon::parse($record->return_date)->format('M d, Y')
                        ),

                    Tables\Columns\TextColumn::make('duration')
                        ->label('Duration')
                        ->size('sm')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(
                            fn($record) =>
                            Carbon::parse($record->departure_date)
                                ->diffInDays(Carbon::parse($record->return_date)) + 1 . ' days'
                        ),
                ])->space(1)->alignment('end'),
            ])->from('md'),

            // Status & Approval Timeline
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    // Status Badge
                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'secondary' => 'draft',
                            'warning' => 'pending',
                            'info' => 'recommended',
                            'success' => 'approved',
                            'danger' => 'rejected',
                        ])
                        ->icons([
                            'heroicon-o-pencil' => 'draft',
                            'heroicon-o-clock' => 'pending',
                            'heroicon-o-hand-thumb-up' => 'recommended',
                            'heroicon-o-check-badge' => 'approved',
                            'heroicon-o-x-circle' => 'rejected',
                        ])
                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                        ->size('md'),

                    // Approval Progress Indicators
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('approval_progress')
                            ->label('Approval Progress')
                            ->formatStateUsing(function ($record) {
                                $steps = [];

                                if ($record->recommended_by_assistant_director) {
                                    $recommender = $record->recommender?->name ?? 'Assistant Director';
                                    $steps[] = '✓ Recommended by ' . $recommender;
                                }

                                if ($record->approved_by_center_director) {
                                    $approver = $record->approver?->name ?? 'Center Director';
                                    $steps[] = '✓ Approved by ' . $approver;
                                }

                                if (empty($steps)) {
                                    return '⏳ Awaiting review';
                                }

                                return implode(' • ', $steps);
                            })
                            ->size('sm')
                            ->color('gray'),
                    ]),

                    // Creator Info
                    Tables\Columns\TextColumn::make('creator.name')
                        ->label('Created By')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-user')
                        ->iconColor('gray')
                        ->limit(20),
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
                    'draft' => 'Draft',
                    'pending' => 'Pending Review',
                    'recommended' => 'Recommended',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->native(false)
                ->indicator('Status'),

            Tables\Filters\SelectFilter::make('travel_type')
                ->label('Travel Type')
                ->options([
                    'solo' => 'Solo Travel',
                    'batch' => 'Batch Travel',
                ])
                ->query(fn(Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                    'solo' => $query->whereRaw('JSON_LENGTH(employee_ids) = 1 OR employee_ids IS NULL'),
                    'batch' => $query->whereRaw('JSON_LENGTH(employee_ids) > 1'),
                    default => $query,
                })
                ->native(false)
                ->indicator('Type'),

            Tables\Filters\Filter::make('departure_period')
                ->form([
                    Forms\Components\DatePicker::make('departure_from')
                        ->label('Departure From')
                        ->native(false),
                    Forms\Components\DatePicker::make('departure_until')
                        ->label('Departure Until')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['departure_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('departure_date', '>=', $date),
                        )
                        ->when(
                            $data['departure_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('departure_date', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['departure_from'] ?? null) {
                        $indicators['from'] = 'Departing from ' . Carbon::parse($data['departure_from'])->toFormattedDateString();
                    }

                    if ($data['departure_until'] ?? null) {
                        $indicators['until'] = 'Departing until ' . Carbon::parse($data['departure_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),

            Tables\Filters\TernaryFilter::make('recommended_by_assistant_director')
                ->label('Recommended')
                ->placeholder('All orders')
                ->trueLabel('Recommended orders')
                ->falseLabel('Not recommended')
                ->indicator('Recommendation'),

            Tables\Filters\TernaryFilter::make('approved_by_center_director')
                ->label('Approved')
                ->placeholder('All orders')
                ->trueLabel('Approved orders')
                ->falseLabel('Not approved')
                ->indicator('Approval'),
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
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        in_array($record->status, ['draft', 'rejected'])
                    ),

                Tables\Actions\Action::make('submit')
                    ->label('Submit for Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'draft'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Submit Travel Order for Review')
                    ->modalDescription('Are you sure you want to submit this travel order? It will be sent to administrators for review.')
                    ->action(fn(TravelOrder $record) => $record->update(['status' => 'pending']))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Travel Order Submitted')
                            ->body('Your travel order has been submitted for review.')
                    ),

                Tables\Actions\Action::make('withdraw')
                    ->label('Withdraw')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Withdraw Travel Order')
                    ->modalDescription('This will return the travel order to draft status.')
                    ->action(fn(TravelOrder $record) => $record->update(['status' => 'draft']))
                    ->successNotification(
                        Notification::make()
                            ->warning()
                            ->title('Travel Order Withdrawn')
                            ->body('Your travel order has been returned to draft status.')
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('travel-order.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn(TravelOrder $record) => $record->status === 'approved'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $record->created_by === Auth::id() &&
                        $record->status === 'draft'
                    ),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    /* ============================================================
       HELPER METHODS
       ============================================================ */

    protected static function generateTravelOrderNumber(): string
    {
        $monthYear = now()->format('m-Y');

        $lastRecord = TravelOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();

        $nextNumber = $lastRecord ? ((int) substr($lastRecord->travel_order_no, -3)) + 1 : 1;
        $number = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return "{$monthYear}-{$number}";
    }

    /* ============================================================
       RESOURCE CONFIGURATION
       ============================================================ */

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
        return auth()->user()?->role === 'admin' &&
            TravelOrder::where('status', 'pending')->count() > 0
            ? 'warning'
            : null;
    }

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('created_by', Auth::id());
    }
}
