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
    protected static ?int $navigationSort = 4;

    /* ============================================================
       ACCESS CONTROL
       ============================================================ */

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::check();
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /* ============================================================
       FORM  (unchanged)
       ============================================================ */

    public static function form(Form $form): Form
    {
        $isEmployee = !(Auth::user()?->isAdmin() ?? false);

        return $form->schema([
            Forms\Components\Placeholder::make('readonly_notice')
                ->label('')->content('📋 You are viewing this event in read-only mode.')
                ->visible($isEmployee)->columnSpanFull(),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->placeholder('Enter event title...')->columnSpanFull()->disabled($isEmployee),
                        Forms\Components\Textarea::make('description')->rows(5)->placeholder('Describe what this event is about...')->columnSpanFull()->disabled($isEmployee),
                    ])
                        ->heading('Event Details')
                        ->description($isEmployee ? 'This event was created by an administrator.' : 'Provide a clear title and description for the event.')
                        ->icon('heroicon-o-pencil-square'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\DatePicker::make('event_date')->label('Event Date')->required()->native(false)->displayFormat('M d, Y')->default(now())->prefixIcon('heroicon-o-calendar')->disabled($isEmployee),
                        Forms\Components\TimePicker::make('event_time')->label('Event Time')->required()->seconds(false)->default('09:00')->prefixIcon('heroicon-o-clock')->disabled($isEmployee),
                        Forms\Components\TextInput::make('location')->required()->maxLength(255)->placeholder('e.g. Conference Room A, Main Hall...')->prefixIcon('heroicon-o-map-pin')->columnSpanFull()->disabled($isEmployee),
                    ])
                        ->heading('Schedule & Location')->description('Set when and where the event will take place.')->icon('heroicon-o-map-pin')->columns(2),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Toggle::make('is_active')->label('Active')->helperText('Toggle to show or hide this event.')->default(true)->onIcon('heroicon-m-eye')->offIcon('heroicon-m-eye-slash')->onColor('success')->inline(false)->disabled($isEmployee),
                    ])->heading('Visibility')->icon('heroicon-o-eye'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('type')
                            ->options(['event' => 'Event', 'meeting' => 'Meeting', 'deadline' => 'Deadline', 'training' => 'Training', 'holiday' => 'Holiday'])
                            ->required()->default('event')->native(false)->selectablePlaceholder(false)->helperText('Categorize the type of event.')->columnSpanFull()->disabled($isEmployee),
                    ])->heading('Event Type')->icon('heroicon-o-tag'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('color')
                            ->options(['green' => 'Green', 'blue' => 'Blue', 'red' => 'Red', 'amber' => 'Amber', 'purple' => 'Purple'])
                            ->required()->default('blue')->native(false)->selectablePlaceholder(false)->helperText('Color used to highlight this event on the dashboard.')->columnSpanFull()->disabled($isEmployee),
                    ])->heading('Accent Color')->icon('heroicon-o-swatch'),
                ])->columnSpan(1),
            ]),
        ]);
    }

    /* ============================================================
       TABLE
       ============================================================ */

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()?->isAdmin() ?? false;

        return $table
            ->columns(self::getCardLayoutColumns($isAdmin))
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(['event' => 'Event', 'meeting' => 'Meeting', 'deadline' => 'Deadline', 'training' => 'Training', 'holiday' => 'Holiday'])
                    ->native(false)->placeholder('All Types'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')->placeholder('All Statuses')
                    ->trueLabel('Active only')->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Only')
                    ->query(fn(Builder $query) => $query->upcoming())->toggle(),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn(Builder $query) => $query->thisMonth())->toggle(),

                Tables\Filters\Filter::make('past_events')
                    ->label('Past Events')
                    ->query(fn(Builder $query) => $query->where('event_date', '<', now()))->toggle(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('quickView')
                        ->label('Quick View')->icon('heroicon-m-eye')->color('info')
                        ->modalHeading(fn($record) => $record->title)
                        ->modalContent(fn($record) => view('filament.resources.event.quick-view', ['record' => $record]))
                        ->modalWidth('2xl')->modalFooterActions(fn() => [])->slideOver(),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->label(fn() => $isAdmin ? 'Edit' : 'View Details'),

                    Tables\Actions\Action::make('toggle_active')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                        ->label(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->action(fn($record) => $record->update(['is_active' => !$record->is_active]))
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => $record->is_active ? 'Deactivate Event' : 'Activate Event')
                        ->modalDescription(fn($record) => $record->is_active ? 'This event will no longer appear on the dashboard.' : 'This event will become visible on the dashboard.')
                        ->modalSubmitActionLabel(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->visible($isAdmin),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')->visible($isAdmin),
                ])->label('Actions')->icon('heroicon-o-ellipsis-vertical')->size(\Filament\Support\Enums\ActionSize::Small)->color('gray')->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')->label('Activate Selected')->icon('heroicon-o-eye')->color('success')
                        ->action(fn($records) => $records->each->update(['is_active' => true]))->requiresConfirmation()->deselectRecordsAfterCompletion()->visible($isAdmin),
                    Tables\Actions\BulkAction::make('deactivate')->label('Deactivate Selected')->icon('heroicon-o-eye-slash')->color('warning')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))->requiresConfirmation()->deselectRecordsAfterCompletion()->visible($isAdmin),
                    Tables\Actions\DeleteBulkAction::make()->visible($isAdmin),
                ]),
            ])
            ->defaultSort('event_date', 'asc')
            ->striped()->poll('60s')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateHeading('No Events Scheduled')
            ->emptyStateDescription('No events have been scheduled yet.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Create Event')->icon('heroicon-o-plus')->visible($isAdmin),
            ]);
    }

    /* ============================================================
       CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getCardLayoutColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                // Left: Title, Description & Type
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->label('Event')
                        ->searchable()->sortable()
                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->icon('heroicon-o-calendar-days')->iconColor('primary'),

                    Tables\Columns\TextColumn::make('description')
                        ->label('Description')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->color('gray')->limit(70)->wrap()
                        ->formatStateUsing(fn($state) => Str::limit($state ?? '', 70)),

                    // WHY: BadgeColumn was removed in Filament v3. Use
                    // TextColumn->badge() with ->color() and ->icon() closures.
                    Tables\Columns\TextColumn::make('type')
                        ->label('Type')
                        ->badge()
                        ->sortable()
                        ->color(fn(string $state) => match ($state) {
                            'event' => 'success',
                            'meeting' => 'info',
                            'deadline' => 'danger',
                            'training' => 'warning',
                            'holiday' => 'primary',
                            default => 'gray',
                        })
                        ->icon(fn(string $state) => match ($state) {
                            'event' => 'heroicon-m-star',
                            'meeting' => 'heroicon-m-user-group',
                            'deadline' => 'heroicon-m-exclamation-circle',
                            'training' => 'heroicon-m-academic-cap',
                            'holiday' => 'heroicon-m-sun',
                            default => null,
                        })
                        ->formatStateUsing(fn(string $state) => ucfirst($state)),
                ])->space(1),

                // Middle: Date, Time & Location
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('event_date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->icon('heroicon-m-calendar')
                        ->color(fn($record) => $record->isUpcoming() ? 'success' : 'gray')
                        ->weight(fn($record) => $record->isUpcoming() ? 'semibold' : 'normal')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->sortable(),

                    Tables\Columns\TextColumn::make('event_time')
                        ->label('Time')
                        ->time('g:i A')
                        ->icon('heroicon-m-clock')->iconColor('info')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->color('gray')->sortable(),

                    Tables\Columns\TextColumn::make('location')
                        ->label('Location')
                        ->icon('heroicon-m-map-pin')->iconColor('warning')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->color('gray')
                        ->limit(30)->tooltip(fn($record) => $record->location)
                        ->searchable(),
                ])->space(1),

                // Right: Status & Creator
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\IconColumn::make('is_active')
                        ->label('Status')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')->falseColor('danger')
                        ->size(Tables\Columns\IconColumn\IconColumnSize::Large),

                    Tables\Columns\TextColumn::make('creator.name')
                        ->label('Created By')
                        ->icon('heroicon-m-user-circle')->iconColor('gray')
                        ->color('gray')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->sortable()
                        ->visible($isAdmin),
                ])->space(1)->alignment(\Filament\Support\Enums\Alignment::End),
            ])->from('md'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
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
