<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;

class EditSaln extends EditRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!auth()->user()->hasRole('admin')) {
            unset($data['remarks']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!auth()->user()->hasRole('admin')) {
            unset($data['remarks']);
        }
        return $data;
    }

    protected function getFormSchema(): array
    {
        $schema = parent::getFormSchema();
        $schema[] = Section::make('Admin Remarks')->schema([
            Textarea::make('remarks')->label('Remarks')->rows(3)->visible(fn() => auth()->user()->hasRole('admin')),
        ]);
        return $schema;
    }
}
