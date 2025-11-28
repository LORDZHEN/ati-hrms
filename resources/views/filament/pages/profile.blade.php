<x-filament::page>
    <div class="max-w-3xl mx-auto space-y-8">

        {{-- Profile Card --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 flex items-center gap-6 border border-green-200 relative overflow-hidden dark:bg-gray-800 dark:border-green-700">
            {{-- Decorative gradient accent --}}
            <div class="absolute inset-y-0 left-0 w-2 bg-gradient-to-b from-green-500 to-yellow-400 rounded-l-2xl"></div>

            <img
                src="{{ Auth::user()->profile_photo_url }}"
                alt="Profile Photo"
                class="w-20 h-20 rounded-full object-cover border-4 border-green-500 shadow-md dark:border-green-400"
            >

            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->email }}</p>

                {{-- Employee ID --}}
                <div class="flex items-center text-sm mt-1">
                    <x-heroicon-o-identification class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400" />
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Employee ID:</span> {{ Auth::user()->employee_id }}
                </div>

                @if (Auth::user()->position)
                    <div class="flex items-center text-sm mt-2">
                        <x-heroicon-o-briefcase class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" />
                        <span class="font-semibold text-gray-800 dark:text-gray-200">Position:</span> {{ Auth::user()->position }}
                    </div>
                @endif

                @if (Auth::user()->employment_status)
                    <div class="flex items-center text-sm mt-1">
                        <x-heroicon-o-check-badge class="w-4 h-4 mr-2 text-yellow-500 dark:text-yellow-400" />
                        <span class="font-semibold text-gray-800 dark:text-gray-200">Status:</span> {{ Auth::user()->employment_status }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Update Profile & Password Components --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 space-y-6 border border-green-200 dark:bg-gray-800 dark:border-green-700">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-8 bg-gradient-to-b from-green-500 to-yellow-400 rounded"></div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Account Settings</h3>
            </div>

            @livewire('employee.update-profile')

            <div class="border-t border-gray-200 pt-6 dark:border-gray-600">
                @livewire('employee.change-password')
            </div>
        </div>
    </div>
</x-filament::page>
