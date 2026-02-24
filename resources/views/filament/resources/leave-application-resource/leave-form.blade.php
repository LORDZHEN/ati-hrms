{{-- Main wrapper for the Leave Application interactive form --}}
<div class="leave-form-container">
    @include('filament.resources.leave-application-resource.form-pages.form-page')
</div>

<style>
    /* =============================================
       LEAVE FORM - GLOBAL STYLES
       ============================================= */
    .leave-form-container {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        color: #000;
        max-width: 100%;
        margin: 0 auto;
    }

    .leave-form-page {
        background: #fff;
        border: 2px solid #000;
        padding: 8mm 10mm;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
    }

    /* Header */
    .leave-header {
        position: relative;
        margin-bottom: 3mm;
    }

    .cs-note {
        position: absolute;
        left: 0;
        top: 0;
        font-size: 7.5pt;
        line-height: 1.3;
    }

    .leave-header-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding-top: 3px;
    }

    .leave-logo {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        object-fit: contain;
    }

    .leave-agency-info {
        text-align: center;
    }

    .leave-agency-text {
        font-size: 8.5pt;
        line-height: 1.35;
    }

    .leave-form-title {
        font-size: 13pt;
        font-weight: bold;
        letter-spacing: 1.2px;
        margin-top: 3px;
    }

    /* Tables */
    .leave-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .leave-table td {
        border: 1pt solid #000;
        padding: 3px 5px;
        vertical-align: top;
        font-size: 8.5pt;
        line-height: 1.3;
    }

    /* Labels */
    .leave-label {
        font-weight: bold;
        font-size: 8pt;
    }

    .leave-value {
        font-weight: normal;
    }

    /* Section bands */
    .leave-section-band {
        background: #d9d9d9;
        font-weight: bold;
        font-size: 9pt;
        text-align: center;
        padding: 3px;
        letter-spacing: 0.3px;
    }

    /* =============================================
       STATIC CHECKBOX (Section 7.B — non-interactive)
       Used only for the print/admin recommendation area.
       ============================================= */
    .leave-checkbox {
        display: inline-block;
        width: 10pt;
        height: 10pt;
        border: 1.5pt solid #000;
        margin-right: 3px;
        vertical-align: middle;
        position: relative;
        background: #fff;
    }

    .leave-checkbox.checked {
        background-color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .leave-checkbox.checked::after {
        content: '✓';
        color: #fff !important;
        font-size: 8pt;
        font-weight: 900;
        position: absolute;
        top: -2px;
        left: 0.5px;
    }

    /* =============================================
       INTERACTIVE CHECKBOX (lf-checkbox)
       Used for all interactive selections in 6.A and 6.B.
       Visually identical to .leave-checkbox above so the
       form looks the same in both fill-up and print views.
       Alpine :class toggles lf-checkbox--checked on/off.
       ============================================= */
    .lf-checkbox {
        display: inline-block;
        flex-shrink: 0;
        width: 10pt;
        height: 10pt;
        border: 1.5pt solid #000;
        margin-right: 3px;
        vertical-align: middle;
        position: relative;
        background: #fff;
        cursor: pointer;
        /* Subtle hover feedback */
        transition: background 0.1s, border-color 0.1s;
    }

    .lf-checkbox:hover {
        border-color: #555;
        background: #f5f5f5;
    }

    /* Filled state — mirrors .leave-checkbox.checked exactly */
    .lf-checkbox--checked {
        background-color: #000 !important;
        border-color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .lf-checkbox--checked::after {
        content: '✓';
        color: #fff !important;
        font-size: 8pt;
        font-weight: 900;
        position: absolute;
        top: -2px;
        left: 0.5px;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* =============================================
       NATIVE INPUTS
       ============================================= */
    .leave-input {
        border: none !important;
        background: transparent !important;
        font-size: 8pt !important;
        padding: 2px 4px !important;
        width: 100% !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        font-family: Arial, sans-serif !important;
        color: #000 !important;
    }

    .leave-input:focus {
        outline: 2px solid #3b82f6 !important;
        outline-offset: -2px !important;
        background: #f0f9ff !important;
    }

    .leave-input::placeholder {
        color: #999 !important;
        font-style: italic !important;
    }

    textarea.leave-input {
        resize: vertical;
        min-height: 40px;
    }

    /* Underlines */
    .leave-underline {
        display: inline-block;
        border-bottom: 0.8pt solid #000;
        min-width: 45px;
        padding: 0 2px;
    }

    .leave-underline-full {
        display: block;
        border-bottom: 0.8pt solid #000;
        width: 100%;
        margin-top: 3px;
        min-height: 12px;
    }

    /* Leave types list */
    .leave-type-item {
        margin-bottom: 1.5px;
        font-size: 7.5pt;
        line-height: 1.3;
        display: flex;
        align-items: flex-start;
        gap: 3px;
    }

    /* Details sections */
    .leave-detail-section {
        margin-bottom: 6px;
        font-size: 7.5pt;
        line-height: 1.35;
    }

    .leave-detail-section em {
        font-style: italic;
    }

    /* Credits table */
    .leave-credits-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    .leave-credits-table td {
        border: 0.8pt solid #000;
        padding: 2px 3px;
        text-align: center;
        font-size: 7.5pt;
    }

    .leave-credits-table .label-cell {
        text-align: left;
        font-style: italic;
    }

    .leave-credits-table .header-cell {
        font-weight: bold;
        background: #e9e9e9;
    }

    /* Signatures */
    .leave-signature-area {
        text-align: center;
        margin-top: 10px;
    }

    .leave-signature-line {
        display: inline-block;
        border-bottom: 0.8pt solid #000;
        width: 140px;
        min-height: 12px;
    }

    .leave-signature-label {
        font-size: 6.5pt;
        margin-top: 1px;
        display: block;
    }

    /* Field label */
    .leave-field-label {
        font-size: 7pt;
        display: block;
        margin-bottom: 2px;
        color: #555;
    }

    /* Grid layouts */
    .leave-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 6px;
    }

    .leave-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        margin-bottom: 6px;
    }

    /* =============================================
       PRINT STYLES
       ============================================= */
    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        .leave-form-page {
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        /* Both static and interactive checked boxes must print filled */
        .leave-checkbox.checked,
        .lf-checkbox--checked {
            background-color: #000 !important;
            background: #000 !important;
            border: 1.5pt solid #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .leave-checkbox.checked::after,
        .lf-checkbox--checked::after {
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Hide hover effect on print */
        .lf-checkbox:hover {
            background: inherit;
            border-color: #000;
        }
    }
</style>
