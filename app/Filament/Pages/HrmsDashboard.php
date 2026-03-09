<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\TravelOrder;
use App\Models\LocatorSlip;
use App\Models\Saln;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\TransactionHistory;
use Carbon\Carbon;

class HrmsDashboard extends Page
{
    protected static string $view = 'filament.pages.hrms-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    public $user;
    public $stats = [];
    public bool $mustChangePassword;
    public $modules = [];
    public $recentActivities = [];
    public $monthlyTrends = [];
    public $upcomingEvents = [];
    public $announcements = [];
    public $birthdayCelebrants = [];
    public $pendingActions = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->mustChangePassword = (bool) $this->user->must_change_password;

        if ($this->user->isAdmin()) {
            $this->stats['total_users'] = User::count();

            $this->stats['active_employees'] = User::whereIn('role', [
                User::ROLE_REGULAR,
                User::ROLE_JOB_ORDER,
            ])
                ->where('status', 'active')
                ->count();

            $this->buildPendingActions();
        } else {
            $this->stats['my_leaves'] = LeaveApplication::where('employee_id', $this->user->id)->count();
            $this->stats['my_travel_orders'] = TravelOrder::whereJsonContains('employee_ids', ['id' => $this->user->id])->count();
            $this->stats['my_pds'] = \App\Models\PersonalDataSheet::where('user_id', $this->user->id)->count();
        }

        $this->buildModules();
        $this->attachModuleStats();
        $this->buildAnnouncements();
        $this->buildUpcomingEvents();
        $this->buildBirthdayCelebrants();

        if ($this->user->isAdmin()) {
            $this->buildRecentActivities();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recent Activities — employee name fallback chain
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildRecentActivities(): void
    {
        $this->recentActivities = TransactionHistory::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn(TransactionHistory $tx) => [
                'type' => $tx->transaction_type,
                'employee' => $this->resolveEmployeeName($tx),
                'status' => ucfirst($tx->status),
                'date' => $tx->created_at->setTimezone('Asia/Manila')->format('M d, Y'),
                'icon' => $tx->resolved_icon,
                'color' => $tx->color ?? 'gray',
                'timestamp' => $tx->created_at,
                'url' => $tx->record_url
                    ?? route('filament.hrms.resources.transaction-histories.view', $tx->id),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Resolve a human-readable employee name from a TransactionHistory record.
     */
    protected function resolveEmployeeName(TransactionHistory $tx): string
    {
        if (filled($tx->employee_name)) {
            return $tx->employee_name;
        }

        if ($tx->relationLoaded('user') && $tx->user) {
            return $tx->user->full_name
                ?? $tx->user->name
                ?? 'Unknown Employee';
        }

        if ($tx->user_id) {
            $user = User::find($tx->user_id);
            if ($user) {
                return $user->full_name ?? $user->name ?? "Employee #{$tx->user_id}";
            }
            return "Employee #{$tx->user_id}";
        }

        return 'Unknown Employee';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pending Actions (admin only)
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildPendingActions(): void
    {
        $this->pendingActions = array_slice([
            [
                'title' => 'Leave Approvals',
                'count' => LeaveApplication::where('status', 'pending')->count(),
                'icon' => 'heroicon-o-calendar',
                'color' => 'blue',
                'route' => 'filament.hrms.resources.leave-applications.index',
            ],
            [
                'title' => 'Travel Orders',
                'count' => TravelOrder::where('status', 'pending')->count(),
                'icon' => 'heroicon-o-briefcase',
                'color' => 'amber',
                'route' => 'filament.hrms.resources.travel-orders.index',
            ],
            [
                'title' => 'Locator Slips',
                'count' => LocatorSlip::where('status', 'pending')->count(),
                'icon' => 'heroicon-o-map-pin',
                'color' => 'purple',
                'route' => 'filament.hrms.resources.locator-slips.index',
            ],
            [
                'title' => 'PDS for Review',
                'count' => \App\Models\PersonalDataSheet::where('status', 'submitted')->count(),
                'icon' => 'heroicon-o-identification',
                'color' => 'green',
                'route' => 'filament.hrms.resources.pds.index',
            ],
            [
                'title' => 'SALN Unreviewed',
                'count' => \App\Models\Saln::whereNull('remarks')->count(),
                'icon' => 'heroicon-o-document-text',
                'color' => 'rose',
                'route' => 'filament.hrms.resources.salns.index',
            ],
        ], 0, 5);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Announcements / Events / Birthdays
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildAnnouncements(): void
    {
        $this->announcements = Announcement::active()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'title' => $a->title,
                'message' => $a->message,
                'date' => $a->created_at->setTimezone('Asia/Manila')->format('M d, Y'),
                'priority' => $a->priority,
                'icon' => $a->icon,
            ])
            ->toArray();
    }

    protected function buildUpcomingEvents(): void
    {
        $this->upcomingEvents = Event::active()
            ->upcoming(60)
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'title' => $e->title,
                'date' => $e->event_date->format('M d, Y'),
                'time' => Carbon::parse($e->event_time)->setTimezone('Asia/Manila')->format('g:i A'),
                'location' => $e->location,
                'type' => $e->type,
                'color' => $e->color,
            ])
            ->toArray();
    }

    protected function buildBirthdayCelebrants(): void
    {
        $currentMonth = now('Asia/Manila')->month;

        $this->birthdayCelebrants = User::whereIn('role', [
            User::ROLE_REGULAR,
            User::ROLE_JOB_ORDER,
        ])
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $currentMonth)
            ->orderByRaw('DAY(birthday)')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'name' => $u->full_name ?? $u->name,
                'date' => $u->birthday
                    ? Carbon::parse($u->birthday)->setTimezone('Asia/Manila')->format('M d')
                    : 'N/A',
                'department' => $u->department ?? 'N/A',
                'photo' => $u->profile_photo_url ?? null,
                'is_today' => $u->birthday
                    && Carbon::parse($u->birthday)
                        ->setTimezone('Asia/Manila')
                        ->isSameDay(now('Asia/Manila')),
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Modules
    //
    // Job Order users only see DTR — all other modules (Leave, Locator Slip,
    // Travel Order, PDS, SALN) are restricted to Regular employees and admins.
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildModules(): void
    {
        $isJobOrder = $this->user->role === User::ROLE_JOB_ORDER;

        // DTR is available to everyone (admin, regular, job_order).
        $dtrModule = [
            'title' => 'Daily Time Record',
            'route' => 'filament.hrms.resources.daily-time-records.index',
            'icon' => 'heroicon-o-clock',
            'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
            'icon_color' => 'text-emerald-600 dark:text-emerald-400',
            'admin_text' => 'View and manage all employee DTRs',
            'employee_text' => 'Track your daily time records',
            'stat_key' => 'dtr_count',
        ];

        // Modules only for Regular employees (and admin).
        $regularModules = [
            [
                'title' => 'Leave Application',
                'route' => 'filament.hrms.resources.leave-applications.index',
                'icon' => 'heroicon-o-calendar',
                'icon_bg' => 'bg-blue-100 dark:bg-blue-900/40',
                'icon_color' => 'text-blue-600 dark:text-blue-400',
                'admin_text' => 'Review & approve leave requests',
                'employee_text' => 'File or view your leave requests',
                'stat_key' => 'leave_count',
            ],
            [
                'title' => 'Locator Slip',
                'route' => 'filament.hrms.resources.locator-slips.index',
                'icon' => 'heroicon-o-map-pin',
                'icon_bg' => 'bg-purple-100 dark:bg-purple-900/40',
                'icon_color' => 'text-purple-600 dark:text-purple-400',
                'admin_text' => 'Manage employee whereabouts',
                'employee_text' => 'Submit your location slips',
                'stat_key' => 'locator_count',
            ],
            [
                'title' => 'Travel Order',
                'route' => 'filament.hrms.resources.travel-orders.index',
                'icon' => 'heroicon-o-briefcase',
                'icon_bg' => 'bg-amber-100 dark:bg-amber-900/40',
                'icon_color' => 'text-amber-600 dark:text-amber-400',
                'admin_text' => 'Process travel authorizations',
                'employee_text' => 'View your travel orders',
                'stat_key' => 'travel_count',
            ],
            [
                'title' => 'Personal Data Sheet',
                'route' => 'filament.hrms.resources.pds.index',
                'icon' => 'heroicon-o-identification',
                'icon_bg' => 'bg-teal-100 dark:bg-teal-900/40',
                'icon_color' => 'text-teal-600 dark:text-teal-400',
                'admin_text' => 'Review & manage employee PDS',
                'employee_text' => 'View or submit your PDS',
                'stat_key' => 'pds_count',
            ],
            [
                'title' => 'SALN',
                'route' => 'filament.hrms.resources.salns.index',
                'icon' => 'heroicon-o-document-text',
                'icon_bg' => 'bg-rose-100 dark:bg-rose-900/40',
                'icon_color' => 'text-rose-600 dark:text-rose-400',
                'admin_text' => 'Review & manage SALNs',
                'employee_text' => 'File your annual SALN',
                'stat_key' => 'saln_count',
            ],
        ];

        if ($this->user->isAdmin()) {
            // Admin sees Employees tile + DTR + all regular modules.
            $this->modules = array_merge(
                [
                    [
                        'title' => 'Employees',
                        'route' => 'filament.hrms.resources.employees.index',
                        'icon' => 'heroicon-o-users',
                        'icon_bg' => 'bg-green-100 dark:bg-green-900/40',
                        'icon_color' => 'text-green-600 dark:text-green-400',
                        'admin_text' => 'Manage employee records',
                        'employee_text' => '',
                        'stat_key' => 'employee_count',
                    ],
                ],
                [$dtrModule],
                $regularModules
            );
        } elseif ($isJobOrder) {
            // Job Order users only see DTR.
            $this->modules = [$dtrModule];
        } else {
            // Regular employees see DTR + all regular modules.
            $this->modules = array_merge([$dtrModule], $regularModules);
        }
    }

    protected function attachModuleStats(): void
    {
        $isJobOrder = $this->user->role === User::ROLE_JOB_ORDER;

        $statCounts = [
            'employee_count' => $this->stats['active_employees'] ?? 0,
            'dtr_count' => 0,
            'leave_count' => $this->user->isAdmin()
                ? LeaveApplication::count()
                : ($this->stats['my_leaves'] ?? 0),
            'locator_count' => $this->user->isAdmin()
                ? LocatorSlip::count()
                : LocatorSlip::where('user_id', $this->user->id)->count(),
            'travel_count' => $this->user->isAdmin()
                ? TravelOrder::count()
                : ($this->stats['my_travel_orders'] ?? 0),
            'pds_count' => $this->user->isAdmin()
                ? \App\Models\PersonalDataSheet::count()
                : ($this->stats['my_pds'] ?? 0),
            'saln_count' => $this->user->isAdmin()
                ? Saln::count()
                : Saln::where('user_id', $this->user->id)->count(),
        ];

        foreach ($this->modules as &$module) {
            $module['stat'] = $statCounts[$module['stat_key']] ?? 0;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function getGreeting(): string
    {
        $hour = now('Asia/Manila')->hour;
        $timeGreeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        return $timeGreeting . ', ' . ($this->user->full_name ?: $this->user->name) . '!';
    }

    public function getCurrentDate(): string
    {
        return now('Asia/Manila')->format('l, F j, Y');
    }

    public function getCurrentTime(): string
    {
        return now('Asia/Manila')->format('g:i A');
    }
}
