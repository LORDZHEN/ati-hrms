<style>
    /* Page background */
    .custom-dashboard { background: #f8fafc; padding: 20px; }
    .dark .custom-dashboard { background: #1f2937; }

    /* Greeting title */
    .custom-dashboard h2 { color: #047857 !important; font-weight: 800; }
    .dark .custom-dashboard h2 { color: #4ade80 !important; }

    /* Generic card style */
    .dashboard-card {
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all .2s ease;
        border: 1px solid #e5e7eb;
        display: block;
        background: #ffffff;
    }
    .dark .dashboard-card { background: #374151; border-color: #4b5563; color: #f3f4f6; }
    .dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.12); }

    /* Stats cards */
    .stat-card {
        padding: 18px;
        border-radius: 12px;
        text-align: center;
        background: linear-gradient(135deg,#34d399,#059669);
        color: white;
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .stat-card span { font-size: 2rem; font-weight: 800; }
    .stat-card.amber { background: linear-gradient(135deg,#fbbf24,#b45309); }
    .dark .stat-card { background: #059669; }
    .dark .stat-card.amber { background: #b45309; }

    /* Modal */
    .first-login-modal {
        background: white !important;
        border-radius: 16px;
        border: 1px solid #ddd;
        padding: 25px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .dark .first-login-modal { background: #374151 !important; border-color: #4b5563; color: #f3f4f6; }

    .first-login-btn {
        background: #d41111ff !important; color: white !important;
        padding: 10px 18px; border-radius: 8px; font-weight: 600;
    }
    .first-login-btn:hover { background: #047857 !important; }
    .dark .first-login-btn { background: #10b981 !important; color: #111827 !important; }
    .dark .first-login-btn:hover { background: #059669 !important; }

    /* Icon inside cards */
    .dashboard-icon { width: 44px; height: 44px; margin-right: 10px; }
    .dark .dashboard-icon { filter: brightness(120%); }

    /* Text colors for dark mode */
    .custom-dashboard p, .custom-dashboard h3, .custom-dashboard span { color: #374151; }
    .dark .custom-dashboard p, .dark .custom-dashboard h3, .dark .custom-dashboard span { color: #d1d5db; }
</style>

<x-filament::page>

    {{-- First Login Modal --}}
    @if($mustChangePassword)
        <div x-data="{ open: true }" x-show="open" x-trap="open" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div x-transition class="first-login-modal max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4 text-red-600 dark:text-red-400">Action Required</h2>
                <p class="mb-6 text-gray-700 dark:text-gray-300">
                    You are currently using a temporary password. Please update it now to secure your account.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="window.location.href='{{ route('filament.hrms.pages.profile') }}'" class="first-login-btn">
                        Update Password
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-6 custom-dashboard">

        {{-- Stats Cards --}}
        @if($user->isAdmin())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-4">
                <div class="stat-card flex flex-col items-center justify-center gap-2">
                    <x-heroicon-o-users class="w-6 h-6 text-white dark:text-gray-100" />
                    Total Users
                    <span>{{ $stats['total_users'] }}</span>
                </div>
                <div class="stat-card amber flex flex-col items-center justify-center gap-2">
                    <x-heroicon-o-user-group class="w-6 h-6 text-white dark:text-gray-100" />
                    Active Employees
                    <span>{{ $stats['active_employees'] }}</span>
                </div>
            </div>
        @endif

        {{-- Recent Activity Feed --}}
<div class="mt-6">
    <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">Recent Activities</h3>
    <div class="space-y-2">
        @forelse($recentActivities as $activity)
            @php
                // Define color classes for each activity type
                $colorClasses = match($activity['type']) {
                    'Leave Application' => ['bg' => 'bg-blue-100 dark:bg-blue-800', 'text' => 'text-blue-800 dark:text-blue-100'],
                    'Travel Order' => ['bg' => 'bg-yellow-100 dark:bg-yellow-800', 'text' => 'text-yellow-800 dark:text-yellow-100'],
                    'DTR Approval' => ['bg' => 'bg-emerald-100 dark:bg-emerald-800', 'text' => 'text-emerald-800 dark:text-emerald-100'],
                    'SALN Upload' => ['bg' => 'bg-pink-100 dark:bg-pink-800', 'text' => 'text-pink-800 dark:text-pink-100'],
                    'Locator Slip' => ['bg' => 'bg-purple-100 dark:bg-purple-800', 'text' => 'text-purple-800 dark:text-purple-100'],
                    default => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-800 dark:text-gray-100'],
                };

                // Icon color (same as badge text for consistency)
                $iconColor = str_replace(['bg-', 'dark:bg-'], ['text-', 'dark:text-'], $colorClasses['bg']);
            @endphp

            <div class="dashboard-card flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <x-dynamic-component :component="$activity['icon']" class="w-6 h-6 {{ $iconColor }}" />
                    <div>
                        <p class="text-sm font-medium">
                            <span class="font-semibold">{{ $activity['employee'] }}</span> - {{ $activity['type'] }}
                            {{-- Activity Badge --}}
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold {{ $colorClasses['bg'] }} {{ $colorClasses['text'] }}">
                                {{ $activity['type'] }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['status'] }} | {{ $activity['date'] }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="dashboard-card text-center text-gray-500 dark:text-gray-400">
                No recent activities
            </div>
        @endforelse
    </div>
</div>



        {{-- Modules Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($modules as $module)
                <a href="{{ route($module['route']) }}" class="dashboard-card {{ $module['bg_light'] }} {{ $module['bg_dark'] }}">
                    <div class="flex items-center">
                        <x-dynamic-component :component="$module['icon']" class="dashboard-icon {{ $module['text_light'] }} {{ $module['text_dark'] }}" />
                        <div>
                            <h3 class="text-lg font-semibold {{ $module['text_light'] }} {{ $module['text_dark'] }}">
                                {{ $module['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $user->isAdmin() ? $module['admin_text'] : $module['employee_text'] }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>

</x-filament::page>


