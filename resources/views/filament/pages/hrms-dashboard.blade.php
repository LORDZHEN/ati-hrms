<x-filament::page>

    {{-- First Login Password Prompt --}}
    @if(auth()->check() && auth()->user()->must_change_password)
<div
    x-data="{ open: true }"
    x-show="open"
    x-trap="open"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
>
        <div
            x-transition
            class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700"
        >
            <h2 class="text-xl font-semibold mb-4 text-red-600 dark:text-red-400">
                Action Required
            </h2>

            <p class="mb-6 text-gray-700 dark:text-gray-300">
                You are currently using a temporary password. Please update it now to secure your account.
            </p>

            <div class="flex justify-end gap-3">
    {{-- Later Button --}}
    <button
        @click="window.location.href='{{ route('filament.hrms.pages.profile') }}'"
        class="px-4 py-2 bg-emerald-600 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 rounded font-medium transition-all duration-200"
    >
        Update Password
    </button>

    {{-- Update Password Button --}}
    {{-- <button
        @click="window.location.href='{{ route('filament.hrms.pages.profile') }}'"
        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded shadow-lg transition-all duration-200"
    >
        Update Password
    </button> --}}
</div>


        </div>
    </div>
    @endif

    {{-- Dashboard Content --}}
    <div class="space-y-6">

        <h2 class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
            {{ $this->getGreeting() }}
        </h2>

        <p class="text-gray-600 dark:text-gray-300">
            @if($this->user->role === 'admin')
                You are logged in as an <span class="font-semibold text-amber-600">Admin</span>. You can manage employees and view all reports.
            @elseif($this->user->role === 'employee')
                You are logged in as an <span class="font-semibold text-emerald-600">Employee</span>. Here’s a quick look at your profile.
            @endif
        </p>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-emerald-50 dark:bg-emerald-900/20 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">Total Users</h3>
                <p class="text-2xl font-bold text-emerald-900 dark:text-white">{{ $this->stats['total_users'] }}</p>
            </div>

            @if($this->user->role === 'admin')
                <div class="bg-amber-50 dark:bg-amber-900/20 shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-amber-700 dark:text-amber-300">Active Employees</h3>
                    <p class="text-2xl font-bold text-amber-900 dark:text-white">{{ $this->stats['active_employees'] }}</p>
                </div>
            @elseif($this->user->role === 'employee')
                <div class="bg-emerald-50 dark:bg-emerald-900/20 shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">Profile Status</h3>
                    <p class="text-2xl font-bold text-emerald-900 dark:text-white capitalize">
                        {{ $this->stats['your_profile_status'] }}
                    </p>
                </div>
            @endif

        </div>

    </div>

</x-filament::page>
