<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTimeRecordResource\Pages;
use App\Models\EmployeeDtr;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyTimeRecordResource extends Resource
{
    protected static ?string $model = EmployeeDtr::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Daily Time Record';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?string $modelLabel = 'Daily Time Record';
    protected static ?string $pluralModelLabel = 'Daily Time Record';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('Employee')
                ->options(
                    User::where('role', User::ROLE_EMPLOYEE)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->default(fn() =>
                    Auth::user()->isAdmin()
                        ? null
                        : Auth::id()
                )
                ->required(fn() => Auth::user()->isAdmin())   // Required only for admin
                ->disabled(fn() => !Auth::user()->isAdmin()) // Employee sees but cannot edit
                ->dehydrated(fn() => true),                  // Always send value when saving

            Forms\Components\FileUpload::make('file_path')
                ->label('DTR File')
                ->visible(fn() => Auth::user()->isAdmin())
                ->required(fn() => Auth::user()->isAdmin())
                ->directory('dtr_files')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv',
                ]),

            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->placeholder('Optional note/remarks')
                ->visible(fn() => Auth::user()->isAdmin()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->visible(fn() => Auth::user()->isAdmin()),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded On')
                    ->date('M d, Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()->isAdmin()),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => Auth::user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => Auth::user()->isAdmin()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return Auth::user()->isAdmin()
            ? $query
            : $query->where('employee_id', Auth::id());
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()->isAdmin() && isset($data['file_path'])) {
            $data['file_path'] = $data['file_path']->store('dtr_files');
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyTimeRecords::route('/'),
            'create' => Pages\CreateDailyTimeRecord::route('/create'),
            'edit' => Pages\EditDailyTimeRecord::route('/{record}/edit'),
        ];
    }
}
