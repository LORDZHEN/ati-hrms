<div>
    {{-- Trigger Button --}}
    <x-filament::button
        wire:click="openModal"
        icon="heroicon-o-shield-check"
        color="warning"
        class="w-full justify-center"
    >
        <span class="font-semibold">Change Your Password</span>
    </x-filament::button>

    {{-- Modal Overlay --}}
    @if ($changingPassword)
        <div
            x-data="{ show: @entangle('changingPassword').live }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-8 bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            {{-- Modal Container --}}
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="$wire.closeModal()"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700"
                @keydown.escape.window="$wire.closeModal()"
            >
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-yellow-500 to-green-600 px-6 py-5 border-b border-gray-200 dark:border-gray-700 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-lock-closed class="w-6 h-6 text-white/80" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white/80 drop-shadow-md">
                                    Update Password
                                </h2>
                                <p class="text-sm text-white/90 mt-1">
                                    Choose a strong password to secure your account
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="p-2 hover:bg-white/20 rounded-xl transition-all"
                        >
                            <x-heroicon-o-x-mark class="w-5 h-5 text-white/80" />
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="updatePassword" class="p-6 space-y-5 bg-gradient-to-br from-gray-50 to-amber-50 dark:from-gray-900 dark:to-gray-800">

                    {{-- Current Password --}}
                    <div x-data="{ showCurrent: false }" class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-key class="w-5 h-5 text-amber-600" />
                            Current Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input
                                x-bind:type="showCurrent ? 'text' : 'password'"
                                wire:model.defer="current_password"
                                placeholder="Enter your current password"
                                class="w-full px-4 py-3 pr-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-amber-500 dark:focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20
                                    transition-all placeholder:text-gray-400"
                            />
                            {{-- <button type="button" @click="showCurrent = !showCurrent"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <x-heroicon-o-eye x-show="!showCurrent" class="w-5 h-5" />
                                <x-heroicon-o-eye-slash x-show="showCurrent" class="w-5 h-5" />
                            </button> --}}
                        </div>
                        @error('current_password')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div x-data="{ showNew: false }"
                         class="bg-white dark:bg-gray-800 transition-colors
                            @if($password)
                                @if($passwordStrength === 'weak') border-red-400 dark:border-red-600
                                @elseif($passwordStrength === 'medium') border-yellow-400 dark:border-yellow-600
                                @else border-green-400 dark:border-green-600
                                @endif
                            @else border-gray-200 dark:border-gray-700
                            @endif">

                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-amber-600" />
                            New Password <span class="text-red-600">*</span>
                        </label>

                        <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-xs text-blue-800 dark:text-blue-300 flex items-start gap-2">
                                <x-heroicon-o-information-circle class="w-4 h-4 flex-shrink-0 mt-0.5" />
                                <span>Must contain uppercase, lowercase, number, and special character</span>
                            </p>
                        </div>

                        <div class="relative">
                            <input
                                x-bind:type="showNew ? 'text' : 'password'"
                                wire:model.live="password"
                                placeholder="Create a strong password"
                                class="w-full px-4 py-3 pr-12 rounded-lg border-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:ring-4 transition-all placeholder:text-gray-400
                                    @if($password)
                                        @if($passwordStrength === 'weak') border-red-500 dark:border-red-500 focus:border-red-500 focus:ring-red-500/20
                                        @elseif($passwordStrength === 'medium') border-yellow-500 dark:border-yellow-500 focus:border-yellow-500 focus:ring-yellow-500/20
                                        @else border-green-500 dark:border-green-500 focus:border-green-500 focus:ring-green-500/20
                                        @endif
                                    @else
                                        border-gray-300 dark:border-gray-600 focus:border-amber-500 focus:ring-amber-500/20
                                    @endif"
                            />
                            {{-- <button type="button" @click="showNew = !showNew"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <x-heroicon-o-eye x-show="!showNew" class="w-5 h-5" />
                                <x-heroicon-o-eye-slash x-show="showNew" class="w-5 h-5" />
                            </button> --}}
                        </div>

                        {{-- Password Strength Indicator --}}
                        @if($password)
                            <div class="mt-4 space-y-3">

                                {{-- Strength Bar --}}
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Password Strength</span>
                                        {{-- FIX: inline style avoids Tailwind JIT purging dynamic color classes --}}
                                        <span class="text-xs font-bold" style="color: {{ $passwordStrength === 'weak' ? '#ef4444' : ($passwordStrength === 'medium' ? '#eab308' : '#22c55e') }}">
                                            {{ ucfirst($passwordStrength) }}
                                        </span>
                                    </div>
                                    {{-- FIX: Segmented bar uses inline style so colors are never purged by Tailwind --}}
                                    @php
                                        $seg1Color = $passwordStrength === 'weak' ? '#ef4444' : ($passwordStrength === 'medium' ? '#eab308' : '#22c55e');
                                        $seg2Color = $passwordStrength === 'weak' ? '#e5e7eb' : ($passwordStrength === 'medium' ? '#eab308' : '#22c55e');
                                        $seg3Color = $passwordStrength === 'strong' ? '#22c55e' : '#e5e7eb';
                                    @endphp
                                    <div class="flex gap-1.5" style="height: 10px;">
                                        <div class="flex-1 rounded-full" style="background-color: {{ $seg1Color }};"></div>
                                        <div class="flex-1 rounded-full" style="background-color: {{ $seg2Color }};"></div>
                                        <div class="flex-1 rounded-full" style="background-color: {{ $seg3Color }};"></div>
                                    </div>
                                </div>

                                {{-- Requirements Checklist --}}
                                @if($passwordStrength === 'strong')
                                    {{-- Strong: just show success message --}}
                                    <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            Password is <span class="text-green-600 dark:text-green-400 font-bold">Strong</span> — all requirements met!
                                        </span>
                                    </div>
                                @else
                                    {{-- Weak or Medium: show only MISSING requirements --}}
                                    <div class="space-y-2">
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Missing requirements:
                                        </p>
                                        @if(!$hasMinLength)
                                            <div class="flex items-center gap-2 text-xs p-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                                <x-heroicon-o-x-circle class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0" />
                                                <span class="text-red-700 dark:text-red-300 font-medium">At least 8 characters</span>
                                            </div>
                                        @endif
                                        @if(!$hasUppercase || !$hasLowercase)
                                            <div class="flex items-center gap-2 text-xs p-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                                <x-heroicon-o-x-circle class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0" />
                                                <span class="text-red-700 dark:text-red-300 font-medium">
                                                    @if(!$hasUppercase && !$hasLowercase) Uppercase and lowercase letters
                                                    @elseif(!$hasUppercase) At least one uppercase letter
                                                    @else At least one lowercase letter
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        @if(!$hasNumber)
                                            <div class="flex items-center gap-2 text-xs p-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                                <x-heroicon-o-x-circle class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0" />
                                                <span class="text-red-700 dark:text-red-300 font-medium">At least one number</span>
                                            </div>
                                        @endif
                                        @if(!$hasSpecial)
                                            <div class="flex items-center gap-2 text-xs p-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                                <x-heroicon-o-x-circle class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0" />
                                                <span class="text-red-700 dark:text-red-300 font-medium">At least one special character (!@#$%^&*)</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        @endif

                        @error('password')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div x-data="{ showConfirm: false }"
                         class="bg-white dark:bg-gray-800  transition-colors
                            @if($password_confirmation)
                                @if($passwordsMatch === true) border-green-400 dark:border-green-600
                                @elseif($passwordsMatch === false) border-red-400 dark:border-red-600
                                @else border-gray-200 dark:border-gray-700
                                @endif
                            @else border-gray-200 dark:border-gray-700
                            @endif">

                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-amber-600" />
                            Confirm New Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input
                                x-bind:type="showConfirm ? 'text' : 'password'"
                                wire:model.live="password_confirmation"
                                placeholder="Re-enter your new password"
                                class="w-full px-4 py-3 pr-12 rounded-lg border-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:ring-4 transition-all placeholder:text-gray-400
                                    @if($password_confirmation)
                                        @if($passwordsMatch === true) border-green-500 dark:border-green-500 focus:border-green-500 focus:ring-green-500/20
                                        @elseif($passwordsMatch === false) border-red-500 dark:border-red-500 focus:border-red-500 focus:ring-red-500/20
                                        @else border-gray-300 dark:border-gray-600 focus:border-amber-500 focus:ring-amber-500/20
                                        @endif
                                    @else
                                        border-gray-300 dark:border-gray-600 focus:border-amber-500 focus:ring-amber-500/20
                                    @endif"
                            />
                            {{-- <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <x-heroicon-o-eye x-show="!showConfirm" class="w-5 h-5" />
                                <x-heroicon-o-eye-slash x-show="showConfirm" class="w-5 h-5" />
                            </button> --}}
                        </div>

                        {{-- Match Indicator --}}
                        @if($passwordsMatch === true)
                            <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-xs text-green-700 dark:text-green-300 font-semibold flex items-center gap-2">
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                    Passwords match perfectly!
                                </p>
                            </div>
                        @elseif($passwordsMatch === false)
                            <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-xs text-red-700 dark:text-red-300 font-semibold flex items-center gap-2">
                                    <x-heroicon-o-x-circle class="w-5 h-5" />
                                    Passwords do not match
                                </p>
                            </div>
                        @endif

                        @error('password_confirmation')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-4 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                        <x-filament::button
                            type="button"
                            wire:click="closeModal"
                            color="gray"
                            class="px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-md hover:shadow-lg"
                        >
                            Cancel
                        </x-filament::button>

                        <x-filament::button
                            type="submit"
                            icon="heroicon-o-lock-closed"
                            color="warning"
                            class="flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            :disabled="$passwordStrength !== 'strong' || $passwordsMatch !== true"
                        >
                            <span class="font-semibold">Update Password</span>
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
