{{-- Main wrapper for the SALN interactive form --}}
<div class="saln-form-container">
    @include('filament.resources.saln-resource.form-pages.page-1')
    @include('filament.resources.saln-resource.form-pages.page-2')
</div>

<style>
    .saln-form-container {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        color: #000;
        max-width: 100%;
        margin: 0 auto;
    }

    .saln-form-page {
        background: #fff;
        border: 2px solid #000;
        padding: 20px 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .saln-form-title {
        font-weight: bold;
        font-size: 13pt;
        text-align: center;
        margin: 5px 0;
        text-decoration: underline;
        letter-spacing: 0.3px;
    }

    .saln-header-date {
        text-align: right;
        font-size: 7pt;
        margin-bottom: 5px;
        line-height: 1.1;
    }

    .saln-required-by {
        text-align: center;
        font-size: 8pt;
        margin-bottom: 8px;
    }

    .saln-section-header {
        font-weight: bold;
        text-align: center;
        text-decoration: underline;
        margin: 12px 0 5px 0;
        font-size: 9.5pt;
    }

    .saln-section-subtitle {
        text-align: center;
        font-style: italic;
        margin-bottom: 6px;
        font-size: 8pt;
    }

    .saln-subsection-title {
        font-size: 8.5pt;
        font-weight: bold;
        margin: 8px 0 3px 0;
    }

    .saln-subsection-desc {
        font-size: 7.5pt;
        font-style: italic;
    }

    .saln-table {
        width: 100%;
        border-collapse: collapse;
        margin: 5px 0;
    }

    .saln-table th,
    .saln-table td {
        border: 1px solid #000;
        padding: 3px 4px;
        text-align: center;
        vertical-align: middle;
        font-size: 7.5pt;
    }

    .saln-table th {
        background-color: #f0f0f0;
        font-weight: bold;
        font-size: 7pt;
        line-height: 1.1;
    }

    .saln-net-worth-box {
        border: 2px solid #000;
        padding: 6px;
        margin: 8px 0;
        text-align: center;
        font-weight: bold;
        font-size: 9.5pt;
        background-color: #f5f5f5;
    }

    .saln-signature-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 10px;
    }

    .saln-signature-box { flex: 1; }

    .saln-signature-line {
        border-bottom: 1.5px solid #000;
        margin: 20px 0 4px 0;
        min-height: 20px;
    }

    .saln-signature-label {
        text-align: center;
        font-size: 8pt;
        font-weight: bold;
    }

    .saln-certification {
        text-align: justify;
        font-size: 8pt;
        margin: 6px 0;
        line-height: 1.3;
    }

    .saln-note-section {
        border-top: 2px solid #000;
        padding-top: 5px;
        margin-top: 10px;
        font-size: 7pt;
        line-height: 1.2;
    }

    /* =============================================
       NATIVE INPUT STYLES - matching PDS exactly
       ============================================= */
    .saln-input {
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

    .saln-input:focus {
        outline: 2px solid #3b82f6 !important;
        outline-offset: -2px !important;
        background: #f0f9ff !important;
    }

    .saln-input::placeholder {
        color: #999 !important;
        font-style: italic !important;
    }

    textarea.saln-input {
        resize: vertical;
        min-height: 40px;
    }

    select.saln-input {
        cursor: pointer;
    }

    /* Checkbox label - matching PDS .checkbox-label */
    .saln-checkbox-label {
        display: inline-flex !important;
        align-items: center !important;
        cursor: pointer !important;
        font-size: 8pt !important;
        padding: 4px 8px !important;
        border: 2px solid #ccc !important;
        border-radius: 4px !important;
        background: #fff !important;
        transition: all 0.2s !important;
        gap: 6px !important;
    }

    .saln-checkbox-label:hover {
        background: #f0fdf4 !important;
        border-color: #22c55e !important;
    }

    .saln-checkbox-label:has(input[type="checkbox"]:checked) {
        background: #dcfce7 !important;
        border-color: #22c55e !important;
    }

    .saln-checkbox-input {
        width: 16px !important;
        height: 16px !important;
        cursor: pointer !important;
        accent-color: #22c55e !important;
        flex-shrink: 0 !important;
    }

    /* Add/Remove buttons - matching PDS .pds-btn-add/remove */
    .saln-btn-add,
    .saln-btn-remove {
        padding: 5px 10px;
        font-size: 8pt;
        cursor: pointer;
        border: 1px solid #ccc;
        background: #f9fafb;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .saln-btn-add:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .saln-btn-remove {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    .saln-btn-remove:hover {
        background: #fca5a5;
    }

    /* Repeater card */
    .saln-repeater-item {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 8px;
        background: #f9fafb;
        border-radius: 4px;
    }

    .saln-field-label {
        font-size: 7pt;
        display: block;
        margin-bottom: 2px;
        color: #555;
    }

    .saln-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 6px; }
    .saln-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 6px; }
    .saln-grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px; margin-bottom: 6px; }
    .saln-grid-2-1 { display: grid; grid-template-columns: 2fr 1fr; gap: 8px; margin-bottom: 6px; }
    .saln-grid-4-real { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 8px; margin-bottom: 6px; }

    @media print {
        .saln-form-page { page-break-after: always; border: none; box-shadow: none; }
        .saln-btn-add, .saln-btn-remove { display: none !important; }
    }
</style>
