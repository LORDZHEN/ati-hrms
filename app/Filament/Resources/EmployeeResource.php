<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'employee-resource';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $title = 'Employees';
    protected static ?string $modelLabel = 'Employee';
    protected static ?string $pluralModelLabel = 'Employees';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::getPersonalInformationSection(),
            self::getAccountInformationSection(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions(self::getTableActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', ['employee', 'admin'])
            ->latest('created_at');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = self::getPendingEmployeesCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return self::getPendingEmployeesCount() > 0 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'view'  => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'create' => Pages\CreateEmployee::route('/create'),
        ];
    }

    // Private helper methods

    private static function getPersonalInformationSection(): array
    {
        return [
            TextInput::make('first_name')
                ->label('First Name')
                ->required()
                ->maxLength(255),

            TextInput::make('middle_name')
                ->label('Middle Name')
                ->maxLength(255),

            TextInput::make('last_name')
                ->label('Last Name')
                ->required()
                ->maxLength(255),

            TextInput::make('name')
                ->hidden()
                ->dehydrated()
                ->default(fn($get) => self::generateFullName($get)),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('employee_id')
                ->label('Employee ID')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),

            DatePicker::make('birthday')
                ->label('Date of Birth')
                ->required()
                ->maxDate(now()->subYears(18))
                ->native(false),
        ];
    }

    private static function getAccountInformationSection(): array
    {
        $isAdmin = auth()->user()?->role === 'admin';

        return [
            Select::make('role')
                ->label('Role')
                ->options([
                    'admin' => 'Admin',
                    'employee' => 'Employee',
                ])
                ->default('employee')
                ->required()
                ->visible($isAdmin),

            Select::make('status')
                ->label('Account Status')
                ->options([
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('pending')
                ->required(),
        ];
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('employee_id')
                ->label('Employee ID')
                ->sortable()
                ->searchable()
                ->default('N/A'),

            TextColumn::make('name')
                ->label('Name')
                ->searchable(['first_name', 'middle_name', 'last_name'])
                ->sortable(),

            TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable()
                ->copyable(),

            TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'active' => 'success',
                    'pending' => 'warning',
                    'inactive' => 'danger',
                    default => 'gray',
                })
                ->sortable(),

            TextColumn::make('email_verified_at')
                ->label('Verification')
                ->badge()
                ->color(fn($state): string => $state ? 'success' : 'danger')
                ->formatStateUsing(fn($state): string => $state ? 'Verified' : 'Not Verified')
                ->sortable(),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ]),

            SelectFilter::make('verified')
                ->label('Email Verified')
                ->options([
                    'yes' => 'Verified',
                    'no' => 'Not Verified',
                ])
                ->query(fn(Builder $query, array $data): Builder =>
                    $data['value'] === 'yes'
                        ? $query->whereNotNull('email_verified_at')
                        : $query->whereNull('email_verified_at')
                ),
        ];
    }

    private static function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make()
                ->label('View')
                ->icon('heroicon-o-eye')
                ->visible(fn(): bool => auth()->user()?->role === 'admin'),
        ];
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
            ->count();
    }

    public static function getRelations(): array
    {
        return [];
    }
}
