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

class LocatorSlipResource extends Resource
{
    protected static ?string $model = LocatorSlip::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'locator-slip';
    protected static ?string $navigationLabel = 'Locator Slip';
    protected static ?string $title = 'Locator Slips';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
{
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
                                // ->disabled(fn($record) => $record?->exists)
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
                                // ->disabled(fn($record) => $record?->exists)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $set('transaction_type', 'official');
                                        $set('personal_transaction', false);
                                    }
                                }),
                        ]),
                    Forms\Components\Hidden::make('transaction_type')->default('official'),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('employee_name')
                                ->label('Name')
                                ->disabled()
                                ->dehydrated()
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    $component->state($record->employee?->name ?? auth()->user()->name);
                                })
                                ->required(),

                            Forms\Components\TextInput::make('position')
                                ->label('Position')
                                ->disabled()
                                ->dehydrated()
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    $component->state($record->employee?->position ?? auth()->user()->position);
                                })
                                ->required(),
                        ]),

                    Forms\Components\TextInput::make('office_department')
                        ->label('Department')
                        ->disabled()
                        ->dehydrated()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            $component->state($record->employee?->office_department ?? auth()->user()->department);
                        })
                        ->required(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('destination')
                                ->label('Destination')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('purpose')
                                ->label('Purpose')
                                // ->required()
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
                        ->disabled()
                        ->dehydrated()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            $component->state($record->employee?->name ?? auth()->user()->name);
                        })
                        ->required(),
                ])
                ->collapsible()
                ->collapsed(false),

            // Approval Section (Admin Only)
            Forms\Components\Section::make('Approval Section')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'disapproved' => 'Disapproved',
                        ])
                        ->required()
                        ->reactive(),

                    Forms\Components\TextInput::make('approved_by')
                        ->label('Approved By')
                        ->disabled()
                        ->afterStateHydrated(function ($component) {
                            $component->state(auth()->user()->name);
                        })
                        ->dehydrated(),

                    Forms\Components\Textarea::make('admin_remarks')
                        ->label('Remarks / Reason for Disapproval')
                        ->rows(3)
                        ->visible(fn(callable $get) => $get('status') === 'disapproved'),
                ])
                ->visible(fn() => auth()->user()->role === 'admin')
                ->collapsible()
                ->collapsed(true),

            Forms\Components\Hidden::make('user_id')->default(fn() => auth()->id()),
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
                        'danger' => 'disapproved',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')->label('Submitted')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'disapproved' => 'Disapproved',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(LocatorSlip $record) => $record->status === 'pending' && (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id)),

                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('locator_slip.print', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'approved'),

                // Admin Approve Action
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->user()->name,
                            'approved_at' => now(),
                        ]);
                        $record->user->notify(new \App\Notifications\LocatorSlipStatusUpdated($record));
                    })
                    ->visible(fn($record) => $record->status === 'pending' && auth()->user()->role === 'admin'),

                // Admin Disapprove Action
                Action::make('disapprove')
                    ->label('Disapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_remarks')->label('Reason for Disapproval')->required()->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'disapproved',
                            'approved_by' => auth()->user()->name,
                            'admin_remarks' => $data['admin_remarks'],
                        ]);
                        $record->user->notify(new \App\Notifications\LocatorSlipStatusUpdated($record));
                    })
                    ->visible(fn($record) => $record->status === 'pending' && auth()->user()->role === 'admin'),
            ])
            ->bulkActions([])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return $query;
                }
                return $query->where('user_id', $user->id);
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

    public static function canEdit($record): bool
    {
        return $record->status === 'pending' && (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id);
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'pending' && (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id);
    }

    // Show badge on navigation for pending leave applications
    public static function getNavigationBadge(): ?string
    {
        if (!(auth()->user()?->is_admin ?? false)) {
            return null; // No badge for non-admin users
        }

        $count = LocatorSlip::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    // Optional: change badge color dynamically
    public static function getNavigationBadgeColor(): ?string
    {
        if (!(auth()->user()?->is_admin ?? false)) {
            return null; // No badge for non-admin users
        }
        $count = LocatorSlip::where('status', 'pending')->count();
        return $count > 0 ? 'warning' : 'success';
    }

}
