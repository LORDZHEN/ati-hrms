<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTimeRecordResource\Pages;
use App\Models\EmployeeDtr;
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
    protected static ?string $slug = 'daily-time-records';
    protected static ?string $title = 'Daily Time Record';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->searchable()
                ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN)
                ->required(),

            Forms\Components\FileUpload::make('file_path')
                ->directory('dtr')
                ->required()
                ->acceptedFileTypes([
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv',
                ])
                ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),

            Forms\Components\Textarea::make('notes')
                ->nullable()
                ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->label('Notes'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded On')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(fn($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === \App\Models\User::ROLE_ADMIN),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === \App\Models\User::ROLE_ADMIN) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
            ->where('employee_id', $user->id);
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
