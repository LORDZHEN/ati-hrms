{{--
    resources/views/filament/pages/biometric-mapping-manager.blade.php

    ── DESIGN SYSTEM ────────────────────────────────────────────────────────────
    Aligned with the application's custom design system:
    • Fonts: Outfit (body), Instrument Serif (display), JetBrains Mono (IDs/meta)
    • Emerald-first palette with amber accents — matches HrmsDashboard + Profile
    • CSS custom properties: --surface-0/1/2, --border-soft, --text-*, --shadow-*
    • Dark mode via .dark class prefix (identical to reference files)
    • Hero banner matches ss-hero / pf-hero / d-hero pattern from system pages

    ── ALPINE STATE ─────────────────────────────────────────────────────────────
    rows         — Livewire $rows mirrored to Alpine for immediate UI reactivity
    filter       — 'all' | 'mapped' | 'unmapped'
    search       — text filter applied on top of tab filter
    drawerOpen   — controls the read-only history slide-over
    filtered     — computed: rows after both filters
--}}

<x-filament-panels::page>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap');

    :root {
        --emerald-100:#d1fae5;--emerald-400:#34d399;--emerald-500:#10b981;
        --emerald-600:#059669;--emerald-700:#047857;
        --amber-400:#fbbf24;--amber-500:#f59e0b;--amber-600:#d97706;
        --surface-0:#ffffff;--surface-1:#f8faf9;--surface-2:#f0f4f2;
        --border-soft:#e2e8e5;--border-mid:#c8d5d0;
        --text-primary:#0d1f18;--text-secondary:#3d5a50;--text-muted:#7a9690;
        --shadow-xs:0 1px 2px rgba(5,150,105,0.04);
        --shadow-sm:0 2px 8px rgba(5,150,105,0.06),0 1px 3px rgba(0,0,0,0.04);
        --shadow-md:0 8px 24px rgba(5,150,105,0.10),0 2px 8px rgba(0,0,0,0.06);
        --shadow-lg:0 20px 48px rgba(5,150,105,0.14),0 4px 16px rgba(0,0,0,0.08);
        --radius-sm:10px;--radius-md:14px;--radius-lg:20px;--radius-xl:26px;
    }

    .dark{
        --surface-0:#0b1a14;--surface-1:#0f2119;--surface-2:#152b21;
        --border-soft:#1e3a2c;--border-mid:#27503c;
        --text-primary:#e8f5ef;--text-secondary:#9dcfba;--text-muted:#4d8a72;
        --shadow-sm:0 2px 8px rgba(0,0,0,0.25);
        --shadow-md:0 8px 24px rgba(0,0,0,0.35);
        --shadow-lg:0 20px 48px rgba(0,0,0,0.5);
    }

    .bm-root*{box-sizing:border-box;}
    .bm-root{font-family:'Outfit',sans-serif;color:var(--text-primary);}

    /* HERO */
    .bm-hero{position:relative;border-radius:var(--radius-xl);overflow:hidden;margin-bottom:1.5rem;min-height:148px;background:#071812;}
    .bm-hero-canvas{position:absolute;inset:0;background:radial-gradient(ellipse 65% 90% at 80% 55%,rgba(5,150,105,.55) 0%,transparent 62%),radial-gradient(ellipse 40% 60% at 95% 10%,rgba(245,158,11,.28) 0%,transparent 55%),radial-gradient(ellipse 50% 75% at 5% 90%,rgba(16,185,129,.18) 0%,transparent 55%),linear-gradient(145deg,#040e08 0%,#0a2016 45%,#071410 100%);}
    .bm-hero-noise{position:absolute;inset:0;opacity:.025;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");background-size:128px 128px;}
    .bm-hero-mesh{position:absolute;inset:0;background-image:linear-gradient(rgba(16,185,129,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(16,185,129,.05) 1px,transparent 1px);background-size:40px 40px;mask-image:radial-gradient(ellipse at center,black 40%,transparent 80%);}
    .bm-hero-decor{position:absolute;top:1.25rem;right:1.75rem;display:flex;gap:6px;flex-wrap:wrap;width:60px;opacity:.22;}
    .bm-hero-dot{width:4px;height:4px;border-radius:50%;background:#34d399;}
    .bm-hero-content{position:relative;z-index:3;padding:1.875rem 2.25rem;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;}
    .bm-hero-icon-wrap{flex-shrink:0;width:56px;height:56px;border-radius:15px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);display:flex;align-items:center;justify-content:center;}
    .bm-hero-eyebrow{display:inline-flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#6ee7b7;font-family:'JetBrains Mono',monospace;font-size:.625rem;font-weight:500;letter-spacing:.14em;text-transform:uppercase;padding:.3rem .875rem;border-radius:999px;margin-bottom:.5rem;width:fit-content;}
    .bm-hero-title{font-family:'Instrument Serif',serif;font-style:italic;font-size:1.875rem;font-weight:400;color:#fff;line-height:1.1;margin-bottom:.3rem;}
    .bm-hero-sub{font-size:.8125rem;color:rgba(255,255,255,.45);}
    .bm-hero-bar{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent 0%,rgba(16,185,129,.6) 20%,rgba(245,158,11,.8) 50%,rgba(16,185,129,.6) 80%,transparent 100%);background-size:300% 100%;animation:bm-bar-slide 4s linear infinite;}
    @keyframes bm-bar-slide{0%{background-position:100% 0;}100%{background-position:-100% 0;}}

    /* SECTION HEADER */
    .bm-section-hd{display:flex;align-items:center;gap:.625rem;margin-bottom:.875rem;}
    .bm-section-badge{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,var(--emerald-100),#bbf7d0);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .dark .bm-section-badge{background:linear-gradient(135deg,rgba(16,185,129,.18),rgba(5,150,105,.12));}
    .bm-section-title{font-size:.875rem;font-weight:700;color:var(--emerald-600);letter-spacing:-.01em;}
    .dark .bm-section-title{color:var(--emerald-400);}
    .bm-section-rule{height:1px;background:linear-gradient(90deg,var(--border-soft),transparent);margin-bottom:1rem;}
    .bm-section-link{display:inline-flex;align-items:center;gap:.35rem;font-size:.6875rem;font-weight:700;color:var(--emerald-600);text-decoration:none;border:1.5px solid currentColor;padding:.25rem .625rem;border-radius:999px;transition:all .2s ease;white-space:nowrap;}
    .dark .bm-section-link{color:var(--emerald-400);}
    .bm-section-link:hover{background:var(--emerald-600);color:#fff;border-color:var(--emerald-600);}

    /* CARD */
    .bm-card{background:var(--surface-0);border-radius:var(--radius-lg);border:1px solid var(--border-soft);box-shadow:var(--shadow-sm);padding:1.25rem;margin-bottom:1.25rem;transition:box-shadow .25s ease;}
    .bm-card:hover{box-shadow:var(--shadow-md);}

    /* STAT CARDS */
    .bm-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem;}
    .bm-stat{background:var(--surface-0);border-radius:var(--radius-lg);border:1px solid var(--border-soft);box-shadow:var(--shadow-xs);padding:1.125rem 1.25rem;display:flex;align-items:center;gap:.875rem;transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;overflow:hidden;}
    .bm-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;transform:scaleX(0);transform-origin:left;transition:transform .3s ease;}
    .bm-stat:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);}
    .bm-stat:hover::before{transform:scaleX(1);}
    .bm-stat.total{cursor:default;}
    .bm-stat.mapped{cursor:pointer;}
    .bm-stat.mapped::before{background:linear-gradient(90deg,#16a34a,#059669);}
    .bm-stat.mapped:hover{border-color:rgba(22,163,74,.3);}
    .bm-stat.unmapped{cursor:pointer;}
    .bm-stat.unmapped::before{background:linear-gradient(90deg,var(--amber-500),var(--amber-600));}
    .bm-stat.unmapped:hover{border-color:rgba(245,158,11,.3);}
    .bm-stat-ico{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:transform .3s ease;}
    .bm-stat:hover .bm-stat-ico{transform:scale(1.12) rotate(-8deg);}
    .bm-stat-ico.gray{background:var(--surface-2);}
    .bm-stat-ico.green{background:#dcfce7;}
    .bm-stat-ico.amber{background:#fef3c7;}
    .dark .bm-stat-ico.gray{background:rgba(255,255,255,.06);}
    .dark .bm-stat-ico.green{background:rgba(22,163,74,.12);}
    .dark .bm-stat-ico.amber{background:rgba(245,158,11,.12);}
    .bm-stat-num{font-family:'JetBrains Mono',monospace;font-size:1.875rem;font-weight:700;color:var(--text-primary);line-height:1;}
    .bm-stat-label{font-size:.75rem;color:var(--text-muted);margin-top:.2rem;}
    .bm-stat-cta{margin-left:auto;flex-shrink:0;font-size:.625rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);opacity:0;transition:opacity .2s ease;}
    .bm-stat.mapped:hover .bm-stat-cta,.bm-stat.unmapped:hover .bm-stat-cta{opacity:1;}

    /* TOOLBAR */
    .bm-toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;padding:1rem 1.25rem;border-bottom:1px solid var(--border-soft);background:var(--surface-1);}
    .dark .bm-toolbar{background:rgba(16,185,129,.02);}
    .bm-filter-tabs{display:flex;border-radius:var(--radius-sm);border:1px solid var(--border-soft);overflow:hidden;font-family:'JetBrains Mono',monospace;font-size:.6875rem;font-weight:600;}
    .bm-filter-tab{padding:.5rem 1.125rem;cursor:pointer;border:none;background:var(--surface-0);color:var(--text-muted);transition:all .18s ease;letter-spacing:.04em;text-transform:uppercase;border-right:1px solid var(--border-soft);}
    .bm-filter-tab:last-child{border-right:none;}
    .bm-filter-tab:hover{background:var(--surface-1);color:var(--text-primary);}
    .bm-filter-tab.active-all{background:var(--emerald-600);color:#fff;}
    .bm-filter-tab.active-mapped{background:#16a34a;color:#fff;}
    .bm-filter-tab.active-unmapped{background:var(--amber-500);color:#fff;}
    .bm-search-wrap{position:relative;flex:1;max-width:340px;}
    .bm-search-ico{position:absolute;left:.875rem;top:50%;transform:translateY(-50%);pointer-events:none;width:15px;height:15px;color:var(--text-muted);}
    .bm-search{width:100%;background:var(--surface-0);border:1px solid var(--border-soft);border-radius:var(--radius-sm);padding:.5625rem .875rem .5625rem 2.375rem;font-family:'Outfit',sans-serif;font-size:.8125rem;color:var(--text-primary);transition:border-color .2s,box-shadow .2s;outline:none;}
    .bm-search::placeholder{color:var(--text-muted);}
    .bm-search:focus{border-color:var(--emerald-500);box-shadow:0 0 0 3px rgba(16,185,129,.1);}
    .bm-legend{display:flex;align-items:center;gap:.875rem;margin-left:auto;}
    .bm-legend-item{display:flex;align-items:center;gap:.375rem;font-size:.6875rem;font-weight:600;color:var(--text-muted);font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:.06em;}
    .bm-legend-dot{width:8px;height:8px;border-radius:50%;}

    /* TABLE */
    .bm-table{width:100%;border-collapse:collapse;}
    .bm-thead tr{background:var(--surface-1);border-bottom:1px solid var(--border-soft);}
    .bm-th{padding:.625rem 1rem;text-align:left;font-family:'JetBrains Mono',monospace;font-size:.5625rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);white-space:nowrap;}
    .bm-th.center{text-align:center;}
    .bm-tbody tr{border-bottom:1px solid var(--border-soft);transition:background .15s ease;}
    .bm-tbody tr:last-child{border-bottom:none;}
    .bm-tbody tr:hover{background:var(--surface-1);}
    .dark .bm-tbody tr:hover{background:rgba(16,185,129,.03);}
    .bm-tbody tr.row-unmapped{background:rgba(245,158,11,.03);}
    .dark .bm-tbody tr.row-unmapped{background:rgba(245,158,11,.04);}
    .bm-td{padding:.75rem 1rem;vertical-align:middle;}
    .bm-td.center{text-align:center;}
    .bm-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
    .bm-emp-name{font-size:.875rem;font-weight:700;color:var(--text-primary);}
    .bm-plantilla{font-family:'JetBrains Mono',monospace;font-size:.6875rem;color:var(--text-muted);letter-spacing:.03em;}
    .bm-role-badge{display:inline-flex;align-items:center;border-radius:999px;padding:.2rem .625rem;font-family:'JetBrains Mono',monospace;font-size:.5625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;}
    .bm-role-badge.regular{background:#dbeafe;color:#1d4ed8;}
    .bm-role-badge.joborder{background:#ede9fe;color:#6d28d9;}
    .dark .bm-role-badge.regular{background:rgba(37,99,235,.15);color:#93c5fd;}
    .dark .bm-role-badge.joborder{background:rgba(109,40,217,.15);color:#c4b5fd;}
    .bm-device-input{width:100%;background:var(--surface-0);border:1.5px solid var(--border-soft);border-radius:var(--radius-sm);padding:.5rem .75rem;font-family:'JetBrains Mono',monospace;font-size:.8125rem;font-weight:500;color:var(--text-primary);transition:all .2s ease;outline:none;}
    .bm-device-input::placeholder{color:var(--text-muted);}
    .bm-device-input:focus{border-color:var(--emerald-500);box-shadow:0 0 0 3px rgba(16,185,129,.1);}
    .bm-device-input.has-value{border-color:rgba(22,163,74,.4);background:rgba(22,163,74,.04);color:#166534;}
    .dark .bm-device-input.has-value{color:#86efac;background:rgba(22,163,74,.06);border-color:rgba(22,163,74,.3);}
    .bm-history-btn{width:32px;height:32px;border-radius:9px;border:1px solid var(--border-soft);background:var(--surface-1);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);transition:all .2s ease;}
    .bm-history-btn:hover{background:var(--emerald-600);border-color:var(--emerald-600);color:#fff;transform:scale(1.08);box-shadow:0 4px 12px rgba(5,150,105,.25);}

    /* FOOTER */
    .bm-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;padding:.875rem 1.25rem;background:var(--surface-1);border-top:1px solid var(--border-soft);}
    .dark .bm-footer{background:rgba(16,185,129,.02);}
    .bm-footer-meta{font-family:'JetBrains Mono',monospace;font-size:.625rem;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);}

    /* SAVE BUTTON */
    .bm-save-btn{display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,var(--emerald-500),var(--emerald-700));color:white;border:none;border-radius:var(--radius-md);padding:.6875rem 1.5rem;font-family:'Outfit',sans-serif;font-size:.9375rem;font-weight:700;cursor:pointer;transition:all .25s ease;box-shadow:0 4px 14px rgba(5,150,105,.28);}
    .bm-save-btn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 8px 22px rgba(5,150,105,.38);}
    .bm-save-btn:disabled{opacity:.6;cursor:not-allowed;}

    /* DRAWER */
    .bm-drawer-overlay{position:fixed;inset:0;z-index:50;display:flex;justify-content:flex-end;}
    .bm-drawer-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);}
    .bm-drawer-panel{position:relative;z-index:10;width:100%;max-width:420px;background:var(--surface-0);box-shadow:var(--shadow-lg);display:flex;flex-direction:column;height:100%;border-left:1px solid var(--border-soft);}
    .bm-drawer-header{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-soft);background:var(--surface-1);}
    .dark .bm-drawer-header{background:rgba(16,185,129,.03);}
    .bm-drawer-title{font-size:.9375rem;font-weight:700;color:var(--text-primary);}
    .bm-drawer-sub{font-size:.75rem;color:var(--text-muted);margin-top:.125rem;}
    .bm-drawer-close{width:30px;height:30px;border-radius:8px;border:1px solid var(--border-soft);background:var(--surface-0);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);transition:all .2s ease;}
    .bm-drawer-close:hover{background:#fef2f2;border-color:rgba(220,38,38,.2);color:#dc2626;}
    .bm-drawer-body{flex:1;overflow-y:auto;padding:1.25rem 1.5rem;}
    .bm-drawer-footer{padding:1rem 1.5rem;border-top:1px solid var(--border-soft);background:var(--surface-1);}
    .dark .bm-drawer-footer{background:rgba(16,185,129,.02);}
    .bm-history-item{border-radius:var(--radius-md);border:1px solid var(--border-soft);padding:1rem 1.125rem;margin-bottom:.625rem;background:var(--surface-1);}
    .bm-history-item:last-child{margin-bottom:0;}
    .bm-history-item.active{border-color:rgba(22,163,74,.3);background:rgba(22,163,74,.04);}
    .dark .bm-history-item.active{background:rgba(22,163,74,.07);}
    .bm-history-device{font-family:'JetBrains Mono',monospace;font-size:1.25rem;font-weight:700;line-height:1;}
    .bm-history-device.active-id{color:#166534;}
    .dark .bm-history-device.active-id{color:#86efac;}
    .bm-history-device.inactive-id{color:var(--text-muted);}
    .bm-history-meta{font-family:'JetBrains Mono',monospace;font-size:.5625rem;color:var(--text-muted);margin-top:.5rem;line-height:1.7;}

    /* CONTEXT BANNER */
    .bm-context-banner{display:flex;align-items:center;gap:.75rem;background:var(--surface-1);border:1px solid var(--border-soft);border-radius:var(--radius-md);padding:.75rem 1.125rem;margin-bottom:1.25rem;font-size:.8125rem;color:var(--text-secondary);position:relative;overflow:hidden;}
    .bm-context-banner::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--emerald-500),var(--emerald-700));border-radius:0 2px 2px 0;}
    .bm-context-link{font-weight:700;color:var(--emerald-600);text-decoration:none;}
    .dark .bm-context-link{color:var(--emerald-400);}
    .bm-context-link:hover{text-decoration:underline;}

    /* EMPTY STATE */
    .bm-empty{padding:3.5rem 1.5rem;text-align:center;border-radius:var(--radius-sm);background:var(--surface-1);border:2px dashed var(--border-soft);margin:1rem;}
    .bm-empty-title{font-size:.9375rem;font-weight:700;color:var(--text-muted);margin-bottom:.25rem;}
    .bm-empty-text{font-size:.8125rem;color:var(--text-muted);opacity:.75;}
    .bm-empty-btn{margin-top:.875rem;font-size:.75rem;font-weight:700;color:var(--emerald-600);background:none;border:none;cursor:pointer;font-family:'Outfit',sans-serif;}
    .dark .bm-empty-btn{color:var(--emerald-400);}

    /* HELP ACCORDION */
    .bm-help{border-radius:var(--radius-lg);overflow:hidden;border:1px solid rgba(59,130,246,.2);background:rgba(239,246,255,.6);margin-top:1.25rem;}
    .dark .bm-help{background:rgba(59,130,246,.06);border-color:rgba(59,130,246,.15);}
    .bm-help-header{display:flex;align-items:center;justify-content:space-between;padding:.875rem 1.25rem;cursor:pointer;background:rgba(239,246,255,.8);border:none;width:100%;text-align:left;}
    .dark .bm-help-header{background:rgba(59,130,246,.08);}
    .bm-help-header-left{display:flex;align-items:center;gap:.5rem;}
    .bm-help-title{font-size:.875rem;font-weight:700;color:#1d4ed8;}
    .dark .bm-help-title{color:#93c5fd;}
    .bm-help-body{padding:0 1.25rem 1.125rem;}
    .bm-help-list{list-style:none;padding:0;margin:0;}
    .bm-help-list li{display:flex;align-items:flex-start;gap:.75rem;padding:.5rem 0;border-bottom:1px solid rgba(59,130,246,.1);font-size:.8125rem;color:#1e40af;line-height:1.5;}
    .dark .bm-help-list li{color:#bfdbfe;border-color:rgba(59,130,246,.08);}
    .bm-help-list li:last-child{border-bottom:none;}
    .bm-help-step-num{flex-shrink:0;width:20px;height:20px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-family:'JetBrains Mono',monospace;font-size:.5625rem;font-weight:700;margin-top:.1rem;}
    .dark .bm-help-step-num{background:rgba(37,99,235,.7);}

    /* ANIMATIONS */
    @keyframes bm-in{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
    .bm-anim{animation:bm-in .4s cubic-bezier(.22,1,.36,1) backwards;}
    .bm-a1{animation-delay:.04s;}.bm-a2{animation-delay:.08s;}.bm-a3{animation-delay:.13s;}
    .bm-spin{animation:bm-spinner .7s linear infinite;}
    @keyframes bm-spinner{to{transform:rotate(360deg);}}

    @media(max-width:768px){
        .bm-hero-content{padding:1.5rem;}
        .bm-hero-title{font-size:1.5rem;}
        .bm-stats-grid{grid-template-columns:1fr;}
    }
</style>

<div
    class="bm-root"
    x-data="{
        rows: @js($rows),
        filter: 'all',
        search: '',
        drawerOpen: false,

        get filtered() {
            let list = this.rows;
            if (this.filter === 'mapped')   list = list.filter(r => r.device_id !== '');
            if (this.filter === 'unmapped') list = list.filter(r => r.device_id === '');
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                list = list.filter(r =>
                    r.name.toLowerCase().includes(q)        ||
                    r.employee_id.toLowerCase().includes(q) ||
                    r.device_id.toLowerCase().includes(q)   ||
                    r.role.toLowerCase().includes(q)
                );
            }
            return list;
        },

        get mappedCount()   { return this.rows.filter(r => r.device_id !== '').length; },
        get unmappedCount() { return this.rows.filter(r => r.device_id === '').length; },
        get totalCount()    { return this.rows.length; },

        openHistory(userId) {
            this.drawerOpen = true;
            $wire.loadHistory(userId);
        }
    }"
>

{{-- HERO --}}
<div class="bm-hero bm-anim">
    <div class="bm-hero-canvas"></div>
    <div class="bm-hero-noise"></div>
    <div class="bm-hero-mesh"></div>
    <div class="bm-hero-decor">
        @for($i=0;$i<15;$i++)<div class="bm-hero-dot"></div>@endfor
    </div>
    <div class="bm-hero-content">
        <div class="bm-hero-icon-wrap">
            <x-heroicon-o-finger-print class="w-7 h-7 text-emerald-400"/>
        </div>
        <div>
            <div class="bm-hero-eyebrow">
                <x-heroicon-o-users class="w-3 h-3"/>
                Biometric Integration — ATI-HRMS
            </div>
            <h1 class="bm-hero-title">Bulk Device Mapping</h1>
            <p class="bm-hero-sub">Assign biometric device IDs to all registered employees in one session.</p>
        </div>
    </div>
    <div class="bm-hero-bar"></div>
</div>

{{-- CONTEXT BANNER --}}
<div class="bm-context-banner bm-anim bm-a1">
    <x-heroicon-m-information-circle class="w-4 h-4 flex-shrink-0" style="color:var(--emerald-600);"/>
    <span>
        This page handles <strong>bulk device ID assignment only</strong>.
        To create, edit, deactivate, or audit individual mapping records, use
        <a href="{{ \App\Filament\Resources\BiometricEmployeeMappingResource::getUrl('index') }}"
           class="bm-context-link">Biometric Mappings →</a>
    </span>
</div>

{{-- STAT CARDS --}}
<div class="bm-stats-grid bm-anim bm-a1">
    <div class="bm-stat total">
        <div class="bm-stat-ico gray">
            <x-heroicon-o-users class="w-5 h-5" style="color:var(--text-secondary);"/>
        </div>
        <div>
            <div class="bm-stat-num" x-text="totalCount"></div>
            <div class="bm-stat-label">Total Employees</div>
        </div>
    </div>
    <div class="bm-stat mapped" @click="filter = 'mapped'" title="Show mapped only">
        <div class="bm-stat-ico green">
            <x-heroicon-o-check-badge class="w-5 h-5" style="color:#16a34a;"/>
        </div>
        <div>
            <div class="bm-stat-num" x-text="mappedCount" style="color:#166534;"></div>
            <div class="bm-stat-label">Mapped</div>
        </div>
        <div class="bm-stat-cta">View ↗</div>
    </div>
    <div class="bm-stat unmapped" @click="filter = 'unmapped'" title="Show unmapped only">
        <div class="bm-stat-ico amber">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" style="color:var(--amber-600);"/>
        </div>
        <div>
            <div class="bm-stat-num" x-text="unmappedCount" style="color:var(--amber-600);"></div>
            <div class="bm-stat-label">Unmapped</div>
        </div>
        <div class="bm-stat-cta">View ↗</div>
    </div>
</div>

{{-- MAIN CARD --}}
<div class="bm-card bm-anim bm-a2" style="padding:0;overflow:hidden;">

    {{-- Toolbar --}}
    <div class="bm-toolbar">
        <div class="bm-filter-tabs">
            <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bm-filter-tab active-all' : 'bm-filter-tab'"
                    class="bm-filter-tab">All</button>
            <button @click="filter = 'mapped'"
                    :class="filter === 'mapped' ? 'bm-filter-tab active-mapped' : 'bm-filter-tab'"
                    class="bm-filter-tab">Mapped</button>
            <button @click="filter = 'unmapped'"
                    :class="filter === 'unmapped' ? 'bm-filter-tab active-unmapped' : 'bm-filter-tab'"
                    class="bm-filter-tab">Unmapped</button>
        </div>
        <div class="bm-search-wrap">
            <svg class="bm-search-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input x-model="search" type="text" placeholder="Search name, plantilla ID, device…" class="bm-search">
        </div>
        <div class="bm-legend">
            <div class="bm-legend-item">
                <span class="bm-legend-dot" style="background:#16a34a;"></span> Mapped
            </div>
            <div class="bm-legend-item">
                <span class="bm-legend-dot" style="background:var(--amber-500);"></span> Unmapped
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table class="bm-table">
            <thead class="bm-thead">
                <tr>
                    <th class="bm-th" style="width:32px;"></th>
                    <th class="bm-th">Employee Name</th>
                    <th class="bm-th">Plantilla ID</th>
                    <th class="bm-th" style="width:110px;">Role</th>
                    <th class="bm-th" style="width:180px;">
                        Device ID <span style="font-weight:400;opacity:.55;margin-left:4px;">"No :" in XLS</span>
                    </th>
                    <th class="bm-th center" style="width:72px;">History</th>
                </tr>
            </thead>
            <tbody class="bm-tbody">

                <template x-for="(row, index) in filtered" :key="row.user_id">
                    <tr :class="row.device_id ? '' : 'row-unmapped'">
                        <td class="bm-td">
                            <span class="bm-dot"
                                  :style="row.device_id
                                      ? 'background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15);'
                                      : 'background:var(--amber-500);box-shadow:0 0 0 3px rgba(245,158,11,.15);'">
                            </span>
                        </td>
                        <td class="bm-td"><div class="bm-emp-name" x-text="row.name"></div></td>
                        <td class="bm-td"><span class="bm-plantilla" x-text="row.employee_id"></span></td>
                        <td class="bm-td">
                            <span class="bm-role-badge"
                                  :class="row.role === 'Regular' ? 'regular' : 'joborder'"
                                  x-text="row.role"></span>
                        </td>
                        {{--
                            Device ID input.
                            @input writes to Alpine rows by user_id (not filtered index) so
                            searching/filtering while typing doesn't cause index mismatches.
                            $wire.set syncs back to Livewire so save() reads the correct value.
                        --}}
                        <td class="bm-td">
                            <input
                                type="text"
                                :value="row.device_id"
                                :class="row.device_id ? 'bm-device-input has-value' : 'bm-device-input'"
                                @input="
                                    const val = $event.target.value;
                                    const idx = rows.findIndex(r => r.user_id === row.user_id);
                                    if (idx !== -1) {
                                        rows[idx].device_id = val;
                                        row.device_id = val;
                                        clearTimeout(window['_bm_t_' + row.user_id]);
                                        window['_bm_t_' + row.user_id] = setTimeout(() => {
                                            $wire.set('rows.' + idx + '.device_id', val);
                                        }, 300);
                                    }
                                "
                                placeholder="e.g. 42"
                                maxlength="50"
                            >
                        </td>
                        <td class="bm-td center">
                            <button @click="openHistory(row.user_id)" class="bm-history-btn" title="View mapping history (read-only)">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>

                <template x-if="filtered.length === 0">
                    <tr>
                        <td colspan="6">
                            <div class="bm-empty">
                                <svg style="width:2.25rem;height:2.25rem;margin:0 auto .75rem;opacity:.18;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <div class="bm-empty-title">No employees match your current filter</div>
                                <div class="bm-empty-text">Try adjusting the filter tabs or search query.</div>
                                <button @click="filter = 'all'; search = ''" class="bm-empty-btn">↩ Clear all filters</button>
                            </div>
                        </td>
                    </tr>
                </template>

            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="bm-footer">
        <div class="bm-footer-meta">
            Showing <span x-text="filtered.length"></span> of <span x-text="totalCount"></span> employees
            &nbsp;·&nbsp; Blank = unmapped &nbsp;·&nbsp; Clearing a device ID deactivates it on save
        </div>
        <button wire:click="save" wire:loading.attr="disabled" class="bm-save-btn">
            <span wire:loading wire:target="save">
                <svg class="bm-spin" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="save">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="save">Save All Mappings</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </div>

</div>{{-- end main card --}}

{{-- HISTORY SLIDE-OVER DRAWER (read-only) --}}
<div
    x-show="drawerOpen"
    class="bm-drawer-overlay"
    @keydown.escape.window="drawerOpen = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display:none;"
>
    <div class="bm-drawer-backdrop" @click="drawerOpen = false"></div>
    <div
        class="bm-drawer-panel"
        x-show="drawerOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
    >
        <div class="bm-drawer-header">
            <div>
                <div class="bm-drawer-title">Mapping History</div>
                <div class="bm-drawer-sub"><span x-text="$wire.historyEmployeeName"></span></div>
            </div>
            <button @click="drawerOpen = false" class="bm-drawer-close">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="bm-drawer-body">
            <div wire:loading wire:target="loadHistory"
                 style="display:flex;align-items:center;gap:.625rem;justify-content:center;padding:3rem 0;font-size:.8125rem;color:var(--text-muted);">
                <svg class="bm-spin" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Loading history…
            </div>
            <div wire:loading.remove wire:target="loadHistory">
                @if (empty($historyRows))
                    <div class="bm-empty" style="margin:0;">
                        <svg style="width:2rem;height:2rem;margin:0 auto .625rem;opacity:.18;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="bm-empty-title">No mapping history yet</div>
                        <div class="bm-empty-text">This employee hasn't been mapped to a device.</div>
                    </div>
                @else
                    @foreach ($historyRows as $h)
                        <div class="bm-history-item {{ $h['is_active'] ? 'active' : '' }}">
                            <div style="display:flex;align-items:center;gap:.625rem;margin-bottom:.5rem;">
                                <div class="bm-history-device {{ $h['is_active'] ? 'active-id' : 'inactive-id' }}">{{ $h['device_id'] }}</div>
                                @if ($h['is_active'])
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;border-radius:999px;padding:.175rem .625rem;background:rgba(22,163,74,.1);font-family:'JetBrains Mono',monospace;font-size:.5625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#166534;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;"></span>Active
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;border-radius:999px;padding:.175rem .625rem;background:var(--surface-2);font-family:'JetBrains Mono',monospace;font-size:.5625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);">Inactive</span>
                                @endif
                                @if ($h['device_name'] !== '—')
                                    <span style="font-size:.75rem;color:var(--text-muted);">{{ $h['device_name'] }}</span>
                                @endif
                            </div>
                            <div class="bm-history-meta">Created &nbsp;{{ $h['created_at'] }}<br>Updated &nbsp;{{ $h['updated_at'] }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bm-drawer-footer">
            <p style="font-size:.6875rem;color:var(--text-muted);line-height:1.6;">
                Read-only. To edit or deactivate, use
                <a href="{{ \App\Filament\Resources\BiometricEmployeeMappingResource::getUrl('index') }}" class="bm-context-link">Biometric Mappings</a>.
                Inactive records are retained for audit.
            </p>
        </div>
    </div>
</div>

</div>{{-- end root Alpine component --}}

{{-- HELP ACCORDION --}}
<div x-data="{ helpOpen: false }" class="bm-help bm-anim bm-a3">
    <button @click="helpOpen = !helpOpen" class="bm-help-header">
        <div class="bm-help-header-left">
            <svg style="width:16px;height:16px;color:#2563eb;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="bm-help-title">How to use this page</span>
        </div>
        <svg width="14" height="14"
            style="color:#2563eb;transition:transform .2s;flex-shrink:0;"
            :style="helpOpen ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="helpOpen" class="bm-help-body"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">
        <ul class="bm-help-list">
            @foreach([
                'Open the XLS attendance file from your biometric device alongside this page.',
                'Go to the <strong>Logs</strong> sheet — find the <strong>"No :"</strong> column. That number is each employee\'s Device ID.',
                'Click the <strong>Unmapped</strong> tab (or amber stat card) to focus on employees still needing a device ID.',
                'Type each employee\'s Device ID into the input on their row. Green = mapped, amber = still needs a number.',
                'Click <strong>Save All Mappings</strong>. All saves happen in one transaction — all succeed or none.',
                'Click the <strong>clock icon</strong> on any row to view that employee\'s full mapping history (read-only).',
                'To edit, deactivate, or manage individual records, use <strong>Biometric Mappings</strong> in the banner above.',
            ] as $i => $step)
                <li>
                    <span class="bm-help-step-num">{{ $i + 1 }}</span>
                    <span>{!! $step !!}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

</x-filament-panels::page>
