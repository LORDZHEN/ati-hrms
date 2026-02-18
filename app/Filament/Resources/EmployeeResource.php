<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?string $slug = 'employees';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $modelLabel = 'Employee';
    protected static ?string $pluralModelLabel = 'Employees';
    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Personal Information')
                ->description('Enter the employee\'s personal details')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateFullName($set, $get)
                            )
                            ->autofocus(),

                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateFullName($set, $get)
                            )
                            ->placeholder('Optional'),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateFullName($set, $get)
                            ),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('employee@company.com'),

                        DatePicker::make('birthday')
                            ->label('Date of Birth')
                            ->required()
                            ->maxDate(now()->subYears(18))
                            ->native(false)
                            ->displayFormat('F d, Y')
                            ->prefixIcon('heroicon-o-cake')
                            ->helperText('Must be at least 18 years old'),
                    ]),

                    Placeholder::make('full_name_preview')
                        ->label('Full Name Preview')
                        ->content(fn(callable $get) => self::generateFullName($get) ?: 'Enter names above to see preview')
                        ->helperText('This is how the name will appear in the system'),

                    TextInput::make('name')
                        ->hidden()
                        ->dehydrated()
                        ->default(fn(callable $get) => self::generateFullName($get)),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Employment Details')
                ->description('Configure employment and system access')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('employee_id')
                            ->label('Employee ID')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-identification')
                            ->placeholder('EMP-001')
                            ->helperText('Unique identifier for the employee'),

                        Select::make('status')
                            ->label('Account Status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-shield-check'),
                    ]),

                    Select::make('role')
                        ->label('System Role')
                        ->options([
                            'admin' => 'Administrator',
                            'employee' => 'Employee',
                        ])
                        ->default('employee')
                        ->required()
                        ->native(false)
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false)
                        ->prefixIcon('heroicon-o-key')
                        ->helperText('Determines system access level'),
                ])
                ->columns(1)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('employee_id')
                            ->label('ID')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-m-identification')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('name')
                            ->label('Employee')
                            ->searchable(['first_name', 'middle_name', 'last_name'])
                            ->sortable()
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->description(fn($record) => $record->email)
                            ->icon('heroicon-m-user-circle')
                            ->iconColor('gray'),
                    ])->space(2),

                    Stack::make([
                        TextColumn::make('status')
                            ->badge()
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
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Pending Approval',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                default => ucfirst($state),
                            }),

                        TextColumn::make('email_verified_at')
                            ->label('Email')
                            ->badge()
                            ->color(fn($state): string => $state ? 'success' : 'danger')
                            ->icon(fn($state): string => $state ? 'heroicon-m-check-badge' : 'heroicon-m-envelope')
                            ->formatStateUsing(fn($state): string => $state ? 'Verified' : 'Not Verified'),

                        TextColumn::make('role')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'admin' => 'danger',
                                'employee' => 'info',
                                default => 'gray',
                            })
                            ->icon(fn(string $state): string => match ($state) {
                                'admin' => 'heroicon-m-shield-check',
                                'employee' => 'heroicon-m-user',
                                default => 'heroicon-m-question-mark-circle',
                            })
                            ->formatStateUsing(fn(string $state): string => ucfirst($state))
                            ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                    ])->space(1)->alignment('end'),
                ])->from('md'),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->date('M d, Y')
                    ->description(fn($record) => $record->created_at->diffForHumans())
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->iconColor('gray')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('birthday')
                    ->label('Age')
                    ->formatStateUsing(
                        fn($record) => $record->birthday
                        ? Carbon::parse($record->birthday)->age . ' years'
                        : 'N/A'
                    )
                    ->icon('heroicon-m-cake')
                    ->iconColor('gray')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-arrow-path')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Account Status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->native(false)
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('verified')
                    ->label('Email Verification')
                    ->placeholder('All employees')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn(Builder $query) => $query->whereNull('email_verified_at'),
                    )
                    ->native(false),

                Filter::make('pending_approval')
                    ->label('Pending Approval')
                    ->query(
                        fn(Builder $query) => $query
                            ->where('status', 'pending')
                            ->whereNull('email_verified_at')
                    )
                    ->toggle()
                    ->default(false),

                Filter::make('recent')
                    ->label('Recently Added')
                    ->query(fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Filter::make('age_range')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('age_from')
                                ->label('From Age')
                                ->numeric()
                                ->placeholder('18'),
                            TextInput::make('age_to')
                                ->label('To Age')
                                ->numeric()
                                ->placeholder('65'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['age_from'], function ($query, $age) {
                                $date = now()->subYears((int) $age)->endOfDay();
                                $query->where('birthday', '<=', $date);
                            })
                            ->when($data['age_to'], function ($query, $age) {
                                $date = now()->subYears((int) $age)->startOfDay();
                                $query->where('birthday', '>=', $date);
                            });
                    })
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
                    Tables\Actions\ViewAction::make()
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),

                    // verify_email removed — now lives inside ViewEmployee
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false)
                        ->action(function ($record) {
                            \Filament\Notifications\Notification::make()
                                ->title('Password reset email sent')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button()
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn() => Auth::user()?->isAdmin() ?? false),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
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
            ->emptyStateHeading('No Employees Found')
            ->emptyStateDescription('Get started by adding your first employee.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Employee')
                    ->icon('heroicon-m-plus')
                    ->visible(fn() => Auth::user()?->isAdmin() ?? false),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('30s');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', ['employee', 'admin'])
            ->latest('created_at');
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = self::getPendingEmployeesCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = self::getPendingEmployeesCount();
        return $count > 0 ? 'warning' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $count = self::getPendingEmployeesCount();
        return $count > 0 ? "{$count} pending approval" : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    private static function updateFullName(callable $set, callable $get): void
    {
        $set('name', self::generateFullName($get));
    }

    private static function generateFullName(callable $get): string
    {
        return trim(implode(' ', array_filter([
            $get('first_name'),
            $get('middle_name'),
            $get('last_name'),
        ])));
    }

    private static function getPendingEmployeesCount(): int
    {
        return User::query()
            ->where('role', 'employee')
            ->where('status', 'pending')
            ->whereNull('email_verified_at')
            ->count();
    }
}
