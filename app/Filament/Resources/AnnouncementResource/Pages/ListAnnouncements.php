<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use App\Models\Announcement;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Announcement')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-list-bullet')
                ->badge(Announcement::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-eye')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(Announcement::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-eye-slash')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(Announcement::where('is_active', false)->count())
                ->badgeColor('gray'),

            'high_priority' => Tab::make('High Priority')
                ->icon('heroicon-o-flag')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('priority', 'high'))
                ->badge(Announcement::where('priority', 'high')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
