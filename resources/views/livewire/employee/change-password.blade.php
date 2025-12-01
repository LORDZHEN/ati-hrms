<div class="max-w-3xl mx-auto space-y-6">

    {{-- Modern Button to Open Modal --}}
    <x-filament::button
        color="danger"
        icon="heroicon-o-lock-closed"
        wire:click="$set('changingPassword', true)"
        class="w-full justify-center text-white bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 transition-all duration-300 shadow-lg transform hover:scale-105"
    >
        Change Password
    </x-filament::button>

    {{-- Success Toast --}}
    <div
    x-data="{ show: false, message: '' }"
    x-on:password-updated.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
    x-on:password-updated.window="$wire.on('passwordUpdated', message => { show = true; setTimeout(() => show = false, 3000); })"
    x-show="show"
    x-transition.opacity
    class="fixed top-5 right-5 p-4 bg-green-500 dark:bg-green-600 text-white rounded-lg shadow-lg z-50"
>
    <span x-text="message"></span>
</div>


    {{-- Modal --}}
    @if ($changingPassword)
        <div
            x-data="{ show: @entangle('changingPassword') }"
            x-show="show"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        >
            <div
                x-show="show"
                x-transition
                @click.away="show = false"
                class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-2xl p-8 border border-gray-200 dark:border-gray-700"
            >

                {{-- Header --}}
                <div class="mb-6 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 justify-center md:justify-start">
                        <x-heroicon-o-lock-closed class="w-7 h-7 text-red-500" />
                        Update Password
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        For security, make sure your new password is strong and unique.
                    </p>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="updatePassword" class="space-y-5">

                    {{-- Current Password --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="current_password"
                            placeholder="Enter your current password"
                            class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-2 focus:ring-red-400 dark:focus:ring-red-600"
                        />
                        @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="password"
                            placeholder="Enter a new password"
                            class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-2 focus:ring-red-400 dark:focus:ring-red-600"
                        />
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirm New Password --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="password_confirmation"
                            placeholder="Confirm your new password"
                            class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-2 focus:ring-red-400 dark:focus:ring-red-600"
                        />
                        @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col md:flex-row items-center md:justify-end gap-3 mt-4">
                        <x-filament::button 
                            type="submit" 
                            icon="heroicon-o-check-circle" 
                            color="success" 
                            class="w-full md:w-auto bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 shadow-md transform hover:scale-105 transition-all duration-300"
                        >
                            Save New Password
                        </x-filament::button>
                        <x-filament::button 
                            color="secondary" 
                            wire:click="$set('changingPassword', false)" 
                            class="w-full md:w-auto"
                        >
                            Cancel
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
