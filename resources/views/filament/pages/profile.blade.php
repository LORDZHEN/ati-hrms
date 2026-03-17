<x-filament-panels::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap');

    :root {
        --emerald-100: #d1fae5; --emerald-400: #34d399;
        --emerald-500: #10b981; --emerald-600: #059669; --emerald-700: #047857;
        --amber-500: #f59e0b; --amber-600: #d97706;
        --surface-0: #ffffff; --surface-1: #f8faf9; --surface-2: #f0f4f2;
        --border-soft: #e2e8e5; --border-mid: #c8d5d0;
        --text-primary: #0d1f18; --text-secondary: #3d5a50; --text-muted: #7a9690;
        --shadow-xs: 0 1px 2px rgba(5,150,105,0.04);
        --shadow-sm: 0 2px 8px rgba(5,150,105,0.06), 0 1px 3px rgba(0,0,0,0.04);
        --shadow-md: 0 8px 24px rgba(5,150,105,0.10), 0 2px 8px rgba(0,0,0,0.06);
        --shadow-lg: 0 20px 48px rgba(5,150,105,0.14), 0 4px 16px rgba(0,0,0,0.08);
        --radius-sm: 10px; --radius-md: 14px; --radius-lg: 20px; --radius-xl: 26px;
    }

    .dark {
        --surface-0: #0b1a14; --surface-1: #0f2119; --surface-2: #152b21;
        --border-soft: #1e3a2c; --border-mid: #27503c;
        --text-primary: #e8f5ef; --text-secondary: #9dcfba; --text-muted: #4d8a72;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.25);
        --shadow-md: 0 8px 24px rgba(0,0,0,0.35);
        --shadow-lg: 0 20px 48px rgba(0,0,0,0.5);
    }

    .pf-root * { box-sizing: border-box; }

    .pf-root {
        font-family: 'Outfit', sans-serif;
        background: var(--surface-1);
        min-height: 100vh;
        padding: 1.5rem 1.25rem;
        color: var(--text-primary);
        max-width: 860px; margin: 0 auto;
    }

    /* ── TEMP PASSWORD BANNER ── */
    .pf-pw-banner {
        display: flex; align-items: center; gap: 1rem;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1.5px solid rgba(220,38,38,0.3);
        border-radius: var(--radius-lg);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        position: relative; overflow: hidden;
        animation: pf-in 0.4s ease-out backwards;
    }

    .dark .pf-pw-banner { background: rgba(220,38,38,0.1); border-color: rgba(220,38,38,0.35); }

    .pf-pw-banner::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
        background-size: 200% 100%; animation: pf-shimmer 2s linear infinite;
    }

    .pf-pw-banner-icon {
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(220,38,38,0.1); border: 1.5px solid rgba(220,38,38,0.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        animation: pf-pulse-red 2.5s ease-in-out infinite;
    }

    @keyframes pf-pulse-red {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.35); }
        50%       { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
    }

    .pf-pw-banner-title { font-size: 0.9375rem; font-weight: 700; color: #dc2626; margin-bottom: 0.2rem; }
    .dark .pf-pw-banner-title { color: #fca5a5; }
    .pf-pw-banner-text { font-size: 0.8125rem; color: #7f1d1d; line-height: 1.5; }
    .dark .pf-pw-banner-text { color: #fecaca; }
    .pf-pw-banner-cta { display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 700; color: #dc2626; white-space: nowrap; flex-shrink: 0; }
    .dark .pf-pw-banner-cta { color: #fca5a5; }

    /* ── HERO ── */
    .pf-hero {
        position: relative; border-radius: var(--radius-xl);
        overflow: hidden; margin-bottom: 1.25rem;
        min-height: 172px; background: #071812;
    }

    .pf-hero-canvas {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 65% 90% at 80% 55%, rgba(5,150,105,0.58) 0%, transparent 62%),
            radial-gradient(ellipse 45% 65% at 95% 10%, rgba(245,158,11,0.32) 0%, transparent 58%),
            radial-gradient(ellipse 55% 80% at 5%  90%, rgba(16,185,129,0.22) 0%, transparent 58%),
            linear-gradient(145deg, #040e08 0%, #0a2016 45%, #071410 100%);
    }

    .pf-hero-noise {
        position: absolute; inset: 0; opacity: 0.025;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
        background-size: 128px 128px;
    }

    .pf-hero-mesh {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(16,185,129,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(16,185,129,0.05) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .pf-hero-decor {
        position: absolute; top: 1.25rem; right: 1.75rem;
        display: flex; gap: 6px; flex-wrap: wrap; width: 60px; opacity: 0.25;
    }

    .pf-hero-dot { width: 4px; height: 4px; border-radius: 50%; background: #34d399; }

    .pf-hero-content {
        position: relative; z-index: 3;
        padding: 1.875rem 2.25rem;
        display: flex; align-items: center; gap: 1.75rem; flex-wrap: wrap;
    }

    /* Avatar */
    .pf-avatar-ring {
        flex-shrink: 0; position: relative;
        width: 90px; height: 90px; border-radius: 50%;
        background: rgba(16,185,129,0.15);
        border: 3px solid rgba(16,185,129,0.5);
        box-shadow: 0 0 0 3px rgba(16,185,129,0.12), 0 8px 28px rgba(0,0,0,0.45);
        display: flex; align-items: center; justify-content: center;
        animation: pf-ring-pulse 3s ease-in-out infinite;
    }

    @keyframes pf-ring-pulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(16,185,129,0.12), 0 8px 28px rgba(0,0,0,0.45); }
        50%       { box-shadow: 0 0 0 8px rgba(16,185,129,0.05), 0 8px 28px rgba(0,0,0,0.45); }
    }

    .pf-avatar { width: 78px; height: 78px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.9); display: block; }

    .pf-avatar-online {
        position: absolute; bottom: 4px; right: 4px;
        width: 14px; height: 14px; background: #22c55e;
        border-radius: 50%; border: 2.5px solid #0f2d1c;
        animation: pf-dot-pulse 2s infinite;
    }

    @keyframes pf-dot-pulse {
        0%, 100% { box-shadow: 0 0 0 2px rgba(34,197,94,0.35); }
        50%       { box-shadow: 0 0 0 5px rgba(34,197,94,0.1); }
    }

    .pf-hero-text { flex: 1; min-width: 0; }

    .pf-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
        color: #6ee7b7; font-family: 'JetBrains Mono', monospace;
        font-size: 0.625rem; font-weight: 500; letter-spacing: 0.14em; text-transform: uppercase;
        padding: 0.3rem 0.875rem; border-radius: 999px; margin-bottom: 0.5rem; width: fit-content;
    }

    .pf-hero-name {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem; font-weight: 400;
        color: #ffffff; line-height: 1.1; margin-bottom: 0.35rem;
    }

    .pf-hero-email {
        display: inline-flex; align-items: center; gap: 0.4rem;
        font-size: 0.8125rem; color: rgba(255,255,255,0.5); margin-bottom: 0.875rem;
    }

    .pf-hero-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .pf-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.75); font-size: 0.6875rem; font-weight: 600;
        padding: 0.375rem 0.875rem; border-radius: 999px;
        backdrop-filter: blur(12px); white-space: nowrap;
    }

    .pf-pill.green { border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.15); color: #6ee7b7; }
    .pf-pill.amber { border-color: rgba(245,158,11,0.4); background: rgba(245,158,11,0.12); color: #fcd34d; }
    .pf-pill.red   { border-color: rgba(220,38,38,0.5); background: rgba(220,38,38,0.15); color: #fca5a5; animation: pf-badge-pulse 2s ease-in-out infinite; }

    @keyframes pf-badge-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

    .pf-hero-bar {
        position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(16,185,129,0.6) 20%, rgba(245,158,11,0.8) 50%, rgba(16,185,129,0.6) 80%, transparent 100%);
        background-size: 300% 100%; animation: pf-shimmer 4s linear infinite;
    }

    @keyframes pf-shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

    /* ── SECTION HEADER ── */
    .pf-section-hd { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.875rem; }
    .pf-section-badge {
        width: 30px; height: 30px; border-radius: 9px;
        background: linear-gradient(135deg, var(--emerald-100), #bbf7d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dark .pf-section-badge { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(5,150,105,0.12)); }
    .pf-section-title { font-size: 0.875rem; font-weight: 700; color: var(--emerald-600); letter-spacing: -0.01em; }
    .dark .pf-section-title { color: var(--emerald-400); }
    .pf-section-rule { height: 1px; background: linear-gradient(90deg, var(--border-soft), transparent); margin-bottom: 1rem; }

    /* ── CARD ── */
    .pf-card {
        background: var(--surface-0); border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft); box-shadow: var(--shadow-sm);
        padding: 1.25rem; margin-bottom: 1.25rem;
        transition: box-shadow 0.25s ease;
    }
    .pf-card:hover { box-shadow: var(--shadow-md); }

    /* ── INFO GRID ── */
    .pf-info-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem; margin-bottom: 0;
    }

    @media (max-width: 860px) { .pf-info-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .pf-info-grid { grid-template-columns: 1fr; } }

    .pf-info-item {
        display: flex; align-items: center; gap: 0.875rem;
        background: var(--surface-1); border-radius: var(--radius-sm);
        border: 1px solid var(--border-soft);
        padding: 0.875rem 1rem; box-shadow: var(--shadow-xs);
        transition: all 0.2s ease; position: relative; overflow: hidden;
    }

    .pf-info-item::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--emerald-500), var(--amber-500));
        transform: scaleX(0); transform-origin: left; transition: transform 0.3s ease;
    }

    .pf-info-item:hover::before { transform: scaleX(1); }
    .pf-info-item:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); border-color: rgba(16,185,129,0.25); }

    .pf-info-ico {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .pf-info-item:hover .pf-info-ico { transform: scale(1.1) rotate(-5deg); }

    .pf-info-ico.green  { background: #f0fdf4; color: #16a34a; }
    .pf-info-ico.amber  { background: #fffbeb; color: #d97706; }
    .pf-info-ico.blue   { background: #eff6ff; color: #2563eb; }
    .pf-info-ico.purple { background: #faf5ff; color: #7c3aed; }
    .pf-info-ico.rose   { background: #fff1f2; color: #e11d48; }

    .dark .pf-info-ico.green  { background: rgba(22,163,74,0.12); }
    .dark .pf-info-ico.amber  { background: rgba(217,119,6,0.12); }
    .dark .pf-info-ico.blue   { background: rgba(37,99,235,0.12); }
    .dark .pf-info-ico.purple { background: rgba(109,40,217,0.12); }
    .dark .pf-info-ico.rose   { background: rgba(225,29,72,0.12); }

    .pf-info-label { font-family: 'JetBrains Mono', monospace; font-size: 0.5625rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 0.2rem; }
    .pf-info-value { font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pf-info-value.mono { font-family: 'JetBrains Mono', monospace; font-size: 0.875rem; }

    /* ── ACTION CARDS ── */
    .pf-actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
    @media (max-width: 640px) { .pf-actions-grid { grid-template-columns: 1fr; } }

    .pf-action-card {
        background: var(--surface-0); border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft); overflow: hidden;
        box-shadow: var(--shadow-sm); position: relative;
        transition: box-shadow 0.2s ease;
    }

    .pf-action-card:hover { box-shadow: var(--shadow-md); }

    .pf-action-card.pw-urgent { border-color: rgba(220,38,38,0.35); box-shadow: 0 0 0 3px rgba(220,38,38,0.08), var(--shadow-sm); }

    .pf-action-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--emerald-500), var(--amber-500));
        transform: scaleX(0); transform-origin: left; transition: transform 0.3s ease;
    }

    .pf-action-card.pw-urgent::before { background: linear-gradient(90deg, #dc2626, #f97316); transform: scaleX(1); }
    .pf-action-card:hover::before { transform: scaleX(1); }

    .pf-action-hd {
        display: flex; align-items: center; gap: 0.875rem;
        padding: 1.125rem 1.25rem 0.875rem;
        border-bottom: 1px solid var(--border-soft);
    }

    .pf-action-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .pf-action-icon.green { background: linear-gradient(135deg, #16a34a, #15803d); box-shadow: 0 4px 12px rgba(22,163,74,0.28); }
    .pf-action-icon.amber { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245,158,11,0.28); }
    .pf-action-icon.red   { background: linear-gradient(135deg, #dc2626, #991b1b); box-shadow: 0 4px 12px rgba(220,38,38,0.3); }

    .pf-action-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); }
    .pf-action-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
    .pf-action-sub.urgent { color: #dc2626; font-weight: 600; }
    .dark .pf-action-sub.urgent { color: #fca5a5; }

    .pf-action-body { padding: 1rem 1.25rem 1.25rem; }

    /* ── SECURITY TIPS ── */
    .pf-tips { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    @media (max-width: 520px) { .pf-tips { grid-template-columns: 1fr; } }

    .pf-tip {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.625rem 0.875rem; border-radius: var(--radius-sm);
        border-left: 3px solid var(--emerald-500);
        background: var(--surface-1);
        font-size: 0.8125rem; font-weight: 500; color: #166534;
        transition: all 0.2s ease;
    }

    .dark .pf-tip { background: rgba(16,185,129,0.04); color: #86efac; border-left-color: var(--emerald-400); }
    .pf-tip:hover { transform: translateX(4px); box-shadow: var(--shadow-xs); }

    .pf-tip-dot {
        width: 22px; height: 22px; border-radius: 50%;
        background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(5,150,105,0.25);
    }

    /* ── ANIMATIONS ── */
    @keyframes pf-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .pf-anim { animation: pf-in 0.45s cubic-bezier(0.22,1,0.36,1) backwards; }
    .pf-a1 { animation-delay: 0.04s; } .pf-a2 { animation-delay: 0.09s; }
    .pf-a3 { animation-delay: 0.14s; } .pf-a4 { animation-delay: 0.19s; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .pf-root { padding: 1rem; }
        .pf-hero-content { padding: 1.5rem; }
        .pf-hero-name { font-size: 1.5rem; }
        .pf-avatar-ring { width: 72px; height: 72px; }
        .pf-avatar { width: 62px; height: 62px; }
    }
    @media (max-width: 480px) {
        .pf-hero-name { font-size: 1.25rem; }
        .pf-hero-content { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<div class="pf-root"
     x-data="{ mustChangePw: @js($mustChangePassword) }"
     @password-changed.window="mustChangePw = false">

    {{-- Temp Password Banner --}}
    <div x-show="mustChangePw"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="pf-pw-banner">
        <div class="pf-pw-banner-icon">
            <x-heroicon-o-shield-exclamation class="w-6 h-6 text-red-600" />
        </div>
        <div style="flex:1;">
            <div class="pf-pw-banner-title">⚠ Temporary Password Active</div>
            <div class="pf-pw-banner-text">You are using a temporary password. Please update it now using the <strong>Change Password</strong> form below.</div>
        </div>
        <div class="pf-pw-banner-cta">
            <x-heroicon-o-arrow-down class="w-4 h-4" />
            Change below
        </div>
    </div>

    {{-- Hero --}}
    {{--
        BUG FIX #6 — The hero avatar now uses wire:model on the public
        $profilePhotoUrl property of the Profile page component.
        When onProfileSaved() updates $profilePhotoUrl, Livewire pushes
        the new value to the browser and the Alpine :src binding updates
        automatically — no redirect, no page reload.
    --}}
    <div class="pf-hero pf-anim"
         x-data="{ heroAvatarUrl: @entangle('profilePhotoUrl').live }"
         @profile-saved.window="heroAvatarUrl = $event.detail.avatarUrl">
        <div class="pf-hero-canvas"></div>
        <div class="pf-hero-noise"></div>
        <div class="pf-hero-mesh"></div>
        <div class="pf-hero-decor">
            @for($i=0;$i<15;$i++)<div class="pf-hero-dot"></div>@endfor
        </div>

        <div class="pf-hero-content">
            <div class="pf-avatar-ring">
                {{-- :src binds to the Alpine property which is entangled with the Livewire property --}}
                <img :src="heroAvatarUrl"
                     alt="{{ Auth::user()->name }}"
                     class="pf-avatar"
                     x-on:error="heroAvatarUrl = 'https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=059669&size=256&bold=true'">
                <div class="pf-avatar-online"></div>
            </div>

            <div class="pf-hero-text">
                <div class="pf-hero-eyebrow">
                    <x-heroicon-o-user-circle class="w-3 h-3" />
                    My Profile
                </div>
                <h1 class="pf-hero-name">{{ Auth::user()->name }}</h1>
                <div class="pf-hero-email">
                    <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                    {{ Auth::user()->email }}
                </div>
                <div class="pf-hero-pills">
                    @if(Auth::user()->employee_id)
                        <div class="pf-pill">
                            <x-heroicon-o-identification class="w-3.5 h-3.5" />
                            {{ Auth::user()->employee_id }}
                        </div>
                    @endif
                    @if(Auth::user()->position)
                        <div class="pf-pill amber">
                            <x-heroicon-o-briefcase class="w-3.5 h-3.5" />
                            {{ Auth::user()->position }}
                        </div>
                    @endif
                    @if(Auth::user()->employment_status)
                        <div class="pf-pill green">
                            <x-heroicon-o-check-badge class="w-3.5 h-3.5" />
                            {{ Auth::user()->employment_status }}
                        </div>
                    @endif
                    @if(Auth::user()->must_change_password)
                        <div class="pf-pill red">
                            <x-heroicon-o-key class="w-3.5 h-3.5" />
                            Temporary Password
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pf-hero-bar"></div>
    </div>

    {{-- Profile Information --}}
    <div class="pf-card pf-anim pf-a1">
        <div class="pf-section-hd">
            <div class="pf-section-badge">
                <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="pf-section-title">Profile Information</span>
        </div>
        <div class="pf-section-rule"></div>

        <div class="pf-info-grid">
            @if(Auth::user()->name)
            <div class="pf-info-item">
                <div class="pf-info-ico green"><x-heroicon-o-user class="w-5 h-5" /></div>
                <div class="min-w-0">
                    <div class="pf-info-label">Full Name</div>
                    <div class="pf-info-value">{{ Auth::user()->name }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->employee_id)
            <div class="pf-info-item">
                <div class="pf-info-ico amber"><x-heroicon-o-identification class="w-5 h-5" /></div>
                <div class="min-w-0">
                    <div class="pf-info-label">Employee ID</div>
                    <div class="pf-info-value mono">{{ Auth::user()->employee_id }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->position)
            <div class="pf-info-item">
                <div class="pf-info-ico green"><x-heroicon-o-briefcase class="w-5 h-5" /></div>
                <div class="min-w-0">
                    <div class="pf-info-label">Position</div>
                    <div class="pf-info-value">{{ Auth::user()->position }}</div>
                </div>
            </div>
            @endif

            @php
                $roleLabel = match(Auth::user()->role) {
                    'admin'     => 'Admin',
                    'regular'   => 'Regular Employee',
                    'job_order' => 'Job Order',
                    default     => ucfirst(Auth::user()->role ?? 'Unknown'),
                };
            @endphp
            <div class="pf-info-item">
                <div class="pf-info-ico amber"><x-heroicon-o-check-badge class="w-5 h-5" /></div>
                <div class="min-w-0">
                    <div class="pf-info-label">Employment Status</div>
                    <div class="pf-info-value">{{ $roleLabel }}</div>
                </div>
            </div>

            <div class="pf-info-item">
                <div class="pf-info-ico green"><x-heroicon-o-calendar class="w-5 h-5" /></div>
                <div class="min-w-0">
                    <div class="pf-info-label">Member Since</div>
                    <div class="pf-info-value">{{ Auth::user()->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="pf-actions-grid pf-anim pf-a2">

        <div class="pf-action-card">
            <div class="pf-action-hd">
                <div class="pf-action-icon green"><x-heroicon-o-user class="w-5 h-5 text-white" /></div>
                <div>
                    <div class="pf-action-title">Update Profile</div>
                    <div class="pf-action-sub">Edit your personal information</div>
                </div>
            </div>
            <div class="pf-action-body">
                @livewire('employee.update-profile')
            </div>
        </div>

        <div class="pf-action-card {{ $mustChangePassword ? 'pw-urgent' : '' }}" id="change-password-card">
            <div class="pf-action-hd">
                <div class="pf-action-icon {{ $mustChangePassword ? 'red' : 'amber' }}">
                    <x-heroicon-o-lock-closed class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="pf-action-title">Change Password</div>
                    @if($mustChangePassword)
                        <div class="pf-action-sub urgent">⚠ Temporary password — update required</div>
                    @else
                        <div class="pf-action-sub">Update your security credentials</div>
                    @endif
                </div>
            </div>
            <div class="pf-action-body">
                @livewire('employee.change-password')
            </div>
        </div>

    </div>

    {{-- Security Tips --}}
    <div class="pf-card pf-anim pf-a3">
        <div class="pf-section-hd">
            <div class="pf-section-badge">
                <x-heroicon-o-shield-check class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="pf-section-title">Security Best Practices</span>
        </div>
        <div class="pf-section-rule"></div>

        <div class="pf-tips">
            @foreach([
                ['Use a strong, unique password',   'heroicon-o-lock-closed'],
                ['Never share your credentials',    'heroicon-o-eye-slash'],
                ['Update password regularly',       'heroicon-o-arrow-path'],
                ['Log out from shared devices',     'heroicon-o-arrow-right-on-rectangle'],
            ] as [$tip, $icon])
            <div class="pf-tip">
                <div class="pf-tip-dot">
                    <x-dynamic-component :component="$icon" class="w-2.5 h-2.5 text-white" />
                </div>
                {{ $tip }}
            </div>
            @endforeach
        </div>
    </div>

</div>

@if($mustChangePassword)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const card = document.getElementById('change-password-card');
            if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 600);
    });
</script>
@endif

</x-filament-panels::page>