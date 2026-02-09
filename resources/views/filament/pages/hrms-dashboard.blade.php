<style>
    /* Page background with subtle gradient */
    .custom-dashboard {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 24px;
        min-height: 100vh;
    }
    .dark .custom-dashboard {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }

    /* Greeting section with animation */
    .greeting-section {
        margin-bottom: 32px;
        animation: fadeInDown 0.6s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-dashboard h2 {
        color: #047857 !important;
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 8px;
    }
    .dark .custom-dashboard h2 { color: #4ade80 !important; }

    .greeting-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }
    .dark .greeting-subtitle { color: #9ca3af; }

    /* Enhanced card style with better shadows */
    .dashboard-card {
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 6px rgba(0,0,0,0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e5e7eb;
        display: block;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .dashboard-card:hover::before {
        transform: translateX(100%);
    }

    .dark .dashboard-card {
        background: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3), 0 4px 6px rgba(0,0,0,0.2);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1), 0 4px 8px rgba(0,0,0,0.06);
    }

    .dark .dashboard-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.4), 0 4px 8px rgba(0,0,0,0.3);
    }

    /* Stats cards with improved gradients */
    .stat-card {
        padding: 24px;
        border-radius: 16px;
        text-align: center;
        background: linear-gradient(135deg, #34d399 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 16px 32px rgba(5, 150, 105, 0.5);
    }

    .stat-card .icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 4px;
    }

    .stat-card span.stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.95;
    }

    .stat-card.amber {
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.4);
    }

    .stat-card.amber:hover {
        box-shadow: 0 16px 32px rgba(217, 119, 6, 0.5);
    }

    .dark .stat-card {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    .dark .stat-card.amber {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    }

    /* Section headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .section-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .dark .section-header h3 {
        color: #f3f4f6;
    }

    .section-header::after {
        content: '';
        flex: 1;
        height: 2px;
        background: linear-gradient(90deg, #e5e7eb, transparent);
    }

    .dark .section-header::after {
        background: linear-gradient(90deg, #4b5563, transparent);
    }

    /* Activity cards enhanced */
    .activity-card {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.3s ease, border-left 0.3s ease;
    display: flex;
    align-items: center;
    gap: 16px;
    border-left: 4px solid transparent;
}

    .dark .activity-card {
    background: #1f2937;
    border-color: transparent;
}

    .activity-card:hover {
    transform: translateX(6px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

    .dark .activity-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .activity-card[data-type="Leave Application"] {
    border-left-color: #3b82f6;
    }
    .activity-card[data-type="Travel Order"] {
        border-left-color: #fbbf24;
    }
    .activity-card[data-type="DTR Approval"] {
        border-left-color: #10b981;
    }
    .activity-card[data-type="SALN Upload"] {
        border-left-color: #ec4899;
    }
    .activity-card[data-type="Locator Slip"] {
        border-left-color: #8b5cf6;
    }

    .activity-icon-wrapper {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .activity-card:hover .activity-icon-wrapper {
        transform: rotate(5deg) scale(1.1);
    }

    /* Badge improvements */
    .activity-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Module cards enhanced */
    .module-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .module-card:hover::before {
        opacity: 1;
    }

    .dark .module-card {
        background: #374151;
        border-color: #4b5563;
    }

    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .module-icon-wrapper {
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .module-card:hover .module-icon-wrapper {
        transform: scale(1.1) rotate(-5deg);
    }

    /* Modal improvements */
    .first-login-modal {
        background: white !important;
        border-radius: 20px;
        border: none;
        padding: 32px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 480px;
    }

    .dark .first-login-modal {
        background: #374151 !important;
        color: #f3f4f6;
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px;
        background: #fee2e2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark .modal-icon {
        background: #7f1d1d;
    }

    .first-login-btn {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
        color: white !important;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
    }

    .first-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.5);
    }

    .dark .first-login-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        background: white;
        border: 2px dashed #e5e7eb;
        border-radius: 16px;
    }

    .dark .empty-state {
        background: #374151;
        border-color: #4b5563;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        opacity: 0.4;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .custom-dashboard {
            padding: 16px;
        }

        .custom-dashboard h2 {
            font-size: 1.5rem;
        }

        .stat-card span.stat-number {
            font-size: 2rem;
        }
    }

    /* Animation classes */
    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stagger-item {
        animation: fadeIn 0.5s ease-out backwards;
    }

    .stagger-item:nth-child(1) { animation-delay: 0.1s; }
    .stagger-item:nth-child(2) { animation-delay: 0.2s; }
    .stagger-item:nth-child(3) { animation-delay: 0.3s; }
    .stagger-item:nth-child(4) { animation-delay: 0.4s; }
    .stagger-item:nth-child(5) { animation-delay: 0.5s; }
</style>

<x-filament::page>

    {{-- First Login Modal --}}
    @if($mustChangePassword)
        <div x-data="{ open: true }" x-show="open" x-trap="open"
             class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="first-login-modal w-full">
                <div class="modal-icon">
                    <x-heroicon-o-shield-exclamation class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
                <h2 class="text-2xl font-bold mb-3 text-red-600 dark:text-red-400 text-center">Action Required</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300 text-center">
                    You are currently using a temporary password. Please update it now to secure your account.
                </p>
                <div class="flex justify-center">
                    <button @click="window.location.href='{{ route('filament.hrms.pages.profile') }}'"
                            class="first-login-btn">
                        <span class="flex items-center gap-2">
                            <x-heroicon-o-lock-closed class="w-5 h-5" />
                            Update Password Now
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-8 custom-dashboard">

        {{-- Greeting Section --}}
        <div class="greeting-section">
            <h2>{{ $this->getGreeting() }}</h2>
            <p class="greeting-subtitle">Welcome back! Here's what's happening today.</p>
        </div>

        {{-- Stats Cards --}}
        @if($user->isAdmin())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="stat-card stagger-item">
                    <div class="icon-wrapper">
                        <x-heroicon-o-users class="w-8 h-8 text-white" />
                    </div>
                    <span class="stat-number">{{ $stats['total_users'] }}</span>
                    <span class="stat-label">Total Users</span>
                </div>
                <div class="stat-card amber stagger-item">
                    <div class="icon-wrapper">
                        <x-heroicon-o-user-group class="w-8 h-8 text-white" />
                    </div>
                    <span class="stat-number">{{ $stats['active_employees'] }}</span>
                    <span class="stat-label">Active Employees</span>
                </div>
            </div>
        @endif

        {{-- Recent Activity Feed --}}
        <div>
            <div class="section-header">
                <x-heroicon-o-clock class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                <h3>Recent Activities</h3>
            </div>

            <div class="space-y-3">
                @forelse($recentActivities as $index => $activity)
                    @php
                        $colorClasses = match($activity['type']) {
                            'Leave Application' => [
                                'bg' => 'bg-blue-50 dark:bg-blue-900/30',
                                'text' => 'text-blue-700 dark:text-blue-300',
                                'icon_bg' => 'bg-blue-100 dark:bg-blue-800/50'
                            ],
                            'Travel Order' => [
                                'bg' => 'bg-yellow-50 dark:bg-yellow-900/30',
                                'text' => 'text-yellow-700 dark:text-yellow-300',
                                'icon_bg' => 'bg-yellow-100 dark:bg-yellow-800/50'
                            ],
                            'DTR Approval' => [
                                'bg' => 'bg-emerald-50 dark:bg-emerald-900/30',
                                'text' => 'text-emerald-700 dark:text-emerald-300',
                                'icon_bg' => 'bg-emerald-100 dark:bg-emerald-800/50'
                            ],
                            'SALN Upload' => [
                                'bg' => 'bg-pink-50 dark:bg-pink-900/30',
                                'text' => 'text-pink-700 dark:text-pink-300',
                                'icon_bg' => 'bg-pink-100 dark:bg-pink-800/50'
                            ],
                            'Locator Slip' => [
                                'bg' => 'bg-purple-50 dark:bg-purple-900/30',
                                'text' => 'text-purple-700 dark:text-purple-300',
                                'icon_bg' => 'bg-purple-100 dark:bg-purple-800/50'
                            ],
                            default => [
                                'bg' => 'bg-gray-50 dark:bg-gray-800/30',
                                'text' => 'text-gray-700 dark:text-gray-300',
                                'icon_bg' => 'bg-gray-100 dark:bg-gray-700/50'
                            ],
                        };
                    @endphp

                    <div class="activity-card stagger-item"
                        style="animation-delay: {{ $index * 0.1 }}s"
                        data-type="{{ $activity['type'] }}">
                        <div class="activity-icon-wrapper {{ $colorClasses['icon_bg'] }}">
                            <x-dynamic-component :component="$activity['icon']"
                                class="w-6 h-6 {{ $colorClasses['text'] }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $activity['employee'] }}
                                </p>
                                <span class="activity-badge {{ $colorClasses['bg'] }} {{ $colorClasses['text'] }}">
                                    {{ $activity['type'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $activity['status'] }} • {{ $activity['date'] }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <x-heroicon-o-inbox class="empty-state-icon text-gray-400 dark:text-gray-500" />
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No recent activities</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Activities will appear here as they happen</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Modules Section --}}
        <div>
            <div class="section-header">
                <x-heroicon-o-squares-2x2 class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                <h3>Quick Access</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($modules as $index => $module)
                    <a href="{{ route($module['route']) }}"
                       class="module-card {{ $module['text_light'] }} stagger-item"
                       style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="module-icon-wrapper {{ $module['bg_light'] }} {{ $module['bg_dark'] }}">
                            <x-dynamic-component :component="$module['icon']"
                                class="w-7 h-7 {{ $module['text_light'] }} {{ $module['text_dark'] }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $module['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $user->isAdmin() ? $module['admin_text'] : $module['employee_text'] }}
                            </p>
                        </div>
                        <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400 dark:text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                @endforeach
            </div>
        </div>

    </div>

</x-filament::page>
