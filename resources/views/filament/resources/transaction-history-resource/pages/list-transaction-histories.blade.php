<x-filament-panels::page>

{{--
    Transaction History — Modern Activity Timeline
    ===============================================
    Features:
    • Date-grouped entries with dividers
    • Colored status badges (pending→amber, approved→green, rejected→red, filed→blue)
    • Module icons
    • Employee initials avatar with color-coded background
    • Clickable "View Record" link to the source module
    • Stats bar showing counts per module
    • Paginated (50 per page)
    • Responsive two-column layout on wide screens
--}}

<style>
    /* ── Reset & Base ─────────────────────────────────────────── */
    .th-root {
        font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 14px;
    }

    /* ── Stats bar ────────────────────────────────────────────── */
    .th-stats {
        display: flex;
        flex-wrap: wrap;
        gap: .625rem;
        margin-bottom: 1.5rem;
    }

    .th-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .9rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
        border: 1.5px solid;
        cursor: default;
        transition: transform .15s;
    }

    .th-stat-pill:hover { transform: translateY(-1px); }

    /* Module colour map */
    .th-stat-leave    { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
    .th-stat-travel   { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
    .th-stat-locator  { background: #f5f3ff; border-color: #c4b5fd; color: #5b21b6; }
    .th-stat-saln     { background: #fff1f2; border-color: #fca5a5; color: #9f1239; }
    .th-stat-pds      { background: #ecfdf5; border-color: #6ee7b7; color: #065f46; }
    .th-stat-employee { background: #f0fdf4; border-color: #86efac; color: #15803d; }
    .th-stat-dtr      { background: #f8fafc; border-color: #94a3b8; color: #334155; }
    .th-stat-default  { background: #f8fafc; border-color: #cbd5e1; color: #475569; }

    .dark .th-stat-leave    { background: rgba(29,78,216,.15);  border-color: #3b82f6; color: #93c5fd; }
    .dark .th-stat-travel   { background: rgba(146,64,14,.15);  border-color: #f59e0b; color: #fcd34d; }
    .dark .th-stat-locator  { background: rgba(91,33,182,.15);  border-color: #8b5cf6; color: #c4b5fd; }
    .dark .th-stat-saln     { background: rgba(159,18,57,.15);  border-color: #f87171; color: #fca5a5; }
    .dark .th-stat-pds      { background: rgba(6,95,70,.15);    border-color: #34d399; color: #6ee7b7; }
    .dark .th-stat-employee { background: rgba(21,128,61,.15);  border-color: #4ade80; color: #86efac; }
    .dark .th-stat-dtr      { background: rgba(51,65,85,.2);    border-color: #64748b; color: #94a3b8; }

    /* ── Date divider ─────────────────────────────────────────── */
    .th-date-group {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 1.75rem 0 1rem;
    }

    .th-date-label {
        font-size: .6875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: #6b7280;
        white-space: nowrap;
        padding: .25rem .75rem;
        background: #f3f4f6;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
    }

    .dark .th-date-label {
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.1);
        color: #9ca3af;
    }

    .th-date-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, #e5e7eb, transparent);
    }

    .dark .th-date-line { background: linear-gradient(90deg, rgba(255,255,255,.08), transparent); }

    /* ── Timeline spine ───────────────────────────────────────── */
    .th-timeline {
        position: relative;
        padding-left: 2.25rem;
    }

    .th-timeline::before {
        content: '';
        position: absolute;
        left: .875rem;
        top: 0; bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #d1fae5, #bfdbfe, #e9d5ff, #fce7f3, #d1fae5);
        border-radius: 999px;
        opacity: .5;
    }

    /* ── Entry card ───────────────────────────────────────────── */
    .th-entry {
        position: relative;
        display: flex;
        gap: .875rem;
        padding: 1rem 1.125rem;
        margin-bottom: .625rem;
        border-radius: 14px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        text-decoration: none;
        transition: all .2s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .dark .th-entry {
        background: rgba(255,255,255,.03);
        border-color: rgba(255,255,255,.08);
    }

    .th-entry:hover {
        transform: translateX(4px);
        border-color: rgba(5,150,105,.3);
        box-shadow: 0 4px 18px rgba(5,150,105,.1);
    }

    /* Spine dot */
    .th-entry::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 1.25rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #d1d5db;
        transition: border-color .2s, background .2s;
    }

    .th-entry:hover::before {
        border-color: #059669;
        background: #d1fae5;
    }

    /* ── Avatar ───────────────────────────────────────────────── */
    .th-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: .8rem;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }

    .th-avatar img {
        width: 40px; height: 40px;
        border-radius: 50%; object-fit: cover;
    }

    /* Avatar background colours cycle through 8 greens/blues */
    .th-av-0 { background: linear-gradient(135deg,#059669,#047857); }
    .th-av-1 { background: linear-gradient(135deg,#2563eb,#1d4ed8); }
    .th-av-2 { background: linear-gradient(135deg,#7c3aed,#6d28d9); }
    .th-av-3 { background: linear-gradient(135deg,#db2777,#be185d); }
    .th-av-4 { background: linear-gradient(135deg,#d97706,#b45309); }
    .th-av-5 { background: linear-gradient(135deg,#0891b2,#0e7490); }
    .th-av-6 { background: linear-gradient(135deg,#16a34a,#15803d); }
    .th-av-7 { background: linear-gradient(135deg,#dc2626,#b91c1c); }

    /* ── Module icon badge ────────────────────────────────────── */
    .th-mod-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .th-mod-leave    { background: #dbeafe; color: #1d4ed8; }
    .th-mod-travel   { background: #fef3c7; color: #92400e; }
    .th-mod-locator  { background: #ede9fe; color: #5b21b6; }
    .th-mod-saln     { background: #ffe4e6; color: #9f1239; }
    .th-mod-pds      { background: #d1fae5; color: #065f46; }
    .th-mod-employee { background: #dcfce7; color: #15803d; }
    .th-mod-dtr      { background: #f1f5f9; color: #334155; }
    .th-mod-default  { background: #f1f5f9; color: #475569; }

    .dark .th-mod-leave    { background: rgba(29,78,216,.18);  color: #93c5fd; }
    .dark .th-mod-travel   { background: rgba(146,64,14,.18);  color: #fcd34d; }
    .dark .th-mod-locator  { background: rgba(91,33,182,.18);  color: #c4b5fd; }
    .dark .th-mod-saln     { background: rgba(159,18,57,.18);  color: #fca5a5; }
    .dark .th-mod-pds      { background: rgba(6,95,70,.18);    color: #6ee7b7; }
    .dark .th-mod-employee { background: rgba(21,128,61,.18);  color: #86efac; }
    .dark .th-mod-dtr      { background: rgba(51,65,85,.25);   color: #94a3b8; }

    /* ── Entry body text ──────────────────────────────────────── */
    .th-body { flex: 1; min-width: 0; }

    .th-employee-name {
        font-size: .875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: .2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .th-employee-name { color: #f9fafb; }

    .th-description {
        font-size: .8rem;
        color: #6b7280;
        line-height: 1.4;
        margin-bottom: .45rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .dark .th-description { color: #9ca3af; }

    .th-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .4rem;
    }

    /* ── Status badge ─────────────────────────────────────────── */
    .th-badge {
        display: inline-flex;
        align-items: center;
        padding: .15rem .6rem;
        border-radius: 999px;
        font-size: .625rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .th-badge-pending    { background: #fef3c7; color: #92400e; }
    .th-badge-approved   { background: #d1fae5; color: #065f46; }
    .th-badge-rejected   { background: #ffe4e6; color: #9f1239; }
    .th-badge-filed      { background: #dbeafe; color: #1d4ed8; }
    .th-badge-uploaded   { background: #dbeafe; color: #1d4ed8; }
    .th-badge-submitted  { background: #dbeafe; color: #1d4ed8; }
    .th-badge-registered { background: #d1fae5; color: #065f46; }
    .th-badge-cancelled  { background: #f3f4f6; color: #4b5563; }
    .th-badge-default    { background: #f3f4f6; color: #4b5563; }

    .dark .th-badge-pending    { background: rgba(146,64,14,.2);  color: #fde68a; }
    .dark .th-badge-approved   { background: rgba(6,95,70,.2);    color: #6ee7b7; }
    .dark .th-badge-rejected   { background: rgba(159,18,57,.2);  color: #fca5a5; }
    .dark .th-badge-filed,
    .dark .th-badge-uploaded,
    .dark .th-badge-submitted  { background: rgba(29,78,216,.2);  color: #93c5fd; }
    .dark .th-badge-registered { background: rgba(6,95,70,.2);    color: #6ee7b7; }
    .dark .th-badge-cancelled  { background: rgba(75,85,99,.2);   color: #9ca3af; }

    /* ── Module chip ──────────────────────────────────────────── */
    .th-module-chip {
        font-size: .625rem;
        font-weight: 700;
        color: #6b7280;
        padding: .15rem .5rem;
        background: #f3f4f6;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .dark .th-module-chip {
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.1);
        color: #9ca3af;
    }

    /* ── Timestamp ────────────────────────────────────────────── */
    .th-time {
        font-size: .6875rem;
        color: #9ca3af;
        white-space: nowrap;
    }

    /* ── View link arrow ──────────────────────────────────────── */
    .th-view-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: #f3f4f6;
        color: #9ca3af;
        flex-shrink: 0;
        align-self: center;
        transition: all .2s;
    }

    .th-entry:hover .th-view-arrow {
        background: #059669;
        color: #fff;
    }

    .dark .th-view-arrow { background: rgba(255,255,255,.07); color: #6b7280; }

    /* ── Empty state ──────────────────────────────────────────── */
    .th-empty {
        text-align: center;
        padding: 4rem 2rem;
        color: #9ca3af;
    }

    /* ── Pagination wrapper ───────────────────────────────────── */
    .th-pagination {
        margin-top: 2rem;
    }

    /* ── Fade-in animation ────────────────────────────────────── */
    @keyframes th-in {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .th-in { animation: th-in .3s ease-out backwards; }

    @media (max-width: 640px) {
        .th-timeline { padding-left: 1.5rem; }
        .th-entry { flex-wrap: wrap; }
    }
</style>

<div class="th-root" x-data>

    {{-- ── Stats bar ─────────────────────────────────────────────────── --}}
    <div class="th-stats th-in">

        {{-- Today's total --}}
        <span class="th-stat-pill th-stat-default">
            <x-heroicon-o-clock class="w-3.5 h-3.5" />
            Today: {{ $todayCount }}
        </span>

        @foreach($stats as $module => $count)
            @php
                $slug = strtolower($module);
                $cssClass = match($slug) {
                    'leave'    => 'th-stat-leave',
                    'travel'   => 'th-stat-travel',
                    'locator'  => 'th-stat-locator',
                    'saln'     => 'th-stat-saln',
                    'pds'      => 'th-stat-pds',
                    'employee' => 'th-stat-employee',
                    'dtr'      => 'th-stat-dtr',
                    default    => 'th-stat-default',
                };
                $icon = \App\Models\TransactionHistory::moduleIcon($module);
            @endphp
            <span class="th-stat-pill {{ $cssClass }}">
                <x-dynamic-component :component="$icon" class="w-3.5 h-3.5" />
                {{ $module }}: {{ $count }}
            </span>
        @endforeach

    </div>

    {{-- ── Timeline ────────────────────────────────────────────────────── --}}
    @if($grouped->isEmpty())
        <div class="th-empty">
            <x-heroicon-o-clock class="w-8 h-8 mx-auto mb-3 opacity-30" />
            <p class="font-bold text-lg mb-1">No transactions logged yet</p>
            <p class="text-sm">Activities will appear here as employees use HRMS modules.</p>
        </div>
    @else

        @foreach($grouped as $date => $entries)

            {{-- Date divider --}}
            <div class="th-date-group th-in">
                <span class="th-date-label">
                    @php
                        $d = \Carbon\Carbon::parse($date)->setTimezone('Asia/Manila');
                        if ($d->isToday())        { echo 'Today'; }
                        elseif ($d->isYesterday()) { echo 'Yesterday'; }
                        else                       { echo $d->format('l, F j, Y'); }
                    @endphp
                </span>
                <div class="th-date-line"></div>
                <span class="th-time">{{ $entries->count() }} {{ Str::plural('entry', $entries->count()) }}</span>
            </div>

            {{-- Entries for this date --}}
            <div class="th-timeline">
                @foreach($entries as $idx => $tx)
                    @php
                        $modSlug   = strtolower($tx->module);
                        $modCss    = match($modSlug) {
                            'leave'    => 'th-mod-leave',
                            'travel'   => 'th-mod-travel',
                            'locator'  => 'th-mod-locator',
                            'saln'     => 'th-mod-saln',
                            'pds'      => 'th-mod-pds',
                            'employee' => 'th-mod-employee',
                            'dtr'      => 'th-mod-dtr',
                            default    => 'th-mod-default',
                        };
                        $statusCss = 'th-badge-' . strtolower($tx->status);
                        if (!in_array(strtolower($tx->status), ['pending','approved','rejected','filed','uploaded','submitted','registered','cancelled'])) {
                            $statusCss = 'th-badge-default';
                        }
                        $avClass   = 'th-av-' . (abs(crc32($tx->employee_name)) % 8);
                        $icon      = $tx->resolved_icon;
                        $viewUrl   = route('filament.hrms.resources.transaction-histories.view', $tx->id);
                        $recordUrl = $tx->record_url;
                    @endphp

                    {{-- Entry card links to detail view --}}
                    <a href="{{ $viewUrl }}"
                       class="th-entry th-in"
                       style="animation-delay: {{ 0.04 * $idx }}s"
                       title="View details for this transaction">

                        {{-- Avatar --}}
                        <div class="th-avatar {{ $avClass }}">
                            @if($tx->user?->profile_photo_url)
                                <img src="{{ $tx->user->profile_photo_url }}" alt="{{ $tx->employee_name }}">
                            @else
                                {{ $tx->initials }}
                            @endif
                        </div>

                        {{-- Module icon --}}
                        <div class="th-mod-icon {{ $modCss }}">
                            <x-dynamic-component :component="$icon" class="w-4 h-4" />
                        </div>

                        {{-- Body --}}
                        <div class="th-body">
                            <div class="th-employee-name">{{ $tx->employee_name }}</div>
                            <div class="th-description">{{ $tx->description }}</div>
                            <div class="th-meta">
                                <span class="th-badge {{ $statusCss }}">{{ ucfirst($tx->status) }}</span>
                                <span class="th-module-chip">{{ $tx->module }}</span>
                                <span class="th-time">
                                    {{ $tx->created_at->setTimezone('Asia/Manila')->format('g:i A') }}
                                </span>
                                @if($recordUrl)
                                    <a href="{{ $recordUrl }}"
                                       target="_blank"
                                       onclick="event.stopPropagation()"
                                       class="th-time underline hover:text-emerald-600 transition-colors"
                                       title="Open original record">
                                        View Record ↗
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Caret --}}
                        <div class="th-view-arrow">
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                        </div>

                    </a>
                @endforeach
            </div>

        @endforeach

        {{-- Pagination --}}
        <div class="th-pagination">
            {{ $transactions->links() }}
        </div>

    @endif

</div>

</x-filament-panels::page>
