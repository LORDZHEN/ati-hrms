<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Widget
{
    protected static string $view = 'filament.widgets.notification-bell';

    public $notifications = [];
    public $unreadCount = 0;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();

        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead($id): void
    {
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

}
