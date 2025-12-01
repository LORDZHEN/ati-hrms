<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\TravelOrder;
use App\Models\LocatorSlip;
use App\Models\Saln;

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

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->mustChangePassword = $this->user->must_change_password;

        // Stats (admin only)
        if ($this->user->isAdmin()) {
            $this->stats['total_users'] = User::count();
            $this->stats['active_employees'] = User::where('role', 'employee')
                ->where('status', 'active')
                ->count();
        }

        // Module cards (shared by all)
        $baseModules = [
            [
                'title' => 'Daily Time Record',
                'route' => 'filament.hrms.resources.daily-time-records.index',
                'icon' => 'heroicon-o-clock',
                'bg_light' => 'bg-emerald-50',
                'bg_dark' => 'dark:bg-emerald-900',
                'text_light' => 'text-emerald-700',
                'text_dark' => 'dark:text-emerald-400',
                'admin_text' => 'View and manage all employee DTRs',
                'employee_text' => 'View your assigned DTRs',
            ],
            [
                'title' => 'Leave Application',
                'route' => 'filament.hrms.resources.leave-applications.index',
                'icon' => 'heroicon-o-calendar',
                'bg_light' => 'bg-blue-50',
                'bg_dark' => 'dark:bg-blue-900',
                'text_light' => 'text-blue-700',
                'text_dark' => 'dark:text-blue-400',
                'admin_text' => 'View & approve leave requests',
                'employee_text' => 'File or view your leave requests',
            ],
            [
                'title' => 'Locator Slip',
                'route' => 'filament.hrms.resources.locator-slip.index',
                'icon' => 'heroicon-o-map-pin',
                'bg_light' => 'bg-purple-50',
                'bg_dark' => 'dark:bg-purple-900',
                'text_light' => 'text-purple-700',
                'text_dark' => 'dark:text-purple-400',
                'admin_text' => 'View & manage locator slips',
                'employee_text' => 'View your locator slips',
            ],
            [
                'title' => 'Travel Order',
                'route' => 'filament.hrms.resources.travel-order.index',
                'icon' => 'heroicon-o-briefcase',
                'bg_light' => 'bg-yellow-50',
                'bg_dark' => 'dark:bg-yellow-900',
                'text_light' => 'text-yellow-700',
                'text_dark' => 'dark:text-yellow-400',
                'admin_text' => 'View & manage travel orders',
                'employee_text' => 'View your travel orders',
            ],
            [
                'title' => 'SALN',
                'route' => 'filament.hrms.resources.saln.index',
                'icon' => 'heroicon-o-document-text',
                'bg_light' => 'bg-pink-50',
                'bg_dark' => 'dark:bg-pink-900',
                'text_light' => 'text-pink-700',
                'text_dark' => 'dark:text-pink-400',
                'admin_text' => 'View & manage SALNs',
                'employee_text' => 'View your filed SALNs',
            ],
        ];

        // If admin, add Employees card first
        if ($this->user->isAdmin()) {
            $employeeModule = [
                'title' => 'Employees',
                'route' => 'filament.hrms.resources.employee-resource.index',
                'icon' => 'heroicon-o-rectangle-stack',
                'bg_light' => 'bg-amber-50',
                'bg_dark' => 'dark:bg-amber-900',
                'text_light' => 'text-amber-700',
                'text_dark' => 'dark:text-amber-400',
                'admin_text' => 'View & manage all employees',
                'employee_text' => '', // Not visible to employees
            ];

            $this->modules = array_merge([$employeeModule], $baseModules);
        } else {
            $this->modules = $baseModules;
        }

        // Recent Activities
        $this->recentActivities = collect();

        if ($this->user->isAdmin()) {
            // Admin sees all recent activities
            $leaves = LeaveApplication::latest()->take(5)->get();
            $travelOrders = TravelOrder::latest()->take(5)->get();
            $locatorSlips = LocatorSlip::latest()->take(5)->get();
            $salns = Saln::latest()->take(5)->get();
        } else {
            // Employee sees only their own activities
            $leaves = LeaveApplication::where('employee_id', $this->user->id)->latest()->take(5)->get();
            $travelOrders = TravelOrder::whereJsonContains('employee_ids', ['id' => $this->user->id])
                ->latest()->take(5)->get();
            $locatorSlips = LocatorSlip::where('user_id', $this->user->id)->latest()->take(5)->get();
            $salns = Saln::where('user_id', $this->user->id)->latest()->take(5)->get();
        }

        // Helper function to push activities
        $addActivity = function ($type, $employee, $status, $date, $icon) {
            $this->recentActivities->push([
                'type' => $type,
                'employee' => $employee,
                'status' => $status,
                'date' => $date,
                'icon' => $icon,
            ]);
        };

        // Populate activities
        foreach ($leaves as $leave) {
            $addActivity(
                'Leave Application',
                $leave->employee?->full_name ?? ($leave->full_name ?? 'Unknown'),
                ucfirst($leave->status),
                $leave->created_at->format('M d, Y'),
                'heroicon-o-calendar'
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
                'heroicon-o-briefcase'
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
                'heroicon-o-map-pin'
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
                'heroicon-o-document-text'
            );
        }

        // Sort by date descending and take latest 5
        $this->recentActivities = $this->recentActivities
            ->sortByDesc('date')
            ->take(5);
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
}
