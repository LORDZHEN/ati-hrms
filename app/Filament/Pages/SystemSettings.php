<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\FilingSeasonService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SystemSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $title = 'System Settings';
    protected static ?string $slug = 'system-settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.system-settings';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public bool $filingSeasonEnabled = false;

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
        $this->filingSeasonEnabled = app(FilingSeasonService::class)->isEnabled();
    }

    public function toggleFilingSeason(): void
    {
        $service = app(FilingSeasonService::class);
        $nowEnabled = $service->toggle(notify: true);
        $this->filingSeasonEnabled = $nowEnabled;

        if ($nowEnabled) {
            Notification::make()
                ->title('Filing Season Opened')
                ->body('Employees have been notified via in-app notification.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Filing Season Closed')
                ->body('Employees have been notified via in-app notification.')
                ->warning()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
