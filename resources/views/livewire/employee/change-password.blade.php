<div class="max-w-3xl mx-auto space-y-6">

    {{-- Open Modal Button --}}
    <x-filament::button
        wire:click="$set('changingPassword', true)"
        icon="heroicon-o-lock-closed"
        color="danger"
        class="w-full justify-center py-3 text-base font-semibold rounded-xl shadow-sm"
    >
        Change Password
    </x-filament::button>

    {{-- Success Toast --}}
    <div
    x-data="{ show: false, message: '' }"
    x-on:password-updated.window="
    @this.set('changingPassword', false);
    setTimeout(() => {
        message = $event.detail.message;
        show = true;
        setTimeout(() => window.location.reload(), 2000);
    }, 300);
"

    x-show="show"
        x-transition
        class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg"
>
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span x-text="message"></span>
    </div>

    {{-- Modal --}}
    @if ($changingPassword)
    <div
        x-data="{ show: @entangle('changingPassword') }"
        x-show="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900"
    >
        <div
            x-show="show"
            x-transition.scale
            @click.away="show = false"
            class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700"
        >

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30">
                        <x-heroicon-o-lock-closed class="w-6 h-6 text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Update Password
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Choose a strong password to keep your account secure
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="updatePassword" class="px-8 py-6 space-y-6">

                {{-- Current Password --}}
                <div x-data="{ show: false }" class="space-y-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Current Password
                    </label>

                    <x-filament::input.wrapper class="mt-2">
                        <x-filament::input
                            x-bind:type="show ? 'text' : 'password'"
                            wire:model.defer="current_password"
                            placeholder="Enter current password"
                            class="rounded-xl px-4 py-2"
                        />
                        <x-slot name="suffix">
                            <button type="button" @click="show = !show">
                                <x-heroicon-o-eye x-show="!show" class="w-5 h-5 text-gray-400" />
                                <x-heroicon-o-eye-slash x-show="show" class="w-5 h-5 text-gray-400" />
                            </button>
                        </x-slot>
                    </x-filament::input.wrapper>

                    @error('current_password')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div x-data="{ showNew: false }" class="space-y-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        New Password
                    </label>
                    <p class="text-xs text-gray-500">
                        Must contain uppercase, lowercase, number, and special character
                    </p>

                    <x-filament::input.wrapper class="mt-2">
                        <x-filament::input
                            x-bind:type="showNew ? 'text' : 'password'"
                            wire:model.live="password"
                            placeholder="Create new password"
                            class="rounded-xl px-4 py-2 transition-all"
                            @class([
                                'ring-2 ring-red-500' => $passwordStrength === 'weak',
                                'ring-2 ring-amber-400' => $passwordStrength === 'medium',
                                'ring-2 ring-emerald-500' => $passwordStrength === 'strong',
                            ])
                        />
                        <x-slot name="suffix">
                            <button type="button" @click="showNew = !showNew">
                                <x-heroicon-o-eye x-show="!showNew" class="w-5 h-5 text-gray-400" />
                                <x-heroicon-o-eye-slash x-show="showNew" class="w-5 h-5 text-gray-400" />
                            </button>
                        </x-slot>
                    </x-filament::input.wrapper>

                    {{-- Strength Bar --}}
                    @if ($password)
                        <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                            <div
                                class="h-full transition-all
                                @if($passwordStrength === 'weak') bg-red-500 w-1/3
                                @elseif($passwordStrength === 'medium') bg-amber-400 w-2/3
                                @else bg-emerald-500 w-full
                                @endif">
                            </div>
                        </div>
                    @endif

                    @if ($passwordStrength === 'strong')
                        <p class="text-xs font-medium text-emerald-600">
                            Strong password ✔
                        </p>
                    @endif
                </div>

                {{-- Confirm Password --}}
                <div x-data="{ showConfirm: false }" class="space-y-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirm Password
                    </label>

                    <x-filament::input.wrapper class="mt-2">
                        <x-filament::input
                            x-bind:type="showConfirm ? 'text' : 'password'"
                            wire:model.live="password_confirmation"
                            placeholder="Confirm password"
                            class="rounded-xl px-4 py-2 transition-all"
                            @class([
                                'ring-2 ring-emerald-500' => $passwordsMatch === true,
                                'ring-2 ring-red-500' => $passwordsMatch === false,
                            ])
                        />
                        <x-slot name="suffix">
                            <button type="button" @click="showConfirm = !showConfirm">
                                <x-heroicon-o-eye x-show="!showConfirm" class="w-5 h-5 text-gray-400" />
                                <x-heroicon-o-eye-slash x-show="showConfirm" class="w-5 h-5 text-gray-400" />
                            </button>
                        </x-slot>
                    </x-filament::input.wrapper>

                    @if ($passwordsMatch === true)
                        <p class="text-xs text-emerald-600">Passwords match ✔</p>
                    @elseif ($passwordsMatch === false)
                        <p class="text-xs text-red-600">Passwords do not match ✖</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-check"
                        color="primary"
                        class="rounded-xl px-6"
                        :disabled="$passwordStrength !== 'strong'"
                    >
                        Save Password
                    </x-filament::button>

                    <x-filament::button
                        color="gray"
                        wire:click="$set('changingPassword', false)"
                        class="rounded-xl"
                    >
                        Cancel
                    </x-filament::button>
                </div>

            </form>
        </div>
    </div>
    @endif
</div>
