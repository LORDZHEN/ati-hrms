<x-filament::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap');

    :root {
        --emerald-50:  #ecfdf5;
        --emerald-100: #d1fae5;
        --emerald-400: #34d399;
        --emerald-500: #10b981;
        --emerald-600: #059669;
        --emerald-700: #047857;
        --emerald-900: #064e3b;
        --amber-400:   #fbbf24;
        --amber-500:   #f59e0b;
        --amber-600:   #d97706;

        --surface-0:   #ffffff;
        --surface-1:   #f8faf9;
        --surface-2:   #f0f4f2;
        --border-soft: #e2e8e5;
        --border-mid:  #c8d5d0;
        --text-primary: #0d1f18;
        --text-secondary: #3d5a50;
        --text-muted: #7a9690;

        --shadow-xs: 0 1px 2px rgba(5,150,105,0.04);
        --shadow-sm: 0 2px 8px rgba(5,150,105,0.06), 0 1px 3px rgba(0,0,0,0.04);
        --shadow-md: 0 8px 24px rgba(5,150,105,0.10), 0 2px 8px rgba(0,0,0,0.06);
        --shadow-lg: 0 20px 48px rgba(5,150,105,0.14), 0 4px 16px rgba(0,0,0,0.08);
        --shadow-glow: 0 0 0 3px rgba(16,185,129,0.12);

        --radius-sm: 10px;
        --radius-md: 14px;
        --radius-lg: 20px;
        --radius-xl: 26px;
    }

    .dark {
        --surface-0:   #0b1a14;
        --surface-1:   #0f2119;
        --surface-2:   #152b21;
        --border-soft: #1e3a2c;
        --border-mid:  #27503c;
        --text-primary: #e8f5ef;
        --text-secondary: #9dcfba;
        --text-muted: #4d8a72;
        --shadow-xs: 0 1px 2px rgba(0,0,0,0.3);
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.25);
        --shadow-md: 0 8px 24px rgba(0,0,0,0.35);
        --shadow-lg: 0 20px 48px rgba(0,0,0,0.5);
    }

    /* ═══════════ RESET & BASE ═══════════ */
    .d-root * { box-sizing: border-box; }

    .d-root {
        font-family: 'Outfit', sans-serif;
        background: var(--surface-1);
        min-height: 100vh;
        padding: 1.5rem 1.25rem;
        color: var(--text-primary);
    }

    /* ═══════════ HERO ═══════════ */
    .d-hero {
        position: relative;
        border-radius: var(--radius-xl);
        overflow: hidden;
        margin-bottom: 1.5rem;
        min-height: 168px;
        background: #071812;
    }

    .d-hero-canvas {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 70% 120% at 85% 60%, rgba(5,150,105,0.6) 0%, transparent 60%),
            radial-gradient(ellipse 50% 80% at 95% 5%,  rgba(245,158,11,0.3) 0%, transparent 55%),
            radial-gradient(ellipse 60% 90% at 0%  90%, rgba(16,185,129,0.2) 0%, transparent 55%),
            linear-gradient(145deg, #040e08 0%, #0a2016 45%, #071410 100%);
    }

    .d-hero-noise {
        position: absolute; inset: 0;
        opacity: 0.025;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
        background-size: 128px 128px;
    }

    .d-hero-mesh {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.05) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(ellipse 80% 100% at 50% 50%, black 30%, transparent 80%);
    }

    .d-hero-orbs {
        position: absolute; inset: 0; pointer-events: none;
    }

    .d-hero-orb {
        position: absolute; border-radius: 50%;
        filter: blur(40px); opacity: 0.15;
    }

    .d-hero-orb-1 { width: 200px; height: 200px; background: #10b981; top: -60px; right: 15%; animation: d-float 8s ease-in-out infinite; }
    .d-hero-orb-2 { width: 140px; height: 140px; background: #f59e0b; bottom: -40px; right: 5%; animation: d-float 6s ease-in-out infinite reverse; }

    @keyframes d-float {
        0%, 100% { transform: translateY(0) scale(1); }
        50%       { transform: translateY(-12px) scale(1.05); }
    }

    .d-hero-decor {
        position: absolute; top: 1.25rem; right: 1.75rem;
        display: flex; gap: 6px; flex-wrap: wrap; width: 60px;
    }

    .d-hero-decor-dot { width: 4px; height: 4px; border-radius: 50%; background: rgba(52,211,153,0.4); }

    .d-hero-content {
        position: relative; z-index: 3;
        padding: 1.875rem 2.25rem;
        display: flex; align-items: center;
        justify-content: space-between; gap: 2rem;
        flex-wrap: wrap;
    }

    .d-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.12);
        border: 1px solid rgba(16,185,129,0.25);
        color: #6ee7b7;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.625rem; font-weight: 500;
        letter-spacing: 0.14em; text-transform: uppercase;
        padding: 0.3rem 0.875rem; border-radius: 999px;
        margin-bottom: 0.75rem; width: fit-content;
    }

    .d-hero-name {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem; font-weight: 400; font-style: italic;
        color: #ffffff; line-height: 1.1;
        margin-bottom: 0.375rem; letter-spacing: -0.01em;
    }

    .d-hero-name strong { font-style: normal; font-weight: 400; color: #6ee7b7; }

    .d-hero-sub {
        font-size: 0.8125rem; font-weight: 400;
        color: rgba(255,255,255,0.4); letter-spacing: 0.01em;
    }

    .d-hero-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.625rem; }

    .d-hero-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end; }

    .d-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(12px);
        color: rgba(255,255,255,0.75);
        font-size: 0.6875rem; font-weight: 600;
        padding: 0.375rem 0.875rem; border-radius: 999px;
        white-space: nowrap; transition: all 0.2s ease;
    }

    .d-pill:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.18); }

    .d-pill-green {
        background: rgba(16,185,129,0.15);
        border-color: rgba(16,185,129,0.35);
        color: #6ee7b7;
    }

    .d-hero-bar {
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg,
            transparent 0%,
            rgba(16,185,129,0.6) 20%,
            rgba(245,158,11,0.8) 50%,
            rgba(16,185,129,0.6) 80%,
            transparent 100%
        );
        background-size: 300% 100%;
        animation: d-bar-slide 4s linear infinite;
    }

    @keyframes d-bar-slide {
        0%   { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }

    /* ═══════════ SECTION HEADER ═══════════ */
    .d-section-hd {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .d-section-hd-left { display: flex; align-items: center; gap: 0.625rem; }

    .d-section-badge {
        width: 30px; height: 30px; border-radius: 9px;
        background: linear-gradient(135deg, var(--emerald-100), #bbf7d0);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .dark .d-section-badge {
        background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(5,150,105,0.12));
    }

    .d-section-title {
        font-size: 0.875rem; font-weight: 700;
        color: var(--emerald-600); letter-spacing: -0.01em;
    }

    .dark .d-section-title { color: var(--emerald-400); }

    .d-section-rule {
        height: 1px;
        background: linear-gradient(90deg, var(--border-soft), transparent);
        margin-bottom: 1rem;
    }

    .d-section-link {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.6875rem; font-weight: 700;
        color: var(--emerald-600); text-decoration: none;
        border: 1.5px solid currentColor;
        padding: 0.25rem 0.625rem; border-radius: 999px;
        transition: all 0.2s ease; white-space: nowrap;
    }

    .dark .d-section-link { color: var(--emerald-400); }
    .d-section-link:hover { background: var(--emerald-600); color: #fff; border-color: var(--emerald-600); }
    .dark .d-section-link:hover { background: var(--emerald-500); border-color: var(--emerald-500); }

    /* ═══════════ CARD ═══════════ */
    .d-card {
        background: var(--surface-0);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .d-card:hover { box-shadow: var(--shadow-md); }

    /* ═══════════ ADMIN GRID ═══════════ */
    .d-widget-2col {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 1rem; margin-bottom: 1.25rem;
    }

    .d-admin-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: auto auto;
        grid-template-areas:
            "activities pending announcements"
            "activities pending events";
        gap: 1rem; margin-bottom: 1.25rem;
    }

    .d-area-activities    { grid-area: activities; }
    .d-area-pending       { grid-area: pending; }
    .d-area-announcements { grid-area: announcements; }
    .d-area-events        { grid-area: events; }

    /* ═══════════ MODULE GRID ═══════════ */
    .d-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(196px, 1fr));
        gap: 0.875rem; margin-bottom: 1.25rem;
    }

    .d-module {
        background: var(--surface-0);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft);
        padding: 1.25rem;
        text-decoration: none; display: block;
        position: relative; overflow: hidden;
        transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
        box-shadow: var(--shadow-xs);
    }

    .d-module::after {
        content: '';
        position: absolute; inset: 0;
        border-radius: inherit;
        background: linear-gradient(135deg, rgba(16,185,129,0.04), rgba(245,158,11,0.03));
        opacity: 0; transition: opacity 0.3s ease;
    }

    .d-module:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: var(--shadow-lg);
        border-color: rgba(16,185,129,0.25);
    }

    .d-module:hover::after { opacity: 1; }

    .d-module-accent {
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--emerald-500), var(--amber-500));
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s ease; border-radius: inherit;
    }

    .d-module:hover .d-module-accent { transform: scaleX(1); }

    .d-module-top {
        display: flex; align-items: flex-start;
        justify-content: space-between; margin-bottom: 1rem;
    }

    .d-module-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.3s ease; flex-shrink: 0;
    }

    .d-module:hover .d-module-icon { transform: scale(1.12) rotate(-8deg); }

    .d-module-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.625rem; font-weight: 600;
        color: var(--text-primary); line-height: 1;
    }

    .d-module-title {
        font-size: 0.875rem; font-weight: 700;
        color: var(--text-primary); margin-bottom: 0.25rem;
    }

    .d-module-desc {
        font-size: 0.75rem; color: var(--text-muted); line-height: 1.45;
    }

    .d-module-foot {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 1rem; padding-top: 0.875rem;
        border-top: 1px solid var(--border-soft);
    }

    .d-module-label {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5625rem; font-weight: 500;
        text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--text-muted);
    }

    .d-module-btn {
        width: 28px; height: 28px; border-radius: 8px;
        background: linear-gradient(135deg, var(--emerald-600), var(--emerald-700));
        display: flex; align-items: center; justify-content: center;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(5,150,105,0.3);
    }

    .d-module:hover .d-module-btn {
        background: linear-gradient(135deg, var(--amber-500), var(--amber-600));
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        transform: translateX(2px);
    }

    /* ═══════════ ACTIVITY ROW ═══════════ */
    .d-activity {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.625rem 0.75rem; border-radius: var(--radius-sm);
        text-decoration: none; transition: all 0.2s ease;
        border: 1px solid transparent; margin-bottom: 0.375rem;
        position: relative;
    }

    .d-activity:last-child { margin-bottom: 0; }

    .d-activity:hover {
        background: var(--surface-1);
        border-color: rgba(16,185,129,0.15);
        transform: translateX(4px);
    }

    .dark .d-activity:hover { background: rgba(16,185,129,0.04); }

    .d-activity-ico {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .d-activity-ico.blue   { background: #eff6ff; color: #2563eb; }
    .d-activity-ico.amber  { background: #fffbeb; color: #d97706; }
    .d-activity-ico.purple { background: #faf5ff; color: #7c3aed; }
    .d-activity-ico.rose   { background: #fff1f2; color: #e11d48; }
    .d-activity-ico.green  { background: #f0fdf4; color: #16a34a; }
    .d-activity-ico.gray   { background: #f9fafb; color: #6b7280; }
    .d-activity-ico.teal   { background: #f0fdfa; color: #0d9488; }
    .d-activity-ico.red    { background: #fef2f2; color: #dc2626; }

    .dark .d-activity-ico.blue   { background: rgba(37,99,235,0.1); }
    .dark .d-activity-ico.amber  { background: rgba(217,119,6,0.1); }
    .dark .d-activity-ico.purple { background: rgba(124,58,237,0.1); }
    .dark .d-activity-ico.rose   { background: rgba(225,29,72,0.1); }
    .dark .d-activity-ico.green  { background: rgba(22,163,74,0.1); }
    .dark .d-activity-ico.gray   { background: rgba(107,114,128,0.1); }
    .dark .d-activity-ico.teal   { background: rgba(13,148,136,0.1); }
    .dark .d-activity-ico.red    { background: rgba(220,38,38,0.1); }

    .d-activity-body { flex: 1; min-width: 0; }

    .d-activity-name {
        font-size: 0.8125rem; font-weight: 700; color: var(--text-primary);
        margin-bottom: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .d-activity-meta { display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }

    .d-chip {
        display: inline-flex; align-items: center;
        padding: 0.1rem 0.475rem; border-radius: 999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5625rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.08em;
    }

    .d-chip.blue   { background: #dbeafe; color: #1d4ed8; }
    .d-chip.amber  { background: #fef3c7; color: #92400e; }
    .d-chip.purple { background: #ede9fe; color: #6d28d9; }
    .d-chip.rose   { background: #ffe4e6; color: #9f1239; }
    .d-chip.green  { background: #dcfce7; color: #15803d; }
    .d-chip.gray   { background: #f3f4f6; color: #374151; }
    .d-chip.teal   { background: #ccfbf1; color: #0f766e; }
    .d-chip.red    { background: #fee2e2; color: #b91c1c; }

    .d-activity-date { font-size: 0.625rem; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }

    .d-activity-arrow {
        width: 22px; height: 22px; border-radius: 6px;
        background: var(--border-soft);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: translateX(-4px);
        transition: all 0.2s ease; flex-shrink: 0;
    }

    .d-activity:hover .d-activity-arrow {
        opacity: 1; transform: translateX(0);
        background: var(--emerald-600);
    }

    /* ═══════════ PENDING ACTIONS ═══════════ */
    .d-pending {
        display: flex; align-items: center; gap: 0.875rem;
        padding: 0.875rem 1rem; border-radius: var(--radius-sm);
        text-decoration: none; background: var(--surface-1);
        transition: all 0.2s ease; margin-bottom: 0.375rem;
        border: 1px solid transparent; position: relative; overflow: hidden;
    }

    .dark .d-pending { background: rgba(16,185,129,0.03); }
    .d-pending:last-child { margin-bottom: 0; }

    .d-pending::before {
        content: '';
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px; border-radius: 0 2px 2px 0;
        background: var(--border-soft); transition: background 0.2s;
    }

    .d-pending:hover::before { background: var(--emerald-500); }

    .d-pending:hover {
        background: var(--surface-0);
        border-color: rgba(16,185,129,0.15);
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
    }

    .d-pending-ico {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .d-pending-ico.blue   { background: #eff6ff; color: #2563eb; }
    .d-pending-ico.amber  { background: #fffbeb; color: #d97706; }
    .d-pending-ico.purple { background: #faf5ff; color: #7c3aed; }
    .d-pending-ico.green  { background: #f0fdf4; color: #16a34a; }
    .d-pending-ico.rose   { background: #fff1f2; color: #e11d48; }
    .d-pending-ico.gray   { background: #f9fafb; color: #6b7280; }

    .dark .d-pending-ico.blue   { background: rgba(37,99,235,0.1); }
    .dark .d-pending-ico.amber  { background: rgba(217,119,6,0.1); }
    .dark .d-pending-ico.purple { background: rgba(124,58,237,0.1); }
    .dark .d-pending-ico.green  { background: rgba(22,163,74,0.1); }
    .dark .d-pending-ico.rose   { background: rgba(225,29,72,0.1); }

    .d-pending-body { flex: 1; }

    .d-pending-title { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); }

    .d-pending-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem; }

    .d-pending-count {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
        display: flex; align-items: center; justify-content: center;
        color: white; font-family: 'JetBrains Mono', monospace;
        font-weight: 700; font-size: 0.9375rem; flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
    }

    /* ═══════════ ANNOUNCEMENTS ═══════════ */
    .d-announce {
        display: block; text-decoration: none;
        padding: 0.625rem 0.875rem; border-radius: var(--radius-sm);
        border-left: 3px solid var(--border-soft);
        background: var(--surface-1); margin-bottom: 0.375rem;
        transition: all 0.2s ease;
    }

    .dark .d-announce { background: rgba(16,185,129,0.04); }
    .d-announce:last-child { margin-bottom: 0; }
    .d-announce.high   { border-left-color: #dc2626; background: #fef2f2; }
    .d-announce.medium { border-left-color: #d97706; background: #fffbeb; }
    .d-announce.low    { border-left-color: var(--emerald-500); }
    .dark .d-announce.high   { background: rgba(220,38,38,0.07); }
    .dark .d-announce.medium { background: rgba(217,119,6,0.07); }

    .d-announce:hover { transform: translateX(4px); box-shadow: var(--shadow-sm); }

    .d-announce-row { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.2rem; }

    .d-announce-title {
        font-size: 0.8125rem; font-weight: 700; color: var(--text-primary);
        flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .d-announce-date { font-size: 0.6rem; color: var(--text-muted); white-space: nowrap; font-family: 'JetBrains Mono', monospace; }

    .d-announce-msg {
        font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ═══════════ EVENTS ═══════════ */
    .d-event {
        display: flex; gap: 0.75rem;
        padding: 0.625rem 0.75rem; border-radius: var(--radius-sm);
        text-decoration: none; transition: all 0.25s ease;
        background: var(--surface-1); border: 1px solid transparent;
        margin-bottom: 0.375rem;
    }

    .dark .d-event { background: rgba(16,185,129,0.03); }
    .d-event:last-child { margin-bottom: 0; }

    .d-event:hover {
        background: var(--surface-0); border-color: rgba(16,185,129,0.2);
        box-shadow: var(--shadow-sm); transform: translateY(-2px);
    }

    .d-event-cal {
        flex-shrink: 0; width: 40px;
        border-radius: var(--radius-sm);
        background: linear-gradient(160deg, var(--emerald-600), var(--emerald-700));
        text-align: center; color: white;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 0.3rem 0;
        transition: background 0.2s ease;
    }

    .d-event:hover .d-event-cal { background: linear-gradient(160deg, var(--amber-500), var(--amber-600)); }

    .d-event-mon { font-family: 'JetBrains Mono', monospace; font-size: 0.5rem; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.85; }
    .d-event-day { font-size: 1.0625rem; font-weight: 800; line-height: 1.1; }
    .d-event-body { flex: 1; min-width: 0; }

    .d-event-title {
        font-size: 0.8125rem; font-weight: 700; color: var(--text-primary);
        margin-bottom: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .d-event-meta { font-size: 0.6875rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

    /* ═══════════ BIRTHDAY ═══════════ */
    .d-bday {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 0.875rem; border-radius: var(--radius-sm);
        background: var(--surface-1); transition: all 0.2s ease; margin-bottom: 0.375rem;
        border: 1px solid transparent;
    }

    .dark .d-bday { background: rgba(16,185,129,0.03); }
    .d-bday:last-child { margin-bottom: 0; }

    .d-bday:hover { background: var(--surface-0); transform: scale(1.01); box-shadow: var(--shadow-sm); border-color: var(--border-soft); }

    .d-bday.today {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid rgba(245,158,11,0.3);
    }

    .dark .d-bday.today { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.25); }

    .d-bday-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--emerald-500), var(--amber-500));
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 800; font-size: 0.6875rem; flex-shrink: 0;
        font-family: 'Outfit', sans-serif;
    }

    .d-bday-name { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); }
    .d-bday-dept { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.05rem; }

    .d-bday-date {
        margin-left: auto;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem; font-weight: 600; color: var(--amber-600); white-space: nowrap;
    }

    /* ═══════════ EMPTY STATE ═══════════ */
    .d-empty {
        text-align: center; padding: 2rem 1.25rem;
        border-radius: var(--radius-sm);
        background: var(--surface-1); border: 2px dashed var(--border-soft);
    }

    .dark .d-empty { background: rgba(16,185,129,0.03); }
    .d-empty-icon { opacity: 0.18; margin: 0 auto 0.625rem; width: 2.25rem !important; height: 2.25rem !important; }
    .d-empty-title { font-size: 0.875rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.2rem; }
    .d-empty-text  { font-size: 0.75rem; color: var(--text-muted); opacity: 0.75; }

    /* ═══════════ PASSWORD MODAL ═══════════ */
    .d-pw-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.7); backdrop-filter: blur(16px);
        z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem;
    }

    .d-pw-modal {
        background: var(--surface-0);
        border-radius: 24px; padding: 2.5rem 2.25rem;
        max-width: 440px; width: 100%;
        border: 1.5px solid rgba(220,38,38,0.2);
        box-shadow: 0 0 0 1px rgba(220,38,38,0.08), 0 32px 80px rgba(0,0,0,0.4);
        position: relative; overflow: hidden;
    }

    .d-pw-modal::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
        background-size: 200% 100%;
        animation: d-bar-slide 2s linear infinite;
    }

    .d-pw-icon-wrap {
        width: 64px; height: 64px; border-radius: 50%;
        margin: 0 auto 1.25rem;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 2px solid rgba(220,38,38,0.18);
        display: flex; align-items: center; justify-content: center;
        animation: d-pw-pulse 2.5s ease-in-out infinite;
    }

    @keyframes d-pw-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.3); }
        50%       { box-shadow: 0 0 0 10px rgba(220,38,38,0); }
    }

    .d-pw-title {
        font-family: 'Instrument Serif', serif;
        font-size: 1.75rem; font-weight: 400; font-style: italic;
        color: #dc2626; text-align: center; margin-bottom: 0.75rem;
    }

    .d-pw-text {
        text-align: center; font-size: 0.9rem;
        color: var(--text-secondary); line-height: 1.65; margin-bottom: 1.75rem;
    }

    .d-pw-btn {
        width: 100%; padding: 0.9375rem 1.5rem;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: white; border: none; border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9375rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 0.625rem;
        box-shadow: 0 8px 24px rgba(220,38,38,0.3);
        transition: all 0.25s ease; text-decoration: none;
    }

    .d-pw-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(220,38,38,0.4); color: white; }

    /* ═══════════ ANIMATIONS ═══════════ */
    @keyframes d-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .d-anim { animation: d-in 0.45s cubic-bezier(0.22,1,0.36,1) backwards; }
    .d-a1  { animation-delay: 0.04s; }
    .d-a2  { animation-delay: 0.08s; }
    .d-a3  { animation-delay: 0.13s; }
    .d-a4  { animation-delay: 0.18s; }
    .d-a5  { animation-delay: 0.23s; }

    /* ═══════════ RESPONSIVE ═══════════ */
    @media (max-width: 1100px) {
        .d-admin-grid {
            grid-template-columns: 1fr 1fr !important;
            grid-template-areas: "activities pending" "announcements events" !important;
        }
    }

    @media (max-width: 768px) {
        .d-root { padding: 1rem; }
        .d-hero-content { padding: 1.5rem; }
        .d-hero-name { font-size: 1.5rem; }
        .d-widget-2col { grid-template-columns: 1fr; }
        .d-admin-grid {
            grid-template-columns: 1fr !important;
            grid-template-areas: "activities" "pending" "announcements" "events" !important;
        }
        .d-modules-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .d-modules-grid { grid-template-columns: 1fr; }
        .d-hero-name { font-size: 1.25rem; }
    }
</style>

{{-- Password Change Modal --}}
@if($mustChangePassword)
    <div x-data="{ open: true }"
         x-show="open"
         x-trap.noscroll="open"
         class="d-pw-overlay"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="d-pw-modal"
             x-transition:enter="transition ease-out duration-350"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="d-pw-icon-wrap">
                <x-heroicon-o-shield-exclamation class="w-7 h-7 text-red-600" />
            </div>
            <h2 class="d-pw-title">Security Alert</h2>
            <p class="d-pw-text">
                You are using a temporary password. Please update it immediately to secure your account and continue using the system.
            </p>
            <a href="{{ route('filament.hrms.pages.profile') }}" class="d-pw-btn">
                <x-heroicon-o-lock-closed class="w-4 h-4" />
                Update Password Now
            </a>
        </div>
    </div>
@endif

@php
    $iconColorMap = [
        'blue'   => '#2563eb', 'amber'  => '#d97706', 'purple' => '#7c3aed',
        'rose'   => '#e11d48', 'green'  => '#059669', 'gray'   => '#6b7280',
        'teal'   => '#0d9488', 'red'    => '#dc2626',
    ];
@endphp

<div class="d-root">

    {{-- HERO --}}
    <div class="d-hero d-anim">
        <div class="d-hero-canvas"></div>
        <div class="d-hero-noise"></div>
        <div class="d-hero-mesh"></div>
        <div class="d-hero-orbs">
            <div class="d-hero-orb d-hero-orb-1"></div>
            <div class="d-hero-orb d-hero-orb-2"></div>
        </div>
        <div class="d-hero-decor">
            @for($i=0;$i<15;$i++)<div class="d-hero-decor-dot"></div>@endfor
        </div>

        <div class="d-hero-content">
            <div>
                <div class="d-hero-eyebrow">
                    <x-heroicon-o-building-office-2 class="w-3 h-3" />
                    ATI Human Resource Management System
                </div>
                <h1 class="d-hero-name">
                    {{ Str::before($this->getGreeting(), ',') }},<br>
                    <strong>{{ Str::after($this->getGreeting(), ', ') }}</strong>
                </h1>
                <p class="d-hero-sub">Your workspace overview for today.</p>
            </div>
            <div class="d-hero-right">
                <div class="d-hero-pills">
                    <div class="d-pill">
                        <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                        {{ $this->getCurrentDate() }}
                    </div>
                    <div class="d-pill">
                        <x-heroicon-o-clock class="w-3.5 h-3.5" />
                        {{ $this->getCurrentTime() }}
                    </div>
                    <div class="d-pill d-pill-green">
                        @if($user->isAdmin())
                            <x-heroicon-o-shield-check class="w-3.5 h-3.5" />
                            Administrator
                        @else
                            <x-heroicon-o-user class="w-3.5 h-3.5" />
                            {{ $user->getRoleDisplayName() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-hero-bar"></div>
    </div>

    {{-- ── EMPLOYEE VIEW ── --}}
    @if(!$user->isAdmin())
        <div class="d-widget-2col d-anim d-a1">
            <div class="d-card">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-megaphone class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Announcements</span>
                    </div>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="d-section-link">View All <x-heroicon-o-arrow-right class="w-3 h-3" /></a>
                </div>
                <div class="d-section-rule"></div>
                @forelse($announcements as $a)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="d-announce {{ $a['priority'] }}">
                        <div class="d-announce-row">
                            <div class="d-announce-title">{{ $a['title'] }}</div>
                            <div class="d-announce-date">{{ $a['date'] }}</div>
                        </div>
                        <div class="d-announce-msg">{{ Str::limit($a['message'], 100) }}</div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-bell-slash class="d-empty-icon text-gray-400" /><div class="d-empty-title">No Announcements</div><div class="d-empty-text">Check back later.</div></div>
                @endforelse
            </div>

            <div class="d-card">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Upcoming Events</span>
                    </div>
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="d-section-link">View All <x-heroicon-o-arrow-right class="w-3 h-3" /></a>
                </div>
                <div class="d-section-rule"></div>
                @forelse($upcomingEvents as $e)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="d-event">
                        <div class="d-event-cal">
                            <div class="d-event-mon">{{ \Carbon\Carbon::parse($e['date'])->format('M') }}</div>
                            <div class="d-event-day">{{ \Carbon\Carbon::parse($e['date'])->format('d') }}</div>
                        </div>
                        <div class="d-event-body">
                            <div class="d-event-title">{{ $e['title'] }}</div>
                            <div class="d-event-meta">
                                <span class="flex items-center gap-1"><x-heroicon-o-clock class="w-3 h-3" />{{ $e['time'] }}</span>
                                <span class="flex items-center gap-1"><x-heroicon-o-map-pin class="w-3 h-3" />{{ $e['location'] }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-calendar-days class="d-empty-icon text-gray-400" /><div class="d-empty-title">No Upcoming Events</div><div class="d-empty-text">Stay tuned.</div></div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ── ADMIN VIEW ── --}}
    @if($user->isAdmin())
        <div class="d-admin-grid d-anim d-a1">

            {{-- Recent Activities --}}
            <div class="d-card d-area-activities">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-clock class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Recent Activities</span>
                    </div>
                </div>
                <div class="d-section-rule"></div>
                @forelse($recentActivities as $activity)
                    @php $ic = $iconColorMap[$activity['color']] ?? '#6b7280'; @endphp
                    <a href="{{ $activity['url'] }}" class="d-activity">
                        <div class="d-activity-ico {{ $activity['color'] }}">
                            <x-dynamic-component :component="$activity['icon']" style="width:1rem;height:1rem;color:{{ $ic }};flex-shrink:0;" />
                        </div>
                        <div class="d-activity-body">
                            <div class="d-activity-name">{{ $activity['employee'] }}</div>
                            <div class="d-activity-meta">
                                <span class="d-chip {{ $activity['color'] }}">{{ $activity['type'] }}</span>
                                <span class="d-activity-date">{{ $activity['date'] }}</span>
                            </div>
                        </div>
                        <div class="d-activity-arrow"><x-heroicon-o-arrow-right class="w-3 h-3 text-white" /></div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-inbox class="d-empty-icon text-gray-400" /><div class="d-empty-title">No Recent Activities</div></div>
                @endforelse
            </div>

            {{-- Pending Actions --}}
            <div class="d-card d-area-pending">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-bell-alert class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Pending Actions</span>
                    </div>
                </div>
                <div class="d-section-rule"></div>
                @forelse($pendingActions as $action)
                    @php $ic = $iconColorMap[$action['color']] ?? '#6b7280'; @endphp
                    <a href="{{ route($action['route']) }}" class="d-pending">
                        <div class="d-pending-ico {{ $action['color'] }}">
                            <x-dynamic-component :component="$action['icon']" style="width:1.125rem;height:1.125rem;color:{{ $ic }};flex-shrink:0;" />
                        </div>
                        <div class="d-pending-body">
                            <div class="d-pending-title">{{ $action['title'] }}</div>
                            <div class="d-pending-sub">Requires attention</div>
                        </div>
                        <div class="d-pending-count">{{ $action['count'] }}</div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-check-circle class="d-empty-icon text-green-400" /><div class="d-empty-title">All Caught Up!</div></div>
                @endforelse
            </div>

            {{-- Announcements --}}
            <div class="d-card d-area-announcements">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-megaphone class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Announcements</span>
                    </div>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="d-section-link">View All <x-heroicon-o-arrow-right class="w-3 h-3" /></a>
                </div>
                <div class="d-section-rule"></div>
                @forelse($announcements as $a)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="d-announce {{ $a['priority'] }}">
                        <div class="d-announce-row">
                            <div class="d-announce-title">{{ $a['title'] }}</div>
                            <div class="d-announce-date">{{ $a['date'] }}</div>
                        </div>
                        <div class="d-announce-msg">{{ Str::limit($a['message'], 90) }}</div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-bell-slash class="d-empty-icon text-gray-400" /><div class="d-empty-title">No Announcements</div></div>
                @endforelse
            </div>

            {{-- Events --}}
            <div class="d-card d-area-events">
                <div class="d-section-hd">
                    <div class="d-section-hd-left">
                        <div class="d-section-badge"><x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                        <span class="d-section-title">Upcoming Events</span>
                    </div>
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="d-section-link">View All <x-heroicon-o-arrow-right class="w-3 h-3" /></a>
                </div>
                <div class="d-section-rule"></div>
                @forelse($upcomingEvents as $e)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="d-event">
                        <div class="d-event-cal">
                            <div class="d-event-mon">{{ \Carbon\Carbon::parse($e['date'])->format('M') }}</div>
                            <div class="d-event-day">{{ \Carbon\Carbon::parse($e['date'])->format('d') }}</div>
                        </div>
                        <div class="d-event-body">
                            <div class="d-event-title">{{ $e['title'] }}</div>
                            <div class="d-event-meta">
                                <span class="flex items-center gap-1"><x-heroicon-o-clock class="w-3 h-3" />{{ $e['time'] }}</span>
                                <span class="flex items-center gap-1"><x-heroicon-o-map-pin class="w-3 h-3" />{{ $e['location'] }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="d-empty"><x-heroicon-o-calendar-days class="d-empty-icon text-gray-400" /><div class="d-empty-title">No Upcoming Events</div></div>
                @endforelse
            </div>

        </div>
    @endif

    {{-- ── MODULES ── --}}
    <div class="d-anim d-a3" style="margin-bottom:1.25rem;">
        <div class="d-section-hd" style="margin-bottom:1rem;">
            <div class="d-section-hd-left">
                <div class="d-section-badge"><x-heroicon-o-squares-2x2 class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                <span class="d-section-title">Quick Access Modules</span>
            </div>
        </div>

        <div class="d-modules-grid">
            @foreach($modules as $index => $module)
                <a href="{{ route($module['route']) }}"
                   class="d-module d-anim"
                   style="animation-delay:{{ 0.05 * ($index + 1) }}s;">
                    <div class="d-module-accent"></div>
                    <div class="d-module-top">
                        <div class="d-module-icon {{ $module['icon_bg'] }}">
                            <x-dynamic-component :component="$module['icon']" class="w-5 h-5 {{ $module['icon_color'] }}" />
                        </div>
                        <div class="d-module-num">{{ $module['stat'] }}</div>
                    </div>
                    <div class="d-module-title">{{ $module['title'] }}</div>
                    <div class="d-module-desc">{{ $user->isAdmin() ? $module['admin_text'] : $module['employee_text'] }}</div>
                    <div class="d-module-foot">
                        <span class="d-module-label">Total Records</span>
                        <div class="d-module-btn"><x-heroicon-o-arrow-right class="w-3 h-3 text-white" /></div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── BIRTHDAYS ── --}}
    <div class="d-card d-anim d-a4">
        <div class="d-section-hd">
            <div class="d-section-hd-left">
                <div class="d-section-badge"><x-heroicon-o-cake class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" /></div>
                <span class="d-section-title">Birthday Celebrants This Month</span>
            </div>
        </div>
        <div class="d-section-rule"></div>
        @forelse($birthdayCelebrants as $c)
            <div class="d-bday {{ $c['is_today'] ? 'today' : '' }}">
                <div class="d-bday-avatar">{{ strtoupper(substr($c['name'], 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="d-bday-name">{{ $c['name'] }}@if($c['is_today']) <span>🎉</span>@endif</div>
                    <div class="d-bday-dept">{{ $c['department'] }}</div>
                </div>
                <div class="d-bday-date">{{ $c['date'] }}</div>
            </div>
        @empty
            <div class="d-empty">
                <x-heroicon-o-face-smile class="d-empty-icon text-gray-400" />
                <div class="d-empty-title">No Birthdays This Month</div>
                <div class="d-empty-text">We'll celebrate next month!</div>
            </div>
        @endforelse
    </div>

</div>

</x-filament::page>
