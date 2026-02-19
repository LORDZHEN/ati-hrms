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
    protected static ?string $slug = 'announcements';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

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
       FORM
       ============================================================ */

    public static function form(Form $form): Form
    {
        $isEmployee = ! (Auth::user()?->isAdmin() ?? false);

        return $form->schema([
            Forms\Components\Placeholder::make('readonly_notice')
                ->label('')->content('📋 You are viewing this announcement in read-only mode.')
                ->visible($isEmployee)->columnSpanFull(),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->placeholder('Enter announcement title...')->columnSpanFull()->disabled($isEmployee),
                        Forms\Components\Textarea::make('message')->required()->rows(6)->placeholder('Write your announcement message here...')->columnSpanFull()->disabled($isEmployee),
                    ])
                    ->heading('Announcement Content')
                    ->description($isEmployee ? 'This announcement was posted by an administrator.' : 'Write a clear and concise announcement for your employees.')
                    ->icon('heroicon-o-pencil-square'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\DatePicker::make('publish_date')->label('Publish Date')->placeholder('Publish immediately')->helperText('Leave empty to publish right away.')->prefixIcon('heroicon-o-calendar')->disabled($isEmployee),
                        Forms\Components\DatePicker::make('expiry_date')->label('Expiry Date')->placeholder('No expiration')->helperText('Leave empty to keep it active indefinitely.')->prefixIcon('heroicon-o-calendar-days')->after('publish_date')->disabled($isEmployee),
                    ])
                    ->heading('Publishing Schedule')->description('Control when this announcement is visible.')->icon('heroicon-o-clock')->columns(2),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Toggle::make('is_active')->label('Active')->helperText('Toggle to show or hide this announcement.')->default(true)->onIcon('heroicon-m-eye')->offIcon('heroicon-m-eye-slash')->onColor('success')->inline(false)->disabled($isEmployee),
                    ])->heading('Visibility')->icon('heroicon-o-eye'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('priority')->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])->required()->default('medium')->native(false)->selectablePlaceholder(false)->helperText('High priority announcements appear at the top.')->columnSpanFull()->disabled($isEmployee),
                    ])->heading('Priority')->icon('heroicon-o-flag'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('icon')->options(Announcement::getIconOptions())->required()->default('heroicon-o-megaphone')->native(false)->helperText('Choose an icon to represent this announcement.')->columnSpanFull()->disabled($isEmployee),
                    ])->heading('Icon')->icon('heroicon-o-swatch'),

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('duration_hours')->label('Auto-Expire After')->options(Announcement::getDurationOptions())->default('')->native(false)->helperText('Automatically deactivate this announcement after the selected time from now.')->columnSpanFull()->dehydrated(false)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->expires_at && $record->expires_at->isFuture()) $component->state('');
                            }),
                        Forms\Components\Placeholder::make('expires_at_label')->label('Current Auto-Expire')
                            ->content(function ($record): string {
                                if (!$record || !$record->expires_at) return 'Not set (manual control)';
                                if ($record->expires_at->isPast()) return '⛔ Expired — ' . $record->expires_at->format('M d, Y g:i A');
                                return '⏱ ' . $record->expires_at->diffForHumans() . ' (' . $record->expires_at->format('M d, Y g:i A') . ')';
                            })
                            ->visible(fn($record) => $record !== null),
                ])->heading('Auto-Expire')->description('Set a countdown timer to deactivate automatically.')->icon('heroicon-o-clock')->hidden($isEmployee),
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
                Tables\Filters\SelectFilter::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->native(false)->placeholder('All Priorities'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')->placeholder('All Statuses')
                    ->trueLabel('Active only')->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('active_now')
                    ->label('Currently Published')
                    ->query(fn(Builder $query) => $query->active())->toggle(),

                Tables\Filters\Filter::make('has_auto_expire')
                    ->label('Has Auto-Expire')
                    ->query(fn(Builder $query) => $query->whereNotNull('expires_at'))
                    ->toggle()->visible($isAdmin),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (7 days)')
                    ->query(fn(Builder $query) => $query->whereNotNull('expiry_date')->whereBetween('expiry_date', [now(), now()->addDays(7)]))
                    ->toggle()->visible($isAdmin),

                Tables\Filters\Filter::make('no_expiry')
                    ->label('No Expiry Set')
                    ->query(fn(Builder $query) => $query->whereNull('expiry_date'))
                    ->toggle()->visible($isAdmin),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('quickView')
                        ->label('Quick View')->icon('heroicon-m-eye')->color('info')
                        ->modalHeading(fn($record) => $record->title)
                        ->modalContent(fn($record) => view('filament.resources.announcements.quick-view', ['record' => $record]))
                        ->modalWidth('3xl')->modalFooterActions(fn() => [])->slideOver(),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->label(fn() => $isAdmin ? 'Edit' : 'View Details'),

                    Tables\Actions\Action::make('toggle_active')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                        ->label(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->action(fn($record) => $record->update(['is_active' => !$record->is_active]))
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => $record->is_active ? 'Deactivate Announcement' : 'Activate Announcement')
                        ->modalDescription(fn($record) => $record->is_active ? 'This announcement will no longer be visible to employees.' : 'This announcement will become visible to employees.')
                        ->modalSubmitActionLabel(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->visible($isAdmin),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')->visible($isAdmin),
                ])->label('Actions')->icon('heroicon-o-ellipsis-vertical')->size('sm')->color('gray')->button(),
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
            ->defaultSort('created_at', 'desc')
            ->striped()->poll('60s')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateHeading('No Announcements Yet')
            ->emptyStateDescription('No announcements have been posted.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Create Announcement')->icon('heroicon-o-plus')->visible($isAdmin),
            ]);
    }

    /* ============================================================
       CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getCardLayoutColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\Layout\Split::make([
                // Left: Title, Message Preview & Priority
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->label('Title')
                        ->searchable()->sortable()
                        ->weight('bold')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->icon('heroicon-o-megaphone')->iconColor('primary'),

                    Tables\Columns\TextColumn::make('message')
                        ->label('Message')
                        ->size('sm')->color('gray')
                        ->limit(80)->wrap()
                        ->formatStateUsing(fn($state) => Str::limit($state, 80)),

                    Tables\Columns\BadgeColumn::make('priority')
                        ->label('Priority')
                        ->colors(['danger' => 'high', 'warning' => 'medium', 'success' => 'low'])
                        ->icons(['heroicon-m-arrow-up' => 'high', 'heroicon-m-minus' => 'medium', 'heroicon-m-arrow-down' => 'low'])
                        ->sortable(),
                ])->space(1),

                // Middle: Schedule Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('publish_date')
                        ->label('Published')
                        ->date('M d, Y')->placeholder('Immediately')
                        ->icon('heroicon-m-calendar')->iconColor('gray')
                        ->size('sm')->color('gray')->sortable(),

                    Tables\Columns\TextColumn::make('expiry_date')
                        ->label('Expires')
                        ->date('M d, Y')->placeholder('No expiry')
                        ->icon('heroicon-m-calendar-days')->iconColor('gray')
                        ->size('sm')->color('gray')->sortable(),

                    Tables\Columns\TextColumn::make('expires_at')
                        ->label('Auto-Expires')
                        ->dateTime('M d, Y g:i A')->placeholder('—')
                        ->icon('heroicon-m-clock')
                        ->color(fn($record) => $record->expires_at?->isPast() ? 'danger' : 'warning')
                        ->size('sm')->sortable()
                        ->visible($isAdmin),
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
                        ->color('gray')->size('sm')->sortable()
                        ->visible($isAdmin),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Posted')
                        ->since()->color('gray')->size('sm')->sortable(),
                ])->space(1)->alignment('end'),
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
