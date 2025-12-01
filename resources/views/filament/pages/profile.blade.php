<x-filament::page>
    <div class="max-w-4xl mx-auto space-y-12">

        {{-- Modern Profile Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="relative flex flex-col md:flex-row items-center md:items-start p-6 gap-6">
                
                {{-- Profile Photo --}}
                <div class="flex-shrink-0">
                    <img
                        src="{{ Auth::user()->profile_photo_url }}"
                        alt="Profile Photo"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md dark:border-gray-700"
                    >
                </div>

                {{-- User Info --}}
                <div class="flex-1 space-y-3 text-center md:text-left">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>

                    {{-- Info Grid --}}
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-identification class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Employee ID:</span>
                            <span>{{ Auth::user()->employee_id }}</span>
                        </div>
                        @if(Auth::user()->position)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-briefcase class="w-5 h-5 text-green-500 dark:text-green-400"/>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Position:</span>
                            <span>{{ Auth::user()->position }}</span>
                        </div>
                        @endif
                        @if(Auth::user()->employment_status)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check-badge class="w-5 h-5 text-yellow-500 dark:text-yellow-400"/>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Status:</span>
                            <span>{{ Auth::user()->employment_status }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-purple-500 dark:text-purple-400"/>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Member Since:</span>
                            <span>{{ Auth::user()->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Decorative Overlay Accent --}}
            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-yellow-300 opacity-10 rounded-3xl pointer-events-none"></div>
        </div>

        {{-- Settings Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Update Profile --}}
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-heroicon-o-user-circle class="w-5 h-5 text-green-500"/>
                    Update Profile
                </h2>
                @livewire('employee.update-profile')
            </div>

            {{-- Change Password --}}
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-heroicon-o-lock-closed class="w-5 h-5 text-purple-500"/>
                    Change Password
                </h2>
                @livewire('employee.change-password')
            </div>
        </div>
    </div>
</x-filament::page>
