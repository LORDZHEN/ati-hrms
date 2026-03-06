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

    <style>
        @verbatim
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&family=Playfair+Display:wght@700;800&display=swap');
        @keyframes cp-shimmer  { 0%   { background-position:0%   50%; } 100% { background-position:200% 50%; } }
        @keyframes cp-fadein   { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        @keyframes cp-pulse-am { 0%,100% { box-shadow:0 0 0 0 rgba(245,158,11,.5); } 50% { box-shadow:0 0 0 8px rgba(245,158,11,0); } }
        @keyframes cp-dot-pu   { 0%,100% { box-shadow:0 0 0 0 rgba(245,158,11,.5); } 50% { box-shadow:0 0 0 4px rgba(245,158,11,0); } }
        @endverbatim

        .cp-modal{font-family:'DM Sans',sans-serif;position:relative;background:#fff;border-radius:20px;overflow:hidden;border:1.5px solid #e5e7eb;box-shadow:0 24px 80px rgba(0,0,0,.18),0 8px 24px rgba(0,0,0,.10);display:flex;flex-direction:column;max-height:90vh;min-height:0;}
        .dark .cp-modal{background:#1a120a;border-color:#2d1f0a;box-shadow:0 24px 80px rgba(0,0,0,.65),0 8px 24px rgba(0,0,0,.45);}
        .cp-scroll{overflow-y:auto;flex:1;min-height:0;scrollbar-width:thin;scrollbar-color:#d97706 transparent;overscroll-behavior:contain;}
        .cp-stripe{position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#d97706,#059669,#f59e0b,#10b981,#d97706);background-size:200% 100%;animation:cp-shimmer 3s linear infinite;z-index:10;}
        .cp-hero{position:relative;overflow:hidden;padding:1.625rem 1.75rem 1.375rem;background:#1a0c00;flex-shrink:0;}
        .cp-hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse 70% 110% at 88% 50%,rgba(217,119,6,.52) 0%,transparent 65%),radial-gradient(ellipse 40% 80% at 98% 8%,rgba(5,150,105,.28) 0%,transparent 60%),linear-gradient(135deg,#1a0c00 0%,#2d1500 55%,#1a0e00 100%);}
        .cp-hero-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(245,158,11,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(245,158,11,.07) 1px,transparent 1px);background-size:28px 28px;mask-image:radial-gradient(ellipse at 75% 50%,black 30%,transparent 75%);}
        .cp-hero-dots{position:absolute;top:.875rem;right:1.125rem;display:grid;grid-template-columns:repeat(4,1fr);gap:5px;opacity:.2;}
        .cp-hero-dot{width:3px;height:3px;border-radius:50%;background:#f59e0b;}
        .cp-hero-content{position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
        .cp-hero-left{display:flex;align-items:center;gap:1rem;}
        .cp-hero-icon{width:62px;height:62px;border-radius:50%;background:rgba(245,158,11,.15);border:3px solid rgba(245,158,11,.55);box-shadow:0 0 0 3px rgba(245,158,11,.18),0 6px 20px rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;animation:cp-pulse-am 3s ease-in-out infinite;}
        .cp-hero-eyebrow{display:inline-flex;align-items:center;gap:.35rem;background:rgba(245,158,11,.14);border:1px solid rgba(245,158,11,.28);color:#fcd34d;font-size:.5875rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.2rem .625rem;border-radius:999px;margin-bottom:.3rem;}
        .cp-hero-title{font-family:'Playfair Display',serif;font-size:1.3125rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.02em;}
        .cp-hero-sub{font-size:.78125rem;color:rgba(255,255,255,.42);margin-top:.1rem;}
        .cp-hero-close{flex-shrink:0;width:32px;height:32px;border-radius:9px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.11);color:rgba(255,255,255,.55);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s ease;}
        .cp-hero-close:hover{background:rgba(220,38,38,.18);border-color:rgba(220,38,38,.38);color:#fca5a5;transform:rotate(90deg);}
        .cp-body{padding:1.25rem 1.375rem .5rem;background:#fafaf7;display:flex;flex-direction:column;gap:.875rem;}
        .dark .cp-body{background:#110c04;}
        .cp-section{background:#fff;border-radius:14px;border:1.5px solid #e5e7eb;padding:1rem 1.125rem;box-shadow:0 1px 3px rgba(0,0,0,.05);animation:cp-fadein .35s ease-out backwards;}
        .dark .cp-section{background:#1f1408;border-color:#2d1f0a;}
        .cp-section:nth-child(1){animation-delay:.05s;}
        .cp-section:nth-child(2){animation-delay:.10s;}
        .cp-section:nth-child(3){animation-delay:.15s;}
        .cp-section-hdr{display:flex;align-items:center;gap:.5rem;margin-bottom:.875rem;padding-bottom:.625rem;border-bottom:1px solid #f3f4f6;}
        .dark .cp-section-hdr{border-bottom-color:#2d1f0a;}
        .cp-section-ico{width:26px;height:26px;border-radius:7px;background:linear-gradient(135deg,#fef3c7,#fde68a);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .dark .cp-section-ico{background:linear-gradient(135deg,rgba(245,158,11,.22),rgba(217,119,6,.14));}
        .cp-section-ttl{font-size:.8125rem;font-weight:700;color:#d97706;letter-spacing:-.01em;}
        .dark .cp-section-ttl{color:#fbbf24;}
        .cp-field{display:flex;flex-direction:column;gap:.3rem;}
        .cp-lbl{display:flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.08em;}
        .dark .cp-lbl{color:#fef3c7;}
        .cp-input{width:100%;padding:.5875rem .875rem;border-radius:10px;border:1.5px solid #e5e7eb;background:#f9faf7;color:#111827;font-size:.875rem;font-weight:500;transition:all .18s ease;outline:none;}
        .dark .cp-input{background:#1a0c00;border-color:#2d1f0a;color:#fef9f0;}
        .cp-input::placeholder{color:#9ca3af;}
        .dark .cp-input::placeholder{color:#4b3a1e;}
        .cp-input:focus{border-color:#d97706;background:#fff;box-shadow:0 0 0 3px rgba(217,119,6,.11);}
        .dark .cp-input:focus{background:#1f1408;box-shadow:0 0 0 3px rgba(245,158,11,.14);}
        .cp-border-red   {border-color:#ef4444 !important;}
        .cp-border-yellow{border-color:#eab308 !important;}
        .cp-border-green {border-color:#22c55e !important;}
        .cp-pw-wrap{position:relative;display:block;width:100%;}
        .cp-pw-wrap input{padding-left:.875rem !important;padding-right:2.75rem !important;padding-top:.5875rem !important;padding-bottom:.5875rem !important;}
        .cp-eye{position:absolute !important;top:50% !important;right:.75rem !important;transform:translateY(-50%) !important;display:flex !important;align-items:center !important;background:none !important;border:none !important;padding:0 !important;cursor:pointer !important;z-index:10 !important;width:1.25rem !important;height:1.25rem !important;color:#9ca3af;transition:color .18s;}
        .cp-eye:hover{color:#d97706;}
        .dark .cp-eye:hover{color:#fbbf24;}
        .cp-badge{display:inline-flex;align-items:center;gap:.3rem;font-size:.65rem;font-weight:700;padding:.2rem .625rem;border-radius:999px;letter-spacing:.04em;text-transform:uppercase;}
        .cp-badge-weak  {background:rgba(220,38,38,.1); color:#dc2626;border:1px solid rgba(220,38,38,.25);}
        .cp-badge-medium{background:rgba(234,179,8,.1); color:#ca8a04;border:1px solid rgba(234,179,8,.25);}
        .cp-badge-strong{background:rgba(5,150,105,.1); color:#059669;border:1px solid rgba(5,150,105,.25);}
        .dark .cp-badge-weak  {background:rgba(220,38,38,.18); color:#f87171;}
        .dark .cp-badge-medium{background:rgba(234,179,8,.18); color:#fbbf24;}
        .dark .cp-badge-strong{background:rgba(16,185,129,.18);color:#34d399;}
        .cp-banner{display:flex;align-items:flex-start;gap:.5rem;padding:.625rem .875rem;border-radius:10px;font-size:.75rem;font-weight:600;}
        .cp-info {background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2); color:#2563eb;}
        .cp-red  {background:rgba(220,38,38,.08); border:1px solid rgba(220,38,38,.2);  color:#dc2626;}
        .cp-green{background:rgba(5,150,105,.08); border:1px solid rgba(5,150,105,.2);  color:#059669;}
        .dark .cp-info {background:rgba(59,130,246,.12);color:#93c5fd;border-color:rgba(59,130,246,.28);}
        .dark .cp-red  {background:rgba(220,38,38,.12); color:#fca5a5;border-color:rgba(220,38,38,.28);}
        .dark .cp-green{background:rgba(5,150,105,.12); color:#6ee7b7;border-color:rgba(5,150,105,.28);}
        .cp-err{font-size:.6875rem;font-weight:600;color:#dc2626;display:flex;align-items:center;gap:.25rem;}
        .dark .cp-err{color:#f87171;}
        .cp-footer{display:flex;align-items:center;justify-content:flex-end;gap:.625rem;padding:1rem 1.375rem 1.25rem;background:#fafaf7;border-top:1px solid #e5e7eb;flex-shrink:0;}
        .dark .cp-footer{background:#110c04;border-top-color:#2d1f0a;}
        .cp-footer-l{margin-right:auto;}
        .cp-pill{display:inline-flex;align-items:center;gap:.3rem;background:rgba(217,119,6,.09);border:1px solid rgba(217,119,6,.22);color:#d97706;font-size:.65rem;font-weight:700;padding:.25rem .625rem;border-radius:999px;}
        .dark .cp-pill{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.25);color:#fcd34d;}
        .cp-dot{width:6px;height:6px;border-radius:50%;background:#f59e0b;animation:cp-dot-pu 2s ease-in-out infinite;}
        .cp-btn-cancel{padding:.5875rem 1.125rem;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:.875rem;font-weight:600;cursor:pointer;transition:all .18s ease;}
        .dark .cp-btn-cancel{background:#1f1408;border-color:#2d1f0a;color:#fef3c7;}
        .cp-btn-cancel:hover{background:#f3f4f6;border-color:#d1d5db;transform:translateY(-1px);}
        .cp-btn-save{position:relative;padding:.5875rem 1.375rem;border-radius:10px;border:none;background:linear-gradient(135deg,#d97706 0%,#b45309 55%,#92400e 100%);color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.5rem;transition:all .2s ease;box-shadow:0 4px 14px rgba(217,119,6,.38);overflow:hidden;}
        .cp-btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(217,119,6,.48);}
        .cp-btn-save:active{transform:translateY(0);}
        .cp-btn-save:disabled{background:linear-gradient(135deg,#9ca3af,#6b7280);box-shadow:none;cursor:not-allowed;transform:none;opacity:.6;}
        [x-cloak]{display:none !important;}
    </style>

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
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background:rgba(0,0,0,.82);backdrop-filter:blur(10px);display:none;"
        >
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-6"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-6"
                @click.away="$wire.closeModal()"
                @keydown.escape.window="$wire.closeModal()"
                class="cp-modal w-full max-w-lg"
            >
                <div class="cp-stripe"></div>

                {{-- HERO --}}
                <div class="cp-hero">
                    <div class="cp-hero-bg"></div>
                    <div class="cp-hero-grid"></div>
                    <div class="cp-hero-dots">
                        @for ($i = 0; $i < 16; $i++)
                            <div class="cp-hero-dot"></div>
                        @endfor
                    </div>
                    <div class="cp-hero-content">
                        <div class="cp-hero-left">
                            <div class="cp-hero-icon">
                                <x-heroicon-o-lock-closed style="width:28px;height:28px;color:#fbbf24;" />
                            </div>
                            <div>
                                <div class="cp-hero-eyebrow">
                                    <x-heroicon-o-shield-check style="width:9px;height:9px;" />
                                    Security Settings
                                </div>
                                <div class="cp-hero-title">Update Password</div>
                                <div class="cp-hero-sub">Choose a strong password to secure your account</div>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="cp-hero-close">
                            <x-heroicon-o-x-mark style="width:15px;height:15px;" />
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="updatePassword" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                    <div class="cp-scroll">
                        <div class="cp-body">

                            {{-- ── CURRENT PASSWORD ── --}}
                            <div class="cp-section" x-data="{ showCurrent: false }">
                                <div class="cp-section-hdr">
                                    <div class="cp-section-ico">
                                        <x-heroicon-o-key style="width:13px;height:13px;color:#d97706;" />
                                    </div>
                                    <span class="cp-section-ttl">Current Password</span>
                                </div>
                                <div class="cp-field">
                                    <label class="cp-lbl">
                                        <x-heroicon-o-key style="width:11px;height:11px;color:#d97706;" />
                                        Current Password <span style="color:#dc2626;">*</span>
                                    </label>
                                    <div class="cp-pw-wrap">
                                        <input
                                            x-bind:type="showCurrent ? 'text' : 'password'"
                                            wire:model.defer="current_password"
                                            placeholder="Enter your current password"
                                            class="cp-input"
                                        />
                                        <button type="button" @click="showCurrent = !showCurrent" tabindex="-1" class="cp-eye">
                                            <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="showCurrent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;display:none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="cp-err" style="margin-top:.375rem;">
                                            <x-heroicon-o-exclamation-circle style="width:12px;height:12px;" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── NEW PASSWORD ── --}}
                            {{-- Compute strength class BEFORE the section markup --}}
                            @if ($password && $passwordStrength === 'weak')
                                @php $strengthClass = 'cp-border-red'; @endphp
                            @elseif ($password && $passwordStrength === 'medium')
                                @php $strengthClass = 'cp-border-yellow'; @endphp
                            @elseif ($password && $passwordStrength === 'strong')
                                @php $strengthClass = 'cp-border-green'; @endphp
                            @else
                                @php $strengthClass = ''; @endphp
                            @endif

                            {{-- Compute bar colours BEFORE the section markup --}}
                            @if ($passwordStrength === 'medium')
                                @php
                                    $bar1 = 'background:#eab308';
                                    $bar2 = 'background:#eab308';
                                    $bar3 = 'background:#e5e7eb';
                                @endphp
                            @elseif ($passwordStrength === 'strong')
                                @php
                                    $bar1 = 'background:#10b981';
                                    $bar2 = 'background:#10b981';
                                    $bar3 = 'background:#10b981';
                                @endphp
                            @else
                                @php
                                    $bar1 = 'background:#ef4444';
                                    $bar2 = 'background:#e5e7eb';
                                    $bar3 = 'background:#e5e7eb';
                                @endphp
                            @endif

                            <div class="cp-section" x-data="{ showNew: false }">
                                <div class="cp-section-hdr">
                                    <div class="cp-section-ico">
                                        <x-heroicon-o-lock-closed style="width:13px;height:13px;color:#d97706;" />
                                    </div>
                                    <span class="cp-section-ttl">New Password</span>
                                </div>
                                <div class="cp-field">
                                    <div class="cp-banner cp-info" style="margin-bottom:.75rem;">
                                        <x-heroicon-o-information-circle style="width:14px;height:14px;flex-shrink:0;margin-top:1px;" />
                                        <span>Must contain uppercase, lowercase, number, and special character</span>
                                    </div>
                                    <label class="cp-lbl">
                                        <x-heroicon-o-lock-closed style="width:11px;height:11px;color:#d97706;" />
                                        New Password <span style="color:#dc2626;">*</span>
                                    </label>
                                    <div class="cp-pw-wrap">
                                        <input
                                            x-bind:type="showNew ? 'text' : 'password'"
                                            wire:model.live="password"
                                            placeholder="Create a strong password"
                                            class="cp-input {{ $strengthClass }}"
                                        />
                                        <button type="button" @click="showNew = !showNew" tabindex="-1" class="cp-eye">
                                            <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="showNew" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;display:none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>

                                    @if ($password)
                                        <div style="margin-top:.875rem;display:flex;flex-direction:column;gap:.625rem;">
                                            <div>
                                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                                                    <span style="font-size:.65rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;">Password Strength</span>
                                                    @if ($passwordStrength === 'weak')
                                                        <span class="cp-badge cp-badge-weak">Weak</span>
                                                    @elseif ($passwordStrength === 'medium')
                                                        <span class="cp-badge cp-badge-medium">Medium</span>
                                                    @else
                                                        <span class="cp-badge cp-badge-strong">Strong</span>
                                                    @endif
                                                </div>
                                                <div style="display:flex;gap:6px;height:8px;">
                                                    <div style="flex:1;border-radius:999px;{{ $bar1 }};"></div>
                                                    <div style="flex:1;border-radius:999px;{{ $bar2 }};"></div>
                                                    <div style="flex:1;border-radius:999px;{{ $bar3 }};"></div>
                                                </div>
                                            </div>

                                            @if ($passwordStrength === 'strong')
                                                <div class="cp-banner cp-green">
                                                    <x-heroicon-o-check-circle style="width:14px;height:14px;flex-shrink:0;" />
                                                    <span>Password is <strong>Strong</strong> — all requirements met!</span>
                                                </div>
                                            @else
                                                <div style="display:flex;flex-direction:column;gap:.4rem;">
                                                    <span style="font-size:.6rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.09em;">Missing requirements:</span>
                                                    @if (!$hasMinLength)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>At least 8 characters</span>
                                                        </div>
                                                    @endif
                                                    @if (!$hasUppercase && !$hasLowercase)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>Uppercase and lowercase letters</span>
                                                        </div>
                                                    @elseif (!$hasUppercase)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>At least one uppercase letter</span>
                                                        </div>
                                                    @elseif (!$hasLowercase)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>At least one lowercase letter</span>
                                                        </div>
                                                    @endif
                                                    @if (!$hasNumber)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>At least one number</span>
                                                        </div>
                                                    @endif
                                                    @if (!$hasSpecial)
                                                        <div class="cp-banner cp-red">
                                                            <x-heroicon-o-x-circle style="width:13px;height:13px;flex-shrink:0;" />
                                                            <span>At least one special character (!@#$%^&amp;*)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @error('password')
                                        <p class="cp-err" style="margin-top:.375rem;">
                                            <x-heroicon-o-exclamation-circle style="width:12px;height:12px;" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── CONFIRM PASSWORD ── --}}
                            {{-- Compute confirm border class BEFORE the section markup --}}
                            @if ($password_confirmation && $passwordsMatch === true)
                                @php $confirmClass = 'cp-border-green'; @endphp
                            @elseif ($password_confirmation && $passwordsMatch === false)
                                @php $confirmClass = 'cp-border-red'; @endphp
                            @else
                                @php $confirmClass = ''; @endphp
                            @endif

                            <div class="cp-section" x-data="{ showConfirm: false }">
                                <div class="cp-section-hdr">
                                    <div class="cp-section-ico">
                                        <x-heroicon-o-shield-check style="width:13px;height:13px;color:#d97706;" />
                                    </div>
                                    <span class="cp-section-ttl">Confirm New Password</span>
                                </div>
                                <div class="cp-field">
                                    <label class="cp-lbl">
                                        <x-heroicon-o-lock-closed style="width:11px;height:11px;color:#d97706;" />
                                        Confirm Password <span style="color:#dc2626;">*</span>
                                    </label>
                                    <div class="cp-pw-wrap">
                                        <input
                                            x-bind:type="showConfirm ? 'text' : 'password'"
                                            wire:model.live="password_confirmation"
                                            placeholder="Re-enter your new password"
                                            class="cp-input {{ $confirmClass }}"
                                        />
                                        <button type="button" @click="showConfirm = !showConfirm" tabindex="-1" class="cp-eye">
                                            <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;display:none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>

                                    @if ($passwordsMatch === true)
                                        <div class="cp-banner cp-green" style="margin-top:.5rem;">
                                            <x-heroicon-o-check-circle style="width:14px;height:14px;flex-shrink:0;" />
                                            <span>Passwords match perfectly!</span>
                                        </div>
                                    @elseif ($passwordsMatch === false)
                                        <div class="cp-banner cp-red" style="margin-top:.5rem;">
                                            <x-heroicon-o-x-circle style="width:14px;height:14px;flex-shrink:0;" />
                                            <span>Passwords do not match</span>
                                        </div>
                                    @endif

                                    @error('password_confirmation')
                                        <p class="cp-err" style="margin-top:.375rem;">
                                            <x-heroicon-o-exclamation-circle style="width:12px;height:12px;" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                        </div>{{-- /cp-body --}}
                    </div>{{-- /cp-scroll --}}

                    <div class="cp-footer">
                        <div class="cp-footer-l">
                            <div class="cp-pill">
                                <div class="cp-dot"></div>
                                Changing Password
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="cp-btn-cancel">Cancel</button>
                        <button
                            type="submit"
                            class="cp-btn-save"
                            {{ ($passwordStrength !== 'strong' || $passwordsMatch !== true) ? 'disabled' : '' }}
                        >
                            <x-heroicon-o-lock-closed style="width:15px;height:15px;" />
                            Update Password
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
