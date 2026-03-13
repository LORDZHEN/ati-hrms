<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?string $slug = 'employees';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $modelLabel = 'Employee';
    protected static ?string $pluralModelLabel = 'Employees';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    /* ============================================================
       NAVIGATION
       ============================================================ */

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = self::getPendingCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return self::getPendingCount() > 0 ? 'warning' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $count = self::getPendingCount();
        return $count > 0 ? "{$count} pending approval" : null;
    }

    /* ============================================================
       FORM (Edit only — no create)
       ============================================================ */

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Personal Information')
                ->description("Update the employee's personal details")
                ->icon('heroicon-o-user')
                ->collapsible()
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('first_name')->label('First Name')->required()->maxLength(255)->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set, callable $get) => self::syncFullName($set, $get)),
                        TextInput::make('middle_name')->label('Middle Name')->maxLength(255)->placeholder('Optional')->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set, callable $get) => self::syncFullName($set, $get)),
                        TextInput::make('last_name')->label('Last Name')->required()->maxLength(255)->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set, callable $get) => self::syncFullName($set, $get)),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('email')->label('Email Address')->email()->required()->maxLength(255)->unique(ignoreRecord: true)->prefixIcon('heroicon-o-envelope'),
                        DatePicker::make('birthday')->label('Date of Birth')->required()->maxDate(now()->subYears(18))->native(false)->displayFormat('F d, Y')->prefixIcon('heroicon-o-cake'),
                    ]),
                    Placeholder::make('full_name_preview')->label('Full Name Preview')
                        ->content(fn(callable $get) => self::buildFullName($get) ?: 'Enter names above to see preview'),
                    TextInput::make('name')->hidden()->dehydrated()->default(fn(callable $get) => self::buildFullName($get)),
                ]),

            Section::make('Employment Details')
                ->description('Update employment and system access')
                ->icon('heroicon-o-briefcase')
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('employee_id')->label('Employee ID')->required()->maxLength(50)->unique(ignoreRecord: true)->prefixIcon('heroicon-o-identification'),
                        Select::make('status')->label('Account Status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()->native(false)->prefixIcon('heroicon-o-shield-check'),
                    ]),
                    Select::make('role')->label('System Role')
                        ->options([
                            User::ROLE_ADMIN => 'Administrator',
                            User::ROLE_REGULAR => 'Regular Employee',
                            User::ROLE_JOB_ORDER => 'Job Order',
                        ])
                        ->required()->native(false)
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false)
                        ->prefixIcon('heroicon-o-key')
                        ->helperText('Determines system access level'),
                ]),
        ]);
    }

    /* ============================================================
       TABLE
       ============================================================ */

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getCardLayoutColumns())
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->multiple()->native(false)->preload(),

                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        User::ROLE_REGULAR => 'Regular Employee',
                        User::ROLE_JOB_ORDER => 'Job Order',
                        User::ROLE_ADMIN => 'Administrator',
                    ])
                    ->multiple()->native(false)->preload(),

                TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->placeholder('All employees')
                    ->trueLabel('Verified only')->falseLabel('Unverified only')
                    ->queries(
                        true: fn(Builder $q) => $q->whereNotNull('email_verified_at'),
                        false: fn(Builder $q) => $q->whereNull('email_verified_at'),
                    )
                    ->native(false),

                Filter::make('pending_approval')
                    ->label('Pending Approval')
                    ->query(fn(Builder $q) => $q->where('status', 'pending'))
                    ->toggle(),

                Filter::make('recent')
                    ->label('Recently Added (7 days)')
                    ->query(fn(Builder $q) => $q->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Filter::make('age_range')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('age_from')->label('Min Age')->numeric()->placeholder('18'),
                            TextInput::make('age_to')->label('Max Age')->numeric()->placeholder('65'),
                        ]),
                    ])
                    ->query(
                        fn(Builder $q, array $data) => $q
                            ->when($data['age_from'], fn($q, $age) => $q->where('birthday', '<=', now()->subYears((int) $age)->endOfDay()))
                            ->when($data['age_to'], fn($q, $age) => $q->where('birthday', '>=', now()->subYears((int) $age)->startOfDay()))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['age_from'] ?? null)
                            $indicators[] = 'Age from: ' . $data['age_from'];
                        if ($data['age_to'] ?? null)
                            $indicators[] = 'Age to: ' . $data['age_to'];
                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('View Details')->icon('heroicon-o-eye')->color('info'),
                    Tables\Actions\EditAction::make()->label('Edit')->icon('heroicon-o-pencil')->color('warning')
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')->icon('heroicon-o-key')->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false)
                        ->action(fn() => Notification::make()->title('Password reset email sent')->success()->send()),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical')->button()->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')->icon('heroicon-o-check-circle')->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')->icon('heroicon-o-x-circle')->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'inactive']))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('30s')
            ->emptyStateHeading('No Employees Found')
            ->emptyStateDescription('New registrations from the registration page will appear here.')
            ->emptyStateIcon('heroicon-o-user-group');
        // ↑ No emptyStateActions — Add Employee is removed intentionally
    }

    /* ============================================================
       CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getCardLayoutColumns(): array
    {
        return [
            Tables\Columns\Layout\Split::make([

                // Left: Identity
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Full Name')
                        ->searchable(['first_name', 'middle_name', 'last_name'])
                        ->sortable()
                        ->weight(FontWeight::Bold)
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary'),

                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->size('sm')->color('blue')
                        ->icon('heroicon-o-envelope')->iconColor('blue')
                        ->copyable()->copyMessage('Email copied!')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('employee_id')
                        ->label('Employee ID')
                        ->badge()->color('primary')
                        ->icon('heroicon-o-identification')
                        ->size('sm')
                        ->searchable(),
                ])->space(1),

                // Middle: Role & Status
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('role')
                        ->label('Role')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => User::getRoles()[$state] ?? ucfirst($state))
                        ->color(fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'danger',
                            User::ROLE_REGULAR => 'info',
                            User::ROLE_JOB_ORDER => 'warning',
                            default => 'gray',
                        })
                        ->icon(fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'heroicon-m-shield-check',
                            User::ROLE_REGULAR => 'heroicon-m-user',
                            User::ROLE_JOB_ORDER => 'heroicon-m-briefcase',
                            default => 'heroicon-m-question-mark-circle',
                        })
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),

                    Tables\Columns\TextColumn::make('email_verified_at')
                        ->label('Email')
                        ->badge()
                        ->formatStateUsing(fn($state): string => $state ? 'Verified' : 'Unverified')
                        ->color(fn($state): string => $state ? 'success' : 'danger')
                        ->icon(fn($state): string => $state ? 'heroicon-m-check-badge' : 'heroicon-m-envelope'),

                    Tables\Columns\TextColumn::make('birthday')
                        ->label('Age')
                        ->formatStateUsing(fn($state): string => $state ? Carbon::parse($state)->age . ' yrs old' : '—')
                        ->size('sm')->color('gray')
                        ->icon('heroicon-o-cake')->iconColor('gray'),
                ])->space(1),

                // Right: Account Status & Registration Date
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            default => ucfirst($state),
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'active' => 'success',
                            'pending' => 'warning',
                            'inactive' => 'danger',
                            default => 'gray',
                        })
                        ->icon(fn(string $state): string => match ($state) {
                            'active' => 'heroicon-m-check-circle',
                            'pending' => 'heroicon-m-clock',
                            'inactive' => 'heroicon-m-x-circle',
                            default => 'heroicon-m-question-mark-circle',
                        })
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Medium),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Registered')
                        ->date('M d, Y')
                        ->size('sm')->color('gray')
                        ->icon('heroicon-o-calendar-days')->iconColor('gray')
                        ->description(fn(User $record): string => $record->created_at->diffForHumans()),
                ])->space(1)->alignment('end'),

            ])->from('md'),
        ];
    }

    /* ============================================================
       QUERY & ACCESS
       ============================================================ */

    /**
     * Show all non-admin users (regular + job_order).
     * Admin accounts are managed separately and don't need to appear here.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->latest('created_at');
    }

    /**
     * Disable create — employees register themselves via the registration page.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /* ============================================================
       PAGES — No 'create' route
       ============================================================ */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    /* ============================================================
       PRIVATE HELPERS
       ============================================================ */

    private static function syncFullName(callable $set, callable $get): void
    {
        $set('name', self::buildFullName($get));
    }

    private static function buildFullName(callable $get): string
    {
        return trim(implode(' ', array_filter([
            $get('first_name'),
            $get('middle_name'),
            $get('last_name'),
        ])));
    }

    private static function getPendingCount(): int
    {
        return User::query()
            ->whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->where('status', 'pending')
            ->count();
    }
}
