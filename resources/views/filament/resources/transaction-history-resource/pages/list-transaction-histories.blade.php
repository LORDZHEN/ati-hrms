<x-filament-panels::page>

{{--
    Transaction History — Matched to Dashboard UI
    ===============================================
    Design language unified with hd-root dashboard:
      • DM Sans + Playfair Display typography
      • Same --g / --a / --ink / --paper / --card token system
      • Hero banner with grid + dots + shimmer stripe
      • hd-card / hd-section-label / hd-section-icon pattern
      • Consistent stat pills, chips, activity rows, empty states
--}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">

<style>
    /* ── Tokens (mirrors dashboard exactly) ───────────────────── */
    :root {
        --g:          #059669;
        --g2:         #10b981;
        --g3:         #d1fae5;
        --a:          #d97706;
        --a2:         #f59e0b;
        --a3:         #fef3c7;
        --ink:        #0f1f16;
        --ink2:       #374151;
        --ink3:       #6b7280;
        --paper:      #f9faf7;
        --card:       #ffffff;
        --border:     #e5e7eb;
        --shadow-sm:  0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md:  0 4px 16px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
        --shadow-lg:  0 12px 40px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
        --radius:     16px;
        --radius-sm:  10px;
    }

    .dark {
        --ink:       #f0fdf4;
        --ink2:      #d1fae5;
        --ink3:      #6ee7b7;
        --paper:     #0a1612;
        --card:      #0f1f18;
        --border:    #1f3429;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.4);
        --shadow-lg: 0 12px 40px rgba(0,0,0,0.5);
    }

    /* ── Root ──────────────────────────────────────────────────── */
    .th-root {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        background: var(--paper);
        min-height: 100vh;
        color: var(--ink);
    }

    /* ── Hero Banner (same structure as hd-hero) ───────────────── */
    .th-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        background: var(--ink);
        min-height: 130px;
    }

    .th-hero-bg {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 55% 80% at 80% 50%, rgba(5,150,105,0.50) 0%, transparent 65%),
            radial-gradient(ellipse 35% 55% at 92% 15%, rgba(217,119,6,0.30) 0%, transparent 60%),
            radial-gradient(ellipse 45% 65% at 5%  85%, rgba(16,185,129,0.18) 0%, transparent 60%),
            linear-gradient(135deg, #071a10 0%, #0f2d1c 40%, #0a1e12 100%);
    }

    .th-hero-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.06) 1px, transparent 1px);
        background-size: 32px 32px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .th-hero-dots {
        position: absolute;
        top: 1.25rem; right: 1.5rem;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        opacity: 0.22;
    }

    .th-hero-dot { width: 4px; height: 4px; border-radius: 50%; background: #10b981; }

    .th-hero-content {
        position: relative; z-index: 2;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .th-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.3);
        color: #6ee7b7;
        font-size: 0.6875rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        margin-bottom: 0.5rem;
    }

    .th-hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.625rem; font-weight: 800;
        color: #ffffff; line-height: 1.15;
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }

    .th-hero-sub { font-size: 0.8125rem; color: rgba(255,255,255,0.5); }

    .th-hero-stripe {
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--g), var(--a2), var(--g));
        background-size: 200% 100%;
        animation: th-shimmer 3s linear infinite;
    }

    @keyframes th-shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }

    /* ── Stats pills (right side of hero) ─────────────────────── */
    .th-hero-stats { display: flex; flex-wrap: wrap; gap: 0.45rem; justify-content: flex-end; }

    .th-hpill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.82);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.7rem; font-weight: 700;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        backdrop-filter: blur(8px); white-space: nowrap;
        transition: transform .15s ease;
        cursor: default;
    }

    .th-hpill:hover { transform: translateY(-1px); }
    .th-hpill-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

    .th-hp-today   { border-color: rgba(255,255,255,0.15); }
    .th-hp-today .th-hpill-dot { background: #6ee7b7; }

    .th-hp-leave   { border-color: rgba(96,165,250,0.3); color: #93c5fd; }
    .th-hp-leave .th-hpill-dot { background: #60a5fa; }

    .th-hp-travel  { border-color: rgba(251,191,36,0.3); color: #fde047; }
    .th-hp-travel .th-hpill-dot { background: #fbbf24; }

    .th-hp-locator { border-color: rgba(167,139,250,0.3); color: #c4b5fd; }
    .th-hp-locator .th-hpill-dot { background: #a78bfa; }

    .th-hp-saln    { border-color: rgba(251,113,133,0.3); color: #fda4af; }
    .th-hp-saln .th-hpill-dot { background: #fb7185; }

    .th-hp-pds     { border-color: rgba(74,222,128,0.3); color: #86efac; }
    .th-hp-pds .th-hpill-dot { background: #4ade80; }

    .th-hp-employee{ border-color: rgba(52,211,153,0.3); color: #6ee7b7; }
    .th-hp-employee .th-hpill-dot { background: #34d399; }

    .th-hp-dtr     { border-color: rgba(148,163,184,0.3); color: #cbd5e1; }
    .th-hp-dtr .th-hpill-dot { background: #94a3b8; }

    /* ── Section label (mirrors hd-section-label exactly) ─────── */
    .th-section-label { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.75rem; }

    .th-section-icon {
        width: 28px; height: 28px; border-radius: 7px;
        background: linear-gradient(135deg, var(--g3), #bbf7d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .dark .th-section-icon {
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.15));
    }

    .th-section-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9375rem; font-weight: 700;
        color: var(--g); letter-spacing: -0.01em;
    }

    .th-section-divider {
        height: 1px;
        background: linear-gradient(90deg, var(--border), transparent);
        margin-bottom: 1rem;
    }

    /* ── Card (mirrors hd-card) ────────────────────────────────── */
    .th-card {
        background: var(--card);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
        margin-bottom: 1.25rem;
    }

    /* ── Date divider ──────────────────────────────────────────── */
    .th-date-row {
        display: flex; align-items: center; gap: 0.875rem;
        margin: 2rem 0 1rem;
    }

    .th-date-row:first-child { margin-top: 0.25rem; }

    .th-date-badge {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.3rem 0.875rem; border-radius: 999px;
        background: var(--card); border: 1.5px solid var(--border);
        font-size: 0.6875rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: var(--ink3); box-shadow: var(--shadow-sm);
        white-space: nowrap; font-family: 'DM Sans', sans-serif;
    }

    .th-date-today {
        background: linear-gradient(135deg, var(--g), #047857);
        border-color: transparent; color: #fff;
        box-shadow: 0 4px 14px rgba(5,150,105,0.35);
    }

    .th-date-line { flex: 1; height: 1px; background: var(--border); }

    .th-date-count { font-size: 0.6875rem; color: var(--ink3); font-family: 'DM Sans', sans-serif; white-space: nowrap; }

    /* ── Timeline spine ────────────────────────────────────────── */
    .th-timeline {
        position: relative;
        padding-left: 1.75rem;
        display: flex; flex-direction: column; gap: 0.375rem;
    }

    .th-timeline::before {
        content: '';
        position: absolute; left: 0;
        top: 1.25rem; bottom: 1.25rem;
        width: 2px; border-radius: 2px;
        background: linear-gradient(180deg,
            rgba(5,150,105,.7)  0%,
            rgba(217,119,6,.5)  33%,
            rgba(16,185,129,.5) 66%,
            rgba(5,150,105,.3)  100%
        );
    }

    /* ── Entry row (mirrors hd-activity exactly) ───────────────── */
    .th-entry {
        position: relative;
        display: flex; flex-wrap: nowrap; align-items: center;
        gap: 0.75rem; padding: 0.625rem 0.75rem;
        border-radius: var(--radius-sm);
        background: var(--paper);
        border: 1.5px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
        min-width: 0; overflow: hidden;
    }

    .dark .th-entry { background: rgba(16,185,129,0.04); }

    .th-entry:last-child { margin-bottom: 0; }

    .th-entry:hover {
        background: var(--card);
        border-color: rgba(5,150,105,0.2);
        transform: translateX(3px);
        box-shadow: var(--shadow-sm);
    }

    /* Spine dot */
    .th-entry::before {
        content: '';
        position: absolute;
        left: -1.9rem; top: 50%;
        transform: translateY(-50%);
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--card); border: 2px solid var(--border);
        transition: border-color .2s, background .2s, transform .2s;
        z-index: 1;
    }

    .th-entry:hover::before {
        border-color: var(--g); background: var(--g3);
        transform: translateY(-50%) scale(1.25);
    }

    /* ── Avatar ────────────────────────────────────────────────── */
    .th-avatar {
        width: 34px; height: 34px; min-width: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.75rem; color: #fff;
        flex-shrink: 0; overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
    }

    .th-avatar img { width: 34px; height: 34px; object-fit: cover; }

    .th-av-0 { background: linear-gradient(135deg,#059669,#047857); }
    .th-av-1 { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
    .th-av-2 { background: linear-gradient(135deg,#8b5cf6,#6d28d9); }
    .th-av-3 { background: linear-gradient(135deg,#f43f5e,#be123c); }
    .th-av-4 { background: linear-gradient(135deg,#d97706,#b45309); }
    .th-av-5 { background: linear-gradient(135deg,#0ea5e9,#0369a1); }
    .th-av-6 { background: linear-gradient(135deg,#22c55e,#15803d); }
    .th-av-7 { background: linear-gradient(135deg,#ef4444,#b91c1c); }

    /* ── Module icon (mirrors hd-activity-ico) ─────────────────── */
    .th-mod-icon {
        width: 32px; height: 32px; min-width: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .th-mi-leave    { background: #dbeafe; color: #1e40af; }
    .th-mi-travel   { background: #fef3c7; color: #92400e; }
    .th-mi-locator  { background: #ede9fe; color: #6d28d9; }
    .th-mi-saln     { background: #ffe4e6; color: #9f1239; }
    .th-mi-pds      { background: #d1fae5; color: #065f46; }
    .th-mi-employee { background: #ccfbf1; color: #115e59; }
    .th-mi-dtr      { background: #f3f4f6; color: #374151; }
    .th-mi-default  { background: #f3f4f6; color: #6b7280; }

    .dark .th-mi-leave    { background: rgba(59,130,246,.12);  color: #93c5fd; }
    .dark .th-mi-travel   { background: rgba(234,179,8,.12);   color: #fde047; }
    .dark .th-mi-locator  { background: rgba(139,92,246,.12);  color: #c4b5fd; }
    .dark .th-mi-saln     { background: rgba(244,63,94,.12);   color: #fda4af; }
    .dark .th-mi-pds      { background: rgba(34,197,94,.12);   color: #86efac; }
    .dark .th-mi-employee { background: rgba(16,185,129,.12);  color: #6ee7b7; }
    .dark .th-mi-dtr      { background: rgba(100,116,139,.12); color: #94a3b8; }

    /* ── Body text ─────────────────────────────────────────────── */
    .th-body { flex: 1 1 0%; min-width: 0; overflow: hidden; }

    .th-name {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; font-weight: 700; color: var(--ink);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 0.175rem; line-height: 1.3;
    }

    .th-desc {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; color: var(--ink3);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 0.3rem; line-height: 1.4;
    }

    .th-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 0.3rem; }

    /* ── Status chip (mirrors hd-chip exactly) ─────────────────── */
    .th-chip {
        display: inline-flex; align-items: center;
        padding: 0.15rem 0.5rem; border-radius: 999px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.625rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
        white-space: nowrap;
    }

    .th-chip-pending    { background: #fef3c7; color: #92400e; }
    .th-chip-approved   { background: #d1fae5; color: #065f46; }
    .th-chip-rejected   { background: #ffe4e6; color: #9f1239; }
    .th-chip-filed,
    .th-chip-uploaded,
    .th-chip-submitted  { background: #dbeafe; color: #1e40af; }
    .th-chip-registered { background: #d1fae5; color: #065f46; }
    .th-chip-cancelled  { background: #f3f4f6; color: #4b5563; }
    .th-chip-default    { background: #f3f4f6; color: #4b5563; }

    .dark .th-chip-pending    { background: rgba(146,64,14,.2);  color: #fde68a; }
    .dark .th-chip-approved   { background: rgba(6,95,70,.2);    color: #6ee7b7; }
    .dark .th-chip-rejected   { background: rgba(159,18,57,.2);  color: #fda4af; }
    .dark .th-chip-filed,
    .dark .th-chip-uploaded,
    .dark .th-chip-submitted  { background: rgba(29,78,216,.2);  color: #93c5fd; }
    .dark .th-chip-registered { background: rgba(6,95,70,.2);    color: #6ee7b7; }
    .dark .th-chip-cancelled  { background: rgba(75,85,99,.2);   color: #9ca3af; }

    /* ── Module label tag ──────────────────────────────────────── */
    .th-mod-label {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.625rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--ink3); background: var(--paper);
        padding: 0.15rem 0.45rem; border-radius: 999px;
        border: 1px solid var(--border); white-space: nowrap;
    }

    /* ── Timestamp ─────────────────────────────────────────────── */
    .th-time { font-size: 0.6875rem; color: var(--ink3); white-space: nowrap; }

    /* ── View record link ──────────────────────────────────────── */
    .th-rec-link {
        font-size: 0.6875rem; font-weight: 700;
        color: var(--g); text-decoration: none; white-space: nowrap;
        opacity: 0.75; transition: opacity .15s;
    }

    .th-rec-link:hover { opacity: 1; }

    /* ── Arrow caret (mirrors hd-activity-caret exactly) ──────── */
    .th-caret {
        width: 22px; height: 22px; border-radius: 5px;
        background: var(--border);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: translateX(-4px);
        transition: all 0.2s ease; flex-shrink: 0;
    }

    .th-entry:hover .th-caret {
        opacity: 1; transform: translateX(0);
        background: var(--g);
    }

    /* ── Empty state (mirrors hd-empty exactly) ────────────────── */
    .th-empty {
        text-align: center; padding: 3rem 1.5rem;
        border-radius: var(--radius-sm);
        background: var(--paper);
        border: 2px dashed var(--border);
    }

    .dark .th-empty { background: rgba(16,185,129,0.03); }

    .th-empty-icon {
        opacity: 0.2; margin: 0 auto 0.75rem;
        width: 2.5rem !important; height: 2.5rem !important;
    }

    .th-empty-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9375rem; font-weight: 700; color: var(--ink3); margin-bottom: 0.25rem;
    }

    .th-empty-text {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; color: var(--ink3); opacity: 0.75;
    }

    /* ── Pagination ────────────────────────────────────────────── */
    .th-pagination { margin-top: 1.5rem; }

    /* ── Animations (same keyframe as hd-fadein) ───────────────── */
    @keyframes hd-fadein {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .th-in { animation: hd-fadein 0.4s ease-out backwards; }
    .th-d1 { animation-delay: 0.05s; }
    .th-d2 { animation-delay: 0.10s; }
    .th-d3 { animation-delay: 0.15s; }

    /* ── Responsive ────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .th-hero-content { padding: 1.25rem 1.5rem; }
        .th-hero-title   { font-size: 1.25rem; }
    }

    @media (max-width: 640px) {
        .th-timeline  { padding-left: 1.25rem; }
        .th-mod-icon  { display: none; }
        .th-entry     { padding: 0.625rem 0.75rem; gap: 0.625rem; }
    }
</style>

<div class="th-root" x-data>

    {{-- ── Hero ─────────────────────────────────────────────────────── --}}
    <div class="th-hero th-in">
        <div class="th-hero-bg"></div>
        <div class="th-hero-grid"></div>

        <div class="th-hero-dots">
            @for($i = 0; $i < 25; $i++)
                <div class="th-hero-dot"></div>
            @endfor
        </div>

        <div class="th-hero-content">
            <div>
                <div class="th-hero-eyebrow">
                    <x-heroicon-o-clock class="w-3 h-3" />
                    Activity Log
                </div>
                <h1 class="th-hero-title">Transaction History</h1>
                <p class="th-hero-sub">A full audit trail of all employee activity across modules.</p>
            </div>

            <div class="th-hero-stats">
                <div class="th-hpill th-hp-today">
                    <span class="th-hpill-dot"></span>
                    <x-heroicon-o-clock class="w-3 h-3" />
                    {{ $todayCount }} today
                </div>

                @foreach($stats as $module => $count)
                    @php
                        $slug    = strtolower($module);
                        $pillCss = match($slug) {
                            'leave'    => 'th-hp-leave',
                            'travel'   => 'th-hp-travel',
                            'locator'  => 'th-hp-locator',
                            'saln'     => 'th-hp-saln',
                            'pds'      => 'th-hp-pds',
                            'employee' => 'th-hp-employee',
                            'dtr'      => 'th-hp-dtr',
                            default    => 'th-hp-today',
                        };
                        $icon = \App\Models\TransactionHistory::moduleIcon($module);
                    @endphp
                    <div class="th-hpill {{ $pillCss }}">
                        <span class="th-hpill-dot"></span>
                        <x-dynamic-component :component="$icon" class="w-3 h-3" />
                        {{ $module }}: {{ $count }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="th-hero-stripe"></div>
    </div>

    {{-- ── Timeline Card ──────────────────────────────────────────── --}}
    @if($grouped->isEmpty())
        <div class="th-card th-in th-d1">
            <div class="th-empty">
                <x-heroicon-o-clock class="th-empty-icon text-gray-400" />
                <div class="th-empty-title">No Transactions Logged Yet</div>
                <div class="th-empty-text">Activities will appear here as employees use HRMS modules.</div>
            </div>
        </div>
    @else
        <div class="th-card th-in th-d1">

            <div class="th-section-label">
                <div class="th-section-icon">
                    <x-heroicon-o-list-bullet class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                </div>
                <span class="th-section-title">Activity Timeline</span>
            </div>
            <div class="th-section-divider"></div>

            @foreach($grouped as $date => $entries)
                @php
                    $d           = \Carbon\Carbon::parse($date)->setTimezone('Asia/Manila');
                    $isToday     = $d->isToday();
                    $isYesterday = $d->isYesterday();
                    $label       = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $d->format('l, F j, Y'));
                @endphp

                {{-- Date divider --}}
                <div class="th-date-row th-in" style="animation-delay:.08s">
                    <div class="th-date-badge {{ $isToday ? 'th-date-today' : '' }}">
                        @if($isToday)<x-heroicon-s-bolt class="w-3 h-3" />@endif
                        {{ $label }}
                    </div>
                    <div class="th-date-line"></div>
                    <span class="th-date-count">
                        {{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}
                    </span>
                </div>

                {{-- Entries --}}
                <div class="th-timeline">
                    @foreach($entries as $idx => $tx)
                        @php
                            $modSlug    = strtolower($tx->module);
                            $modIconCss = match($modSlug) {
                                'leave'    => 'th-mi-leave',
                                'travel'   => 'th-mi-travel',
                                'locator'  => 'th-mi-locator',
                                'saln'     => 'th-mi-saln',
                                'pds'      => 'th-mi-pds',
                                'employee' => 'th-mi-employee',
                                'dtr'      => 'th-mi-dtr',
                                default    => 'th-mi-default',
                            };
                            $statusKey  = strtolower($tx->status);
                            $validSt    = ['pending','approved','rejected','filed','uploaded','submitted','registered','cancelled'];
                            $chipCss    = in_array($statusKey, $validSt) ? "th-chip-{$statusKey}" : 'th-chip-default';
                            $avClass    = 'th-av-' . (abs(crc32($tx->employee_name)) % 8);
                            $icon       = $tx->resolved_icon;
                            $viewUrl    = route('filament.hrms.resources.transaction-histories.view', $tx->id);
                        @endphp

                        <a href="{{ $viewUrl }}"
                           class="th-entry th-in"
                           style="animation-delay:{{ min(0.04 * $idx, 0.45) }}s"
                           title="{{ $tx->employee_name }} — {{ $tx->description }}">

                            {{-- Avatar --}}
                            <div class="th-avatar {{ $avClass }}">
                                @if($tx->user?->profile_photo_url)
                                    <img src="{{ $tx->user->profile_photo_url }}"
                                         alt="{{ $tx->employee_name }}">
                                @else
                                    {{ $tx->initials }}
                                @endif
                            </div>

                            {{-- Module icon --}}
                            <div class="th-mod-icon {{ $modIconCss }}">
                                <x-dynamic-component :component="$icon" class="w-3.5 h-3.5" />
                            </div>

                            {{-- Body --}}
                            <div class="th-body">
                                <div class="th-name">{{ $tx->employee_name }}</div>
                                <div class="th-desc">{{ $tx->description }}</div>
                                <div class="th-meta">
                                    <span class="th-chip {{ $chipCss }}">{{ ucfirst($tx->status) }}</span>
                                    <span class="th-mod-label">{{ $tx->module }}</span>
                                    <span class="th-time">
                                        {{ $tx->created_at->setTimezone('Asia/Manila')->format('g:i A') }}
                                    </span>
                                    @if($tx->record_url)
                                        <a href="{{ $tx->record_url }}"
                                           target="_blank"
                                           onclick="event.stopPropagation()"
                                           class="th-rec-link"
                                           title="Open original record">
                                            View Record ↗
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Caret --}}
                            <div class="th-caret">
                                <x-heroicon-o-arrow-right class="w-3 h-3 text-white" />
                            </div>

                        </a>
                    @endforeach
                </div>

            @endforeach

            <div class="th-pagination">
                {{ $transactions->links() }}
            </div>

        </div>
    @endif

</div>

</x-filament-panels::page>
