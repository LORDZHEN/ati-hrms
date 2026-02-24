<x-filament-panels::page>

    <style>
        /* ── Google Font ── */
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        .profile-root * {
            font-family: 'DM Sans', sans-serif;
        }

        /* ── CSS tokens ── */
        .profile-root {
            --ati-green:      #16a34a;
            --ati-green-dark: #14532d;
            --ati-green-mid:  #166534;
            --ati-amber:      #f59e0b;
            --ati-amber-dark: #b45309;
            --surface:        #ffffff;
            --surface-2:      #f8fafc;
            --border:         #e2e8f0;
            --text-primary:   #0f172a;
            --text-muted:     #64748b;
            --text-xs:        #94a3b8;
            --radius-card:    16px;
            --radius-chip:    8px;
            --shadow-sm:      0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
            --shadow-md:      0 4px 16px rgba(0,0,0,.10), 0 2px 6px rgba(0,0,0,.06);
            --shadow-lg:      0 10px 40px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.08);
        }

        /* ─────────────────────────────────────────────
           HERO BANNER
        ───────────────────────────────────────────── */
        .profile-hero {
            position: relative;
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--ati-green-dark) 0%, var(--ati-green) 50%, var(--ati-amber) 100%);
        }

        /* subtle grain texture overlay */
        .profile-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.06'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .profile-hero-inner {
            position: relative;
            z-index: 1;
            padding: 40px 40px 36px;
            display: flex;
            align-items: flex-end;
            gap: 28px;
        }

        /* ── Avatar ── */
        .profile-avatar-wrap {
            flex-shrink: 0;
            position: relative;
        }

        .profile-avatar {
            width: 108px;
            height: 108px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,.95);
            box-shadow: 0 8px 32px rgba(0,0,0,.25);
            display: block;
        }

        .profile-online-dot {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 16px;
            height: 16px;
            background: #22c55e;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px rgba(34,197,94,.4);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 2px rgba(34,197,94,.4); }
            50%       { box-shadow: 0 0 0 5px rgba(34,197,94,.15); }
        }

        /* ── Hero text ── */
        .profile-hero-text {
            flex: 1;
            min-width: 0;
        }

        .profile-hero-name {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.15;
            margin: 0 0 6px;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 8px rgba(0,0,0,.25);
        }

        .profile-hero-email {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: rgba(255,255,255,.88);
            background: rgba(0,0,0,.18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 20px;
            padding: 4px 12px 4px 8px;
            margin-bottom: 16px;
        }

        .profile-hero-email svg {
            width: 14px;
            height: 14px;
            opacity: .8;
        }

        /* ── Chips row ── */
        .profile-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .profile-chip {
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.28);
            border-radius: var(--radius-chip);
            padding: 6px 14px;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .profile-chip-label {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.65);
        }

        .profile-chip-value {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #fff;
            font-family: 'DM Mono', monospace;
        }

        /* ─────────────────────────────────────────────
           INFO GRID
        ───────────────────────────────────────────── */
        .profile-section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .profile-section-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-section-icon svg {
            width: 20px;
            height: 20px;
        }

        .profile-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        /* info cards */
        .profile-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        @media (max-width: 900px) {
            .profile-info-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 560px) {
            .profile-info-grid { grid-template-columns: 1fr; }
            .profile-hero-inner { flex-direction: column; align-items: flex-start; padding: 28px 24px 24px; }
            .profile-hero-name  { font-size: 1.5rem; }
        }

        .profile-info-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: var(--shadow-sm);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .profile-info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: #bbf7d0;
        }

        .profile-info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform .15s ease;
        }

        .profile-info-card:hover .profile-info-icon {
            transform: scale(1.08);
        }

        .profile-info-icon svg {
            width: 20px;
            height: 20px;
        }

        .profile-info-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-xs);
            margin-bottom: 3px;
        }

        .profile-info-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* green icon bg */
        .icon-green { background: #dcfce7; }
        .icon-green svg { color: #16a34a; }

        /* amber icon bg */
        .icon-amber { background: #fef3c7; }
        .icon-amber svg { color: #d97706; }

        /* blue icon bg */
        .icon-blue { background: #dbeafe; }
        .icon-blue svg { color: #2563eb; }

        /* ─────────────────────────────────────────────
           ACTION CARDS (Update Profile / Change Password)
        ───────────────────────────────────────────── */
        .profile-actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 640px) {
            .profile-actions-grid { grid-template-columns: 1fr; }
        }

        .profile-action-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: box-shadow .2s ease, border-color .2s ease;
        }

        .profile-action-card:hover {
            box-shadow: var(--shadow-md);
        }

        .profile-action-card.green { border-top: 3px solid var(--ati-green); }
        .profile-action-card.amber { border-top: 3px solid var(--ati-amber); }

        .profile-action-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px 22px 16px;
            border-bottom: 1px solid var(--border);
        }

        .profile-action-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-action-icon.green {
            background: linear-gradient(135deg, #16a34a, #15803d);
            box-shadow: 0 4px 12px rgba(22,163,74,.3);
        }

        .profile-action-icon.amber {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 12px rgba(245,158,11,.3);
        }

        .profile-action-icon svg {
            width: 22px;
            height: 22px;
            color: #fff;
        }

        .profile-action-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .profile-action-subtitle {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .profile-action-body {
            padding: 20px 22px 22px;
        }

        /* ─────────────────────────────────────────────
           SECURITY TIPS
        ───────────────────────────────────────────── */
        .profile-tips {
            background: var(--surface);
            border: 1.5px solid #bbf7d0;
            border-radius: var(--radius-card);
            padding: 24px 28px;
            box-shadow: var(--shadow-sm);
        }

        .profile-tips-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 16px;
        }

        @media (max-width: 560px) {
            .profile-tips-grid { grid-template-columns: 1fr; }
        }

        .profile-tip-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #166534;
        }

        .profile-tip-dot {
            width: 20px;
            height: 20px;
            background: var(--ati-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-tip-dot svg {
            width: 11px;
            height: 11px;
            color: #fff;
        }

        /* ── Dark mode overrides ── */
        .dark .profile-root {
            --surface:   #1e293b;
            --surface-2: #0f172a;
            --border:    #334155;
            --text-primary: #f1f5f9;
            --text-muted:   #94a3b8;
            --text-xs:      #64748b;
        }

        .dark .profile-info-card { background: #1e293b; border-color: #334155; }
        .dark .profile-info-card:hover { border-color: #166534; }
        .dark .icon-green { background: rgba(22,163,74,.15); }
        .dark .icon-amber { background: rgba(245,158,11,.15); }
        .dark .icon-blue  { background: rgba(37,99,235,.15); }
        .dark .profile-action-card { background: #1e293b; border-color: #334155; }
        .dark .profile-action-header { border-color: #334155; }
        .dark .profile-tips { background: #1e293b; border-color: #166534; }
        .dark .profile-tip-item { background: rgba(22,163,74,.08); border-color: #166534; color: #86efac; }
        .dark .profile-section-title { color: #f1f5f9; }
        .dark .profile-action-title  { color: #f1f5f9; }
    </style>

    <div class="profile-root space-y-5 max-w-5xl mx-auto pb-6">

        {{-- ═══════════════════════════════════════════
             HERO BANNER
        ═══════════════════════════════════════════ --}}
        <div class="profile-hero">
            <div class="profile-hero-inner">

                {{-- Avatar --}}
                <div class="profile-avatar-wrap">
                    <img
                        src="{{ $this->getProfilePhotoUrl() }}"
                        alt="Profile Photo"
                        class="profile-avatar"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=16a34a&size=256&bold=true'"
                    >
                    <div class="profile-online-dot"></div>
                </div>

                {{-- Name + email + chips --}}
                <div class="profile-hero-text">
                    <h1 class="profile-hero-name">{{ Auth::user()->name }}</h1>

                    <div class="profile-hero-email">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ Auth::user()->email }}
                    </div>

                    <div class="profile-chips">
                        @if(Auth::user()->employee_id)
                        <div class="profile-chip">
                            <span class="profile-chip-label">Employee ID</span>
                            <span class="profile-chip-value">{{ Auth::user()->employee_id }}</span>
                        </div>
                        @endif

                        @if(Auth::user()->position)
                        <div class="profile-chip">
                            <span class="profile-chip-label">Position</span>
                            <span class="profile-chip-value">{{ Auth::user()->position }}</span>
                        </div>
                        @endif

                        @if(Auth::user()->employment_status)
                        <div class="profile-chip">
                            <span class="profile-chip-label">Status</span>
                            <span class="profile-chip-value">{{ Auth::user()->employment_status }}</span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             PROFILE INFORMATION
        ═══════════════════════════════════════════ --}}
        <div class="profile-section-header">
            <div class="profile-section-icon icon-green" style="background:#dcfce7;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#16a34a;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="profile-section-title">Profile Information</span>
        </div>

        <div class="profile-info-grid">

            @if(Auth::user()->name)
            <div class="profile-info-card">
                <div class="profile-info-icon icon-green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Full Name</div>
                    <div class="profile-info-value">{{ Auth::user()->name }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->employee_id)
            <div class="profile-info-card">
                <div class="profile-info-icon icon-amber">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Employee ID</div>
                    <div class="profile-info-value" style="font-family:'DM Mono',monospace;">{{ Auth::user()->employee_id }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->position)
            <div class="profile-info-card">
                <div class="profile-info-icon icon-green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Position</div>
                    <div class="profile-info-value">{{ Auth::user()->position }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->employment_status)
            <div class="profile-info-card">
                <div class="profile-info-icon icon-amber">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Employment Status</div>
                    <div class="profile-info-value">{{ Auth::user()->employment_status }}</div>
                </div>
            </div>
            @endif

            @if(Auth::user()->department)
            <div class="profile-info-card">
                <div class="profile-info-icon icon-blue">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Department</div>
                    <div class="profile-info-value">{{ Auth::user()->department }}</div>
                </div>
            </div>
            @endif

            <div class="profile-info-card">
                <div class="profile-info-icon icon-green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="profile-info-label">Member Since</div>
                    <div class="profile-info-value">{{ Auth::user()->created_at->format('F d, Y') }}</div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
             ACTION CARDS
        ═══════════════════════════════════════════ --}}
        <div class="profile-actions-grid">

            {{-- Update Profile --}}
            <div class="profile-action-card green">
                <div class="profile-action-header">
                    <div class="profile-action-icon green">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="profile-action-title">Update Profile</div>
                        <div class="profile-action-subtitle">Edit your personal information</div>
                    </div>
                </div>
                <div class="profile-action-body">
                    @livewire('employee.update-profile')
                </div>
            </div>

            {{-- Change Password --}}
            <div class="profile-action-card amber">
                <div class="profile-action-header">
                    <div class="profile-action-icon amber">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="profile-action-title">Change Password</div>
                        <div class="profile-action-subtitle">Update your security credentials</div>
                    </div>
                </div>
                <div class="profile-action-body">
                    @livewire('employee.change-password')
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════
             SECURITY TIPS
        ═══════════════════════════════════════════ --}}
        <div class="profile-tips">
            <div class="profile-section-header" style="margin-bottom:0;">
                <div class="profile-section-icon icon-green" style="background:#dcfce7;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#16a34a;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="profile-section-title">Security Best Practices</span>
            </div>

            <div class="profile-tips-grid">
                @foreach(['Use a strong, unique password', 'Never share your credentials', 'Update password regularly', 'Log out from shared devices'] as $tip)
                <div class="profile-tip-item">
                    <div class="profile-tip-dot">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    {{ $tip }}
                </div>
                @endforeach
            </div>
        </div>

    </div>
</x-filament-panels::page>
