<x-filament-panels::page>
    <div class="max-w-7xl mx-auto space-y-2 p-4">

        {{-- Profile Completion Banner --}}
        {{-- @if($this->profileCompletion < 100)
            <div class="bg-gradient-to-r from-green-50 to-amber-50 dark:from-gray-800 dark:to-gray-800 rounded-xl border-2 border-green-500 dark:border-green-600 p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="flex-shrink-0 p-3 bg-green-600 rounded-xl shadow-md">
                            <x-heroicon-o-information-circle class="w-10 h-10 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Complete Your Profile
                            </h3>
                            <p class="text-sm text-black/50 dark:text-gray-300 mt-1">
                                {{ 100 - $this->profileCompletion }}% remaining to unlock full features
                            </p>
                        </div>
                    </div> --}}

                    {{-- Circular Progress --}}
                    {{-- <div class="relative flex-shrink-0 w-24 h-24">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-black/50" />
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="none"
                                class="text-green-600 dark:text-green-500 transition-all duration-1000"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - $this->profileCompletion / 100) }}"
                                stroke-linecap="round" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $this->profileCompletion }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

        {{-- Main Profile Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Header with ATI Colors --}}
            <div class="bg-gradient-to-r from-green-600 to-amber-500 p-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

                    {{-- Profile Photo with Glow Effect --}}
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-50"></div>
                        <img
                            src="{{ Auth::user()->profile_photo_url }}"
                            alt="Profile Photo"
                            class="relative w-32 h-32 md:w-36 md:h-36 rounded-full object-cover border-4 border-white shadow-2xl ring-4 ring-white/30"
                        >
                        {{-- Status Badge --}}
                        <div class="absolute bottom-2 right-2 bg-white rounded-full p-1.5 shadow-lg">
                            <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>

                    {{-- User Info - Fixed text colors --}}
                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <h1 class="text-2xl md:text-4xl font-bold text-black/50 drop-shadow-lg mb-3">
                                {{ Auth::user()->name }}
                            </h1>
                            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/25 backdrop-blur-md rounded-full border border-white/40 shadow-lg">
                                <x-heroicon-o-envelope class="w-5 h-5 text-black/50" />
                                <p class="text-sm font-medium text-black/50">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        {{-- Quick Stats with proper spacing --}}
                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <div class="px-4 py-2.5 bg-white/25 backdrop-blur-sm rounded-xl border border-white/40 min-w-[120px]">
                                <p class="text-xs text-black/50 font-semibold uppercase tracking-wide">Employee ID</p>
                                <p class="text-base font-bold text-black/50 mt-0.5">{{ Auth::user()->employee_id }}</p>
                            </div>

                            @if(Auth::user()->position)
                            <div class="px-4 py-2.5 bg-white/25 backdrop-blur-sm rounded-xl border border-white/40 min-w-[140px]">
                                <p class="text-xs text-black/50 font-semibold uppercase tracking-wide">Position</p>
                                <p class="text-sm font-bold text-black/50 mt-0.5 truncate">{{ Auth::user()->position }}</p>
                            </div>
                            @endif

                            @if(Auth::user()->employment_status)
                            <div class="px-4 py-2.5 bg-white/25 backdrop-blur-sm rounded-xl border border-white/40 min-w-[120px]">
                                <p class="text-xs text-black/50 font-semibold uppercase tracking-wide">Status</p>
                                <p class="text-sm font-bold text-black/50 mt-0.5">{{ Auth::user()->employment_status }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Information Section --}}
            <div class="p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-lg">
                        <x-heroicon-o-information-circle class="w-6 h-6 text-black/50" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Profile Information
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Full Name --}}
                    @if(Auth::user()->name)
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-green-300 dark:hover:border-green-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/40 dark:to-green-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-user class="w-6 h-6 text-green-700 dark:text-green-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Full Name</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Employee ID --}}
                    @if(Auth::user()->employee_id)
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-amber-300 dark:hover:border-amber-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/40 dark:to-amber-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-identification class="w-6 h-6 text-amber-700 dark:text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Employee ID</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->employee_id }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Position --}}
                    @if(Auth::user()->position)
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-green-300 dark:hover:border-green-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/40 dark:to-green-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-briefcase class="w-6 h-6 text-green-700 dark:text-green-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Position</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->position }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Employment Status --}}
                    @if(Auth::user()->employment_status)
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-amber-300 dark:hover:border-amber-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/40 dark:to-amber-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-check-badge class="w-6 h-6 text-amber-700 dark:text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Employment Status</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->employment_status }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Department --}}
                    @if(Auth::user()->department)
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-green-300 dark:hover:border-green-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/40 dark:to-green-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-building-office class="w-6 h-6 text-green-700 dark:text-green-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Department</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->department }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Member Since --}}
                    <div class="group flex items-center gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-md hover:shadow-xl hover:border-amber-300 dark:hover:border-amber-700 transition-all">
                        <div class="flex-shrink-0 p-3 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/40 dark:to-amber-800/40 rounded-xl group-hover:scale-110 transition-transform">
                            <x-heroicon-o-calendar class="w-6 h-6 text-amber-700 dark:text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide mb-1">Member Since</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Cards with proper spacing --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Update Profile Card --}}
            <div class="bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border-2 border-green-200 dark:border-green-800 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0 p-4 bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-lg">
                        <x-heroicon-o-user-circle class="w-8 h-8 text-black/50" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Update Profile</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Edit your personal information</p>
                    </div>
                </div>
                @livewire('employee.update-profile')
            </div>

            {{-- Change Password Card --}}
            <div class="bg-gradient-to-br from-white to-amber-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border-2 border-amber-200 dark:border-amber-800 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0 p-4 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg">
                        <x-heroicon-o-lock-closed class="w-8 h-8 text-black/50" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Change Password</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your security credentials</p>
                    </div>
                </div>
                @livewire('employee.change-password')
            </div>
        </div>

        {{-- Security Tips --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl border-2 border-green-300 dark:border-green-700 p-6 shadow-xl">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 p-4 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl shadow-lg">
                    <x-heroicon-o-shield-check class="w-7 h-7 text-black/50" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Security Best Practices</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4 text-black/50" />
                            </div>
                            <p class="text-sm text-black/50 dark:text-gray-300 font-medium">Use a strong, unique password</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4 text-black/50" />
                            </div>
                            <p class="text-sm text-black/50 dark:text-gray-300 font-medium">Never share your credentials</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4 text-black/50" />
                            </div>
                            <p class="text-sm text-black/50 dark:text-gray-300 font-medium">Update password regularly</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5">
                                <x-heroicon-o-check class="w-4 h-4 text-black/50" />
                            </div>
                            <p class="text-sm text-black/50 dark:text-gray-300 font-medium">Log out from shared devices</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
