<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        if (!(Auth::user()?->isAdmin() ?? false)) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('New Event')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-list-bullet')
                ->badge(Event::count()),

            'upcoming' => Tab::make('Upcoming')
                ->icon('heroicon-o-arrow-right-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->upcoming())
                ->badge(Event::upcoming()->count())
                ->badgeColor('success'),

            'this_month' => Tab::make('This Month')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->thisMonth())
                ->badge(Event::thisMonth()->count())
                ->badgeColor('info'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-eye-slash')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(Event::where('is_active', false)->count())
                ->badgeColor('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
