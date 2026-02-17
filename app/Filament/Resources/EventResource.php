<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter event title...')
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('description')
                                            ->rows(5)
                                            ->placeholder('Describe what this event is about...')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Event Details')
                                    ->description('Provide a clear title and description for the event.')
                                    ->icon('heroicon-o-pencil-square'),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('event_date')
                                            ->label('Event Date')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('M d, Y')
                                            ->default(now())
                                            ->prefixIcon('heroicon-o-calendar'),

                                        Forms\Components\TimePicker::make('event_time')
                                            ->label('Event Time')
                                            ->required()
                                            ->seconds(false)
                                            ->default('09:00')
                                            ->prefixIcon('heroicon-o-clock'),

                                        Forms\Components\TextInput::make('location')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('e.g. Conference Room A, Main Hall...')
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Schedule & Location')
                                    ->description('Set when and where the event will take place.')
                                    ->icon('heroicon-o-map-pin')
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Toggle to show or hide this event.')
                                            ->default(true)
                                            ->onIcon('heroicon-m-eye')
                                            ->offIcon('heroicon-m-eye-slash')
                                            ->onColor('success')
                                            ->inline(false),
                                    ])
                                    ->heading('Visibility')
                                    ->icon('heroicon-o-eye'),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'event'    => 'Event',
                                                'meeting'  => 'Meeting',
                                                'deadline' => 'Deadline',
                                                'training' => 'Training',
                                                'holiday'  => 'Holiday',
                                            ])
                                            ->required()
                                            ->default('event')
                                            ->native(false)
                                            ->selectablePlaceholder(false)
                                            ->helperText('Categorize the type of event.')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Event Type')
                                    ->icon('heroicon-o-tag'),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('color')
                                            ->options([
                                                'green'  => 'Green',
                                                'blue'   => 'Blue',
                                                'red'    => 'Red',
                                                'amber'  => 'Amber',
                                                'purple' => 'Purple',
                                            ])
                                            ->required()
                                            ->default('blue')
                                            ->native(false)
                                            ->selectablePlaceholder(false)
                                            ->helperText('Color used to highlight this event on the dashboard.')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Accent Color')
                                    ->icon('heroicon-o-swatch'),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->size('sm')
                    ->description(fn($record): string => Str::limit($record->description ?? '', 65))
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'event',
                        'info'    => 'meeting',
                        'danger'  => 'deadline',
                        'warning' => 'training',
                        'primary' => 'holiday',
                    ])
                    ->icons([
                        'heroicon-m-star'               => 'event',
                        'heroicon-m-user-group'         => 'meeting',
                        'heroicon-m-exclamation-circle' => 'deadline',
                        'heroicon-m-academic-cap'       => 'training',
                        'heroicon-m-sun'                => 'holiday',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->icon('heroicon-m-calendar')
                    ->color(fn($record) => $record->isUpcoming() ? 'success' : 'gray')
                    ->weight(fn($record) => $record->isUpcoming() ? 'semibold' : 'normal')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('event_time')
                    ->label('Time')
                    ->time('g:i A')
                    ->icon('heroicon-m-clock')
                    ->color('gray')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->icon('heroicon-m-map-pin')
                    ->color('gray')
                    ->size('sm')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->location)
                    ->searchable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->icon('heroicon-m-user-circle')
                    ->color('gray')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'event'    => 'Event',
                        'meeting'  => 'Meeting',
                        'deadline' => 'Deadline',
                        'training' => 'Training',
                        'holiday'  => 'Holiday',
                    ])
                    ->native(false)
                    ->placeholder('All Types'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Statuses')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Only')
                    ->query(fn(Builder $query) => $query->upcoming())
                    ->toggle(),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn(Builder $query) => $query->thisMonth())
                    ->toggle(),

                Tables\Filters\Filter::make('past_events')
                    ->label('Past Events')
                    ->query(fn(Builder $query) => $query->where('event_date', '<', now()))
                    ->toggle(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit'),

                Tables\Actions\Action::make('toggle_active')
                    ->iconButton()
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                    ->tooltip(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->action(fn($record) => $record->update(['is_active' => ! $record->is_active]))
                    ->requiresConfirmation()
                    ->modalHeading(fn($record) => $record->is_active ? 'Deactivate Event' : 'Activate Event')
                    ->modalDescription(fn($record) => $record->is_active
                        ? 'This event will no longer appear on the dashboard.'
                        : 'This event will become visible on the dashboard.')
                    ->modalSubmitActionLabel(fn($record) => $record->is_active ? 'Deactivate' : 'Activate'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date', 'asc')
            ->striped()
            ->poll('60s')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateHeading('No Events Scheduled')
            ->emptyStateDescription('Add your first event to display it on the employee dashboard.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Event')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::upcoming()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Upcoming events';
    }
}
