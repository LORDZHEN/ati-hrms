<x-filament-panels::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap');

    :root {
        --emerald-100: #d1fae5; --emerald-400: #34d399;
        --emerald-500: #10b981; --emerald-600: #059669; --emerald-700: #047857;
        --amber-500: #f59e0b; --amber-600: #d97706;
        --surface-0: #ffffff; --surface-1: #f8faf9;
        --border-soft: #e2e8e5;
        --text-primary: #0d1f18; --text-secondary: #3d5a50; --text-muted: #7a9690;
        --shadow-xs: 0 1px 2px rgba(5,150,105,0.04);
        --shadow-sm: 0 2px 8px rgba(5,150,105,0.06), 0 1px 3px rgba(0,0,0,0.04);
        --shadow-md: 0 8px 24px rgba(5,150,105,0.10), 0 2px 8px rgba(0,0,0,0.06);
        --radius-sm: 10px; --radius-lg: 20px; --radius-xl: 26px;
    }

    .dark {
        --surface-0: #0b1a14; --surface-1: #0f2119;
        --border-soft: #1e3a2c;
        --text-primary: #e8f5ef; --text-secondary: #9dcfba; --text-muted: #4d8a72;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.25); --shadow-md: 0 8px 24px rgba(0,0,0,0.35);
    }

    .th-root * { box-sizing: border-box; }

    .th-root {
        font-family: 'Outfit', sans-serif;
        background: var(--surface-1); min-height: 100vh;
        padding: 1.5rem 1.25rem; color: var(--text-primary);
    }

    /* ── HERO ── */
    .th-hero {
        position: relative; border-radius: var(--radius-xl);
        overflow: hidden; margin-bottom: 1.5rem;
        min-height: 145px; background: #071812;
    }

    .th-hero-canvas {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 90% at 82% 55%, rgba(5,150,105,0.52) 0%, transparent 62%),
            radial-gradient(ellipse 40% 55% at 95% 8%,  rgba(245,158,11,0.28) 0%, transparent 55%),
            radial-gradient(ellipse 50% 70% at 3%  88%, rgba(16,185,129,0.2) 0%, transparent 55%),
            linear-gradient(145deg, #040e08 0%, #0a2016 45%, #071410 100%);
    }

    .th-hero-mesh {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(16,185,129,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(16,185,129,0.05) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .th-hero-decor {
        position: absolute; top: 1.25rem; right: 1.75rem;
        display: flex; gap: 6px; flex-wrap: wrap; width: 60px; opacity: 0.22;
    }
    .th-hero-dot { width: 4px; height: 4px; border-radius: 50%; background: #34d399; }

    .th-hero-content {
        position: relative; z-index: 3; padding: 1.75rem 2.25rem;
        display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;
    }

    .th-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
        color: #6ee7b7; font-family: 'JetBrains Mono', monospace;
        font-size: 0.625rem; font-weight: 500; letter-spacing: 0.14em; text-transform: uppercase;
        padding: 0.3rem 0.875rem; border-radius: 999px;
        margin-bottom: 0.5rem; width: fit-content;
    }

    .th-hero-title {
        font-family: 'Instrument Serif', serif;
        font-size: 1.75rem; font-weight: 400;
        color: #ffffff; line-height: 1.1; margin-bottom: 0.25rem;
    }

    .th-hero-sub { font-size: 0.8125rem; color: rgba(255,255,255,0.44); }

    .th-hero-pills { display: flex; flex-wrap: wrap; gap: 0.4rem; justify-content: flex-end; }

    .th-hpill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.11);
        color: rgba(255,255,255,0.8);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5625rem; font-weight: 600;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        backdrop-filter: blur(12px); white-space: nowrap;
        transition: transform 0.15s ease; cursor: default;
    }
    .th-hpill:hover { transform: translateY(-1px); }
    .th-hpill-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

    .th-hp-today   .th-hpill-dot { background: #6ee7b7; }
    .th-hp-leave   { border-color: rgba(96,165,250,0.3); color: #93c5fd; }
    .th-hp-leave   .th-hpill-dot { background: #60a5fa; }
    .th-hp-travel  { border-color: rgba(251,191,36,0.3); color: #fde047; }
    .th-hp-travel  .th-hpill-dot { background: #fbbf24; }
    .th-hp-locator { border-color: rgba(167,139,250,0.3); color: #c4b5fd; }
    .th-hp-locator .th-hpill-dot { background: #a78bfa; }
    .th-hp-saln    { border-color: rgba(251,113,133,0.3); color: #fda4af; }
    .th-hp-saln    .th-hpill-dot { background: #fb7185; }
    .th-hp-pds     { border-color: rgba(74,222,128,0.3); color: #86efac; }
    .th-hp-pds     .th-hpill-dot { background: #4ade80; }
    .th-hp-employee{ border-color: rgba(52,211,153,0.3); color: #6ee7b7; }
    .th-hp-employee .th-hpill-dot { background: #34d399; }
    .th-hp-dtr     { border-color: rgba(148,163,184,0.3); color: #cbd5e1; }
    .th-hp-dtr     .th-hpill-dot { background: #94a3b8; }

    .th-hero-bar {
        position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(16,185,129,0.6) 20%, rgba(245,158,11,0.8) 50%, rgba(16,185,129,0.6) 80%, transparent 100%);
        background-size: 300% 100%; animation: th-shimmer 4s linear infinite;
    }
    @keyframes th-shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

    /* ── SECTION HEADER ── */
    .th-section-hd { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.875rem; }
    .th-section-badge {
        width: 30px; height: 30px; border-radius: 9px;
        background: linear-gradient(135deg, var(--emerald-100), #bbf7d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dark .th-section-badge { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(5,150,105,0.12)); }
    .th-section-title { font-size: 0.875rem; font-weight: 700; color: var(--emerald-600); }
    .dark .th-section-title { color: var(--emerald-400); }
    .th-section-rule { height: 1px; background: linear-gradient(90deg, var(--border-soft), transparent); margin-bottom: 1rem; }

    /* ── CARD ── */
    .th-card {
        background: var(--surface-0); border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft); box-shadow: var(--shadow-sm);
        padding: 1.25rem; margin-bottom: 1.25rem;
    }

    /* ── DATE ROW ── */
    .th-date-row { display: flex; align-items: center; gap: 0.875rem; margin: 2rem 0 1rem; }
    .th-date-row:first-of-type { margin-top: 0.25rem; }

    .th-date-badge {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.3rem 0.875rem; border-radius: 999px;
        background: var(--surface-0); border: 1.5px solid var(--border-soft);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5625rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--text-muted); box-shadow: var(--shadow-xs); white-space: nowrap;
    }

    .th-date-badge.today {
        background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
        border-color: transparent; color: #fff;
        box-shadow: 0 4px 14px rgba(5,150,105,0.32);
    }

    .th-date-line { flex: 1; height: 1px; background: var(--border-soft); }
    .th-date-count { font-family: 'JetBrains Mono', monospace; font-size: 0.5625rem; color: var(--text-muted); white-space: nowrap; }

    /* ── TIMELINE ── */
    .th-timeline {
        position: relative; padding-left: 1.875rem;
        display: flex; flex-direction: column; gap: 0.375rem;
    }

    .th-timeline::before {
        content: ''; position: absolute; left: 0;
        top: 1.25rem; bottom: 1.25rem;
        width: 2px; border-radius: 2px;
        background: linear-gradient(180deg,
            rgba(5,150,105,.65) 0%,
            rgba(245,158,11,.5) 33%,
            rgba(16,185,129,.4) 66%,
            rgba(5,150,105,.25) 100%
        );
    }

    /* ── ENTRY ── */
    .th-entry {
        position: relative; display: flex; align-items: center;
        gap: 0.75rem; padding: 0.75rem 0.875rem;
        border-radius: var(--radius-sm); background: var(--surface-1);
        border: 1px solid transparent; text-decoration: none;
        transition: all 0.2s ease; min-width: 0; overflow: hidden;
    }

    .dark .th-entry { background: rgba(16,185,129,0.03); }

    .th-entry:hover {
        background: var(--surface-0); border-color: rgba(16,185,129,0.18);
        transform: translateX(4px); box-shadow: var(--shadow-sm);
    }

    .th-entry::before {
        content: ''; position: absolute;
        left: -2rem; top: 50%; transform: translateY(-50%);
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--surface-0); border: 2px solid var(--border-soft);
        transition: all 0.2s; z-index: 1;
    }

    .th-entry:hover::before { border-color: var(--emerald-500); background: var(--emerald-100); transform: translateY(-50%) scale(1.3); }

    /* ── AVATAR ── */
    .th-avatar {
        width: 36px; height: 36px; min-width: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.6875rem; color: #fff; flex-shrink: 0;
        overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.16);
    }

    .th-avatar img { width: 36px; height: 36px; object-fit: cover; }

    .th-av-0 { background: linear-gradient(135deg,#059669,#047857); }
    .th-av-1 { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
    .th-av-2 { background: linear-gradient(135deg,#8b5cf6,#6d28d9); }
    .th-av-3 { background: linear-gradient(135deg,#f43f5e,#be123c); }
    .th-av-4 { background: linear-gradient(135deg,#d97706,#b45309); }
    .th-av-5 { background: linear-gradient(135deg,#0ea5e9,#0369a1); }
    .th-av-6 { background: linear-gradient(135deg,#22c55e,#15803d); }
    .th-av-7 { background: linear-gradient(135deg,#ef4444,#b91c1c); }

    /* ── MODULE ICON ── */
    .th-mod-ico {
        width: 32px; height: 32px; min-width: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .th-mi-leave    { background: #eff6ff; color: #1e40af; }
    .th-mi-travel   { background: #fffbeb; color: #92400e; }
    .th-mi-locator  { background: #faf5ff; color: #6d28d9; }
    .th-mi-saln     { background: #fff1f2; color: #9f1239; }
    .th-mi-pds      { background: #f0fdf4; color: #065f46; }
    .th-mi-employee { background: #f0fdfa; color: #115e59; }
    .th-mi-dtr      { background: #f9fafb; color: #374151; }
    .th-mi-default  { background: #f9fafb; color: #6b7280; }

    .dark .th-mi-leave    { background: rgba(59,130,246,.1);  color: #93c5fd; }
    .dark .th-mi-travel   { background: rgba(234,179,8,.1);   color: #fde047; }
    .dark .th-mi-locator  { background: rgba(139,92,246,.1);  color: #c4b5fd; }
    .dark .th-mi-saln     { background: rgba(244,63,94,.1);   color: #fda4af; }
    .dark .th-mi-pds      { background: rgba(34,197,94,.1);   color: #86efac; }
    .dark .th-mi-employee { background: rgba(16,185,129,.1);  color: #6ee7b7; }
    .dark .th-mi-dtr      { background: rgba(100,116,139,.1); color: #94a3b8; }

    /* ── BODY ── */
    .th-body { flex: 1; min-width: 0; overflow: hidden; }

    .th-name {
        font-size: 0.8125rem; font-weight: 700; color: var(--text-primary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.2rem;
    }

    .th-desc {
        font-size: 0.75rem; color: var(--text-secondary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.3rem;
    }

    .th-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 0.3rem; }

    /* ── CHIPS ── */
    .th-chip {
        display: inline-flex; align-items: center;
        padding: 0.125rem 0.475rem; border-radius: 999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em; white-space: nowrap;
    }

    .th-chip-pending    { background: #fef3c7; color: #92400e; }
    .th-chip-approved   { background: #dcfce7; color: #15803d; }
    .th-chip-rejected   { background: #ffe4e6; color: #9f1239; }
    .th-chip-filed,
    .th-chip-uploaded,
    .th-chip-submitted  { background: #dbeafe; color: #1e40af; }
    .th-chip-registered { background: #dcfce7; color: #15803d; }
    .th-chip-cancelled  { background: #f3f4f6; color: #4b5563; }
    .th-chip-default    { background: #f3f4f6; color: #4b5563; }

    .dark .th-chip-pending    { background: rgba(146,64,14,.18);  color: #fde68a; }
    .dark .th-chip-approved   { background: rgba(21,128,61,.18);  color: #86efac; }
    .dark .th-chip-rejected   { background: rgba(159,18,57,.18);  color: #fda4af; }
    .dark .th-chip-filed,
    .dark .th-chip-uploaded,
    .dark .th-chip-submitted  { background: rgba(29,78,216,.18);  color: #93c5fd; }
    .dark .th-chip-registered { background: rgba(21,128,61,.18);  color: #86efac; }
    .dark .th-chip-cancelled  { background: rgba(75,85,99,.18);   color: #9ca3af; }

    .th-mod-label {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;
        color: var(--text-muted); background: var(--surface-1);
        padding: 0.125rem 0.45rem; border-radius: 999px; border: 1px solid var(--border-soft);
        white-space: nowrap;
    }

    .th-time { font-family: 'JetBrains Mono', monospace; font-size: 0.5625rem; color: var(--text-muted); white-space: nowrap; }

    .th-rec-link {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5rem; font-weight: 700;
        color: var(--emerald-600); text-decoration: none; white-space: nowrap;
        opacity: 0.75; transition: opacity .15s;
    }
    .th-rec-link:hover { opacity: 1; }

    /* ── CARET ── */
    .th-caret {
        width: 22px; height: 22px; border-radius: 6px;
        background: var(--border-soft);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: translateX(-4px);
        transition: all 0.2s ease; flex-shrink: 0;
    }

    .th-entry:hover .th-caret { opacity: 1; transform: translateX(0); background: var(--emerald-600); }

    /* ── EMPTY ── */
    .th-empty {
        text-align: center; padding: 3rem 1.5rem;
        border-radius: var(--radius-sm);
        background: var(--surface-1); border: 2px dashed var(--border-soft);
    }
    .th-empty-icon { opacity: 0.18; margin: 0 auto 0.75rem; width: 2.25rem !important; height: 2.25rem !important; }
    .th-empty-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.25rem; }
    .th-empty-text  { font-size: 0.8125rem; color: var(--text-muted); opacity: 0.75; }

    /* ── ANIMATIONS ── */
    @keyframes th-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .th-anim { animation: th-in 0.45s cubic-bezier(0.22,1,0.36,1) backwards; }
    .th-a1 { animation-delay: 0.04s; }
    .th-a2 { animation-delay: 0.09s; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) { .th-root { padding: 1rem; } .th-hero-content { padding: 1.5rem; } .th-hero-title { font-size: 1.375rem; } }
    @media (max-width: 640px) { .th-timeline { padding-left: 1.25rem; } .th-mod-ico { display: none; } }
</style>

<div class="th-root" x-data>

    {{-- Hero --}}
    <div class="th-hero th-anim">
        <div class="th-hero-canvas"></div>
        <div class="th-hero-mesh"></div>
        <div class="th-hero-decor">
            @for($i=0;$i<15;$i++)<div class="th-hero-dot"></div>@endfor
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

            <div class="th-hero-pills">
                <div class="th-hpill th-hp-today">
                    <span class="th-hpill-dot"></span>
                    <x-heroicon-o-clock class="w-3 h-3" />
                    {{ $todayCount }} today
                </div>

                @foreach($stats as $module => $count)
                    @php
                        $slug = strtolower($module);
                        $pc = match($slug) {
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
                    <div class="th-hpill {{ $pc }}">
                        <span class="th-hpill-dot"></span>
                        <x-dynamic-component :component="$icon" class="w-3 h-3" />
                        {{ $module }}: {{ $count }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="th-hero-bar"></div>
    </div>

    {{-- Timeline --}}
    @if($grouped->isEmpty())
        <div class="th-card th-anim th-a1">
            <div class="th-empty">
                <x-heroicon-o-clock class="th-empty-icon text-gray-400" />
                <div class="th-empty-title">No Transactions Logged Yet</div>
                <div class="th-empty-text">Activities will appear here as employees use HRMS modules.</div>
            </div>
        </div>
    @else
        <div class="th-card th-anim th-a1">
            <div class="th-section-hd">
                <div class="th-section-badge">
                    <x-heroicon-o-list-bullet class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                </div>
                <span class="th-section-title">Activity Timeline</span>
            </div>
            <div class="th-section-rule"></div>

            @foreach($grouped as $date => $entries)
                @php
                    $d = \Carbon\Carbon::parse($date)->setTimezone('Asia/Manila');
                    $isToday = $d->isToday();
                    $label = $isToday ? 'Today' : ($d->isYesterday() ? 'Yesterday' : $d->format('l, F j, Y'));
                @endphp

                <div class="th-date-row">
                    <div class="th-date-badge {{ $isToday ? 'today' : '' }}">
                        @if($isToday)<x-heroicon-s-bolt class="w-3 h-3" />@endif
                        {{ $label }}
                    </div>
                    <div class="th-date-line"></div>
                    <span class="th-date-count">{{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}</span>
                </div>

                <div class="th-timeline">
                    @foreach($entries as $idx => $tx)
                        @php
                            $mslug   = strtolower($tx->module);
                            $micss   = match($mslug) {
                                'leave'    => 'th-mi-leave',
                                'travel'   => 'th-mi-travel',
                                'locator'  => 'th-mi-locator',
                                'saln'     => 'th-mi-saln',
                                'pds'      => 'th-mi-pds',
                                'employee' => 'th-mi-employee',
                                'dtr'      => 'th-mi-dtr',
                                default    => 'th-mi-default',
                            };
                            $sk  = strtolower($tx->status);
                            $valid = ['pending','approved','rejected','filed','uploaded','submitted','registered','cancelled'];
                            $chip = in_array($sk, $valid) ? "th-chip-{$sk}" : 'th-chip-default';
                            $avc  = 'th-av-' . (abs(crc32($tx->employee_name)) % 8);
                            $url  = route('filament.hrms.resources.transaction-histories.view', $tx->id);
                        @endphp

                        <a href="{{ $url }}"
                           class="th-entry"
                           style="animation: th-in 0.4s cubic-bezier(0.22,1,0.36,1) {{ min(0.04*$idx, 0.4) }}s backwards;"
                           title="{{ $tx->employee_name }} — {{ $tx->description }}">

                            <div class="th-avatar {{ $avc }}">
                                @if($tx->user?->profile_photo_url)
                                    <img src="{{ $tx->user->profile_photo_url }}" alt="{{ $tx->employee_name }}">
                                @else
                                    {{ $tx->initials }}
                                @endif
                            </div>

                            <div class="th-mod-ico {{ $micss }}">
                                <x-dynamic-component :component="$tx->resolved_icon" class="w-3.5 h-3.5" />
                            </div>

                            <div class="th-body">
                                <div class="th-name">{{ $tx->employee_name }}</div>
                                <div class="th-desc">{{ $tx->description }}</div>
                                <div class="th-meta">
                                    <span class="th-chip {{ $chip }}">{{ ucfirst($tx->status) }}</span>
                                    <span class="th-mod-label">{{ $tx->module }}</span>
                                    <span class="th-time">{{ $tx->created_at->setTimezone('Asia/Manila')->format('g:i A') }}</span>
                                    @if($tx->record_url)
                                        <a href="{{ $tx->record_url }}" target="_blank"
                                           onclick="event.stopPropagation()" class="th-rec-link">
                                            View Record ↗
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="th-caret">
                                <x-heroicon-o-arrow-right class="w-3 h-3 text-white" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach

            <div style="margin-top:1.5rem;">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif

</div>

</x-filament-panels::page>
