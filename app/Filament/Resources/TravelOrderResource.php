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
use App\Notifications\TravelOrderStatusUpdated;

class TravelOrderResource extends Resource
{
    protected static ?string $model = TravelOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $slug = 'travel-orders';
    protected static ?string $navigationLabel = 'Travel Orders';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 6;

    /* ============================================================
       AUTHORIZATION
       ============================================================ */

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'employee';
    }

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
                            'pending' => 'Pending Review',
                        ])
                        ->default('pending')
                        ->disabled()
                        ->required()
                        ->prefixIcon('heroicon-o-information-circle')
                        ->columnSpan(1),
                ]),

                Forms\Components\Placeholder::make('')->content('')->columnSpanFull(),

                Forms\Components\Radio::make('travel_type')
                    ->label('Travel Type')
                    ->options([
                        'solo'  => 'Solo Travel - Single Employee',
                        'batch' => 'Batch Travel - Multiple Employees',
                    ])
                    ->descriptions([
                        'solo'  => 'Travel order for one employee only',
                        'batch' => 'Travel order for multiple employees',
                    ])
                    ->default('solo')
                    ->live()
                    ->required()
                    ->inline()
                    ->columnSpanFull(),

                // SOLO TRAVEL SECTION
                Forms\Components\Group::make([
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
                ])->visible(fn(Forms\Get $get) => $get('travel_type') === 'solo'),

                // BATCH TRAVEL SECTION
                Forms\Components\Group::make([
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
                ])->visible(fn(Forms\Get $get) => $get('travel_type') === 'batch'),

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
                                $return    = Carbon::parse($get('return_date'));
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
            ->collapsed(true);
    }

    /* ============================================================
       TABLE DEFINITION
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
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->role === 'employee'),
            ]);
    }

    /* ============================================================
       CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getTimelineTableColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([

                // LEFT: Order number, traveler, position, type badge
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

                    Tables\Columns\TextColumn::make('name')
                        ->label('Traveler(s)')
                        ->searchable()
                        ->weight('medium')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('info')
                        ->formatStateUsing(function ($state, $record) {
                            if (empty($record->name)) return 'Not specified';
                            $names = explode(', ', $record->name);
                            $count = count($names);
                            if ($count <= 2) return $record->name;
                            return implode(', ', array_slice($names, 0, 2)) . ' +' . ($count - 2) . ' more';
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

                    Tables\Columns\BadgeColumn::make('travel_type_badge')
                        ->label('Type')
                        ->state(fn($record) => $record->employee_ids && is_array($record->employee_ids) ?
                            (count($record->employee_ids) > 1 ? 'batch' : 'solo') : 'solo')
                        ->colors(['primary' => 'solo', 'success' => 'batch'])
                        ->icons(['heroicon-o-user' => 'solo', 'heroicon-o-user-group' => 'batch'])
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'solo'  => 'Solo',
                            'batch' => 'Batch',
                            default => 'Solo',
                        }),
                ])->space(1),

                // MIDDLE: Destination, travel period, duration, issued date
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
                        ->formatStateUsing(fn($record) =>
                            Carbon::parse($record->departure_date)->format('M d') .
                            ' - ' .
                            Carbon::parse($record->return_date)->format('M d, Y')
                        ),

                    Tables\Columns\TextColumn::make('duration')
                        ->label('Duration')
                        ->size('sm')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn($record) =>
                            Carbon::parse($record->departure_date)
                                ->diffInDays(Carbon::parse($record->return_date)) + 1 . ' days'
                        ),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Issued')
                        ->date('M d, Y')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-document-text')
                        ->iconColor('gray'),
                ])->space(1),

                // RIGHT: Status badge, creator
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'warning' => 'pending',
                            'info'    => 'recommended',
                            'success' => 'approved',
                            'danger'  => 'rejected',
                        ])
                        ->icons([
                            'heroicon-o-clock'         => 'pending',
                            'heroicon-o-hand-thumb-up' => 'recommended',
                            'heroicon-o-check-badge'   => 'approved',
                            'heroicon-o-x-circle'      => 'rejected',
                        ])
                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                        ->size('md'),

                    Tables\Columns\TextColumn::make('creator.name')
                        ->label('Created By')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-user')
                        ->iconColor('gray')
                        ->limit(20),
                ])->space(2)->alignment('end'),

            ])->from('md'),

            // PANEL: Approval progress + rejection remark (collapsible)
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('approval_progress')
                        ->label('Approval Progress')
                        ->formatStateUsing(function ($record) {
                            $steps = [];
                            if ($record->recommended_by_assistant_director) {
                                $recommender = $record->recommender?->name ?? 'Assistant Director';
                                $steps[]     = '✓ Recommended by ' . $recommender;
                            }
                            if ($record->approved_by_center_director) {
                                $approver = $record->approver?->name ?? 'Center Director';
                                $steps[]  = '✓ Approved by ' . $approver;
                            }
                            // Uses the dedicated rejection_remark column
                            if ($record->status === 'rejected' && $record->rejection_remark) {
                                $steps[] = '✗ Remark: ' . $record->rejection_remark;
                            }
                            if (empty($steps)) return '⏳ Awaiting review';
                            return implode(' • ', $steps);
                        })
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('station')
                        ->label('Station')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-building-office')
                        ->iconColor('gray')
                        ->limit(30),
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
                    'pending'     => 'Pending Review',
                    'recommended' => 'Recommended',
                    'approved'    => 'Approved',
                    'rejected'    => 'Rejected',
                ])
                ->native(false)
                ->indicator('Status'),

            Tables\Filters\SelectFilter::make('travel_type')
                ->label('Travel Type')
                ->options(['solo' => 'Solo Travel', 'batch' => 'Batch Travel'])
                ->query(fn(Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                    'solo'  => $query->whereRaw('JSON_LENGTH(employee_ids) = 1 OR employee_ids IS NULL'),
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
                        ->when($data['departure_from'], fn(Builder $query, $date) => $query->whereDate('departure_date', '>=', $date))
                        ->when($data['departure_until'], fn(Builder $query, $date) => $query->whereDate('departure_date', '<=', $date));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['departure_from'] ?? null) $indicators['from']  = 'Departing from ' . Carbon::parse($data['departure_from'])->toFormattedDateString();
                    if ($data['departure_until'] ?? null) $indicators['until'] = 'Departing until ' . Carbon::parse($data['departure_until'])->toFormattedDateString();
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
       ============================================================
       Employee action visibility per status:
         pending   → View
         approved  → View, Print
         rejected  → View, Remarks, Delete
       Admin action visibility:
         any       → View, Print (approved), Delete
    */

    protected static function getContextualActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([

                // ── VIEW (all roles, all statuses) ────────────────────
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                // ── PRINT (approved orders only) ──────────────────────
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(TravelOrder $record) => route('travel-order.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn(TravelOrder $record) => $record->status === 'approved'),

                // ── REMARKS (rejected orders, employee only) ──────────
                // Read-only modal displaying the dedicated rejection_remark column.
                Tables\Actions\Action::make('remarks')
                    ->label('Remarks')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('danger')
                    ->modalHeading('Rejection Remarks')
                    ->modalDescription('The following reason was provided by the administrator for rejecting this travel order.')
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->form([
                        Forms\Components\Placeholder::make('rejection_remark')
                            ->label('Reason for Rejection')
                            ->content(fn(TravelOrder $record) =>
                                $record->rejection_remark ?? 'No remarks provided.'
                            ),
                    ])
                    ->visible(fn(TravelOrder $record) =>
                        Auth::user()->role === 'employee' &&
                        $record->status === 'rejected'
                    ),

                // ── EMPLOYEE: Delete own rejected orders ───────────────
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->visible(fn(TravelOrder $record) =>
                        Auth::user()->role === 'employee' &&
                        $record->created_by === Auth::id() &&
                        $record->status === 'rejected'
                    ),

                // ── ADMIN: Delete any record ───────────────────────────
                Tables\Actions\DeleteAction::make('adminDelete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->visible(fn(TravelOrder $record) =>
                        Auth::user()->role === 'admin'
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
        $monthYear  = now()->format('m-Y');
        $lastRecord = TravelOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()
            ->first();
        $nextNumber = $lastRecord ? ((int) substr($lastRecord->travel_order_no, -3)) + 1 : 1;
        $number     = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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
            'index'  => Pages\ListTravelOrders::route('/'),
            'create' => Pages\CreateTravelOrder::route('/create'),
            'edit'   => Pages\EditTravelOrder::route('/{record}/edit'),
            'view'   => Pages\ViewTravelOrder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') return null;
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
