<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=Playfair+Display:wght@700;800;900&display=swap');

    @keyframes up-shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }

    @keyframes up-fadein {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes up-pulse-green {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
        50%       { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
    }

    .up-modal {
        font-family: 'DM Sans', sans-serif;
        position: relative;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        border: 1.5px solid #e5e7eb;
        box-shadow: 0 24px 80px rgba(0,0,0,0.18), 0 8px 24px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        min-height: 0;
    }

    .up-modal-scroll {
        overflow-y: auto;
        flex: 1;
        min-height: 0;
        scrollbar-width: thin;
        scrollbar-color: #059669 transparent;
        overscroll-behavior: contain;
    }

    .dark .up-modal {
        background: #0f1f18;
        border-color: #1f3429;
        box-shadow: 0 24px 80px rgba(0,0,0,0.65), 0 8px 24px rgba(0,0,0,0.45);
    }

    .up-stripe {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #059669, #f59e0b, #10b981, #d97706, #059669);
        background-size: 200% 100%;
        animation: up-shimmer 3s linear infinite;
        z-index: 10;
    }

    .up-hero {
        position: relative;
        overflow: hidden;
        padding: 1.625rem 1.75rem 1.375rem;
        background: #071a10;
    }

    .up-hero-bg {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 70% 110% at 88% 50%, rgba(5,150,105,0.52) 0%, transparent 65%),
            radial-gradient(ellipse 40% 80% at 98% 8%, rgba(217,119,6,0.28) 0%, transparent 60%),
            linear-gradient(135deg, #071a10 0%, #0f2d1c 55%, #0a1e12 100%);
    }

    .up-hero-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.07) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(ellipse at 75% 50%, black 30%, transparent 75%);
    }

    .up-hero-dots {
        position: absolute;
        top: 0.875rem; right: 1.125rem;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 5px;
        opacity: 0.2;
    }

    .up-hero-dot { width: 3px; height: 3px; border-radius: 50%; background: #10b981; }

    .up-hero-content {
        position: relative; z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .up-hero-left { display: flex; align-items: center; gap: 1rem; }

    .up-hero-avatar { position: relative; flex-shrink: 0; }

    .up-hero-avatar img {
        width: 62px; height: 62px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(16,185,129,0.65);
        box-shadow: 0 0 0 3px rgba(16,185,129,0.18), 0 6px 20px rgba(0,0,0,0.45);
        animation: up-pulse-green 3s ease-in-out infinite;
    }

    .up-hero-online {
        position: absolute; bottom: 2px; right: 2px;
        width: 13px; height: 13px;
        border-radius: 50%;
        background: #10b981;
        border: 2.5px solid #071a10;
        box-shadow: 0 0 6px rgba(16,185,129,0.8);
    }

    .up-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.35rem;
        background: rgba(16,185,129,0.14);
        border: 1px solid rgba(16,185,129,0.28);
        color: #6ee7b7;
        font-size: 0.5875rem; font-weight: 700;
        letter-spacing: 0.1em; text-transform: uppercase;
        padding: 0.2rem 0.625rem; border-radius: 999px;
        margin-bottom: 0.3rem;
    }

    .up-hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.3125rem; font-weight: 800;
        color: #ffffff; line-height: 1.2; letter-spacing: -0.02em;
    }

    .up-hero-sub {
        font-size: 0.78125rem; color: rgba(255,255,255,0.42); margin-top: 0.1rem;
    }

    .up-hero-close {
        flex-shrink: 0;
        width: 32px; height: 32px; border-radius: 9px;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.11);
        color: rgba(255,255,255,0.55);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease;
    }

    .up-hero-close:hover {
        background: rgba(220,38,38,0.18);
        border-color: rgba(220,38,38,0.38);
        color: #fca5a5;
        transform: rotate(90deg);
    }

    .up-body {
        padding: 1.25rem 1.375rem 0.5rem;
        background: #f9faf7;
        display: flex; flex-direction: column; gap: 0.875rem;
    }

    .dark .up-body { background: #0a1612; }

    .up-section {
        background: #ffffff;
        border-radius: 14px;
        border: 1.5px solid #e5e7eb;
        padding: 1rem 1.125rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        animation: up-fadein 0.35s ease-out backwards;
    }

    .dark .up-section { background: #0f1f18; border-color: #1f3429; }
    .up-section:nth-child(1) { animation-delay: 0.05s; }
    .up-section:nth-child(2) { animation-delay: 0.10s; }
    .up-section:nth-child(3) { animation-delay: 0.15s; }

    .up-section-header {
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 0.875rem;
        padding-bottom: 0.625rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .dark .up-section-header { border-bottom-color: #1f3429; }

    .up-section-icon {
        width: 26px; height: 26px; border-radius: 7px;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .dark .up-section-icon {
        background: linear-gradient(135deg, rgba(16,185,129,0.22), rgba(5,150,105,0.14));
    }

    .up-section-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; font-weight: 700;
        color: #059669; letter-spacing: -0.01em;
    }

    .dark .up-section-title { color: #10b981; }

    .up-photo-row { display: flex; align-items: center; gap: 1rem; }

    .up-photo-preview { position: relative; flex-shrink: 0; }

    .up-photo-preview img {
        width: 70px; height: 70px;
        border-radius: 50%; object-fit: cover;
        border: 3px solid #059669;
        box-shadow: 0 4px 16px rgba(5,150,105,0.28);
    }

    .up-photo-badge {
        position: absolute; bottom: 1px; right: 1px;
        width: 18px; height: 18px; border-radius: 50%;
        background: linear-gradient(135deg, #059669, #10b981);
        border: 2px solid #ffffff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(5,150,105,0.45);
    }

    .dark .up-photo-badge { border-color: #0f1f18; }

    .up-photo-info { flex: 1; min-width: 0; }

    .up-photo-name {
        font-size: 0.8125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;
    }

    .dark .up-photo-name { color: #f0fdf4; }

    .up-photo-hint {
        font-size: 0.6875rem; color: #6b7280;
        display: flex; align-items: center; gap: 0.25rem; margin-top: 0.375rem;
    }

    .up-file-input { display: block; width: 100%; font-size: 0.8125rem; color: #374151; cursor: pointer; }
    .dark .up-file-input { color: #d1fae5; }

    .up-file-input::file-selector-button {
        margin-right: 0.75rem; padding: 0.375rem 0.875rem;
        border-radius: 8px; border: none;
        font-size: 0.75rem; font-weight: 700; font-family: 'DM Sans', sans-serif;
        background: linear-gradient(135deg, #059669, #047857);
        color: white; cursor: pointer; transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(5,150,105,0.3);
    }

    .up-file-input::file-selector-button:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        box-shadow: 0 4px 12px rgba(5,150,105,0.4);
    }

    .up-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
    .up-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }

    .up-field { display: flex; flex-direction: column; gap: 0.3rem; }

    .up-label {
        display: flex; align-items: center; gap: 0.35rem;
        font-size: 0.65rem; font-weight: 700; color: #374151;
        text-transform: uppercase; letter-spacing: 0.08em;
    }

    .dark .up-label { color: #d1fae5; }
    .up-ico-green { color: #059669; }
    .up-ico-amber { color: #d97706; }

    .up-input, .up-select {
        width: 100%; padding: 0.5875rem 0.875rem;
        border-radius: 10px; border: 1.5px solid #e5e7eb;
        background: #f9faf7; color: #111827;
        font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 500;
        transition: all 0.18s ease; outline: none;
        -webkit-appearance: none; appearance: none;
    }

    .dark .up-input, .dark .up-select {
        background: #071a10; border-color: #1f3429; color: #f0fdf4;
    }

    .up-input::placeholder { color: #9ca3af; }
    .dark .up-input::placeholder { color: #374151; }

    .up-input:focus, .up-select:focus {
        border-color: #059669; background: #ffffff;
        box-shadow: 0 0 0 3px rgba(5,150,105,0.11), 0 1px 3px rgba(0,0,0,0.05);
    }

    .dark .up-input:focus, .dark .up-select:focus {
        background: #0f1f18;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.14);
    }

    .up-select-wrap { position: relative; }

    .up-select-wrap::after {
        content: '';
        position: absolute; right: 0.875rem; top: 50%;
        transform: translateY(-50%);
        width: 0; height: 0;
        border-left: 4px solid transparent; border-right: 4px solid transparent;
        border-top: 5px solid #6b7280; pointer-events: none;
    }

    .dark .up-select-wrap::after { border-top-color: #6ee7b7; }

    .up-error {
        font-size: 0.6875rem; font-weight: 600; color: #dc2626;
        display: flex; align-items: center; gap: 0.25rem;
    }

    .up-footer {
        display: flex; align-items: center; justify-content: flex-end; gap: 0.625rem;
        padding: 1rem 1.375rem 1.25rem;
        background: #f9faf7;
        border-top: 1px solid #e5e7eb;
    }

    .dark .up-footer { background: #0a1612; border-top-color: #1f3429; }

    .up-footer-left { margin-right: auto; display: flex; align-items: center; gap: 0.5rem; }

    .up-status-pill {
        display: inline-flex; align-items: center; gap: 0.3rem;
        background: rgba(5,150,105,0.09);
        border: 1px solid rgba(5,150,105,0.22);
        color: #059669;
        font-size: 0.65rem; font-weight: 700;
        padding: 0.25rem 0.625rem; border-radius: 999px;
        letter-spacing: 0.04em;
    }

    .dark .up-status-pill {
        background: rgba(16,185,129,0.1);
        border-color: rgba(16,185,129,0.25);
        color: #6ee7b7;
    }

    .up-status-dot {
        width: 6px; height: 6px; border-radius: 50%; background: #10b981;
        animation: up-pulse-green 2s ease-in-out infinite;
    }

    .up-btn-cancel {
        padding: 0.5875rem 1.125rem; border-radius: 10px;
        border: 1.5px solid #e5e7eb; background: #ffffff;
        color: #374151; font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem; font-weight: 600; cursor: pointer;
        transition: all 0.18s ease;
    }

    .dark .up-btn-cancel { background: #0f1f18; border-color: #1f3429; color: #d1fae5; }

    .up-btn-cancel:hover {
        background: #f3f4f6; border-color: #d1d5db; transform: translateY(-1px);
    }

    .dark .up-btn-cancel:hover { background: #1f3429; }

    .up-btn-save {
        position: relative;
        padding: 0.5875rem 1.375rem; border-radius: 10px; border: none;
        background: linear-gradient(135deg, #059669 0%, #047857 55%, #065f46 100%);
        color: white; font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(5,150,105,0.38);
        overflow: hidden;
    }

    .up-btn-save::before {
        content: '';
        position: absolute; top: 0; left: -100%; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.14), transparent);
        transition: left 0.4s ease;
    }

    .up-btn-save:hover::before { left: 100%; }
    .up-btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(5,150,105,0.48); }
    .up-btn-save:active { transform: translateY(0); }

    @media (max-width: 540px) {
        .up-grid-3, .up-grid-2 { grid-template-columns: 1fr; }
        .up-hero-title { font-size: 1.125rem; }
        .up-hero-avatar img { width: 50px; height: 50px; }
    }

    [x-cloak] { display: none !important; }
</style>

{{-- ============================================================
     Wrap everything in a single Alpine x-data so the trigger
     button and the modal share the same "show" state.
     No Livewire openModal / closeModal methods are needed.
     ============================================================ --}}
<div x-data="{ show: false }">

    {{-- Trigger Button --}}
    <x-filament::button
        @click="show = true"
        icon="heroicon-o-pencil-square"
        color="success"
        class="w-full justify-center"
    >
        <span class="font-semibold">Edit Profile Information</span>
    </x-filament::button>

    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-init="$watch('show', val => document.body.style.overflow = val ? 'hidden' : '')"
        @wheel.self.stop
        @touchmove.self.stop
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.82); backdrop-filter: blur(10px);"
        x-cloak
    >
        {{-- Modal Panel --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-6"
            @click.away="show = false"
            @keydown.escape.window="show = false"
            class="up-modal w-full max-w-2xl"
        >
            <div class="up-stripe"></div>

            {{-- HERO — sticky, never scrolls --}}
            <div class="up-hero" style="flex-shrink: 0;">
                <div class="up-hero-bg"></div>
                <div class="up-hero-grid"></div>
                <div class="up-hero-dots">
                    @for($i = 0; $i < 16; $i++)<div class="up-hero-dot"></div>@endfor
                </div>
                <div class="up-hero-content">
                    <div class="up-hero-left">
                        <div class="up-hero-avatar">
                            <img src="{{ $this->avatarUrl }}" alt="Avatar" />
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
                    {{-- Close button uses Alpine --}}
                    <button type="button" @click="show = false" class="up-hero-close">
                        <x-heroicon-o-x-mark style="width:15px;height:15px;" />
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="update" style="display: flex; flex-direction: column; flex: 1; min-height: 0;">
                {{-- Scrollable area: body only --}}
                <div class="up-modal-scroll" style="flex: 1; min-height: 0;">
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
                            <div class="up-photo-preview">
                                <img src="{{ $this->avatarUrl }}" alt="Preview" />
                                <div class="up-photo-badge">
                                    <x-heroicon-o-camera style="width:9px;height:9px;color:white;" />
                                </div>
                            </div>
                            <div class="up-photo-info">
                                <div class="up-photo-name">Choose a new photo</div>
                                <input type="file" wire:model="photo" accept="image/*" class="up-file-input" />
                                <p class="up-photo-hint">
                                    <x-heroicon-o-information-circle style="width:12px;height:12px;flex-shrink:0;" />
                                    JPG, PNG, GIF or WEBP &bull; Max 5 MB
                                </p>
                                @error('photo')
                                    <p class="up-error mt-1">
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
                            <input type="text" wire:model.defer="employee_id" placeholder="e.g. EMP-00123" class="up-input" />
                            @error('employee_id')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                        </div>

                        <div class="up-grid-3" style="margin-bottom:0.75rem;">
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    First Name <span style="color:#dc2626;">*</span>
                                </label>
                                <input type="text" wire:model.defer="first_name" placeholder="First" class="up-input" />
                                @error('first_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    Middle Name
                                </label>
                                <input type="text" wire:model.defer="middle_name" placeholder="Middle" class="up-input" />
                                @error('middle_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-user style="width:11px;height:11px;" class="up-ico-green" />
                                    Last Name <span style="color:#dc2626;">*</span>
                                </label>
                                <input type="text" wire:model.defer="last_name" placeholder="Last" class="up-input" />
                                @error('last_name')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="up-field">
                            <label class="up-label">
                                <x-heroicon-o-tag style="width:11px;height:11px;" class="up-ico-amber" />
                                Suffix
                            </label>
                            <div class="up-select-wrap">
                                <select wire:model.defer="suffix" class="up-select" style="padding-right:2rem;">
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
                    </div>

                    {{-- EMPLOYMENT DETAILS --}}
                    <div class="up-section">
                        <div class="up-section-header">
                            <div class="up-section-icon">
                                <x-heroicon-o-building-office style="width:13px;height:13px;color:#059669;" />
                            </div>
                            <span class="up-section-title">Employment Details</span>
                        </div>

                        <div class="up-grid-2" style="margin-bottom:0.75rem;">
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-briefcase style="width:11px;height:11px;" class="up-ico-green" />
                                    Position
                                </label>
                                <input type="text" wire:model.defer="position" placeholder="e.g. Intern" class="up-input" />
                                @error('position')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                            <div class="up-field">
                                <label class="up-label">
                                    <x-heroicon-o-check-badge style="width:11px;height:11px;" class="up-ico-amber" />
                                    Employment Status
                                </label>
                                <div class="up-select-wrap">
                                    <select wire:model.defer="employment_status" class="up-select" style="padding-right:2rem;">
                                        <option value="">Select Status</option>
                                        <option value="Permanent">Permanent</option>
                                        <option value="Contractual">Contractual</option>
                                        <option value="Probationary">Probationary</option>
                                        <option value="Job Order">Job Order</option>
                                    </select>
                                </div>
                                @error('employment_status')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="up-field">
                            <label class="up-label">
                                <x-heroicon-o-building-office-2 style="width:11px;height:11px;" class="up-ico-green" />
                                Department
                            </label>
                            <div class="up-select-wrap">
                                <select wire:model.defer="department" class="up-select" style="padding-right:2rem;">
                                    <option value="">Select Department</option>
                                    <option value="Administration">Administration</option>
                                    <option value="Human Resources (HR)">Human Resources (HR)</option>
                                    <option value="Finance / Accounting">Finance / Accounting</option>
                                    <option value="Records / Document Control">Records / Document Control</option>
                                    <option value="Training & Extension">Training &amp; Extension</option>
                                    <option value="Planning & Development">Planning &amp; Development</option>
                                    <option value="ICT / Information Technology">ICT / Information Technology</option>
                                    <option value="Monitoring & Evaluation">Monitoring &amp; Evaluation</option>
                                    <option value="Logistics / Operations">Logistics / Operations</option>
                                    <option value="Communications / IEC">Communications / IEC</option>
                                    <option value="Procurement / Property Custody">Procurement / Property Custody</option>
                                    <option value="Support Services">Support Services</option>
                                    <option value="Regional Office">Regional Office</option>
                                </select>
                            </div>
                            @error('department')<p class="up-error"><x-heroicon-o-exclamation-circle style="width:11px;height:11px;" /> {{ $message }}</p>@enderror
                        </div>
                    </div>

                </div>
                {{-- END up-body --}}
                </div>
                {{-- END scrollable area --}}

                {{-- FOOTER — sticky at bottom, never scrolls --}}
                <div class="up-footer" style="flex-shrink: 0;">
                    <div class="up-footer-left">
                        <div class="up-status-pill">
                            <div class="up-status-dot"></div>
                            Editing Profile
                        </div>
                    </div>
                    {{-- Cancel uses Alpine --}}
                    <button type="button" @click="show = false" class="up-btn-cancel">Cancel</button>
                    <button type="submit" class="up-btn-save">
                        <x-heroicon-o-check-circle style="width:15px;height:15px;" />
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@script
<script>
    $wire.on('profileUpdated', () => {
        setTimeout(() => { window.location.reload(); }, 1500);
    });
</script>
@endscript
