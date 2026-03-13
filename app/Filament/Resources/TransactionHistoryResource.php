<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionHistoryResource\Pages;
use App\Models\TransactionHistory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;

class TransactionHistoryResource extends Resource
{
    protected static ?string $model = TransactionHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Transaction History';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'transaction-histories';

    protected static ?string $modelLabel = 'Transaction';
    protected static ?string $pluralModelLabel = 'Transaction History';
    protected static ?string $recordTitleAttribute = 'transaction_type';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('user')->latest();

                if (Auth::check() && !Auth::user()->isAdmin()) {
                    $query->where('user_id', Auth::id());
                }

                return $query;
            })

            ->columns([
                TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(TransactionHistory $r) => $r->user?->email ?? '')
                    ->icon('heroicon-o-user-circle'),

                TextColumn::make('transaction_type')
                    ->label('Transaction')
                    ->searchable()
                    ->sortable()
                    ->description(fn(TransactionHistory $r) => $r->module)
                    ->icon(fn(TransactionHistory $r) => $r->resolved_icon),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn(TransactionHistory $r) => $r->description),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->color(fn(TransactionHistory $r) => TransactionHistory::statusColor($r->status)),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y  g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn(TransactionHistory $r) => $r->created_at->setTimezone('Asia/Manila')->format('F j, Y  g:i:s A')),
            ])

            ->filters([
                SelectFilter::make('module')
                    ->label('Module')
                    ->options([
                        'Leave' => 'Leave Applications',
                        'Travel' => 'Travel Orders',
                        'Locator' => 'Locator Slips',
                        'SALN' => 'SALN',
                        'PDS' => 'Personal Data Sheet',
                        'Employee' => 'Employee Registration',
                        'DTR' => 'Daily Time Record',
                    ])
                    ->placeholder('All Modules'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'filed' => 'Filed',
                        'uploaded' => 'Uploaded',
                        'registered' => 'Registered',
                        'submitted' => 'Submitted',
                        'cancelled' => 'Cancelled',
                    ])
                    ->placeholder('All Statuses'),

                Filter::make('created_at')
                    ->label('Date Range')
                    ->form([
                        DatePicker::make('from')->label('From')->native(false),
                        DatePicker::make('until')->label('Until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])

            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
            ])

            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateIcon(null)
            ->emptyStateHeading('No Transactions Logged')
            ->emptyStateDescription('Activities will appear here as employees use the HRMS modules.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionHistories::route('/'),
            'view' => Pages\ViewTransactionHistory::route('/{record}'),
        ];
    }

    // ── Access Control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, [
            \App\Models\User::ROLE_ADMIN,
            \App\Models\User::ROLE_REGULAR,
            \App\Models\User::ROLE_JOB_ORDER,
        ]);
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, [
            \App\Models\User::ROLE_ADMIN,
            \App\Models\User::ROLE_REGULAR,
            \App\Models\User::ROLE_JOB_ORDER,
        ]);
    }

    public static function canView($record): bool
    {
        if (!Auth::check())
            return false;
        if (Auth::user()->isAdmin())
            return true;

        return $record->user_id === Auth::id();
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {
        return false;
    }
    public static function canDeleteAny(): bool
    {
        return false;
    }

    // ── Navigation Badge ──────────────────────────────────────────────────────

    public static function getNavigationBadge(): ?string
    {
        if (!Auth::check())
            return null;

        $query = TransactionHistory::whereDate('created_at', today());

        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }
}
