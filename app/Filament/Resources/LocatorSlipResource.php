<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocatorSlipResource\Pages;
use App\Models\LocatorSlip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class LocatorSlipResource extends Resource
{
    protected static ?string $model = LocatorSlip::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'locator-slip';
    protected static ?string $navigationLabel = 'Locator Slip';
    protected static ?string $title = 'Locator Slips';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                Forms\Components\Section::make('Agricultural Training Institute - XI')
                    ->description('Office of the Human Resource Management - LOCATOR SLIP')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Checkbox::make('personal_transaction')
                                    ->label('Personal Transaction')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('transaction_type', 'personal');
                                            $set('official_business', false);
                                        }
                                    }),
                                Forms\Components\Checkbox::make('official_business')
                                    ->label('Official Business')
                                    ->default(true)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('transaction_type', 'official');
                                            $set('personal_transaction', false);
                                        }
                                    }),
                            ]),
                        Forms\Components\Hidden::make('transaction_type')
                            ->default('official'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('employee_name')
                                    ->label('Name')
                                    ->default($user->name ?? '')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('position')
                                    ->label('Position')
                                    ->default($user->position ?? '')
                                    ->required()
                                    ->dehydrated(),
                            ]),
                        Forms\Components\TextInput::make('department')
                            ->label('Department')
                            ->default($user->office_department ?? '')
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('destination')
                                    ->label('Destination')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('purpose')
                                    ->label('Purpose')
                                    ->required()
                                    ->rows(3),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('inclusive_date')
                                    ->label('Inclusive Date')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\TimePicker::make('out_time')
                                    ->label('Out')
                                    ->required()
                                    ->default(now())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $time = \Carbon\Carbon::parse($state)->addHours(2);
                                            $set('in_time', $time->format('H:i'));
                                        }
                                    }),
                                Forms\Components\TimePicker::make('in_time')
                                    ->label('In')
                                    ->required()
                                    ->default(fn(callable $get) => \Carbon\Carbon::parse($get('out_time'))->addHours(2)->format('H:i')),
                            ]),
                        Forms\Components\TextInput::make('requested_by')
                            ->label('Requested By')
                            ->default($user->name ?? '')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ]),

                // Admin-only Approval Section
                Forms\Components\Section::make('Approval Section')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('approved_by')
                            ->label('Approved By')
                            ->default($user->name)
                            ->required()
                            ->disabled(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(3)
                            ->required()
                            ->visible(fn(callable $get) => $get('status') === 'rejected'),
                    ])
                    ->visible(fn() => $user->role === 'admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('destination')->label('Destination')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('inclusive_date')->label('Date')->date()->sortable(),
                Tables\Columns\TextColumn::make('out_time')->label('Out Time')->time('H:i'),
                Tables\Columns\TextColumn::make('in_time')->label('In Time')->time('H:i')->placeholder('Not set'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')->label('Submitted')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn(LocatorSlip $record) => $record->status === 'pending'),
                Action::make('Print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('locator_slip.print', $record->id))
                    ->openUrlInNewTab(),

                // Admin Approve / Reject Actions
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (LocatorSlip $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::user()->name,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Locator slip approved successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(LocatorSlip $record) => $record->status === 'pending' && Auth::user()->role === 'admin'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (LocatorSlip $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::user()->name,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Locator slip rejected')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(LocatorSlip $record) => $record->status === 'pending' && Auth::user()->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn(LocatorSlip $record) => $record->status === 'pending'),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->role === 'admin') {
                    return $query; // Admin sees all slips
                }
                return $query->where('user_id', auth()->id()); // Employee sees only their own
            })
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocatorSlips::route('/'),
            'create' => Pages\CreateLocatorSlip::route('/create'),
            'edit' => Pages\EditLocatorSlip::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Ensure the default query respects roles
        if (auth()->user()->role === 'admin') {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
