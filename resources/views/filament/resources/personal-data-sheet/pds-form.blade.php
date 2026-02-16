{{-- Main wrapper for the PDS form view --}}
<div class="pds-form-container">
    {{-- Include all 4 pages of the form --}}
    @include('filament.resources.personal-data-sheet.form-pages.page-1')
    @include('filament.resources.personal-data-sheet.form-pages.page-2')
    @include('filament.resources.personal-data-sheet.form-pages.page-3')
    @include('filament.resources.personal-data-sheet.form-pages.page-4')
</div>

<style>
    /* Global PDS Form Styles */
    .pds-form-container {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        color: #000;
        max-width: 100%;
        margin: 0 auto;
    }

    .pds-form-page {
        background: #fff;
        border: 2px solid #000;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .pds-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .pds-table td,
    .pds-table th {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: middle;
        font-size: 8pt;
    }

    .pds-table th {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
    }

    .label-cell {
        background-color: #d9d9d9;
        font-weight: normal;
        font-size: 7pt;
        width: 25%;
    }

    .input-cell {
        padding: 2px !important;
    }

    .section-title {
        background-color: #969696;
        color: #fff;
        font-weight: bold;
        font-size: 9pt;
        padding: 5px;
        margin: 10px 0 5px 0;
    }

    .warning-text {
        font-size: 8pt;
        text-align: justify;
        margin: 5px 0;
        line-height: 1.3;
    }

    .header-row {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
        font-size: 7pt;
        padding: 4px;
    }

    /* ========================================= */
    /* CUSTOM INPUT STYLES */
    /* ========================================= */

    .pds-input {
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

    .pds-input:focus {
        outline: 2px solid #3b82f6 !important;
        outline-offset: -2px !important;
        background: #f0f9ff !important;
    }

    .pds-input::placeholder {
        color: #999 !important;
        font-style: italic !important;
    }

    /* Textarea specific */
    textarea.pds-input {
        resize: vertical;
        min-height: 40px;
    }

    /* Select dropdown */
    select.pds-input {
        cursor: pointer;
        padding-right: 20px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 4px center !important;
        background-size: 12px !important;
    }

    /* ========================================= */
    /* ENHANCED RADIO BUTTON STYLING */
    /* ========================================= */

    .radio-label {
        display: inline-flex !important;
        align-items: center !important;
        cursor: pointer !important;
        font-size: 8pt !important;
        margin-right: 15px !important;
        padding: 4px 8px !important;
        border: 2px solid #ccc !important;
        border-radius: 4px !important;
        background: #fff !important;
        transition: all 0.2s !important;
    }

    .radio-label:hover {
        background: #f0f9ff !important;
        border-color: #3b82f6 !important;
    }

    .radio-label input[type="radio"]:checked + span {
        font-weight: bold !important;
        color: #1e40af !important;
    }

    .radio-label:has(input[type="radio"]:checked) {
        background: #dbeafe !important;
        border-color: #3b82f6 !important;
        border-width: 2px !important;
    }

    .radio-input {
        width: 18px !important;
        height: 18px !important;
        margin-right: 6px !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        accent-color: #3b82f6 !important;
    }

    /* ========================================= */
    /* ENHANCED CHECKBOX STYLING */
    /* ========================================= */

    .checkbox-label {
        display: inline-flex !important;
        align-items: center !important;
        cursor: pointer !important;
        font-size: 8pt !important;
        padding: 4px 8px !important;
        border: 2px solid #ccc !important;
        border-radius: 4px !important;
        background: #fff !important;
        transition: all 0.2s !important;
    }

    .checkbox-label:hover {
        background: #f0fdf4 !important;
        border-color: #22c55e !important;
    }

    .checkbox-label input[type="checkbox"]:checked + span {
        font-weight: bold !important;
        color: #15803d !important;
    }

    .checkbox-label:has(input[type="checkbox"]:checked) {
        background: #dcfce7 !important;
        border-color: #22c55e !important;
        border-width: 2px !important;
    }

    .checkbox-input {
        width: 18px !important;
        height: 18px !important;
        margin-right: 6px !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        accent-color: #22c55e !important;
    }

    /* ========================================= */
    /* REGULAR LABEL AND INPUT STYLES */
    /* ========================================= */

    /* Regular labels (not radio/checkbox) */
    label:not(.radio-label):not(.checkbox-label) {
        cursor: pointer;
        font-size: 8pt;
        display: inline-flex;
        align-items: center;
        margin-right: 10px;
    }

    /* Regular checkboxes and radios (not in special labels) */
    input[type="checkbox"]:not(.checkbox-input),
    input[type="radio"]:not(.radio-input) {
        width: 16px !important;
        height: 16px !important;
        margin-right: 5px !important;
        cursor: pointer !important;
        accent-color: #3b82f6 !important;
    }

    /* Button styles */
    .pds-btn-add,
    .pds-btn-remove {
        padding: 5px 10px;
        font-size: 8pt;
        cursor: pointer;
        border: 1px solid #ccc;
        background: #f9fafb;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .pds-btn-add:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .pds-btn-remove {
        background: #fee;
        border-color: #fcc;
        color: #c00;
    }

    .pds-btn-remove:hover {
        background: #fcc;
        border-color: #faa;
    }

    /* ========================================= */
    /* OVERRIDE FILAMENT STYLES */
    /* ========================================= */

    /* Remove all Filament field wrappers */
    .pds-form-container .fi-fo-field-wrp {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }

    /* Hide Filament labels but keep our custom ones */
    .pds-form-container .fi-fo-field-wrp-label,
    .pds-form-container .fi-fo-field-wrp-hint {
        display: none !important;
    }

    /* Reset Filament input wrappers */
    .pds-form-container .fi-input-wrp,
    .pds-form-container .fi-select-wrp,
    .pds-form-container .fi-textarea-wrp {
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
    }

    /* Hide Filament select arrow */
    .pds-form-container .fi-select-wrp::after {
        display: none !important;
    }

    /* Repeater items styling */
    .pds-form-container .fi-fo-repeater {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
    }

    .pds-form-container .fi-fo-repeater-item {
        border: 1px solid #ccc !important;
        margin-bottom: 10px !important;
        padding: 10px !important;
        background: #f9fafb !important;
        border-radius: 4px !important;
    }

    /* Action buttons */
    .pds-form-container .fi-fo-repeater-item-actions,
    .pds-form-container .fi-ac-btn-group {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* DatePicker specific */
    .pds-form-container [x-ref="input"] {
        border: none !important;
        background: transparent !important;
    }

    /* Remove Filament focus rings */
    .pds-form-container .fi-input-wrp:focus-within,
    .pds-form-container .fi-select-wrp:focus-within,
    .pds-form-container .fi-textarea-wrp:focus-within {
        box-shadow: none !important;
        border: none !important;
    }

    /* Grid layouts for repeater items */
    .pds-form-container .grid {
        display: grid;
    }

    /* Print media query */
    @media print {
        .pds-form-page {
            page-break-after: always;
            border: none;
            box-shadow: none;
            margin: 0;
            padding: 20px;
        }

        .pds-btn-add,
        .pds-btn-remove {
            display: none !important;
        }

        .pds-input:focus {
            outline: none !important;
            background: transparent !important;
        }

        .radio-label,
        .checkbox-label {
            border: 1px solid #000 !important;
            background: transparent !important;
        }
    }
</style>
