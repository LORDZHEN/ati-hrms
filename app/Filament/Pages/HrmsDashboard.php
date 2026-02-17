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
    public $quickStats = [];
    public $monthlyTrends = [];
    public $upcomingEvents = [];
    public $announcements = [];
    public $leaveStatusChart = [];
    public $attendanceSummary = [];
    public $birthdayCelebrants = [];
    public $pendingActions = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->mustChangePassword = $this->user->must_change_password;

        // Admin stats with enhanced metrics
        if ($this->user->isAdmin()) {
            $this->stats['total_users'] = User::count();
            $this->stats['active_employees'] = User::where('role', 'employee')
                ->where('status', 'active')
                ->count();
            $this->stats['pending_leaves'] = LeaveApplication::where('status', 'pending')->count();
            $this->stats['approved_today'] = LeaveApplication::where('status', 'approved')
                ->whereDate('updated_at', today())
                ->count();

            // Monthly trend data
            $this->monthlyTrends = [
                'leaves' => LeaveApplication::whereMonth('created_at', now()->month)->count(),
                'travel_orders' => TravelOrder::whereMonth('created_at', now()->month)->count(),
                'locator_slips' => LocatorSlip::whereMonth('created_at', now()->month)->count(),
            ];

            // Build leave status chart
            $this->buildLeaveStatusChart();

            // Build pending actions
            $this->buildPendingActions();
        } else {
            // Employee personal stats
            $this->stats['my_leaves'] = LeaveApplication::where('employee_id', $this->user->id)->count();
            $this->stats['pending_leaves'] = LeaveApplication::where('employee_id', $this->user->id)
                ->where('status', 'pending')
                ->count();
            $this->stats['approved_leaves'] = LeaveApplication::where('employee_id', $this->user->id)
                ->where('status', 'approved')
                ->count();
            $this->stats['my_travel_orders'] = TravelOrder::whereJsonContains('employee_ids', ['id' => $this->user->id])->count();

            // Employee attendance summary
            $this->buildAttendanceSummary();
        }

        // Build shared features
        $this->buildQuickStats();
        $this->buildModules();
        $this->attachModuleStats();
        $this->buildRecentActivities();
        $this->buildAnnouncements();
        $this->buildUpcomingEvents();
        $this->buildBirthdayCelebrants();
    }

    protected function buildLeaveStatusChart(): void
    {
        $this->leaveStatusChart = [
            [
                'label' => 'Approved',
                'count' => LeaveApplication::where('status', 'approved')->count(),
                'color' => 'green',
                'percentage' => 0,
            ],
            [
                'label' => 'Pending',
                'count' => LeaveApplication::where('status', 'pending')->count(),
                'color' => 'amber',
                'percentage' => 0,
            ],
            [
                'label' => 'Rejected',
                'count' => LeaveApplication::where('status', 'rejected')->count(),
                'color' => 'red',
                'percentage' => 0,
            ],
        ];

        $total = array_sum(array_column($this->leaveStatusChart, 'count'));
        if ($total > 0) {
            foreach ($this->leaveStatusChart as &$item) {
                $item['percentage'] = round(($item['count'] / $total) * 100, 1);
            }
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
        ];
    }

    protected function buildAttendanceSummary(): void
    {
        // This is sample data - integrate with your actual DTR system
        $this->attendanceSummary = [
            'present' => 20,
            'late' => 2,
            'absent' => 1,
            'on_leave' => 2,
            'total_days' => 25,
        ];
    }

    protected function buildAnnouncements(): void
    {
        // Get active announcements from database
        $this->announcements = Announcement::active()
            ->latest()
            ->take(5)
            ->get()
            ->map(function($announcement) {
                return [
                    'title' => $announcement->title,
                    'message' => $announcement->message,
                    'date' => $announcement->created_at->format('M d, Y'),
                    'priority' => $announcement->priority,
                    'icon' => $announcement->icon,
                ];
            })
            ->toArray();
    }

    protected function buildUpcomingEvents(): void
    {
        // Get upcoming events from database
        $this->upcomingEvents = Event::active()
            ->upcoming(60) // Next 60 days
            ->take(5)
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'date' => $event->event_date->format('M d, Y'),
                    'time' => Carbon::parse($event->event_time)->format('g:i A'),
                    'location' => $event->location,
                    'type' => $event->type,
                    'color' => $event->color,
                ];
            })
            ->toArray();
    }

    protected function buildBirthdayCelebrants(): void
    {
        // Get employees with birthdays this month
        $currentMonth = now()->month;

        $celebrants = User::where('role', 'employee')
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $currentMonth)
            ->orderByRaw('DAY(birthday)')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->full_name ?? $user->name,
                    'date' => $user->birthday ? Carbon::parse($user->birthday)->format('M d') : 'N/A',
                    'department' => $user->department ?? 'N/A',
                    'photo' => $user->profile_photo_url ?? null,
                    'is_today' => $user->birthday && Carbon::parse($user->birthday)->isSameDay(now()),
                ];
            })
            ->toArray();

        $this->birthdayCelebrants = $celebrants;
    }

    protected function buildQuickStats(): void
    {
        if ($this->user->isAdmin()) {
            $this->quickStats = [
                [
                    'label' => 'Pending Approvals',
                    'value' => $this->stats['pending_leaves'],
                    'icon' => 'heroicon-o-clock',
                    'color' => 'orange',
                    'trend' => 'Needs Review',
                    'trend_up' => null,
                ],
                [
                    'label' => 'Approved Today',
                    'value' => $this->stats['approved_today'],
                    'icon' => 'heroicon-o-check-circle',
                    'color' => 'green',
                    'trend' => 'Processed',
                    'trend_up' => true,
                ],
                [
                    'label' => 'Active Employees',
                    'value' => $this->stats['active_employees'],
                    'icon' => 'heroicon-o-users',
                    'color' => 'blue',
                    'trend' => 'Total Staff',
                    'trend_up' => true,
                ],
                [
                    'label' => 'This Month',
                    'value' => array_sum($this->monthlyTrends),
                    'icon' => 'heroicon-o-chart-bar',
                    'color' => 'purple',
                    'trend' => 'Submissions',
                    'trend_up' => true,
                ],
            ];
        } else {
            $this->quickStats = [
                [
                    'label' => 'Pending Requests',
                    'value' => $this->stats['pending_leaves'],
                    'icon' => 'heroicon-o-clock',
                    'color' => 'orange',
                    'trend' => 'In Review',
                    'trend_up' => null,
                ],
                [
                    'label' => 'Approved Leaves',
                    'value' => $this->stats['approved_leaves'],
                    'icon' => 'heroicon-o-check-badge',
                    'color' => 'green',
                    'trend' => 'This Year',
                    'trend_up' => true,
                ],
                [
                    'label' => 'Total Leaves',
                    'value' => $this->stats['my_leaves'],
                    'icon' => 'heroicon-o-calendar',
                    'color' => 'blue',
                    'trend' => 'All Time',
                    'trend_up' => null,
                ],
            ];
        }
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
            $employeeModule = [
                'title' => 'Employees',
                'route' => 'filament.hrms.resources.employees.index',
                'icon' => 'heroicon-o-users',
                'icon_bg' => 'bg-green-100 dark:bg-green-900/40',
                'icon_color' => 'text-green-600 dark:text-green-400',
                'admin_text' => 'Manage employee records',
                'employee_text' => '',
                'stat_key' => 'employee_count',
            ];

            $this->modules = array_merge([$employeeModule], $baseModules);
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

        if ($this->user->isAdmin()) {
            $leaves = LeaveApplication::latest()->take(6)->get();
            $travelOrders = TravelOrder::latest()->take(6)->get();
            $locatorSlips = LocatorSlip::latest()->take(6)->get();
            $salns = Saln::latest()->take(6)->get();
        } else {
            $leaves = LeaveApplication::where('employee_id', $this->user->id)->latest()->take(6)->get();
            $travelOrders = TravelOrder::whereJsonContains('employee_ids', ['id' => $this->user->id])
                ->latest()->take(6)->get();
            $locatorSlips = LocatorSlip::where('user_id', $this->user->id)->latest()->take(6)->get();
            $salns = Saln::where('user_id', $this->user->id)->latest()->take(6)->get();
        }

        $addActivity = function ($type, $employee, $status, $date, $icon, $color) {
            $this->recentActivities->push([
                'type' => $type,
                'employee' => $employee,
                'status' => $status,
                'date' => $date,
                'icon' => $icon,
                'color' => $color,
                'timestamp' => Carbon::parse($date),
            ]);
        };

        foreach ($leaves as $leave) {
            $addActivity(
                'Leave Application',
                $leave->employee?->full_name ?? ($leave->full_name ?? 'Unknown'),
                ucfirst($leave->status),
                $leave->created_at->format('M d, Y'),
                'heroicon-o-calendar',
                'blue'
            );
        }

        foreach ($travelOrders as $order) {
            $employeeName = 'Unknown';
            if (!empty($order->employee_details) && is_array($order->employee_details)) {
                $firstEmployee = $order->employee_details[0] ?? null;
                $employeeName = $firstEmployee['name'] ?? 'Unknown';
            }

            $addActivity(
                'Travel Order',
                $employeeName,
                $order->status_label ?? ucfirst($order->status),
                $order->created_at->format('M d, Y'),
                'heroicon-o-briefcase',
                'amber'
            );
        }

        foreach ($locatorSlips as $slip) {
            $employeeName = $slip->user?->full_name ?? $slip->employee_name ?? 'Unknown';
            $statusLabel = $slip->status_label ?? ucfirst($slip->status);

            $addActivity(
                'Locator Slip',
                $employeeName,
                $statusLabel,
                $slip->created_at?->format('M d, Y') ?? 'N/A',
                'heroicon-o-map-pin',
                'purple'
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
                $saln->created_at?->format('M d, Y') ?? 'N/A',
                'heroicon-o-document-text',
                'rose'
            );
        }

        $this->recentActivities = $this->recentActivities
            ->sortByDesc('timestamp')
            ->take(6)
            ->values();
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;

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
        return now()->format('l, F j, Y');
    }

    public function getCurrentTime(): string
    {
        return now()->format('g:i A');
    }
}
