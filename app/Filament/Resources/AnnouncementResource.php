<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

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
                                            ->placeholder('Enter announcement title...')
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('message')
                                            ->required()
                                            ->rows(6)
                                            ->placeholder('Write your announcement message here...')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Announcement Content')
                                    ->description('Write a clear and concise announcement for your employees.')
                                    ->icon('heroicon-o-pencil-square'),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('publish_date')
                                            ->label('Publish Date')
                                            ->placeholder('Publish immediately')
                                            ->helperText('Leave empty to publish right away.')
                                            ->prefixIcon('heroicon-o-calendar'),

                                        Forms\Components\DatePicker::make('expiry_date')
                                            ->label('Expiry Date')
                                            ->placeholder('No expiration')
                                            ->helperText('Leave empty to keep it active indefinitely.')
                                            ->prefixIcon('heroicon-o-calendar-days')
                                            ->after('publish_date'),
                                    ])
                                    ->heading('Publishing Schedule')
                                    ->description('Control when this announcement is visible.')
                                    ->icon('heroicon-o-clock')
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Toggle to show or hide this announcement.')
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
                                        Forms\Components\Select::make('priority')
                                            ->options([
                                                'low'    => 'Low',
                                                'medium' => 'Medium',
                                                'high'   => 'High',
                                            ])
                                            ->required()
                                            ->default('medium')
                                            ->native(false)
                                            ->selectablePlaceholder(false)
                                            ->helperText('High priority announcements appear at the top.')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Priority')
                                    ->icon('heroicon-o-flag'),

                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('icon')
                                            ->options(Announcement::getIconOptions())
                                            ->required()
                                            ->default('heroicon-o-megaphone')
                                            ->native(false)
                                            ->helperText('Choose an icon to represent this announcement.')
                                            ->columnSpanFull(),
                                    ])
                                    ->heading('Icon')
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
                    ->label('Announcement')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->size('sm')
                    ->description(fn($record): string => Str::limit($record->message, 65))
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priority')
                    ->colors([
                        'danger'  => 'high',
                        'warning' => 'medium',
                        'success' => 'low',
                    ])
                    ->icons([
                        'heroicon-m-arrow-up'   => 'high',
                        'heroicon-m-minus'       => 'medium',
                        'heroicon-m-arrow-down' => 'low',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('Published')
                    ->date('M d, Y')
                    ->placeholder('Immediately')
                    ->icon('heroicon-m-calendar')
                    ->color('gray')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expires')
                    ->date('M d, Y')
                    ->placeholder('No expiry')
                    ->icon('heroicon-m-calendar-days')
                    ->color('gray')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->icon('heroicon-m-user-circle')
                    ->color('gray')
                    ->size('sm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->since()
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
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ])
                    ->native(false)
                    ->placeholder('All Priorities'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Statuses')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('active_now')
                    ->label('Currently Published')
                    ->query(fn(Builder $query) => $query->active())
                    ->toggle(),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (7 days)')
                    ->query(fn(Builder $query) => $query
                        ->whereNotNull('expiry_date')
                        ->whereBetween('expiry_date', [now(), now()->addDays(7)])
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('no_expiry')
                    ->label('No Expiry Set')
                    ->query(fn(Builder $query) => $query->whereNull('expiry_date'))
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
                    ->modalHeading(fn($record) => $record->is_active ? 'Deactivate Announcement' : 'Activate Announcement')
                    ->modalDescription(fn($record) => $record->is_active
                        ? 'This announcement will no longer be visible to employees.'
                        : 'This announcement will become visible to employees.')
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
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('60s')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateHeading('No Announcements Yet')
            ->emptyStateDescription('Create your first announcement to notify your employees.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Announcement')
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
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active announcements';
    }
}
