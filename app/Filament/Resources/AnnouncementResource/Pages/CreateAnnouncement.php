<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Remove "Create" and "Create & create another" buttons — only keep "Cancel"
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->hidden();
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    // Rename "Cancel" button to be more explicit and style it as primary save
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel');
    }

    // Add a single explicit "Save Announcement" button via header actions
    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveAnnouncement')
                ->label('Save Announcement')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(function () {
                    $this->create();
                }),
        ];
    }
}
