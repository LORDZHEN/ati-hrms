@once
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/update-profile.css') }}">
@endpush
@endonce

<div>
    <button
        type="button"
        onclick="window.dispatchEvent(new CustomEvent('open-update-profile-modal'))"
        style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border:none;padding:0.6rem 1.25rem;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:0.875rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;width:100%;justify-content:center;box-shadow:0 4px 14px rgba(5,150,105,0.35);transition:all 0.2s ease;"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(5,150,105,0.45)';"
        onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(5,150,105,0.35)';"
    >
        <svg style="width:16px;height:16px;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
        Edit Profile Information
    </button>

    @if($editingProfile)
    <div
        x-data
        x-init="document.body.style.overflow = 'hidden';"
        @keydown.escape.window="window.dispatchEvent(new CustomEvent('close-update-profile-modal'))"
        @wheel.self.stop
        @touchmove.self.stop
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.82);backdrop-filter:blur(10px);"
    >
        <div
            x-data
            x-init="
                $el.style.opacity='0';
                $el.style.transform='scale(0.95) translateY(24px)';
                requestAnimationFrame(()=>{
                    $el.style.transition='opacity 0.3s ease,transform 0.3s ease';
                    $el.style.opacity='1';
                    $el.style.transform='scale(1) translateY(0)';
                });
            "
            @click.outside="window.dispatchEvent(new CustomEvent('close-update-profile-modal'))"
            class="up-modal w-full max-w-2xl"
        >
            <div class="up-stripe"></div>

            {{-- HERO --}}
            <div class="up-hero" style="flex-shrink:0;">
                <div class="up-hero-bg"></div>
                <div class="up-hero-grid"></div>
                <div class="up-hero-dots">
                    @for($i=0;$i<16;$i++)<div class="up-hero-dot"></div>@endfor
                </div>
                <div class="up-hero-content">
                    <div class="up-hero-left">
                        {{-- Hero avatar — updated by JS via x-bind:src --}}
                        <div class="up-hero-avatar" x-data="{ src: '{{ $this->avatarUrl }}' }"
                             @profile-preview-updated.window="src = $event.detail.url">
                            <img :src="src" alt="Avatar" />
                            <div class="up-hero-online"></div>
                        </div>
                        <div>
                            <div class="up-hero-eyebrow">
                                <x-heroicon-o-user-circle style="width:9px;height:9px;" />
                                Profile Settings
                            </div>
                            <div class="up-hero-title">Update Your Profile</div>
                            <div class="up-hero-sub">Keep your information up to date</div>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="window.dispatchEvent(new CustomEvent('close-update-profile-modal'))"
                        class="up-hero-close"
                    >
                        <x-heroicon-o-x-mark style="width:15px;height:15px;" />
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="update" style="display:flex;flex-direction:column;flex:1;min-height:0;">

                <div class="up-modal-scroll" style="flex:1;min-height:0;">
                <div class="up-body">

                    {{-- PROFILE PHOTO --}}
                    <div class="up-section">
                        <div class="up-section-header">
                            <div class="up-section-icon">
                                <x-heroicon-o-camera style="width:13px;height:13px;color:#059669;" />
                            </div>
                            <span class="up-section-title">Profile Photo</span>
                        </div>
                        <div class="up-photo-row">
                            {{-- Photo preview — updated instantly via JS FileReader --}}
                            <div class="up-photo-preview"
                                 x-data="{ src: '{{ $this->avatarUrl }}' }"
                                 @profile-preview-updated.window="src = $event.detail.url">
                                <img :src="src" alt="Preview" />
                                <div class="up-photo-badge">
                                    <x-heroicon-o-camera style="width:9px;height:9px;color:white;" />
                                </div>
                            </div>
                            <div class="up-photo-info">
                                <div class="up-photo-name">Choose a new photo</div>

                                {{-- Plain HTML file input — no wire:model at all.
                                    JS reads the file as base64 and sends it directly
                                    to the Livewire component via $wire.set().
                                    This bypasses Livewire's temp disk entirely. --}}
                                <input
                                    type="file"
                                    id="photo-input"
                                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                    class="up-file-input"
                                    x-on:change="
                                        const file = $event.target.files[0];
                                        if (!file) return;

                                        // Client-side size check — 5 MB
                                        if (file.size > 5 * 1024 * 1024) {
                                            alert('Image must be 5 MB or smaller.');
                                            $event.target.value = '';
                                            return;
                                        }

                                        const reader = new FileReader();
                                        reader.onload = e => {
                                            const dataUri = e.target.result;

                                            // Instant preview — update both avatars via Alpine event
                                            window.dispatchEvent(new CustomEvent('profile-preview-updated', {
                                                detail: { url: dataUri }
                                            }));

                                            // Send base64 string to Livewire component property
                                            $wire.set('photoBase64', dataUri);
                                        };
                                        reader.readAsDataURL(file);
                                    "
                                />

                                <p class="up-photo-hint">
                                    <x-heroicon-o-information-circle style="width:12px;height:12px;flex-shrink:0;" />
                                    JPG, PNG, GIF or WEBP &bull; Max 5 MB
                                </p>

                                @error('photo')
                                    <p class="up-error" style="margin-top:0.25rem;">
                                        <x-heroicon-o-exclamation-circle style="width:12px;height:12px;" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- PERSONAL INFORMATION --}}
                    <div class="up-section">
                        <div class="up-section-header">
                            <div class="up-section-icon">
                                <x-heroicon-o-user style="width:13px;height:13px;color:#059669;" />
                            </div>
                            <span class="up-section-title">Personal Information</span>
                        </div>

                        <div class="up-field" style="margin-bottom:0.75rem;">
                            <label class="up-label">
                                <x-heroicon-o-identification style="width:11px;height:11px;" class="up-ico-amber" />
                                Employee ID <span style="color:#dc2626;">*</span>
                            </label>
                            <input type="text" wire:model="employee_id" placeholder="e.g. EMP-00123" class="up-input" />
                            @error('employee_id')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                        </div>

                        <div class="up-grid-3" style="margin-bottom:0.75rem;">
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    First Name <span style="color:#dc2626;">*</span>
                                </label>
                                <input type="text" wire:model="first_name" placeholder="First" class="up-input" />
                                @error('first_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    Middle Name
                                </label>
                                <input type="text" wire:model="middle_name" placeholder="Middle" class="up-input" />
                                @error('middle_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    Last Name <span style="color:#dc2626;">*</span>
                                </label>
                                <input type="text" wire:model="last_name" placeholder="Last" class="up-input" />
                                @error('last_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="up-field" style="margin-bottom:0.75rem;">
                            <label class="up-label">
                                <x-heroicon-o-tag style="width:11px;height:11px;" class="up-ico-amber" />
                                Suffix
                            </label>
                            <div class="up-select-wrap">
                                <select wire:model="suffix" class="up-select">
                                    <option value="">None</option>
                                    <option value="Jr">Jr</option>
                                    <option value="Sr">Sr</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                            @error('suffix')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                        </div>

                        <div class="up-field">
                            <label class="up-label">
                                <x-heroicon-o-envelope style="width:11px;height:11px;" class="up-ico-green" />
                                Email Address <span style="color:#dc2626;">*</span>
                            </label>
                            <input type="email" wire:model="email" placeholder="you@example.com" class="up-input" />
                            @error('email')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- EMPLOYMENT DETAILS --}}
                    <div class="up-section">
                        <div class="up-section-header">
                            <div class="up-section-icon">
                                <x-heroicon-o-building-office style="width:13px;height:13px;color:#059669;" />
                            </div>
                            <span class="up-section-title">Employment Details</span>
                        </div>

                        <div class="up-grid-2">
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-briefcase style="width:11px;height:11px;" class="up-ico-green" />
                                    Position
                                </label>
                                <input type="text" wire:model="position" placeholder="e.g. Intern" class="up-input" />
                                @error('position')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>

                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-check-badge style="width:11px;height:11px;" class="up-ico-amber" />
                                    Employment Status
                                </label>
                                @php
                                    $role = Auth::user()->role;
                                    $badgeClass = match($role) {
                                        'admin'     => 'admin',
                                        'regular'   => 'regular',
                                        'job_order' => 'job_order',
                                        default     => 'regular',
                                    };
                                    $statusLabel = match($role) {
                                        'admin'     => 'Admin',
                                        'regular'   => 'Regular',
                                        'job_order' => 'Job Order',
                                        default     => ucfirst($role),
                                    };
                                @endphp
                                <div class="up-status-badge {{ $badgeClass }}">
                                    <span class="up-status-badge-dot"></span>
                                    {{ $statusLabel }}
                                </div>
                                <p class="up-readonly-hint">
                                    <x-heroicon-o-lock-closed style="width:10px;height:10px;" />
                                    Determined by your account role &mdash; not editable
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                </div>

                {{-- FOOTER --}}
                <div class="up-footer" style="flex-shrink:0;">
                    <div class="up-footer-left">
                        <div class="up-status-pill">
                            <div class="up-status-dot"></div>
                            Editing Profile
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="window.dispatchEvent(new CustomEvent('close-update-profile-modal'))"
                        class="up-btn-cancel"
                    >Cancel</button>
                    <button type="submit" class="up-btn-save">
                        <x-heroicon-o-check-circle style="width:15px;height:15px;" />
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif

</div>

@script
<script>
    window.addEventListener('open-update-profile-modal', () => {
        $wire.openModal();
    });

    window.addEventListener('close-update-profile-modal', () => {
        $wire.closeModal();
    });

    $wire.on('modal-closed', () => {
        document.body.style.overflow = '';
    });
</script>
@endscript
