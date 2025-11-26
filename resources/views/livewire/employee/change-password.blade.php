<div class="space-y-6 max-w-3xl mx-auto">
    {{-- Button to Open Modal --}}
    <x-filament::button
        color="danger"
        icon="heroicon-o-lock-closed"
        wire:click="$set('changingPassword', true)"
        class="w-full justify-center"
    >
        Change Password
    </x-filament::button>

    {{-- Modal --}}
    @if ($changingPassword)
        <div
            x-data="{ show: @entangle('changingPassword') }"
            x-show="show"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        >
            <div
                x-show="show"
                x-transition
                @click.away="show = false"
                class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-2xl p-6 border border-gray-200 dark:border-gray-700"
            >
                {{-- Success Notification --}}
                @if (session()->has('success'))
                    <div class="mb-4 p-3 rounded-md bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Header --}}
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <x-heroicon-o-lock-closed class="w-7 h-7 text-danger-500" />
                    Update Your Password
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    For security, make sure your new password is strong and unique.
                </p>

                {{-- Form --}}
                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    {{-- Current Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Current Password
                        </label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="current_password"
                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Enter your current password"
                        />
                        @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            New Password
                        </label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="password"
                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Enter a new password"
                        />
                        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Confirm New Password
                        </label>
                        <x-filament::input
                            type="password"
                            wire:model.defer="password_confirmation"
                            class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Confirm your new password"
                        />
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-4 mt-4">
                        <x-filament::button type="submit" icon="heroicon-o-check-circle" color="danger">
                            Save New Password
                        </x-filament::button>
                        <x-filament::button color="secondary" wire:click="$set('changingPassword', false)">
                            Cancel
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
