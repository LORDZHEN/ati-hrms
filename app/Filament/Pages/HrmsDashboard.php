<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HrmsDashboard extends Page
{
    protected static string $view = 'filament.pages.hrms-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    // Public properties available in Blade
    public $user;
    public $stats = [];
    public bool $mustChangePassword;

    public function mount(): void
    {
        $this->user = Auth::user();

        // Force first login password change for all users with must_change_password flag
        $this->mustChangePassword = $this->user->must_change_password;

        // Shared stats
        $this->stats['total_users'] = User::count();

        if ($this->user->isAdmin()) {
            $this->stats['active_employees'] = User::where('role', 'employee')
                ->where('status', 'active')
                ->count();
        } elseif ($this->user->isEmployee()) {
            $this->stats['your_profile_status'] = $this->user->is_profile_complete ? 'complete' : 'incomplete';
        }
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            $timeGreeting = 'Good morning';
        } elseif ($hour < 18) {
            $timeGreeting = 'Good afternoon';
        } else {
            $timeGreeting = 'Good evening';
        }

        return $timeGreeting . ', ' . $this->user->full_name . '!';
    }
}
