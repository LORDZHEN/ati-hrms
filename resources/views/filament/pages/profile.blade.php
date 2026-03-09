<x-filament-panels::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=Playfair+Display:wght@700;800;900&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --g:   #059669;
        --g2:  #10b981;
        --g3:  #d1fae5;
        --a:   #d97706;
        --a2:  #f59e0b;
        --a3:  #fef3c7;
        --ink:    #0f1f16;
        --ink2:   #374151;
        --ink3:   #6b7280;
        --paper:  #f9faf7;
        --card:   #ffffff;
        --border: #e5e7eb;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
        --shadow-lg: 0 12px 40px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
        --radius:    16px;
        --radius-sm: 10px;
    }

    .dark {
        --ink:    #f0fdf4;
        --ink2:   #d1fae5;
        --ink3:   #6ee7b7;
        --paper:  #0a1612;
        --card:   #0f1f18;
        --border: #1f3429;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.4);
        --shadow-lg: 0 12px 40px rgba(0,0,0,0.5);
    }

    /* ═══════════════════════════════════════
       BASE
    ═══════════════════════════════════════ */
    .pf-root {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        background: var(--paper);
        min-height: 100vh;
        padding: 1.25rem;
        color: var(--ink);
    }

    /* ═══════════════════════════════════════
       TEMP-PASSWORD WARNING BANNER  (NEW)
    ═══════════════════════════════════════ */
    .pf-pw-banner {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1.5px solid rgba(220,38,38,0.35);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
        animation: pf-fadein 0.4s ease-out backwards;
    }

    .dark .pf-pw-banner {
        background: linear-gradient(135deg, rgba(220,38,38,0.12), rgba(185,28,28,0.08));
        border-color: rgba(220,38,38,0.4);
    }

    .pf-pw-banner::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
        background-size: 200% 100%;
        animation: pf-shimmer 2s linear infinite;
    }

    .pf-pw-banner-icon {
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(220,38,38,0.12);
        border: 2px solid rgba(220,38,38,0.25);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        animation: pf-pulse-red 2.5s ease-in-out infinite;
    }

    @keyframes pf-pulse-red {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.4); }
        50%       { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
    }

    .pf-pw-banner-body { flex: 1; }

    .pf-pw-banner-title {
        font-size: 0.9375rem; font-weight: 700;
        color: #dc2626; margin-bottom: 0.2rem;
    }

    .dark .pf-pw-banner-title { color: #fca5a5; }

    .pf-pw-banner-text {
        font-size: 0.8125rem; color: #7f1d1d; line-height: 1.5;
    }

    .dark .pf-pw-banner-text { color: #fecaca; }

    .pf-pw-banner-arrow {
        display: flex; align-items: center; gap: 0.375rem;
        font-size: 0.75rem; font-weight: 700;
        color: #dc2626; white-space: nowrap;
        flex-shrink: 0;
    }

    .dark .pf-pw-banner-arrow { color: #fca5a5; }

    /* ═══════════════════════════════════════
       HERO
    ═══════════════════════════════════════ */
    .pf-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.25rem;
        background: var(--ink);
        min-height: 160px;
    }

    .pf-hero-bg {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 80% at 75% 50%, rgba(5,150,105,0.55) 0%, transparent 65%),
            radial-gradient(ellipse 40% 60% at 90% 20%, rgba(217,119,6,0.35) 0%, transparent 60%),
            radial-gradient(ellipse 50% 70% at 10% 80%, rgba(16,185,129,0.2) 0%, transparent 60%),
            linear-gradient(135deg, #071a10 0%, #0f2d1c 40%, #0a1e12 100%);
    }

    .pf-hero-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.06) 1px, transparent 1px);
        background-size: 32px 32px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .pf-hero-dots {
        position: absolute;
        top: 1.5rem; right: 1.5rem;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        opacity: 0.25;
    }

    .pf-hero-dot {
        width: 4px; height: 4px;
        border-radius: 50%;
        background: #10b981;
    }

    .pf-hero-content {
        position: relative; z-index: 2;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.75rem;
        flex-wrap: wrap;
    }

    /* Avatar ring */
    .pf-avatar-ring {
        flex-shrink: 0;
        position: relative;
        width: 88px; height: 88px;
        border-radius: 50%;
        background: rgba(16,185,129,0.18);
        border: 3px solid rgba(16,185,129,0.55);
        box-shadow: 0 0 0 3px rgba(16,185,129,0.15), 0 8px 28px rgba(0,0,0,0.45);
        display: flex; align-items: center; justify-content: center;
        animation: pf-pulse-ring 3s ease-in-out infinite;
    }

    @keyframes pf-pulse-ring {
        0%, 100% { box-shadow: 0 0 0 3px rgba(16,185,129,0.15), 0 8px 28px rgba(0,0,0,0.45); }
        50%       { box-shadow: 0 0 0 8px rgba(16,185,129,0.06), 0 8px 28px rgba(0,0,0,0.45); }
    }

    .pf-avatar {
        width: 76px; height: 76px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.9);
        box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        display: block;
    }

    .pf-avatar-online {
        position: absolute;
        bottom: 4px; right: 4px;
        width: 14px; height: 14px;
        background: #22c55e;
        border-radius: 50%;
        border: 2.5px solid #0f2d1c;
        box-shadow: 0 0 0 2px rgba(34,197,94,0.35);
        animation: pf-dot-pulse 2s infinite;
    }

    @keyframes pf-dot-pulse {
        0%, 100% { box-shadow: 0 0 0 2px rgba(34,197,94,0.35); }
        50%       { box-shadow: 0 0 0 5px rgba(34,197,94,0.1); }
    }

    .pf-hero-text { flex: 1; min-width: 0; }

    .pf-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.3);
        color: #6ee7b7;
        font-size: 0.6875rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        margin-bottom: 0.5rem;
    }

    .pf-hero-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.875rem; font-weight: 800;
        color: #ffffff; line-height: 1.15;
        margin-bottom: 0.375rem; letter-spacing: -0.02em;
    }

    .pf-hero-email {
        display: inline-flex; align-items: center; gap: 0.4rem;
        font-size: 0.8125rem; font-weight: 500;
        color: rgba(255,255,255,0.55);
        margin-bottom: 0.875rem;
    }

    .pf-hero-pills {
        display: flex; gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pf-pill {
        display: flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.8);
        font-size: 0.75rem; font-weight: 600;
        padding: 0.4rem 0.875rem; border-radius: 999px;
        backdrop-filter: blur(8px);
        white-space: nowrap;
    }

    .pf-pill.green {
        border-color: rgba(16,185,129,0.4);
        background: rgba(16,185,129,0.12);
        color: #6ee7b7;
    }

    .pf-pill.amber {
        border-color: rgba(245,158,11,0.4);
        background: rgba(245,158,11,0.12);
        color: #fcd34d;
    }

    /* Red pill for temporary-password warning */
    .pf-pill.red {
        border-color: rgba(220,38,38,0.5);
        background: rgba(220,38,38,0.15);
        color: #fca5a5;
        animation: pf-badge-pulse 2s ease-in-out infinite;
    }

    @keyframes pf-badge-pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.7; }
    }

    .pf-hero-stripe {
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--g), var(--a2), var(--g));
        background-size: 200% 100%;
        animation: pf-shimmer 3s linear infinite;
    }

    @keyframes pf-shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }

    /* ═══════════════════════════════════════
       SECTION LABEL
    ═══════════════════════════════════════ */
    .pf-section-label {
        display: flex; align-items: center; gap: 0.625rem;
        margin-bottom: 0.75rem;
    }

    .pf-section-icon {
        width: 28px; height: 28px; border-radius: 7px;
        background: linear-gradient(135deg, var(--g3), #bbf7d0);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .dark .pf-section-icon {
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.15));
    }

    .pf-section-title {
        font-size: 0.9375rem; font-weight: 700;
        color: var(--g); letter-spacing: -0.01em;
    }

    .pf-section-divider {
        height: 1px;
        background: linear-gradient(90deg, var(--border), transparent);
        margin-bottom: 1rem;
    }

    /* ═══════════════════════════════════════
       BASE CARD
    ═══════════════════════════════════════ */
    .pf-card {
        background: var(--card);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .pf-card:hover { box-shadow: var(--shadow-md); }

    /* ═══════════════════════════════════════
       INFO GRID
    ═══════════════════════════════════════ */
    .pf-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 900px) { .pf-info-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px) { .pf-info-grid { grid-template-columns: 1fr; } }

    .pf-info-item {
        display: flex; align-items: center; gap: 0.875rem;
        background: var(--card);
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        padding: 0.875rem 1rem;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative; overflow: hidden;
    }

    .pf-info-item::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--g), var(--a2));
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s ease;
    }

    .pf-info-item:hover::before { transform: scaleX(1); }

    .pf-info-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: rgba(5,150,105,0.3);
    }

    .pf-info-ico {
        width: 38px; height: 38px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .pf-info-item:hover .pf-info-ico { transform: scale(1.08) rotate(-4deg); }

    .pf-info-ico.green  { background: #dcfce7; }
    .pf-info-ico.amber  { background: #fef3c7; }
    .pf-info-ico.blue   { background: #dbeafe; }
    .pf-info-ico.purple { background: #ede9fe; }
    .pf-info-ico.rose   { background: #ffe4e6; }

    .dark .pf-info-ico.green  { background: rgba(22,163,74,0.15); }
    .dark .pf-info-ico.amber  { background: rgba(245,158,11,0.15); }
    .dark .pf-info-ico.blue   { background: rgba(37,99,235,0.15); }
    .dark .pf-info-ico.purple { background: rgba(109,40,217,0.15); }
    .dark .pf-info-ico.rose   { background: rgba(244,63,94,0.15); }

    .pf-info-label {
        font-size: 0.625rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: var(--ink3); margin-bottom: 0.2rem;
    }

    .pf-info-value {
        font-size: 0.9375rem; font-weight: 600;
        color: var(--ink); line-height: 1.2;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .pf-info-value.mono { font-family: 'DM Mono', monospace; }

    /* ═══════════════════════════════════════
       ACTION CARDS GRID
    ═══════════════════════════════════════ */
    .pf-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 640px) { .pf-actions-grid { grid-template-columns: 1fr; } }

    .pf-action-card {
        background: var(--card);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
    }

    /* Highlight the Change Password card when a temp password is active */
    .pf-action-card.highlight-pw {
        border-color: rgba(220,38,38,0.4);
        box-shadow: 0 0 0 3px rgba(220,38,38,0.1), var(--shadow-sm);
    }

    .pf-action-card:hover { box-shadow: var(--shadow-md); }

    .pf-action-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--g), var(--a2));
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s ease;
    }

    .pf-action-card.highlight-pw::before {
        background: linear-gradient(90deg, #dc2626, #f97316);
        transform: scaleX(1);
    }

    .pf-action-card:hover::before { transform: scaleX(1); }

    .pf-action-header {
        display: flex; align-items: center; gap: 0.875rem;
        padding: 1.125rem 1.25rem 0.875rem;
        border-bottom: 1px solid var(--border);
    }

    .pf-action-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .pf-action-icon.green {
        background: linear-gradient(135deg, #16a34a, #15803d);
        box-shadow: 0 4px 12px rgba(22,163,74,0.3);
    }

    .pf-action-icon.amber {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }

    /* Red icon variant for the password card when temp password is active */
    .pf-action-icon.red {
        background: linear-gradient(135deg, #dc2626, #991b1b);
        box-shadow: 0 4px 12px rgba(220,38,38,0.35);
    }

    .pf-action-title {
        font-size: 0.9375rem; font-weight: 700;
        color: var(--ink); letter-spacing: -0.01em;
    }

    .pf-action-sub {
        font-size: 0.75rem; color: var(--ink3); margin-top: 1px;
    }

    .pf-action-sub.red { color: #dc2626; font-weight: 600; }
    .dark .pf-action-sub.red { color: #fca5a5; }

    .pf-action-body { padding: 1rem 1.25rem 1.25rem; }

    /* ═══════════════════════════════════════
       SECURITY TIPS
    ═══════════════════════════════════════ */
    .pf-tips-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
        margin-top: 0.875rem;
    }

    @media (max-width: 560px) { .pf-tips-grid { grid-template-columns: 1fr; } }

    .pf-tip {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.625rem 0.875rem;
        border-radius: 8px;
        border-left: 3px solid var(--g);
        background: var(--paper);
        font-size: 0.8125rem; font-weight: 500;
        color: #166534;
        transition: all 0.2s ease;
    }

    .dark .pf-tip {
        background: rgba(16,185,129,0.04);
        color: #86efac;
        border-left-color: var(--g2);
    }

    .pf-tip:hover {
        transform: translateX(3px);
        box-shadow: var(--shadow-sm);
        border-left-width: 4px;
    }

    .pf-tip-dot {
        width: 20px; height: 20px; border-radius: 50%;
        background: var(--g);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ═══════════════════════════════════════
       ANIMATIONS
    ═══════════════════════════════════════ */
    @keyframes pf-fadein {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pf-in { animation: pf-fadein 0.4s ease-out backwards; }
    .pf-d1 { animation-delay: 0.05s; }
    .pf-d2 { animation-delay: 0.10s; }
    .pf-d3 { animation-delay: 0.15s; }
    .pf-d4 { animation-delay: 0.20s; }
    .pf-d5 { animation-delay: 0.25s; }

    /* ═══════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════ */
    @media (max-width: 768px) {
        .pf-root { padding: 0.875rem; }
        .pf-hero-content { padding: 1.25rem 1.5rem; }
        .pf-hero-name { font-size: 1.5rem; }
        .pf-avatar-ring { width: 72px; height: 72px; }
        .pf-avatar { width: 62px; height: 62px; }
    }

    @media (max-width: 480px) {
        .pf-hero-name { font-size: 1.25rem; }
        .pf-hero-content { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<div class="pf-root max-w-5xl mx-auto pb-6"
     x-data="{ mustChangePw: @js($mustChangePassword) }"
     @password-changed.window="mustChangePw = false">

    {{-- ═══════════════════════════════════════════
         TEMPORARY PASSWORD WARNING BANNER  (NEW)
         Shown when must_change_password = true.
         Hidden reactively when ChangePassword emits 'passwordChanged'.
    ═══════════════════════════════════════════ --}}
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
        <div class="pf-pw-banner-body">
            <div class="pf-pw-banner-title">⚠ Temporary Password Active</div>
            <div class="pf-pw-banner-text">
                You are currently using a temporary password. Please update it now using the
                <strong>Change Password</strong> form below before accessing other parts of the system.
            </div>
        </div>
        <div class="pf-pw-banner-arrow">
            <x-heroicon-o-arrow-down class="w-4 h-4" />
            Change below
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO BANNER
    ═══════════════════════════════════════════ --}}
    <div class="pf-hero pf-in">
        <div class="pf-hero-bg"></div>
        <div class="pf-hero-grid"></div>

        <div class="pf-hero-dots">
            @for($i = 0; $i < 25; $i++)
                <div class="pf-hero-dot"></div>
            @endfor
        </div>

        <div class="pf-hero-content">
            {{-- Avatar --}}
            <div class="pf-avatar-ring">
                <img
                    src="{{ $this->getProfilePhotoUrl() }}"
                    alt="{{ Auth::user()->name }}"
                    class="pf-avatar"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=059669&size=256&bold=true'"
                >
                <div class="pf-avatar-online"></div>
            </div>

            {{-- Name + pills --}}
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
                    {{-- FIX: Show a red "Temp Password" pill when must_change_password is active --}}
                    @if(Auth::user()->must_change_password)
                        <div class="pf-pill red">
                            <x-heroicon-o-key class="w-3.5 h-3.5" />
                            Temporary Password
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pf-hero-stripe"></div>
    </div>

    {{-- ═══════════════════════════════════════════
         PROFILE INFORMATION
    ═══════════════════════════════════════════ --}}
    <div class="pf-in pf-d1" style="margin-bottom:1.25rem;">
        <div class="pf-section-label">
            <div class="pf-section-icon">
                <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="pf-section-title">Profile Information</span>
        </div>
        <div class="pf-section-divider"></div>

        <div class="pf-info-grid">

            @if(Auth::user()->name)
            <div class="pf-info-item">
                <div class="pf-info-ico green">
                    <x-heroicon-o-user class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Full Name</div>
                    <div class="pf-info-value">{{ Auth::user()->name }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->employee_id)
            <div class="pf-info-item">
                <div class="pf-info-ico amber">
                    <x-heroicon-o-identification class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Employee ID</div>
                    <div class="pf-info-value mono">{{ Auth::user()->employee_id }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->position)
            <div class="pf-info-item">
                <div class="pf-info-ico green">
                    <x-heroicon-o-briefcase class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Position</div>
                    <div class="pf-info-value">{{ Auth::user()->position }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->employment_status)
            <div class="pf-info-item">
                <div class="pf-info-ico amber">
                    <x-heroicon-o-check-badge class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Employment Status</div>
                    <div class="pf-info-value">{{ Auth::user()->employment_status }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->department)
            <div class="pf-info-item">
                <div class="pf-info-ico blue">
                    <x-heroicon-o-building-office class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Department</div>
                    <div class="pf-info-value">{{ Auth::user()->department }}</div>
                </div>
            </div>
            @endif

            <div class="pf-info-item">
                <div class="pf-info-ico green">
                    <x-heroicon-o-calendar class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div class="min-w-0">
                    <div class="pf-info-label">Member Since</div>
                    <div class="pf-info-value">{{ Auth::user()->created_at->format('F d, Y') }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         ACTION CARDS
         FIX: Change Password card is highlighted red when must_change_password
              is active, and icon colour switches to red to signal urgency.
    ═══════════════════════════════════════════ --}}
    <div class="pf-actions-grid pf-in pf-d2">

        {{-- Update Profile --}}
        <div class="pf-action-card">
            <div class="pf-action-header">
                <div class="pf-action-icon green">
                    <x-heroicon-o-user class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="pf-action-title">Update Profile</div>
                    <div class="pf-action-sub">Edit your personal information</div>
                </div>
            </div>
            <div class="pf-action-body">
                @livewire('employee.update-profile')
            </div>
        </div>

        {{-- Change Password — highlighted when temp password is active --}}
        <div class="pf-action-card {{ $mustChangePassword ? 'highlight-pw' : '' }}"
             id="change-password-card">
            <div class="pf-action-header">
                <div class="pf-action-icon {{ $mustChangePassword ? 'red' : 'amber' }}">
                    <x-heroicon-o-lock-closed class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="pf-action-title">Change Password</div>
                    @if($mustChangePassword)
                        <div class="pf-action-sub red">⚠ Temporary password — update required</div>
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

    {{-- ═══════════════════════════════════════════
         SECURITY TIPS
    ═══════════════════════════════════════════ --}}
    <div class="pf-card pf-in pf-d3">
        <div class="pf-section-label" style="margin-bottom:0;">
            <div class="pf-section-icon">
                <x-heroicon-o-shield-check class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="pf-section-title">Security Best Practices</span>
        </div>
        <div class="pf-section-divider" style="margin-top:0.75rem;"></div>

        <div class="pf-tips-grid">
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

{{-- Auto-scroll to the Change Password card on first load if temp password is active --}}
@if($mustChangePassword)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const card = document.getElementById('change-password-card');
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 600);
    });
</script>
@endif

</x-filament-panels::page>
