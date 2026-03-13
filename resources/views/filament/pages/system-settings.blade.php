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
        --radius-sm: 10px; --radius-md: 14px; --radius-lg: 20px; --radius-xl: 26px;
    }

    .dark {
        --surface-0: #0b1a14; --surface-1: #0f2119;
        --border-soft: #1e3a2c;
        --text-primary: #e8f5ef; --text-secondary: #9dcfba; --text-muted: #4d8a72;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.25); --shadow-md: 0 8px 24px rgba(0,0,0,0.35);
    }

    .ss-root * { box-sizing: border-box; }

    .ss-root {
        font-family: 'Outfit', sans-serif;
        background: var(--surface-1); min-height: 100vh;
        padding: 1.5rem 1.25rem; color: var(--text-primary);
        max-width: 760px; margin: 0 auto;
    }

    /* ── HERO ── */
    .ss-hero {
        position: relative; border-radius: var(--radius-xl);
        overflow: hidden; margin-bottom: 1.5rem;
        min-height: 142px; background: #071812;
    }

    .ss-hero-canvas {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 65% 90% at 80% 55%, rgba(5,150,105,0.55) 0%, transparent 62%),
            radial-gradient(ellipse 40% 60% at 95% 10%, rgba(245,158,11,0.28) 0%, transparent 55%),
            linear-gradient(145deg, #040e08 0%, #0a2016 45%, #071410 100%);
    }

    .ss-hero-mesh {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(16,185,129,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(16,185,129,0.05) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
    }

    .ss-hero-decor {
        position: absolute; top: 1.25rem; right: 1.75rem;
        display: flex; gap: 6px; flex-wrap: wrap; width: 60px; opacity: 0.22;
    }
    .ss-hero-dot { width: 4px; height: 4px; border-radius: 50%; background: #34d399; }

    .ss-hero-content {
        position: relative; z-index: 3;
        padding: 1.75rem 2.25rem;
        display: flex; align-items: center; gap: 1.25rem;
    }

    .ss-hero-icon-wrap {
        flex-shrink: 0; width: 54px; height: 54px; border-radius: 15px;
        background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.28);
        display: flex; align-items: center; justify-content: center;
    }

    .ss-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
        color: #6ee7b7; font-family: 'JetBrains Mono', monospace;
        font-size: 0.625rem; font-weight: 500; letter-spacing: 0.14em; text-transform: uppercase;
        padding: 0.3rem 0.875rem; border-radius: 999px;
        margin-bottom: 0.5rem; width: fit-content;
    }

    .ss-hero-title {
        font-family: 'Instrument Serif', serif;
        font-size: 1.75rem; font-weight: 400;
        color: #ffffff; line-height: 1.1; margin-bottom: 0.25rem;
    }

    .ss-hero-sub { font-size: 0.8125rem; color: rgba(255,255,255,0.45); }

    .ss-hero-bar {
        position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(16,185,129,0.6) 20%, rgba(245,158,11,0.8) 50%, rgba(16,185,129,0.6) 80%, transparent 100%);
        background-size: 300% 100%; animation: ss-shimmer 4s linear infinite;
    }

    @keyframes ss-shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

    /* ── SECTION HEADER ── */
    .ss-section-hd { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.875rem; }
    .ss-section-badge {
        width: 30px; height: 30px; border-radius: 9px;
        background: linear-gradient(135deg, var(--emerald-100), #bbf7d0);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dark .ss-section-badge { background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(5,150,105,0.12)); }
    .ss-section-title { font-size: 0.875rem; font-weight: 700; color: var(--emerald-600); }
    .dark .ss-section-title { color: var(--emerald-400); }
    .ss-section-rule { height: 1px; background: linear-gradient(90deg, var(--border-soft), transparent); margin-bottom: 1rem; }

    /* ── CARD ── */
    .ss-card {
        background: var(--surface-0); border-radius: var(--radius-lg);
        border: 1px solid var(--border-soft); box-shadow: var(--shadow-sm);
        padding: 1.25rem; margin-bottom: 1.25rem;
        transition: box-shadow 0.25s ease;
    }
    .ss-card:hover { box-shadow: var(--shadow-md); }

    /* ── SETTING ROW ── */
    .ss-setting-row {
        display: flex; align-items: center; justify-content: space-between; gap: 1.25rem;
        padding: 1.125rem 1.25rem; border-radius: var(--radius-md);
        border: 1px solid var(--border-soft);
        background: var(--surface-1);
        transition: all 0.2s ease;
    }

    .ss-setting-row.is-open {
        background: rgba(16,185,129,0.04);
        border-color: rgba(16,185,129,0.2);
    }

    .dark .ss-setting-row { background: rgba(16,185,129,0.03); }
    .dark .ss-setting-row.is-open { background: rgba(16,185,129,0.07); border-color: rgba(16,185,129,0.25); }

    .ss-setting-left { display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 0; }

    .ss-setting-ico {
        flex-shrink: 0; width: 46px; height: 46px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        background: var(--surface-0); border: 1px solid var(--border-soft);
        transition: all 0.2s ease;
    }

    .ss-setting-row.is-open .ss-setting-ico {
        background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.2);
    }

    .ss-setting-info { flex: 1; min-width: 0; }

    .ss-setting-name-row { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.3rem; flex-wrap: wrap; }
    .ss-setting-name { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); }

    .ss-status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        border-radius: 999px; padding: 0.175rem 0.625rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5625rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em;
    }

    .ss-status-badge.open { background: rgba(16,185,129,0.12); color: #065f46; }
    .dark .ss-status-badge.open { background: rgba(16,185,129,0.2); color: #6ee7b7; }
    .ss-status-badge.closed { background: #f3f4f6; color: #6b7280; }
    .dark .ss-status-badge.closed { background: rgba(255,255,255,0.07); color: #9ca3af; }

    .ss-status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .ss-status-dot.pulse { background: #059669; animation: ss-dot 1.8s ease-in-out infinite; }
    .ss-status-dot.idle  { background: #9ca3af; }

    @keyframes ss-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.7); }
    }

    .ss-setting-desc { font-size: 0.8125rem; color: var(--text-muted); line-height: 1.5; }
    .ss-setting-hint { margin-top: 0.25rem; font-size: 0.75rem; font-weight: 600; }
    .ss-setting-hint.on  { color: var(--emerald-600); }
    .ss-setting-hint.off { color: var(--text-muted); }
    .dark .ss-setting-hint.on { color: var(--emerald-400); }

    /* ── TOGGLE BUTTON ── */
    .ss-toggle-btn {
        flex-shrink: 0; display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 1.25rem; border-radius: 12px; border: none; cursor: pointer;
        font-family: 'Outfit', sans-serif; font-size: 0.875rem; font-weight: 700;
        transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1); white-space: nowrap;
    }

    .ss-toggle-btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .ss-toggle-btn.btn-close {
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: white; box-shadow: 0 4px 14px rgba(220,38,38,0.28);
    }

    .ss-toggle-btn.btn-close:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(220,38,38,0.38); }

    .ss-toggle-btn.btn-open {
        background: linear-gradient(135deg, var(--emerald-500), var(--emerald-700));
        color: white; box-shadow: 0 4px 14px rgba(5,150,105,0.28);
    }

    .ss-toggle-btn.btn-open:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(5,150,105,0.38); }

    /* ── INFO BOX ── */
    .ss-info-box {
        border-radius: var(--radius-sm); padding: 1rem 1.125rem;
        display: flex; gap: 0.875rem;
        background: rgba(59,130,246,0.04); border: 1.5px solid rgba(59,130,246,0.14);
    }

    .dark .ss-info-box { background: rgba(59,130,246,0.07); border-color: rgba(59,130,246,0.18); }

    .ss-info-content { font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.55; }
    .dark .ss-info-content { color: rgba(219,234,254,0.8); }
    .ss-info-title { font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary); }
    .dark .ss-info-title { color: #bfdbfe; }
    .ss-info-list { margin: 0; padding-left: 1rem; }
    .ss-info-list li { margin-bottom: 0.3rem; }
    .ss-info-list li:last-child { margin-bottom: 0; }

    /* ── SPINNER ── */
    .ss-spin { animation: ss-spinner 0.7s linear infinite; }
    @keyframes ss-spinner { to { transform: rotate(360deg); } }

    /* ── ANIMATIONS ── */
    @keyframes ss-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .ss-anim { animation: ss-in 0.45s cubic-bezier(0.22,1,0.36,1) backwards; }
    .ss-a1 { animation-delay: 0.04s; }
    .ss-a2 { animation-delay: 0.09s; }
    .ss-a3 { animation-delay: 0.14s; }
</style>

<div class="ss-root">

    {{-- Hero --}}
    <div class="ss-hero ss-anim">
        <div class="ss-hero-canvas"></div>
        <div class="ss-hero-mesh"></div>
        <div class="ss-hero-decor">
            @for($i=0;$i<15;$i++)<div class="ss-hero-dot"></div>@endfor
        </div>

        <div class="ss-hero-content">
            <div class="ss-hero-icon-wrap">
                <x-heroicon-o-cog-6-tooth class="w-7 h-7 text-emerald-400" />
            </div>
            <div>
                <div class="ss-hero-eyebrow">
                    <x-heroicon-o-shield-check class="w-3 h-3" />
                    Administrator Access
                </div>
                <h1 class="ss-hero-title">System Settings</h1>
                <p class="ss-hero-sub">Manage system-wide flags that affect employee access and workflows.</p>
            </div>
        </div>

        <div class="ss-hero-bar"></div>
    </div>

    {{-- Settings Card --}}
    <div class="ss-card ss-anim ss-a1">
        <div class="ss-section-hd">
            <div class="ss-section-badge">
                <x-heroicon-o-adjustments-horizontal class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="ss-section-title">System Configuration</span>
        </div>
        <div class="ss-section-rule"></div>

        <div class="ss-setting-row {{ $filingSeasonEnabled ? 'is-open' : '' }}">
            <div class="ss-setting-left">
                <div class="ss-setting-ico">
                    @if($filingSeasonEnabled)
                        <x-heroicon-o-lock-open class="w-5 h-5 text-emerald-600" />
                    @else
                        <x-heroicon-o-lock-closed class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                    @endif
                </div>
                <div class="ss-setting-info">
                    <div class="ss-setting-name-row">
                        <span class="ss-setting-name">Filing Season</span>
                        <span class="ss-status-badge {{ $filingSeasonEnabled ? 'open' : 'closed' }}">
                            <span class="ss-status-dot {{ $filingSeasonEnabled ? 'pulse' : 'idle' }}"></span>
                            {{ $filingSeasonEnabled ? 'Open' : 'Closed' }}
                        </span>
                    </div>
                    <p class="ss-setting-desc">
                        Controls whether employees can edit and resubmit records that have been approved and unlocked by an admin.
                    </p>
                    <p class="ss-setting-hint {{ $filingSeasonEnabled ? 'on' : 'off' }}">
                        @if($filingSeasonEnabled)
                            ✓ Employees with unlocked approved records can currently edit them.
                        @else
                            ✗ No employee can edit approved records, even if individually unlocked.
                        @endif
                    </p>
                </div>
            </div>

            <button
                wire:click="toggleFilingSeason"
                wire:loading.attr="disabled"
                wire:target="toggleFilingSeason"
                class="ss-toggle-btn {{ $filingSeasonEnabled ? 'btn-close' : 'btn-open' }}"
            >
                <span wire:loading wire:target="toggleFilingSeason">
                    <svg class="ss-spin" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </span>

                <span wire:loading.remove wire:target="toggleFilingSeason">
                    @if($filingSeasonEnabled)
                        <x-heroicon-o-lock-closed style="width:15px;height:15px;" />
                    @else
                        <x-heroicon-o-lock-open style="width:15px;height:15px;" />
                    @endif
                </span>

                <span wire:loading.remove wire:target="toggleFilingSeason">
                    {{ $filingSeasonEnabled ? 'Close Filing Season' : 'Open Filing Season' }}
                </span>
                <span wire:loading wire:target="toggleFilingSeason">
                    {{ $filingSeasonEnabled ? 'Closing…' : 'Opening…' }}
                </span>
            </button>
        </div>
    </div>

    {{-- How It Works --}}
    <div class="ss-card ss-anim ss-a2">
        <div class="ss-section-hd">
            <div class="ss-section-badge">
                <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
            </div>
            <span class="ss-section-title">How Filing Season Works</span>
        </div>
        <div class="ss-section-rule"></div>

        <div class="ss-info-box">
            <div style="flex-shrink:0;margin-top:0.1rem;">
                <x-heroicon-o-information-circle style="width:18px;height:18px;color:#3b82f6;" />
            </div>
            <div class="ss-info-content">
                <p class="ss-info-title">Filing Season works together with per-record locks:</p>
                <ul class="ss-info-list">
                    <li><strong>Filing Season OFF</strong> — No employee can edit any approved record, regardless of whether it is individually unlocked.</li>
                    <li><strong>Filing Season ON + record locked</strong> — Employee still cannot edit. Admin must first unlock the specific record.</li>
                    <li><strong>Filing Season ON + record unlocked</strong> — Employee can edit and resubmit. The record re-locks automatically after submission.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

</x-filament-panels::page>
