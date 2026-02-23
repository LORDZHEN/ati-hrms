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
        $this->mustChangePassword = $this->user->must_change_password;

        if ($this->user->isAdmin()) {
            $this->stats['total_users'] = User::count();
            $this->stats['active_employees'] = User::where('role', 'employee')
                ->where('status', 'active')
                ->count();

            $this->buildPendingActions();
        } else {
            $this->stats['my_leaves'] = LeaveApplication::where('employee_id', $this->user->id)->count();
            $this->stats['my_travel_orders'] = TravelOrder::whereJsonContains('employee_ids', ['id' => $this->user->id])->count();
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

    protected function buildPendingActions(): void
    {
        $this->pendingActions = [
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
        ];

        $this->pendingActions = array_slice($this->pendingActions, 0, 5);
    }

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

        $this->birthdayCelebrants = User::where('role', 'employee')
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $currentMonth)
            ->orderByRaw('DAY(birthday)')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'name' => $u->full_name ?? $u->name,
                'date' => $u->birthday ? Carbon::parse($u->birthday)->setTimezone('Asia/Manila')->format('M d') : 'N/A',
                'department' => $u->department ?? 'N/A',
                'photo' => $u->profile_photo_url ?? null,
                'is_today' => $u->birthday && Carbon::parse($u->birthday)->setTimezone('Asia/Manila')->isSameDay(now('Asia/Manila')),
            ])
            ->toArray();
    }

    protected function buildModules(): void
    {
        $baseModules = [
            [
                'title' => 'Daily Time Record',
                'route' => 'filament.hrms.resources.daily-time-records.index',
                'icon' => 'heroicon-o-clock',
                'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
                'icon_color' => 'text-emerald-600 dark:text-emerald-400',
                'admin_text' => 'View and manage all employee DTRs',
                'employee_text' => 'Track your daily time records',
                'stat_key' => 'dtr_count',
            ],
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
            $this->modules = array_merge([
                [
                    'title' => 'Employees',
                    'route' => 'filament.hrms.resources.employees.index',
                    'icon' => 'heroicon-o-users',
                    'icon_bg' => 'bg-green-100 dark:bg-green-900/40',
                    'icon_color' => 'text-green-600 dark:text-green-400',
                    'admin_text' => 'Manage employee records',
                    'employee_text' => '',
                    'stat_key' => 'employee_count',
                ]
            ], $baseModules);
        } else {
            $this->modules = $baseModules;
        }
    }

    protected function attachModuleStats(): void
    {
        $statCounts = [
            'employee_count' => $this->stats['active_employees'] ?? 0,
            'dtr_count' => 0,
            'leave_count' => $this->user->isAdmin()
                ? LeaveApplication::count()
                : $this->stats['my_leaves'],
            'locator_count' => $this->user->isAdmin()
                ? LocatorSlip::count()
                : LocatorSlip::where('user_id', $this->user->id)->count(),
            'travel_count' => $this->user->isAdmin()
                ? TravelOrder::count()
                : $this->stats['my_travel_orders'],
            'saln_count' => $this->user->isAdmin()
                ? Saln::count()
                : Saln::where('user_id', $this->user->id)->count(),
        ];

        foreach ($this->modules as &$module) {
            $module['stat'] = $statCounts[$module['stat_key']] ?? 0;
        }
    }

    protected function buildRecentActivities(): void
    {
        $this->recentActivities = collect();

        $leaves = LeaveApplication::latest()->take(6)->get();
        $travelOrders = TravelOrder::with('creator')->latest()->take(6)->get();
        $locatorSlips = LocatorSlip::latest()->take(6)->get();
        $salns = Saln::latest()->take(6)->get();
        $newEmployees = User::where('role', 'employee')->latest()->take(6)->get();

        $addActivity = function ($type, $employee, $status, $date, $icon, $color, $timestamp, $url) {
            $this->recentActivities->push([
                'type' => $type,
                'employee' => $employee,
                'status' => $status,
                'date' => $date,
                'icon' => $icon,
                'color' => $color,
                'timestamp' => $timestamp,
                'url' => $url,
            ]);
        };

        foreach ($leaves as $leave) {
            $addActivity(
                'Leave Application',
                $leave->employee?->full_name ?? ($leave->full_name ?? 'Unknown'),
                ucfirst($leave->status),
                $leave->created_at->setTimezone('Asia/Manila')->format('M d, Y'),
                'heroicon-o-calendar',
                'blue',
                $leave->created_at,
                route('filament.hrms.resources.leave-applications.view', $leave->id)
            );
        }

        foreach ($travelOrders as $order) {
            $addActivity(
                'Travel Order',
                $order->name ?? $order->creator?->full_name ?? $order->creator?->name ?? 'Unknown',
                $order->status_label ?? ucfirst($order->status),
                $order->created_at->setTimezone('Asia/Manila')->format('M d, Y'),
                'heroicon-o-briefcase',
                'amber',
                $order->created_at,
                route('filament.hrms.resources.travel-orders.view', $order->id)
            );
        }

        foreach ($locatorSlips as $slip) {
            $addActivity(
                'Locator Slip',
                $slip->user?->full_name ?? $slip->employee_name ?? 'Unknown',
                $slip->status_label ?? ucfirst($slip->status),
                $slip->created_at?->setTimezone('Asia/Manila')->format('M d, Y') ?? 'N/A',
                'heroicon-o-map-pin',
                'purple',
                $slip->created_at,
                route('filament.hrms.resources.locator-slips.view', $slip->id)
            );
        }

        foreach ($salns as $saln) {
            $employeeName = $saln->user?->full_name
                ?? trim("{$saln->declarant_first_name} {$saln->declarant_middle_initial} {$saln->declarant_family_name}")
                ?: 'Unknown';

            $addActivity(
                'SALN Upload',
                $employeeName,
                'Filed',
                $saln->created_at?->setTimezone('Asia/Manila')->format('M d, Y') ?? 'N/A',
                'heroicon-o-document-text',
                'rose',
                $saln->created_at,
                route('filament.hrms.resources.salns.view', $saln->id)
            );
        }

        foreach ($newEmployees as $employee) {
            $addActivity(
                'New Registration',
                $employee->full_name ?? $employee->name,
                match ($employee->status) {
                    'pending' => 'Pending Approval',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    default => ucfirst($employee->status),
                },
                $employee->created_at->setTimezone('Asia/Manila')->format('M d, Y'),
                'heroicon-o-user-plus',
                'green',
                $employee->created_at,
                route('filament.hrms.resources.employees.view', $employee->id)
            );
        }

        $this->recentActivities = $this->recentActivities
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();
    }

    public function getGreeting(): string
    {
        $hour = now('Asia/Manila')->hour;

        if ($hour < 12)
            $timeGreeting = 'Good morning';
        elseif ($hour < 18)
            $timeGreeting = 'Good afternoon';
        else
            $timeGreeting = 'Good evening';

        return $timeGreeting . ', ' . $this->user->full_name . '!';
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
