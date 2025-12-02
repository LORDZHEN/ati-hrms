<div class="space-y-6 max-w-3xl mx-auto">
    {{-- Open Modal Button --}}
    <x-filament::button
        icon="heroicon-o-user-circle"
        wire:click="$set('editingProfile', true)"
        color="primary"
        class="w-full justify-center"
    >
        Update Profile
    </x-filament::button>

    {{-- Modal --}}
    @if ($editingProfile)
        <div
            x-data="{ show: @entangle('editingProfile') }"
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
                {{-- Header --}}
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <x-heroicon-o-user-circle class="w-7 h-7 text-primary-500" />
                    Update Your Profile
                </h2>

                <form wire:submit.prevent="update" class="space-y-6" enctype="multipart/form-data">
                    {{-- Profile Photo --}}
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary-500">
                            <img src="{{ $this->avatarUrl }}" alt="Profile Picture" class="w-full h-full object-cover" />
                        </div>

                        <div class="flex-1 space-y-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                New Profile Picture
                            </label>

                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="block w-full text-sm text-gray-700 dark:text-white file:border file:rounded file:px-3 file:py-1.5"
                            >

                            <div wire:loading wire:target="photo" class="text-xs text-gray-500 mt-1">Uploading…</div>
                            @error('photo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Employee ID --}}
                    <div>
                        <label class="text-sm font-medium">Employee ID</label>
                        <input type="text" wire:model.defer="employee_id" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('employee_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium">First Name</label>
                            <input type="text" wire:model.defer="first_name" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('first_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Middle Name</label>
                            <input type="text" wire:model.defer="middle_name" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('middle_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Last Name</label>
                            <input type="text" wire:model.defer="last_name" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('last_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Suffix --}}
                    <div>
                        <label class="text-sm font-medium">Suffix</label>
                        <select wire:model.defer="suffix" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">None</option>
                            <option value="Jr">Jr</option>
                            <option value="Sr">Sr</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                        @error('suffix') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Employment Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Position</label>
                            <input type="text" wire:model.defer="position" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('position') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Employment Status</label>
                            <select wire:model.defer="employment_status" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="">Select Status</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Probationary">Probationary</option>
                                <option value="Job Order">Job Order</option>
                            </select>
                            @error('employment_status') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="text-sm font-medium">Department</label>
                        <select wire:model.defer="department" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
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
                        @error('department') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-4 mt-6">
                        <x-filament::button type="submit" icon="heroicon-o-check-circle" color="success">Save Changes</x-filament::button>
                        <x-filament::button color="secondary" wire:click="$set('editingProfile', false)">Cancel</x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    window.addEventListener('profileUpdated', () => {
        const avatar = document.querySelector('[data-user-avatar]');
        if (avatar) {
            const src = avatar.src.split('?')[0];
            avatar.src = src + '?t=' + new Date().getTime(); // Force refresh
        }
    });
</script>
