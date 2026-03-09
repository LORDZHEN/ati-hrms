<x-filament::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=Playfair+Display:wght@700;800;900&display=swap');

    :root {
        --g: #059669;
        --g2: #10b981;
        --g3: #d1fae5;
        --a: #d97706;
        --a2: #f59e0b;
        --a3: #fef3c7;
        --ink: #0f1f16;
        --ink2: #374151;
        --ink3: #6b7280;
        --paper: #f9faf7;
        --card: #ffffff;
        --border: #e5e7eb;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
        --shadow-lg: 0 12px 40px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
        --radius: 16px;
        --radius-sm: 10px;
    }

    .dark {
        --ink: #f0fdf4;
        --ink2: #d1fae5;
        --ink3: #6ee7b7;
        --paper: #0a1612;
        --card: #0f1f18;
        --border: #1f3429;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.4);
        --shadow-lg: 0 12px 40px rgba(0,0,0,0.5);
    }

    .hd-root {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        background: var(--paper);
        min-height: 100vh;
        padding: 1.25rem;
        color: var(--ink);
    }

    /* ═══════════════════════════════════════
       HERO BANNER
    ═══════════════════════════════════════ */
    .hd-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.25rem;
        background: var(--ink);
        min-height: 160px;
    }

    .hd-hero-bg {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 80% at 75% 50%, rgba(5,150,105,0.55) 0%, transparent 65%),
            radial-gradient(ellipse 40% 60% at 90% 20%, rgba(217,119,6,0.35) 0%, transparent 60%),
            radial-gradient(ellipse 50% 70% at 10% 80%, rgba(16,185,129,0.2) 0%, transparent 60%),
            linear-gradient(135deg, #071a10 0%, #0f2d1c 40%, #0a1e12 100%);
    }

    .hd-hero-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.06) 1px, transparent 1px);
        background-size: 32px 32px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .hd-hero-dots {
        position: absolute;
        top: 1.5rem; right: 1.5rem;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        opacity: 0.25;
    }

    .hd-hero-dot { width: 4px; height: 4px; border-radius: 50%; background: #10b981; }

    .hd-hero-content {
        position: relative; z-index: 2;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .hd-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.3);
        color: #6ee7b7;
        font-size: 0.6875rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        margin-bottom: 0.625rem;
    }

    .hd-hero-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.875rem; font-weight: 800;
        color: #ffffff; line-height: 1.15;
        margin-bottom: 0.375rem; letter-spacing: -0.02em;
    }

    .hd-hero-sub { font-size: 0.875rem; color: rgba(255,255,255,0.55); font-weight: 400; }

    .hd-hero-right {
        display: flex; flex-direction: column;
        align-items: flex-end; gap: 0.75rem;
    }

    .hd-hero-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end; }

    .hd-pill {
        display: flex; align-items: center; gap: 0.4rem;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.8);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; font-weight: 600;
        padding: 0.4rem 0.875rem; border-radius: 999px;
        backdrop-filter: blur(8px); white-space: nowrap;
    }

    .hd-hero-stripe {
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--g), var(--a2), var(--g));
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }

    /* ═══════════════════════════════════════
       SECTION LABEL
    ═══════════════════════════════════════ */
    .hd-section-label { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.75rem; }

    .hd-section-icon {
        width: 28px; height: 28px; border-radius: 7px;
        background: linear-gradient(135deg, var(--g3), #bbf7d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .dark .hd-section-icon {
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.15));
    }

    .hd-section-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9375rem; font-weight: 700;
        color: var(--g); letter-spacing: -0.01em;
    }

    .hd-section-link {
        margin-left: auto;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; font-weight: 700;
        color: var(--g); text-decoration: none;
        display: flex; align-items: center; gap: 0.25rem;
        padding: 0.25rem 0.625rem; border-radius: 999px;
        border: 1.5px solid var(--g);
        transition: all 0.2s ease; white-space: nowrap;
    }

    .hd-section-link:hover { background: var(--g); color: white; }

    .hd-section-divider {
        height: 1px;
        background: linear-gradient(90deg, var(--border), transparent);
        margin-bottom: 1rem;
    }

    /* ═══════════════════════════════════════
       BASE CARD
    ═══════════════════════════════════════ */
    .hd-card {
        background: var(--card);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .hd-card:hover { box-shadow: var(--shadow-md); }

    /* ═══════════════════════════════════════
       GRIDS
    ═══════════════════════════════════════ */
    .hd-widget-grid {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 1rem; margin-bottom: 1.25rem; align-items: start;
    }

    .hd-admin-outer {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: auto auto;
        grid-template-areas:
            "activities pending announcements"
            "activities pending events";
        gap: 1rem; margin-bottom: 1.25rem;
    }

    .hd-admin-activities    { grid-area: activities; }
    .hd-admin-pending       { grid-area: pending; }
    .hd-admin-announcements { grid-area: announcements; }
    .hd-admin-events        { grid-area: events; }

    /* ═══════════════════════════════════════
       MODULE CARDS
    ═══════════════════════════════════════ */
    .hd-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.875rem; margin-bottom: 1.25rem;
    }

    .hd-module {
        background: var(--card);
        border-radius: var(--radius);
        border: 1.5px solid var(--border);
        padding: 1.125rem;
        text-decoration: none; display: block;
        position: relative; overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        box-shadow: var(--shadow-sm);
    }

    .hd-module::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--g), var(--a2));
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s ease;
    }

    .hd-module:hover::before { transform: scaleX(1); }

    .hd-module:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(5,150,105,0.3);
    }

    .hd-module-top {
        display: flex; align-items: flex-start;
        justify-content: space-between; margin-bottom: 0.875rem;
    }

    .hd-module-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.3s ease; flex-shrink: 0;
    }

    .hd-module:hover .hd-module-icon { transform: scale(1.1) rotate(-6deg); }

    .hd-module-stat-badge {
        font-size: 1.375rem; font-weight: 900;
        color: var(--ink); font-family: 'DM Sans', sans-serif; line-height: 1;
    }

    .hd-module-name {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem; font-weight: 700;
        color: var(--ink); margin-bottom: 0.25rem; letter-spacing: -0.01em;
    }

    .hd-module-desc {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; color: var(--ink3); line-height: 1.4;
    }

    .hd-module-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 0.875rem; padding-top: 0.75rem; border-top: 1px solid var(--border);
    }

    .hd-module-label {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.625rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em; color: var(--ink3);
    }

    .hd-module-arrow {
        width: 26px; height: 26px; border-radius: 7px;
        background: var(--g);
        display: flex; align-items: center; justify-content: center;
        transition: all 0.25s ease;
    }

    .hd-module:hover .hd-module-arrow { background: var(--a); transform: translateX(2px); }

    /* ═══════════════════════════════════════
       ACTIVITY LIST
    ═══════════════════════════════════════ */
    .hd-activity {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.625rem 0.75rem; border-radius: var(--radius-sm);
        text-decoration: none; transition: all 0.2s ease;
        border: 1.5px solid transparent; margin-bottom: 0.375rem;
    }

    .hd-activity:last-child { margin-bottom: 0; }

    .hd-activity:hover {
        background: var(--paper);
        border-color: rgba(5,150,105,0.2);
        transform: translateX(3px);
    }

    .dark .hd-activity:hover { background: rgba(16,185,129,0.05); }

    .hd-activity-ico {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .hd-activity-ico.blue   { background: #dbeafe; }
    .hd-activity-ico.amber  { background: #fef3c7; }
    .hd-activity-ico.purple { background: #ede9fe; }
    .hd-activity-ico.rose   { background: #ffe4e6; }
    .hd-activity-ico.green  { background: #d1fae5; }
    .hd-activity-ico.gray   { background: #f3f4f6; }
    .hd-activity-ico.teal   { background: #ccfbf1; }
    .hd-activity-ico.red    { background: #fee2e2; }

    .hd-activity-body { flex: 1; min-width: 0; }

    .hd-activity-name {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; font-weight: 700; color: var(--ink);
        margin-bottom: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .hd-activity-meta { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

    .hd-chip {
        display: inline-flex; align-items: center;
        padding: 0.15rem 0.5rem; border-radius: 999px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.625rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.06em;
    }

    .hd-chip.blue   { background: #dbeafe; color: #1d4ed8; }
    .hd-chip.amber  { background: #fef3c7; color: #92400e; }
    .hd-chip.purple { background: #ede9fe; color: #6d28d9; }
    .hd-chip.rose   { background: #ffe4e6; color: #9f1239; }
    .hd-chip.green  { background: #d1fae5; color: #065f46; }
    .hd-chip.gray   { background: #f3f4f6; color: #374151; }
    .hd-chip.teal   { background: #ccfbf1; color: #115e59; }
    .hd-chip.red    { background: #fee2e2; color: #991b1b; }

    .hd-activity-date { font-family: 'DM Sans', sans-serif; font-size: 0.6875rem; color: var(--ink3); }

    .hd-activity-caret {
        width: 22px; height: 22px; border-radius: 5px;
        background: var(--border);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: translateX(-4px);
        transition: all 0.2s ease; flex-shrink: 0;
    }

    .hd-activity:hover .hd-activity-caret {
        opacity: 1; transform: translateX(0); background: var(--g);
    }

    /* ═══════════════════════════════════════
       ANNOUNCEMENTS
    ═══════════════════════════════════════ */
    .hd-announce {
        display: block; text-decoration: none;
        padding: 0.5rem 0.75rem; border-radius: 8px;
        border-left: 3px solid var(--border);
        background: var(--paper); margin-bottom: 0.375rem;
        transition: all 0.2s ease;
    }

    .dark .hd-announce { background: rgba(16,185,129,0.04); }
    .hd-announce:last-child { margin-bottom: 0; }
    .hd-announce.high   { border-left-color: #dc2626; background: #fef2f2; }
    .hd-announce.medium { border-left-color: #d97706; background: #fffbeb; }
    .hd-announce.low    { border-left-color: #059669; }
    .dark .hd-announce.high   { background: rgba(220,38,38,0.08); }
    .dark .hd-announce.medium { background: rgba(217,119,6,0.08); }

    .hd-announce:hover { transform: translateX(3px); box-shadow: var(--shadow-sm); border-left-width: 4px; }

    .hd-announce-top {
        display: flex; align-items: center; justify-content: space-between;
        gap: 0.5rem; margin-bottom: 0.175rem;
    }

    .hd-announce-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; font-weight: 700; color: var(--ink);
        flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .hd-announce-date {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.625rem; color: var(--ink3); white-space: nowrap; flex-shrink: 0;
    }

    .hd-announce-msg {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; color: var(--ink2); line-height: 1.4;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ═══════════════════════════════════════
       EVENTS
    ═══════════════════════════════════════ */
    .hd-event {
        display: flex; gap: 0.75rem;
        padding: 0.5rem 0.75rem; border-radius: 8px;
        text-decoration: none; transition: all 0.25s ease;
        background: var(--paper); border: 1.5px solid transparent;
        margin-bottom: 0.375rem;
    }

    .dark .hd-event { background: rgba(16,185,129,0.04); }
    .hd-event:last-child { margin-bottom: 0; }

    .hd-event:hover {
        background: var(--card); border-color: rgba(5,150,105,0.25);
        box-shadow: var(--shadow-sm); transform: translateY(-1px);
    }

    .hd-event-cal {
        flex-shrink: 0; width: 38px; border-radius: 7px;
        background: linear-gradient(160deg, var(--g), #047857);
        text-align: center; color: white;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 0.25rem 0; transition: background 0.25s ease;
    }

    .hd-event:hover .hd-event-cal { background: linear-gradient(160deg, var(--a2), var(--a)); }

    .hd-event-mon {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.5rem; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase; opacity: 0.85;
    }

    .hd-event-day { font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 800; line-height: 1.1; }
    .hd-event-body { flex: 1; min-width: 0; }

    .hd-event-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8125rem; font-weight: 700; color: var(--ink);
        margin-bottom: 0.175rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .hd-event-meta {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.6875rem; color: var(--ink3);
        display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
    }

    /* ═══════════════════════════════════════
       PENDING ACTIONS
    ═══════════════════════════════════════ */
    .hd-pending {
        display: flex; align-items: center; gap: 0.875rem;
        padding: 0.75rem 0.875rem; border-radius: var(--radius-sm);
        text-decoration: none; background: var(--paper);
        transition: all 0.2s ease; margin-bottom: 0.375rem;
        border: 1.5px solid transparent;
    }

    .dark .hd-pending { background: rgba(16,185,129,0.04); }
    .hd-pending:last-child { margin-bottom: 0; }

    .hd-pending:hover {
        background: var(--card); border-color: rgba(5,150,105,0.2);
        transform: translateX(3px); box-shadow: var(--shadow-sm);
    }

    .hd-pending-ico {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .hd-pending-ico.blue   { background: #dbeafe; }
    .hd-pending-ico.amber  { background: #fef3c7; }
    .hd-pending-ico.purple { background: #ede9fe; }
    .hd-pending-ico.green  { background: #d1fae5; }
    .hd-pending-ico.rose   { background: #ffe4e6; }
    .hd-pending-ico.gray   { background: #f3f4f6; }
    .hd-pending-ico.teal   { background: #ccfbf1; }
    .hd-pending-ico.red    { background: #fee2e2; }

    .hd-pending-body { flex: 1; }

    .hd-pending-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem; font-weight: 700; color: var(--ink); margin-bottom: 0.125rem;
    }

    .hd-pending-sub { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: var(--ink3); }

    .hd-pending-num {
        width: 38px; height: 38px; border-radius: 10px;
        background: linear-gradient(135deg, var(--g2), var(--g));
        display: flex; align-items: center; justify-content: center;
        color: white; font-family: 'DM Sans', sans-serif;
        font-weight: 900; font-size: 1rem; flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(5,150,105,0.35);
    }

    /* ═══════════════════════════════════════
       BIRTHDAY
    ═══════════════════════════════════════ */
    .hd-bday {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.625rem 0.75rem; border-radius: var(--radius-sm);
        background: var(--paper); transition: all 0.2s ease; margin-bottom: 0.375rem;
    }

    .dark .hd-bday { background: rgba(16,185,129,0.04); }
    .hd-bday:last-child { margin-bottom: 0; }

    .hd-bday:hover { background: var(--card); transform: scale(1.01); box-shadow: var(--shadow-sm); }

    .hd-bday.today {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1.5px solid rgba(217,119,6,0.3);
    }

    .dark .hd-bday.today {
        background: linear-gradient(135deg, rgba(217,119,6,0.12), rgba(245,158,11,0.08));
        border-color: rgba(245,158,11,0.3);
    }

    .hd-bday-ava {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, var(--g2), var(--a2));
        display: flex; align-items: center; justify-content: center;
        color: white; font-family: 'DM Sans', sans-serif;
        font-weight: 800; font-size: 0.75rem; flex-shrink: 0;
    }

    .hd-bday-name { font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: var(--ink); }
    .hd-bday-dept { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: var(--ink3); }

    .hd-bday-date {
        margin-left: auto;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.75rem; font-weight: 700; color: var(--a); white-space: nowrap;
    }

    /* ═══════════════════════════════════════
       EMPTY STATE
    ═══════════════════════════════════════ */
    .hd-empty {
        text-align: center; padding: 1.5rem 1rem;
        border-radius: var(--radius-sm);
        background: var(--paper); border: 2px dashed var(--border);
    }

    .dark .hd-empty { background: rgba(16,185,129,0.03); }
    .hd-empty-icon { opacity: 0.2; margin: 0 auto 0.625rem; width: 2rem !important; height: 2rem !important; }
    .hd-empty-title { font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: var(--ink3); margin-bottom: 0.2rem; }
    .hd-empty-text  { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: var(--ink3); opacity: 0.75; }

    /* ═══════════════════════════════════════
       PASSWORD MODAL
    ═══════════════════════════════════════ */
    .hd-pw-modal {
        background: var(--card);
        border-radius: 24px;
        padding: 2.5rem 2.25rem;
        max-width: 460px; width: 100%;
        border: 2px solid rgba(220,38,38,0.2);
        box-shadow: 0 0 0 1px rgba(220,38,38,0.1), 0 24px 80px rgba(0,0,0,0.3);
        position: relative; overflow: hidden;
    }

    .hd-pw-modal::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
        background-size: 200% 100%;
        animation: shimmer 2s linear infinite;
    }

    .hd-pw-icon {
        width: 68px; height: 68px;
        margin: 0 auto 1.25rem; border-radius: 50%;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 2px solid rgba(220,38,38,0.2);
        display: flex; align-items: center; justify-content: center;
        animation: hd-pulse 2.5s ease-in-out infinite;
    }

    @keyframes hd-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.4); }
        50%       { box-shadow: 0 0 0 10px rgba(220,38,38,0); }
    }

    .hd-pw-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem; font-weight: 900; color: #dc2626;
        text-align: center; margin-bottom: 0.75rem;
    }

    .hd-pw-text {
        font-family: 'DM Sans', sans-serif;
        text-align: center; font-size: 0.9375rem;
        color: var(--ink3); line-height: 1.65; margin-bottom: 1.75rem;
    }

    .hd-pw-btn {
        width: 100%; padding: 0.9375rem 1.5rem;
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: white; border: none; border-radius: 12px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9375rem; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 0.625rem;
        box-shadow: 0 8px 24px rgba(220,38,38,0.35);
        transition: all 0.25s ease;
    }

    .hd-pw-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(220,38,38,0.4); }

    /* ═══════════════════════════════════════
       ANIMATIONS
    ═══════════════════════════════════════ */
    @keyframes hd-fadein {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .hd-in  { animation: hd-fadein 0.4s ease-out backwards; }
    .hd-d1  { animation-delay: 0.05s; }
    .hd-d2  { animation-delay: 0.10s; }
    .hd-d3  { animation-delay: 0.15s; }
    .hd-d4  { animation-delay: 0.20s; }
    .hd-d5  { animation-delay: 0.25s; }
    .hd-d6  { animation-delay: 0.30s; }

    /* ═══════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════ */
    @media (max-width: 1024px) {
        .hd-admin-outer {
            grid-template-columns: 1fr 1fr !important;
            grid-template-areas: "activities pending" "announcements events" !important;
        }
    }

    @media (max-width: 768px) {
        .hd-root { padding: 0.875rem; }
        .hd-hero-content { padding: 1.25rem 1.5rem; }
        .hd-hero-name { font-size: 1.5rem; }
        .hd-hero-right { align-items: flex-start; }
        .hd-widget-grid { grid-template-columns: 1fr !important; }
        .hd-admin-outer {
            grid-template-columns: 1fr !important;
            grid-template-areas: "activities" "pending" "announcements" "events" !important;
        }
        .hd-modules-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .hd-modules-grid { grid-template-columns: 1fr; }
        .hd-hero-name { font-size: 1.25rem; }
    }
</style>

{{-- ════════════════════════════════
     PASSWORD CHANGE MODAL
     FIX: The middleware now handles the redirect automatically. This modal
     is kept as a visible UX reinforcement for users who land on the dashboard
     before the middleware fires (e.g. direct URL hits), but the button now
     does a clean redirect to the profile page.
════════════════════════════════ --}}
@if($mustChangePassword)
    <div x-data="{ open: true }"
         x-show="open"
         x-trap.noscroll="open"
         class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="hd-pw-modal"
             x-transition:enter="transition ease-out duration-350"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="hd-pw-icon">
                <x-heroicon-o-shield-exclamation class="w-8 h-8 text-red-600" />
            </div>
            <h2 class="hd-pw-title">Security Alert</h2>
            <p class="hd-pw-text">
                You are using a temporary password. Please update your password immediately
                to secure your account and continue using the system.
            </p>
            {{-- FIX: use a standard anchor so the full-page navigation triggers
                 the RequirePasswordChange middleware correctly --}}
            <a href="{{ route('filament.hrms.pages.profile') }}" class="hd-pw-btn">
                <x-heroicon-o-lock-closed class="w-4 h-4" />
                Update Password Now
            </a>
        </div>
    </div>
@endif

@php
    $iconColorMap = [
        'blue'   => '#2563eb',
        'amber'  => '#d97706',
        'purple' => '#7c3aed',
        'rose'   => '#e11d48',
        'green'  => '#059669',
        'gray'   => '#6b7280',
        'teal'   => '#0d9488',
        'red'    => '#dc2626',
    ];
@endphp

<div class="hd-root">

    {{-- ── HERO ── --}}
    <div class="hd-hero hd-in">
        <div class="hd-hero-bg"></div>
        <div class="hd-hero-grid"></div>

        <div class="hd-hero-dots">
            @for($i = 0; $i < 25; $i++)
                <div class="hd-hero-dot"></div>
            @endfor
        </div>

        <div class="hd-hero-content">
            <div class="hd-hero-left">
                <div class="hd-hero-eyebrow">
                    <x-heroicon-o-building-office-2 class="w-3 h-3" />
                    ATI Human Resource Management System
                </div>
                <h1 class="hd-hero-name">{{ $this->getGreeting() }}</h1>
                <p class="hd-hero-sub">Here's your workspace overview for today.</p>
            </div>
            <div class="hd-hero-right">
                <div class="hd-hero-pills">
                    <div class="hd-pill">
                        <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                        {{ $this->getCurrentDate() }}
                    </div>
                    <div class="hd-pill">
                        <x-heroicon-o-clock class="w-3.5 h-3.5" />
                        {{ $this->getCurrentTime() }}
                    </div>
                    <div class="hd-pill" style="border-color:rgba(16,185,129,0.4);background:rgba(16,185,129,0.12);color:#6ee7b7;">
                        @if($user->isAdmin())
                            <x-heroicon-o-shield-check class="w-3.5 h-3.5" />
                            Administrator
                        @else
                            <x-heroicon-o-user class="w-3.5 h-3.5" />
                            {{-- FIX: show role display name instead of hardcoded 'Employee' --}}
                            {{ $user->getRoleDisplayName() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="hd-hero-stripe"></div>
    </div>

    {{-- ════════════════════════════════
         EMPLOYEE VIEW
    ════════════════════════════════ --}}
    @if(! $user->isAdmin())
        <div class="hd-widget-grid hd-in hd-d1">

            {{-- Announcements --}}
            <div class="hd-card">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-megaphone class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Announcements</span>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="hd-section-link">
                        View All <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($announcements as $announcement)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}"
                       class="hd-announce {{ $announcement['priority'] }}">
                        <div class="hd-announce-top">
                            <div class="hd-announce-title">{{ $announcement['title'] }}</div>
                            <div class="hd-announce-date">{{ $announcement['date'] }}</div>
                        </div>
                        <div class="hd-announce-msg">{{ Str::limit($announcement['message'], 100) }}</div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-bell-slash class="hd-empty-icon text-gray-400" />
                        <div class="hd-empty-title">No Announcements</div>
                        <div class="hd-empty-text">Check back later for updates.</div>
                    </div>
                @endforelse
            </div>

            {{-- Upcoming Events --}}
            <div class="hd-card">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Upcoming Events</span>
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="hd-section-link">
                        View All <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($upcomingEvents as $event)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="hd-event">
                        <div class="hd-event-cal">
                            <div class="hd-event-mon">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</div>
                            <div class="hd-event-day">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                        </div>
                        <div class="hd-event-body">
                            <div class="hd-event-title">{{ $event['title'] }}</div>
                            <div class="hd-event-meta">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3 h-3" /> {{ $event['time'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-map-pin class="w-3 h-3" /> {{ $event['location'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-calendar-days class="hd-empty-icon text-gray-400" />
                        <div class="hd-empty-title">No Upcoming Events</div>
                        <div class="hd-empty-text">Stay tuned for future events.</div>
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    {{-- ════════════════════════════════
         ADMIN VIEW
    ════════════════════════════════ --}}
    @if($user->isAdmin())
        <div class="hd-admin-outer hd-in hd-d1">

            {{-- Col 1: Recent Activities --}}
            <div class="hd-card hd-admin-activities">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-clock class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Recent Activities</span>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($recentActivities as $activity)
                    @php $iconColor = $iconColorMap[$activity['color']] ?? '#6b7280'; @endphp
                    <a href="{{ $activity['url'] }}" class="hd-activity">
                        <div class="hd-activity-ico {{ $activity['color'] }}">
                            <x-dynamic-component
                                :component="$activity['icon']"
                                style="width:1rem;height:1rem;color:{{ $iconColor }};flex-shrink:0;" />
                        </div>
                        <div class="hd-activity-body">
                            <div class="hd-activity-name">{{ $activity['employee'] }}</div>
                            <div class="hd-activity-meta">
                                <span class="hd-chip {{ $activity['color'] }}">{{ $activity['type'] }}</span>
                                <span class="hd-activity-date">{{ $activity['date'] }}</span>
                            </div>
                        </div>
                        <div class="hd-activity-caret">
                            <x-heroicon-o-arrow-right class="w-3 h-3 text-white" />
                        </div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-inbox class="hd-empty-icon text-gray-400" />
                        <div class="hd-empty-title">No Recent Activities</div>
                        <div class="hd-empty-text">Activities will appear here.</div>
                    </div>
                @endforelse
            </div>

            {{-- Col 2: Pending Actions --}}
            <div class="hd-card hd-admin-pending">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-bell-alert class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Pending Actions</span>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($pendingActions as $action)
                    @php $iconColor = $iconColorMap[$action['color']] ?? '#6b7280'; @endphp
                    <a href="{{ route($action['route']) }}" class="hd-pending">
                        <div class="hd-pending-ico {{ $action['color'] }}">
                            <x-dynamic-component
                                :component="$action['icon']"
                                style="width:1.25rem;height:1.25rem;color:{{ $iconColor }};flex-shrink:0;" />
                        </div>
                        <div class="hd-pending-body">
                            <div class="hd-pending-title">{{ $action['title'] }}</div>
                            <div class="hd-pending-sub">Requires attention</div>
                        </div>
                        <div class="hd-pending-num">{{ $action['count'] }}</div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-check-circle class="hd-empty-icon text-green-400" />
                        <div class="hd-empty-title">All Caught Up!</div>
                        <div class="hd-empty-text">No pending actions right now.</div>
                    </div>
                @endforelse
            </div>

            {{-- Col 3 Row 1: Announcements --}}
            <div class="hd-card hd-admin-announcements">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-megaphone class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Announcements</span>
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}" class="hd-section-link">
                        View All <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($announcements as $announcement)
                    <a href="{{ route('filament.hrms.resources.announcements.index') }}"
                       class="hd-announce {{ $announcement['priority'] }}">
                        <div class="hd-announce-top">
                            <div class="hd-announce-title">{{ $announcement['title'] }}</div>
                            <div class="hd-announce-date">{{ $announcement['date'] }}</div>
                        </div>
                        <div class="hd-announce-msg">{{ Str::limit($announcement['message'], 90) }}</div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-bell-slash class="hd-empty-icon text-gray-400" />
                        <div class="hd-empty-title">No Announcements</div>
                        <div class="hd-empty-text">Nothing new at the moment.</div>
                    </div>
                @endforelse
            </div>

            {{-- Col 3 Row 2: Upcoming Events --}}
            <div class="hd-card hd-admin-events">
                <div class="hd-section-label">
                    <div class="hd-section-icon">
                        <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                    </div>
                    <span class="hd-section-title">Upcoming Events</span>
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="hd-section-link">
                        View All <x-heroicon-o-arrow-right class="w-3 h-3" />
                    </a>
                </div>
                <div class="hd-section-divider"></div>
                @forelse($upcomingEvents as $event)
                    <a href="{{ route('filament.hrms.resources.events.index') }}" class="hd-event">
                        <div class="hd-event-cal">
                            <div class="hd-event-mon">{{ \Carbon\Carbon::parse($event['date'])->format('M') }}</div>
                            <div class="hd-event-day">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                        </div>
                        <div class="hd-event-body">
                            <div class="hd-event-title">{{ $event['title'] }}</div>
                            <div class="hd-event-meta">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3 h-3" /> {{ $event['time'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-map-pin class="w-3 h-3" /> {{ $event['location'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="hd-empty">
                        <x-heroicon-o-calendar-days class="hd-empty-icon text-gray-400" />
                        <div class="hd-empty-title">No Upcoming Events</div>
                        <div class="hd-empty-text">Stay tuned for future events.</div>
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    {{-- ════════════════════════════════
         QUICK ACCESS MODULES
    ════════════════════════════════ --}}
    <div class="hd-in hd-d3" style="margin-bottom:1.25rem;">
        <div class="hd-section-label" style="margin-bottom:1rem;">
            <div class="hd-section-icon">
                <x-heroicon-o-squares-2x2 class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="hd-section-title">Quick Access Modules</span>
        </div>

        <div class="hd-modules-grid">
            @foreach($modules as $index => $module)
                <a href="{{ route($module['route']) }}"
                   class="hd-module hd-in"
                   style="animation-delay:{{ 0.05 * ($index + 1) }}s;">
                    <div class="hd-module-top">
                        <div class="hd-module-icon {{ $module['icon_bg'] }}">
                            <x-dynamic-component :component="$module['icon']"
                                class="w-5 h-5 {{ $module['icon_color'] }}" />
                        </div>
                        <div class="hd-module-stat-badge">{{ $module['stat'] }}</div>
                    </div>
                    <div class="hd-module-name">{{ $module['title'] }}</div>
                    <div class="hd-module-desc">
                        {{ $user->isAdmin() ? $module['admin_text'] : $module['employee_text'] }}
                    </div>
                    <div class="hd-module-footer">
                        <span class="hd-module-label">Total Records</span>
                        <div class="hd-module-arrow">
                            <x-heroicon-o-arrow-right class="w-3 h-3 text-white" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ════════════════════════════════
         BIRTHDAY CELEBRANTS
    ════════════════════════════════ --}}
    <div class="hd-card hd-in hd-d4">
        <div class="hd-section-label">
            <div class="hd-section-icon">
                <x-heroicon-o-cake class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="hd-section-title">Birthday Celebrants This Month</span>
        </div>
        <div class="hd-section-divider"></div>
        @forelse($birthdayCelebrants as $celebrant)
            <div class="hd-bday {{ $celebrant['is_today'] ? 'today' : '' }}">
                <div class="hd-bday-ava">
                    {{ strtoupper(substr($celebrant['name'], 0, 2)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="hd-bday-name">
                        {{ $celebrant['name'] }}
                        @if($celebrant['is_today'])
                            <span class="ml-1">🎉</span>
                        @endif
                    </div>
                    <div class="hd-bday-dept">{{ $celebrant['department'] }}</div>
                </div>
                <div class="hd-bday-date">{{ $celebrant['date'] }}</div>
            </div>
        @empty
            <div class="hd-empty">
                <x-heroicon-o-face-smile class="hd-empty-icon text-gray-400" />
                <div class="hd-empty-title">No Birthdays This Month</div>
                <div class="hd-empty-text">We'll celebrate next month!</div>
            </div>
        @endforelse
    </div>

</div>

</x-filament::page>
