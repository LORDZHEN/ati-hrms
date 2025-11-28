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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountVerifiedMail;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'employee-resource';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $title = 'Employees';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()?->role === 'admin';

        return $form->schema([
            TextInput::make('name')->label('Name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('employee_id_number')->label('Employee ID')->required(),
            DatePicker::make('birthday')->required(),
            Select::make('role')
                ->label('Role')
                ->options([
                    'admin' => 'Admin',
                ])
                ->default('admin')
                ->required()
                ->hidden(!$isAdmin),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id_number')
                    ->label('Employee ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('verification_status')
                    ->label('Verification Status')
                    ->badge()
                    ->color(fn($record) => $record->email_verified_at ? 'success' : 'danger')
                    ->getStateUsing(fn(User $record) => $record->email_verified_at ? 'Verified' : 'Not Verified'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
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
                    ->query(fn($query, array $data) => $data['value'] === 'yes'
                        ? $query->whereNotNull('email_verified_at')
                        : $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->hidden(fn($record) => (bool) $record->email_verified_at)
                    ->requiresConfirmation()
                    ->action(function ($record, $livewire) {
                        $birthday = $record->birthday?->format('mdY');

                        if (!$birthday || strlen($birthday) !== 8) {
                            Notification::make()
                                ->title('Invalid birthday format')
                                ->danger()
                                ->body('Failed to format the birthday into MMDDYYYY.')
                                ->send();
                            return;
                        }

                        $record->update([
                            'email_verified_at' => now(),
                            'password' => bcrypt($birthday),
                            'must_change_password' => true,
                            'status' => 'active',
                        ]);

                        Mail::to($record->email)->send(new AccountVerifiedMail($record, $birthday));

                        Notification::make()
                            ->title('Account Verified')
                            ->body("Temporary password: **{$birthday}**. An email has been sent to the employee.")
                            ->success()
                            ->persistent()
                            ->send();

                        $livewire->redirect($livewire->getUrl());
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'employee'); // only show employees in list
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'admin'; // only admin can create
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = User::where('role', 'employee')
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = User::where('role', 'employee')
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'create' => Pages\CreateEmployee::route('/create'),
        ];
    }
}
