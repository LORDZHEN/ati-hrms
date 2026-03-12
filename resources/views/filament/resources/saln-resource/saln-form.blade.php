{{-- Main wrapper for the SALN interactive form (2025 CSC Format) --}}
{{--
    $isReadOnly is shared by ViewSaln::mount() via view()->share('isReadOnly', true).
    Live totals use Alpine.store('saln') reading data-saln="*" attributes in real time.
--}}
@php $isReadOnly = $isReadOnly ?? false; @endphp

<div class="saln-form-container" @if($isReadOnly) data-readonly="true" @endif>
    @include('filament.resources.saln-resource.form-pages.page-1', ['isReadOnly' => $isReadOnly])
    @include('filament.resources.saln-resource.form-pages.page-2', ['isReadOnly' => $isReadOnly])
    @include('filament.resources.saln-resource.form-pages.page-3', ['isReadOnly' => $isReadOnly])
    @include('filament.resources.saln-resource.form-pages.page-4', ['isReadOnly' => $isReadOnly])
</div>

{{-- ── ALPINE LIVE TOTALS STORE ─────────────────────────────────────────── --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('saln', {
        totalAssets: 0,
        totalLiabilities: 0,
        netWorth: 0,

        fmt(val) {
            return '₱' + val.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        recalculate() {
            let assets = 0;
            let liabilities = 0;

            document.querySelectorAll('[data-saln="real-fmv"]').forEach(el => {
                assets += parseFloat(el.value) || 0;
            });
            document.querySelectorAll('[data-saln="personal-cost"]').forEach(el => {
                assets += parseFloat(el.value) || 0;
            });
            document.querySelectorAll('[data-saln="liability-balance"]').forEach(el => {
                liabilities += parseFloat(el.value) || 0;
            });

            this.totalAssets      = assets;
            this.totalLiabilities = liabilities;
            this.netWorth         = assets - liabilities;
        }
    });
});

document.addEventListener('livewire:update', () => {
    setTimeout(() => {
        if (typeof Alpine !== 'undefined' && Alpine.store('saln')) {
            Alpine.store('saln').recalculate();
        }
    }, 80);
});
</script>

<style>
/* ── READ-ONLY ── */
.saln-form-container[data-readonly="true"] .saln-input {
    background:#f5f5f5!important; cursor:not-allowed!important; color:#333!important;
}
.saln-form-container[data-readonly="true"] .saln-input:focus {
    outline:none!important; background:#f5f5f5!important;
}
.saln-form-container[data-readonly="true"] .saln-checkbox-input,
.saln-form-container[data-readonly="true"] input[type="checkbox"] {
    cursor:not-allowed!important; pointer-events:none!important;
}
.saln-form-container[data-readonly="true"] .saln-checkbox-label {
    cursor:not-allowed!important; pointer-events:none!important; opacity:0.8!important;
}

/* ── BASE ── */
.saln-form-container {
    font-family:Arial,sans-serif; font-size:9pt; line-height:1.2; color:#000; max-width:100%;
}
.saln-form-page {
    background:#fff; border:2px solid #000; padding:14px 18px;
    margin-bottom:28px; box-shadow:0 2px 6px rgba(0,0,0,.1);
}
.saln-annex-label { font-size:14pt; font-weight:bold; }
.saln-form-title {
    font-weight:bold; font-size:12pt; text-align:center;
    text-decoration:underline; margin:4px 0;
}
.saln-form-subtitle { text-align:center; font-size:8pt; margin-bottom:6px; }
.saln-header-ref { font-size:7pt; line-height:1.3; }

/* ── SECTION HEADERS ── */
.saln-section-header {
    font-weight:bold; text-align:center; text-decoration:underline;
    margin:8px 0 3px; font-size:9pt;
}
.saln-section-subtitle { text-align:center; font-style:italic; margin-bottom:4px; font-size:7.5pt; }
.saln-subsection-title { font-size:8.5pt; font-weight:bold; margin:6px 0 3px; }
.saln-subsection-desc  { font-size:7.5pt; font-style:italic; }

/* ── TABLES ── */
.saln-table { width:100%; border-collapse:collapse; margin:3px 0; }
.saln-table th, .saln-table td {
    border:1px solid #000; padding:2px 3px;
    text-align:center; vertical-align:middle; font-size:7.5pt; line-height:1.2;
}
.saln-table th { background:#e8e8e8; font-weight:bold; font-size:7pt; }
.saln-table td.la { text-align:left; }
.saln-table tr.dr  { height:18px; }
.saln-table tr.sub-r { background:#f0f0f0; font-weight:bold; }

/* ── LIVE TOTALS ── */
.saln-live-total-bar {
    display:flex; justify-content:flex-end; align-items:center; gap:10px;
    font-weight:bold; font-size:9pt; border-top:2px solid #000;
    padding:4px 8px; background:#f9fafb;
}
.saln-live-total-bar .total-value {
    min-width:160px; text-align:right; color:#15803d; font-size:10pt;
    font-variant-numeric:tabular-nums;
}
.saln-net-worth-live {
    border:2px solid #000; padding:6px 12px; margin:6px 0;
    display:flex; justify-content:center; align-items:center; gap:10px;
    background:#f5f5f5; font-weight:bold; font-size:9.5pt;
}
.saln-net-worth-live .nw-value {
    font-weight:bold; font-size:12pt; color:#15803d;
    min-width:180px; text-align:right; font-variant-numeric:tabular-nums;
}
.saln-net-worth-live .nw-value.negative { color:#dc2626; }
.saln-liab-subtotal {
    display:flex; justify-content:flex-end; align-items:center; gap:10px;
    font-weight:bold; font-size:9pt; border-top:1px solid #000;
    padding:3px 8px; background:#fef9c3;
}
.saln-liab-subtotal .liab-value {
    min-width:160px; text-align:right; color:#b91c1c; font-size:10pt;
}

/* ── PINFO ── */
.saln-pinfo-label {
    font-weight:bold; font-size:8pt; white-space:nowrap;
    padding:2px 5px; vertical-align:bottom;
}
.saln-pinfo-val { border-bottom:1px solid #000; padding:1px 3px; vertical-align:bottom; }
.saln-name-hint { text-align:center; font-size:6pt; font-style:italic; padding-top:1px; }

/* ── COMPLIANCE ── */
.saln-compliance-block { border:1px solid #000; padding:5px 8px; margin-bottom:8px; font-size:8.5pt; }
.saln-compliance-block .comp-title { font-weight:bold; margin-bottom:4px; }
.saln-compliance-options { display:flex; gap:20px; flex-wrap:wrap; align-items:center; }
.saln-compliance-options label { display:inline-flex; align-items:center; gap:5px; cursor:pointer; font-size:8.5pt; }

/* ── MULTIPLE MARRIAGES ── */
.saln-mm-block { margin:5px 0; font-size:8.5pt; }
.saln-mm-block .mm-title { font-weight:bold; margin-bottom:3px; font-size:8pt; }
.saln-mm-line { border-bottom:1px solid #000; min-height:16px; padding:1px 4px; margin-bottom:3px; }

/* ── SIGNATURE ── */
.saln-signature-row { display:flex; justify-content:space-between; gap:20px; margin-top:10px; }
.saln-signature-box { flex:1; }
.saln-signature-line { border-bottom:1.5px solid #000; margin:22px 0 3px; }
.saln-signature-label { text-align:center; font-size:7.5pt; font-weight:bold; }

/* ── CERTIFICATION ── */
.saln-certification { text-align:justify; font-size:8pt; margin:5px 0; line-height:1.4; }

/* ── NOTES ── */
.saln-note-section { border-top:2px solid #000; padding-top:4px; margin-top:8px; font-size:7pt; line-height:1.2; }
.saln-footnotes { border-top:1px solid #ccc; padding-top:4px; margin-top:5px; font-size:6.5pt; font-style:italic; line-height:1.3; }

/* ── INPUT ── */
.saln-input {
    border:none!important; background:transparent!important;
    font-size:8pt!important; padding:1px 3px!important; width:100%!important;
    box-shadow:none!important; border-radius:0!important;
    font-family:Arial,sans-serif!important; color:#000!important;
}
.saln-input:focus { outline:2px solid #3b82f6!important; background:#eff6ff!important; }
.saln-input::placeholder { color:#999!important; font-style:italic!important; }
textarea.saln-input { resize:vertical; min-height:36px; }

/* ── CHECKBOX ── */
.saln-checkbox-label {
    display:inline-flex!important; align-items:center!important;
    cursor:pointer!important; font-size:8.5pt!important; gap:5px!important;
}
.saln-checkbox-input {
    width:14px!important; height:14px!important; cursor:pointer!important;
    accent-color:#000!important; flex-shrink:0!important;
}

/* ── BUTTONS ── */
.saln-btn-add, .saln-btn-remove {
    padding:4px 10px; font-size:8pt; cursor:pointer;
    border:1px solid #ccc; background:#f9fafb; border-radius:3px; margin-top:4px;
}
.saln-btn-add:hover { background:#e5e7eb; }
.saln-btn-remove { background:#fee2e2; border-color:#fca5a5; color:#dc2626; }
.saln-btn-remove:hover { background:#fca5a5; }

/* ── REPEATER ── */
.saln-repeater-item {
    border:1px solid #ccc; padding:8px 10px; margin-bottom:6px;
    background:#fafafa; border-radius:3px;
}
.saln-field-label { font-size:7pt; display:block; margin-bottom:2px; color:#555; }

/* ── GRIDS ── */
.saln-grid-2     { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:5px; }
.saln-grid-3     { display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-bottom:5px; }
.saln-grid-4     { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:8px; margin-bottom:5px; }
.saln-grid-2-1   { display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:5px; }
.saln-grid-4-real{ display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:8px; margin-bottom:5px; }

@media print {
    .saln-form-page { page-break-after:always; border:none; box-shadow:none; }
    .saln-btn-add, .saln-btn-remove { display:none!important; }
}
</style>
