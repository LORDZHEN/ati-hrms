<x-filament-panels::page>
    <div class="max-w-7xl mx-auto space-y-2 p-4">

        {{-- Main Profile Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">

            {{-- Header with ATI Colors --}}
            {{-- FIXED: removed overflow-hidden so photo circle isn't clipped; added pt-10 for breathing room --}}
            <div class="bg-gradient-to-r from-green-600 to-amber-500 p-8 pt-10 rounded-t-xl" style="background: linear-gradient(to right, #16a34a, #f59e0b);">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

                    {{-- Profile Photo --}}
                    {{-- FIXED: removed absolute+blur glow div that caused clipping; simplified wrapper --}}
                    <div class="flex-shrink-0">
                        <img
                            src="{{ $this->getProfilePhotoUrl() }}"
                            alt="Profile Photo"
                            class="w-32 h-32 md:w-36 md:h-36 rounded-full object-cover border-4 border-white shadow-2xl ring-4 ring-white/30"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=16a34a&size=256'"
                        >
                        {{-- Status Badge --}}
                        <div class="flex justify-end -mt-6 mr-1">
                            <div class="bg-white rounded-full p-1.5 shadow-lg inline-flex">
                                <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <h1 class="text-2xl md:text-4xl font-bold text-white drop-shadow-lg drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] mb-3">
                                {{ Auth::user()->name }}
                            </h1>
                            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-black/20 backdrop-blur-md rounded-full border border-white/40 shadow-lg">
                                <x-heroicon-o-envelope class="w-5 h-5 text-white" />
                                <p class="text-sm font-medium text-white">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <div class="px-4 py-2.5 bg-black/20 backdrop-blur-sm rounded-xl border border-white/40 min-w-[120px]">
                                <p class="text-xs text-white/80 font-semibold uppercase tracking-wide">Employee ID</p>
                                <p class="text-base font-bold text-white mt-0.5">{{ Auth::user()->employee_id }}</p>
                            </div>

                            @if(Auth::user()->position)
                            <div class="px-4 py-2.5 bg-black/20 backdrop-blur-sm rounded-xl border border-white/40 min-w-[140px]">
                                <p class="text-xs text-white/80 font-semibold uppercase tracking-wide">Position</p>
                                <p class="text-sm font-bold text-white mt-0.5 truncate">{{ Auth::user()->position }}</p>
                            </div>
                            @endif

                            @if(Auth::user()->employment_status)
                            <div class="px-4 py-2.5 bg-black/20 backdrop-blur-sm rounded-xl border border-white/40 min-w-[120px]">
                                <p class="text-xs text-white/80 font-semibold uppercase tracking-wide">Status</p>
                                <p class="text-sm font-bold text-white mt-0.5">{{ Auth::user()->employment_status }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Information Section --}}
            <div class="p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-b-xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #16a34a, #15803d);">
                        <x-heroicon-o-information-circle class="w-6 h-6 text-white" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Profile Information
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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

        {{-- Action Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border-2 border-green-200 dark:border-green-800 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0 p-4 bg-gradient-to-br from-green-600 to-green-700 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #16a34a, #15803d);">
                        <x-heroicon-o-user-circle class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Update Profile</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Edit your personal information</p>
                    </div>
                </div>
                @livewire('employee.update-profile')
            </div>

            <div class="bg-gradient-to-br from-white to-amber-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border-2 border-amber-200 dark:border-amber-800 p-6 hover:shadow-2xl transition-all">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0 p-4 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <x-heroicon-o-lock-closed class="w-8 h-8 text-white" />
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
                <div class="flex-shrink-0 p-4 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #16a34a, #059669);">
                    <x-heroicon-o-shield-check class="w-7 h-7 text-white" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Security Best Practices</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5" style="background-color: #16a34a;">
                                <x-heroicon-o-check class="w-4 h-4 text-white" />
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Use a strong, unique password</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5" style="background-color: #16a34a;">
                                <x-heroicon-o-check class="w-4 h-4 text-white" />
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Never share your credentials</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5" style="background-color: #16a34a;">
                                <x-heroicon-o-check class="w-4 h-4 text-white" />
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Update password regularly</p>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex-shrink-0 p-1.5 bg-green-600 rounded-full mt-0.5" style="background-color: #16a34a;">
                                <x-heroicon-o-check class="w-4 h-4 text-white" />
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">Log out from shared devices</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
