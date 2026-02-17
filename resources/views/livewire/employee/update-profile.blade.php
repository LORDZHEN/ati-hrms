<div>
    {{-- Trigger Button --}}
    <x-filament::button
        wire:click="openModal"
        icon="heroicon-o-pencil-square"
        color="success"
        class="w-full justify-center"
    >
        <span class="font-semibold">Edit Profile Information</span>
    </x-filament::button>

    {{-- Modal Overlay --}}
    @if ($editingProfile)
        <div
            x-data="{ show: @entangle('editingProfile').live }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
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
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                @keydown.escape.window="$wire.closeModal()"
            >
                {{-- Modal Header --}}
                <div class="sticky top-0 z-10 bg-gradient-to-r from-green-600 to-yellow-500 px-6 py-5 border-b border-gray-200 dark:border-gray-700 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-user-circle class="w-6 h-6 text-black" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white/80">
                                    Update Your Profile
                                </h2>
                                <p class="text-sm text-white/80">
                                    Keep your information up to date
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                        >
                            <x-heroicon-o-x-mark class="w-5 h-5 text-black" />
                        </button>
                    </div>
                </div>

                {{-- Modal Body with Proper Margins --}}
                <form wire:submit.prevent="update" class="p-4 space-y-2">

                    {{-- Profile Photo Section --}}
                    <div class="flex items-center gap-6 p-3 rounded-xl border-2 bg-white dark:bg-gray-800 border-green-200 dark:border-green-700 shadow-md">
                        <img
                            src="{{ $this->avatarUrl }}"
                            alt="Profile Preview"
                            class="w-20 h-20 rounded-full object-cover border-4 border-green-500 dark:border-green-600 shadow-lg"
                        />
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Profile Picture
                            </label>
                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="block w-full text-sm text-gray-700 dark:text-gray-300
                                    file:mr-4 file:py-2.5 file:px-4
                                    file:rounded-lg file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-gradient-to-r file:from-green-600 file:to-green-700 file:text-white
                                    hover:file:from-green-700 hover:file:to-green-800
                                    file:shadow-md hover:file:shadow-lg
                                    file:transition-all file:cursor-pointer
                                    cursor-pointer"
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                                <x-heroicon-o-information-circle class="w-4 h-4" />
                                JPG, PNG, GIF or WEBP • Max 5 MB
                            </p>
                            @error('photo')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center gap-1 font-semibold">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Employee ID --}}
                    <div class="bg-white dark:bg-gray-800  border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-identification class="w-5 h-5 text-amber-600" />
                            Employee ID <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.defer="employee_id"
                            placeholder="Enter employee ID"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                transition-all placeholder:text-gray-400"
                        />
                        @error('employee_id')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- First Name --}}
                        <div class="bg-white dark:bg-gray-800  border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                                <x-heroicon-o-user class="w-5 h-5 text-green-600" />
                                First Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.defer="first_name"
                                placeholder="First name"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                    transition-all placeholder:text-gray-400"
                            />
                            @error('first_name')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Middle Name --}}
                        <div class="bg-white dark:bg-gray-800  border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                                <x-heroicon-o-user class="w-5 h-5 text-green-600" />
                                Middle Name
                            </label>
                            <input
                                type="text"
                                wire:model.defer="middle_name"
                                placeholder="Middle name"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                    transition-all placeholder:text-gray-400"
                            />
                            @error('middle_name')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                                <x-heroicon-o-user class="w-5 h-5 text-green-600" />
                                Last Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.defer="last_name"
                                placeholder="Last name"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                    transition-all placeholder:text-gray-400"
                            />
                            @error('last_name')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Suffix --}}
                    <div class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-tag class="w-5 h-5 text-amber-600" />
                            Suffix
                        </label>
                        <select
                            wire:model.defer="suffix"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                transition-all"
                        >
                            <option value="">None</option>
                            <option value="Jr">Jr</option>
                            <option value="Sr">Sr</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                        @error('suffix')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Employment Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Position --}}
                        <div class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                                <x-heroicon-o-briefcase class="w-5 h-5 text-green-600" />
                                Position
                            </label>
                            <input
                                type="text"
                                wire:model.defer="position"
                                placeholder="Job position"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                    transition-all placeholder:text-gray-400"
                            />
                            @error('position')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Employment Status --}}
                        <div class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                                <x-heroicon-o-check-badge class="w-5 h-5 text-amber-600" />
                                Employment Status
                            </label>
                            <select
                                wire:model.defer="employment_status"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                    focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                    transition-all"
                            >
                                <option value="">Select Status</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Probationary">Probationary</option>
                                <option value="Job Order">Job Order</option>
                            </select>
                            @error('employment_status')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                        <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-2">
                            <x-heroicon-o-building-office class="w-5 h-5 text-green-600" />
                            Department
                        </label>
                        <select
                            wire:model.defer="department"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium
                                focus:border-green-500 dark:focus:border-green-500 focus:ring-4 focus:ring-green-500/20
                                transition-all"
                        >
                            <option value="">Select Department</option>
                            <option value="Administration">Administration</option>
                            <option value="Human Resources (HR)">Human Resources (HR)</option>
                            <option value="Finance / Accounting">Finance / Accounting</option>
                            <option value="Records / Document Control">Records / Document Control</option>
                            <option value="Training & Extension">Training & Extension</option>
                            <option value="Planning & Development">Planning & Development</option>
                            <option value="ICT / Information Technology">ICT / Information Technology</option>
                            <option value="Monitoring & Evaluation">Monitoring & Evaluation</option>
                            <option value="Logistics / Operations">Logistics / Operations</option>
                            <option value="Communications / IEC">Communications / IEC</option>
                            <option value="Procurement / Property Custody">Procurement / Property Custody</option>
                            <option value="Support Services">Support Services</option>
                            <option value="Regional Office">Regional Office</option>
                        </select>
                        @error('department')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
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
                            icon="heroicon-o-check-circle"
                            color="success"
                            class="flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                        >
                            <span class="font-semibold">Save Changes</span>
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

{{-- Refresh Avatar Script --}}
@script
<script>
    $wire.on('profileUpdated', () => {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
</script>
@endscript
