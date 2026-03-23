<?php

namespace App\Filament\Resources;

use App\Filament\Pages\BiometricMappingManager;
use App\Filament\Resources\BiometricEmployeeMappingResource\Pages;
use App\Models\BiometricEmployeeMapping;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * BiometricEmployeeMappingResource — System backbone for mapping CRUD.
 *
 * ── ROLE IN THE HYBRID ARCHITECTURE ──────────────────────────────────────────
 * This Resource is the PRIMARY data layer for biometric mappings. It owns:
 *   • Creating individual mappings (one employee at a time)
 *   • Editing device IDs and device names
 *   • Soft-deactivating stale mappings (re-enrollment workflow)
 *   • Viewing the full mapping list with active/inactive status
 *   • Audit-safe individual operations with proper validation
 *
 * The BiometricMappingManager custom page handles BULK operations.
 * Use the "Bulk Mapping" button in the header to jump there.
 *
 * ── SAFETY ───────────────────────────────────────────────────────────────────
 * Before saving a new ACTIVE mapping, the Create/Edit pages deactivate any
 * existing active row for the same device_id. This prevents DB constraint
 * violations and ensures no two people share an active device number.
 */
class BiometricEmployeeMappingResource extends Resource
{
    protected static ?string $model = BiometricEmployeeMapping::class;

    protected static ?string $navigationIcon  = 'heroicon-o-finger-print';
    protected static ?string $navigationLabel = 'Biometric Mappings';
    protected static ?string $navigationGroup = 'People & Access';
    protected static ?string $modelLabel      = 'Biometric Mapping';
    protected static ?string $pluralModelLabel = 'Biometric Mappings';
    protected static ?int    $navigationSort  = 3;
    // NOTE: $shouldRegisterNavigation is intentionally absent (defaults to true).
    // This Resource is visible in the sidebar — it is the canonical entry point
    // for mapping management. BiometricMappingManager (Bulk Mapping) is the
    // secondary workflow tool, accessible via the "Bulk Mapping" button below.

    // =========================================================================
    //  PERMISSIONS
    // =========================================================================

    /**
     * Restrict access to administrators only.
     *
     * Biometric device ID mappings are sensitive configuration data — an
     * incorrect mapping sends the wrong employee's DTR to the wrong person.
     * Only admins (isAdmin() = true) may view or interact with this resource.
     *
     * If your system adds an HR_MANAGER role in the future, expand the
     * condition here: e.g. auth()->user()->isAdmin() || auth()->user()->isHrManager()
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Employee → Device Mapping')
                ->description(
                    'Link a system user to their biometric enrollment number. ' .
                    'The Device ID is the number shown in the "No :" column of the XLS Logs sheet.'
                )
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Employee')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn($query) => $query
                                ->whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
                                ->orderBy('name')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false)
                        ->helperText('Select the employee registered in the HRMS system.'),

                    Forms\Components\TextInput::make('device_id')
                        ->label('Device ID (Biometric Enrollment Number)')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('e.g. 42')
                        ->helperText(
                            'The number shown in the "No :" column of the XLS Logs sheet. ' .
                            'This is NOT the government plantilla ID.'
                        )
                        ->rules(['string', 'max:50']),

                    Forms\Components\TextInput::make('device_name')
                        ->label('Device / Reader Name (optional)')
                        ->maxLength(100)
                        ->placeholder('e.g. Main Gate, Training Center Reader')
                        ->helperText('Optional label for multi-device setups.'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText(
                            'Inactive mappings are kept for audit history but are NOT used for matching. ' .
                            'Set to inactive when an employee re-enrolls with a new device number.'
                        )
                        ->onColor('success')
                        ->offColor('danger'),
                ])
                ->columns(2),
        ]);
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn($record) => $record->user?->employee_id
                        ? 'Plantilla ID: ' . $record->user->employee_id
                        : 'No plantilla ID set'),

                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device ID')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Device ID copied!'),

                Tables\Columns\TextColumn::make('device_name')
                    ->label('Device / Reader')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Mapped On')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All mappings')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Employee')
                    ->placeholder('All employees'),
            ], layout: FiltersLayout::AboveContentCollapsible)

            ->defaultSort('is_active', 'desc')

            // ── Header actions ─────────────────────────────────────────────────
            // "Bulk Mapping" links to BiometricMappingManager for initial setup
            // or batch updates. Individual CRUD stays in this Resource.
            ->headerActions([
                Tables\Actions\Action::make('bulk_mapping')
                    ->label('Bulk Mapping')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn() => BiometricMappingManager::getUrl())
                    ->openUrlInNewTab(false),

                Tables\Actions\CreateAction::make()
                    ->label('New Mapping'),
            ])

            // ── Row actions ────────────────────────────────────────────────────
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn($record) => $record->is_active
                            ? 'heroicon-o-x-circle'
                            : 'heroicon-o-check-circle')
                        ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => $record->is_active
                            ? 'Deactivate this mapping?'
                            : 'Activate this mapping?')
                        ->modalDescription(fn($record) => $record->is_active
                            ? 'This device ID will no longer be used for DTR matching. '
                            . 'The record is kept for audit history.'
                            : 'This will make device ID ' . $record->device_id
                            . ' active for ' . $record->user?->name . '.')
                        ->action(function ($record) {
                            if (!$record->is_active) {
                                BiometricEmployeeMapping::deactivateByDeviceId($record->device_id);
                            }

                            $record->update(['is_active' => !$record->is_active]);

                            Log::info('[BiometricMapping] Toggle active', [
                                'mapping_id' => $record->id,
                                'device_id'  => $record->device_id,
                                'user_id'    => $record->user_id,
                                'is_active'  => $record->is_active,
                                'by'         => Auth::id(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title($record->is_active ? 'Mapping activated' : 'Mapping deactivated')
                                ->send();
                        }),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button()
                    ->color('gray'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn($r) => $r->update(['is_active' => false]));
                            Notification::make()
                                ->success()
                                ->title(count($records) . ' mapping(s) deactivated')
                                ->send();
                        }),
                ]),
            ])

            ->emptyStateHeading('No Biometric Mappings Yet')
            ->emptyStateDescription(
                'Add a mapping by clicking "New Mapping" (one at a time) or ' .
                '"Bulk Mapping" to assign device IDs for all employees at once.'
            )
            ->emptyStateIcon('heroicon-o-finger-print')
            ->emptyStateActions([
                Tables\Actions\Action::make('go_bulk')
                    ->label('Bulk Mapping (recommended for initial setup)')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn() => BiometricMappingManager::getUrl()),
            ])
            ->striped();
    }

    // =========================================================================
    //  QUERY
    // =========================================================================

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    // =========================================================================
    //  PAGES
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBiometricEmployeeMappings::route('/'),
            'create' => Pages\CreateBiometricEmployeeMapping::route('/create'),
            'edit'   => Pages\EditBiometricEmployeeMapping::route('/{record}/edit'),
        ];
    }
}
