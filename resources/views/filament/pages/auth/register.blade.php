<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register – HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── Strip ALL Filament page wrapper chrome ── */
        html,
        .fi-body,
        .fi-main,
        .fi-page,
        .fi-layout,
        .fi-simple-layout,
        .fi-simple-main,
        .fi-simple-main-ctn,
        .fi-simple-page,
        [class*="fi-simple"],
        [class*="fi-layout"],
        [class*="fi-page"] {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        :root {
            --g1: #14532d;
            --g2: #166534;
            --g3: #16a34a;
            --g4: #22c55e;
            --g5: #4ade80;
            --ink:    #0f172a;
            --ink2:   #374151;
            --ink3:   #6b7280;
            --bg:     #f0fdf4;
            --card:   #ffffff;
            --border: #d1fae5;
            --border2: #e5e7eb;
            --font:   'Sora', sans-serif;
            --mono:   'JetBrains Mono', monospace;
            --radius: 20px;
        }

        html, body { min-height: 100vh; font-family: var(--font); }

        body {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px 60px;
            position: relative;
            background: transparent;
        }

        /* Photo background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ asset("images/ATI XI.jpg") }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        /* Green overlay — solid enough to be the backdrop, no dot grid */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(22, 163, 74, 0.72) 0%,
                rgba(15, 118, 55, 0.68) 50%,
                rgba(5, 46, 22, 0.80) 100%
            );
            z-index: 0;
        }

        /* ── CARD ── */
        .reg-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 620px;
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(22,163,74,0.12),
                0 20px 60px rgba(0,0,0,0.10),
                0 4px 16px rgba(22,163,74,0.08);
            animation: cardRise 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes cardRise {
            from { opacity: 0; transform: translateY(40px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── TOP ACCENT BAR ── */
        .reg-top-bar {
            height: 5px;
            background: linear-gradient(90deg, var(--g1), var(--g3), var(--g4), var(--g5), var(--g3), var(--g1));
            background-size: 300% 100%;
            animation: barShimmer 4s linear infinite;
        }

        @keyframes barShimmer {
            0%   { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* ── HEADER ── */
        .reg-header {
            padding: 28px 40px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%);
        }

        .reg-logo-wrap { position: relative; flex-shrink: 0; }

        .reg-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            border-radius: 14px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            padding: 8px;
            display: block;
            box-shadow: 0 4px 16px rgba(22,163,74,0.20);
            animation: logoFloat 4s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-4px); }
        }

        .reg-logo-wrap::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 19px;
            border: 2px solid rgba(34,197,94,0.28);
            animation: ringPulse 3s ease-in-out infinite;
        }

        @keyframes ringPulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50%       { opacity: 1; transform: scale(1.04); }
        }

        .reg-header-text { flex: 1; }

        .reg-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border: 1px solid rgba(22,163,74,0.25);
            color: var(--g2);
            font-family: var(--mono);
            font-size: 0.6rem;
            font-weight: 500;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 5px;
        }

        .reg-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--g3);
            animation: blink 1.8s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        .reg-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .reg-subtitle {
            font-size: 0.78rem;
            color: var(--ink3);
            margin-top: 3px;
            font-weight: 400;
        }

        /* ── BODY ── */
        .reg-body { padding: 28px 40px 32px; }

        /* ── ALERTS ── */
        .alert-success {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.83rem;
            font-weight: 600;
            animation: cardRise 0.3s ease;
        }

        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.83rem;
            font-weight: 600;
            animation: cardRise 0.3s ease;
        }

        /* ── FILAMENT OVERRIDES ── */
        .fi-section,
        .fi-section-content,
        .fi-section-header,
        [class*="fi-section"] {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        .fi-section-header-heading,
        [class*="fi-section"] h3 {
            font-family: var(--mono) !important;
            font-size: 0.65rem !important;
            font-weight: 500 !important;
            letter-spacing: 0.12em !important;
            text-transform: uppercase !important;
            color: var(--g3) !important;
        }

        label,
        .fi-label,
        .fi-fo-field-wrp label,
        [class*="fi-fo"] label,
        [class*="fi-label"],
        .fi-fo-checkbox label,
        .fi-checkbox-label,
        .fi-fo-field-wrp-label {
            font-family: var(--font) !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            color: var(--ink2) !important;
        }

        label sup,
        .fi-fo-field-wrp-label-required-indicator { color: #ef4444 !important; }

        .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] {
            font-size: 0.73rem !important;
            color: var(--ink3) !important;
        }

        .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] {
            font-size: 0.73rem !important;
            color: #dc2626 !important;
        }

        .fi-input-wrp,
        .fi-fo-text-input-wrp,
        [class*="fi-input"] { border-radius: 10px !important; }

        .fi-input-wrp input,
        .fi-fo-field-wrp input[type="text"],
        .fi-fo-field-wrp input[type="email"],
        .fi-fo-field-wrp input[type="password"],
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="tel"],
        input[type="date"] {
            font-family: var(--font) !important;
            font-size: 0.875rem !important;
            border-radius: 10px !important;
            border: 1.5px solid var(--border2) !important;
            padding: 11px 16px !important;
            color: var(--ink) !important;
            background: #fafafa !important;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            width: 100%;
        }

        .fi-input-wrp input:focus, input:focus {
            outline: none !important;
            border-color: var(--g3) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15) !important;
        }

        .fi-fo-password .fi-input-wrp,
        .fi-input-wrp[data-has-suffix] {
            border-radius: 10px !important;
            border: 1.5px solid var(--border2) !important;
            background: #fafafa !important;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .fi-fo-password .fi-input-wrp input {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .fi-input-wrp button {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 14px 0 4px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .fi-input-wrp button svg,
        .fi-input-wrp button svg * {
            color: #9ca3af !important;
            stroke: #9ca3af !important;
            width: 18px !important;
            height: 18px !important;
        }

        .fi-input-wrp button:hover svg,
        .fi-input-wrp button:hover svg * {
            color: var(--g3) !important;
            stroke: var(--g3) !important;
        }

        /* ── ADDRESS PICKER ── */
        .address-block { margin-top: 4px; }

        .address-label-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .address-label-icon {
            width: 26px; height: 26px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .address-label-icon svg { width: 13px; height: 13px; color: var(--g3); }

        .address-label-text {
            font-family: var(--mono);
            font-size: 0.62rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--g3);
        }

        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .address-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .address-field label {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            color: var(--ink2) !important;
        }

        .address-field label .req { color: #ef4444; margin-left: 2px; }

        .address-select {
            width: 100%;
            padding: 11px 36px 11px 14px;
            border: 1.5px solid var(--border2);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 0.875rem;
            color: var(--ink);
            background-color: #fafafa;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .address-select:focus {
            outline: none;
            border-color: var(--g3);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
        }

        .address-select:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .address-select.loading {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' stroke='%2316a34a' stroke-width='2' opacity='0.25'/%3E%3Cpath fill='%2316a34a' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z' opacity='0.75'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='0.8s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
            background-size: 18px;
        }

        .address-error {
            font-size: 0.73rem;
            color: #dc2626;
            display: none;
            margin-top: 2px;
        }

        .address-error.visible { display: block; }

        /* ── REGISTER BUTTON ── */
        .btn-register {
            width: 100%;
            margin-top: 24px;
            padding: 14px;
            background: linear-gradient(135deg, var(--g4), var(--g3), var(--g2));
            background-size: 200% 100%;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: var(--font);
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: transform 0.2s, box-shadow 0.2s, background-position 0.4s;
            box-shadow: 0 6px 24px rgba(22,163,74,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(22,163,74,0.45);
            background-position: right center;
        }

        .btn-register:active { transform: translateY(0); }
        .btn-register svg { width: 18px; height: 18px; }

        /* ── FOOTER ── */
        .reg-footer {
            padding: 18px 40px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
        }

        .reg-footer-text { font-size: 0.83rem; color: var(--ink3); }

        .reg-footer-link {
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--g3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s;
        }

        .reg-footer-link:hover { color: var(--g2); text-decoration: underline; }

        /* ── DARK MODE ── */
        @media (prefers-color-scheme: dark) {
            body { background: transparent; }

            body::after {
                background: linear-gradient(
                    135deg,
                    rgba(5, 46, 22, 0.88) 0%,
                    rgba(10, 26, 15, 0.85) 50%,
                    rgba(3, 15, 7, 0.92) 100%
                );
            }

            .reg-card {
                background: #0a1a0f;
                box-shadow:
                    0 0 0 1px rgba(34,197,94,0.10),
                    0 20px 60px rgba(0,0,0,0.5);
            }

            .reg-header {
                background: linear-gradient(135deg, #0d2218 0%, #0a1a0f 60%);
                border-bottom-color: #1a3a22;
            }

            .reg-logo {
                background: linear-gradient(135deg, #052e16, #14532d);
                box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            }

            .reg-badge {
                background: linear-gradient(135deg, #052e16, #14532d);
                border-color: rgba(34,197,94,0.2);
                color: #4ade80;
            }

            .reg-title    { color: #f0fdf4 !important; }
            .reg-subtitle { color: #6ee7b7 !important; }

            .reg-body { background: #0a1a0f; }

            label,
            .fi-label,
            .fi-fo-field-wrp label,
            [class*="fi-fo"] label,
            [class*="fi-label"],
            .fi-fo-checkbox label,
            .fi-checkbox-label,
            .fi-fo-field-wrp-label,
            .address-field label { color: #cbd5e1 !important; }

            .fi-section-header-heading,
            [class*="fi-section"] h3 { color: #4ade80 !important; }

            .address-label-text { color: #4ade80 !important; }
            .address-label-icon { background: linear-gradient(135deg, #052e16, #14532d); }
            .address-label-icon svg { color: #4ade80 !important; }

            .fi-input-wrp input,
            .fi-fo-field-wrp input[type="text"],
            .fi-fo-field-wrp input[type="email"],
            .fi-fo-field-wrp input[type="password"],
            input[type="email"],
            input[type="password"],
            input[type="text"],
            input[type="tel"],
            input[type="date"] {
                background: rgba(255,255,255,0.05) !important;
                border-color: rgba(255,255,255,0.09) !important;
                color: #f1f5f9 !important;
            }

            input::placeholder, textarea::placeholder {
                color: rgba(255,255,255,0.22) !important;
            }

            input:focus {
                background: rgba(255,255,255,0.08) !important;
                border-color: #22c55e !important;
            }

            .fi-fo-password .fi-input-wrp,
            .fi-input-wrp[data-has-suffix] {
                background: rgba(255,255,255,0.05) !important;
                border-color: rgba(255,255,255,0.09) !important;
            }

            .fi-input-wrp button svg,
            .fi-input-wrp button svg * {
                color: #6ee7b7 !important;
                stroke: #6ee7b7 !important;
            }

            .address-select {
                background-color: rgba(255,255,255,0.05);
                border-color: rgba(255,255,255,0.09);
                color: #f1f5f9;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%234ade80' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            }

            .address-select:focus {
                border-color: #22c55e;
                background-color: rgba(255,255,255,0.08);
                box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
            }

            .address-select:disabled {
                background-color: rgba(255,255,255,0.02);
                color: rgba(255,255,255,0.2);
            }

            select option { background-color: #0f2d1c; color: #f1f5f9; }

            input[type="checkbox"] { accent-color: #22c55e; }

            label sup,
            .fi-fo-field-wrp-label-required-indicator { color: #f87171 !important; }

            .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] { color: #6ee7b7 !important; }
            .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] { color: #f87171 !important; }

            .alert-success {
                background: rgba(5,46,22,0.6);
                border-color: #166534;
                color: #86efac;
            }

            .alert-error {
                background: rgba(59,7,7,0.6);
                border-color: #991b1b;
                color: #fca5a5;
            }

            .reg-footer {
                background: linear-gradient(135deg, #0d2218, #0a1a0f);
                border-top-color: #1a3a22;
            }

            .reg-footer-text { color: #6ee7b7; }
            .reg-footer-link { color: #4ade80; }
            .reg-footer-link:hover { color: #86efac; }
        }
    </style>
    @livewireStyles
</head>
<body>

<div>
    <div class="reg-card">

        <div class="reg-top-bar"></div>

        {{-- Header --}}
        <div class="reg-header">
            <div class="reg-logo-wrap">
                <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo" class="reg-logo">
            </div>
            <div class="reg-header-text">
                <div class="reg-badge">New Account</div>
                <h1 class="reg-title">Employee Registration</h1>
                <p class="reg-subtitle">Fill in the form below to create your HRMS account</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="reg-body">

            @if ($showSuccessMessage)
                <div class="alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $successMessage }}
                </div>
            @endif

            @if (session()->has('registration_error'))
                <div class="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ session('registration_error') }}
                </div>
            @endif

            <form wire:submit.prevent="register">

                {{ $this->form }}

                {{-- ── Address Picker (Alpine.js + PSGC API) ── --}}
                <div
                    class="address-block"
                    wire:ignore
                    x-data="psgcAddressPicker()"
                    x-init="init()"
                >
                    <div class="address-label-row">
                        <div class="address-label-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <span class="address-label-text">Address</span>
                    </div>

                    <div class="address-grid">

                        <div class="address-field">
                            <label for="pick_region">Region <span class="req">*</span></label>
                            <select id="pick_region" class="address-select" :class="{ loading: loadingRegions }" :disabled="loadingRegions" x-model="selectedRegion" @change="onRegionChange()">
                                <option value="">-- Select Region --</option>
                                <template x-for="r in regions" :key="r.code">
                                    <option :value="r.code" x-text="r.name"></option>
                                </template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.region }" x-text="errors.region"></span>
                        </div>

                        <div class="address-field">
                            <label for="pick_province">Province <span class="req">*</span></label>
                            <select id="pick_province" class="address-select" :class="{ loading: loadingProvinces }" :disabled="!selectedRegion || loadingProvinces" x-model="selectedProvince" @change="onProvinceChange()">
                                <option value="">-- Select Province --</option>
                                <template x-for="p in provinces" :key="p.code">
                                    <option :value="p.code" x-text="p.name"></option>
                                </template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.province }" x-text="errors.province"></span>
                        </div>

                        <div class="address-field">
                            <label for="pick_city">City / Municipality <span class="req">*</span></label>
                            <select id="pick_city" class="address-select" :class="{ loading: loadingCities }" :disabled="!selectedProvince || loadingCities" x-model="selectedCity" @change="onCityChange()">
                                <option value="">-- Select City --</option>
                                <template x-for="c in cities" :key="c.code">
                                    <option :value="c.code" x-text="c.name"></option>
                                </template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.city }" x-text="errors.city"></span>
                        </div>

                        <div class="address-field">
                            <label for="pick_barangay">Barangay <span class="req">*</span></label>
                            <select id="pick_barangay" class="address-select" :class="{ loading: loadingBarangays }" :disabled="!selectedCity || loadingBarangays" x-model="selectedBarangay" @change="onBarangayChange()">
                                <option value="">-- Select Barangay --</option>
                                <template x-for="b in barangays" :key="b.code">
                                    <option :value="b.code" x-text="b.name"></option>
                                </template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.barangay }" x-text="errors.barangay"></span>
                        </div>

                    </div>
                </div>
                {{-- ── End Address Picker ── --}}

                <button type="submit" class="btn-register">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                    Create Account
                </button>

            </form>
        </div>

        {{-- Footer --}}
        <div class="reg-footer">
            <span class="reg-footer-text">Already have an account?</span>
            <a href="{{ route('filament.hrms.auth.login') }}" class="reg-footer-link">
                Sign in
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>

    </div>

</div>{{-- /single root --}}

@livewireScripts

    <script>
    function psgcAddressPicker() {
        const BASE = 'https://psgc.gitlab.io/api';
        return {
            regions: [], provinces: [], cities: [], barangays: [],
            selectedRegion: '', selectedProvince: '', selectedCity: '', selectedBarangay: '', purokStreet: '',
            loadingRegions: false, loadingProvinces: false, loadingCities: false, loadingBarangays: false,
            errors: { region: '', province: '', city: '', barangay: '' },

            async fetchJSON(url) {
                const res = await fetch(url);
                if (!res.ok) throw new Error(`PSGC API error: ${res.status}`);
                return res.json();
            },

            sortByName(arr) { return arr.slice().sort((a, b) => a.name.localeCompare(b.name)); },

            pushToLivewire() {
                this.$wire.set('data.region_id',    this.selectedRegion,   false);
                this.$wire.set('data.province_id',  this.selectedProvince, false);
                this.$wire.set('data.city_id',      this.selectedCity,     false);
                this.$wire.set('data.barangay_id',  this.selectedBarangay, false);
                this.$wire.set('data.purok_street', this.purokStreet,      false);
            },

            async init() {
                this.loadingRegions = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/regions/`);
                    this.regions = this.sortByName(data);
                } catch (e) { console.error('Failed to load regions:', e); }
                finally { this.loadingRegions = false; }

                this.$nextTick(() => {
                    const form = this.$el.closest('form');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            if (!this.validate()) { e.preventDefault(); e.stopImmediatePropagation(); return false; }
                            this.pushToLivewire();
                        }, true);
                    }
                });
            },

            async onRegionChange() {
                this.selectedProvince = ''; this.selectedCity = ''; this.selectedBarangay = '';
                this.provinces = []; this.cities = []; this.barangays = []; this.errors.region = '';
                if (!this.selectedRegion) return;
                this.loadingProvinces = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/regions/${this.selectedRegion}/provinces/`);
                    this.provinces = this.sortByName(data);
                    if (this.provinces.length === 0) {
                        this.loadingCities = true;
                        const cities = await this.fetchJSON(`${BASE}/regions/${this.selectedRegion}/cities-municipalities/`);
                        this.cities = this.sortByName(cities);
                        this.selectedProvince = this.selectedRegion;
                        this.loadingCities = false;
                    }
                } catch (e) { console.error('Failed to load provinces:', e); }
                finally { this.loadingProvinces = false; }
            },

            async onProvinceChange() {
                this.selectedCity = ''; this.selectedBarangay = ''; this.cities = []; this.barangays = []; this.errors.province = '';
                if (!this.selectedProvince) return;
                this.loadingCities = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/provinces/${this.selectedProvince}/cities-municipalities/`);
                    this.cities = this.sortByName(data);
                } catch (e) { console.error('Failed to load cities:', e); }
                finally { this.loadingCities = false; }
            },

            async onCityChange() {
                this.selectedBarangay = ''; this.barangays = []; this.errors.city = '';
                if (!this.selectedCity) return;
                this.loadingBarangays = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/cities-municipalities/${this.selectedCity}/barangays/`);
                    this.barangays = this.sortByName(data);
                } catch (e) { console.error('Failed to load barangays:', e); }
                finally { this.loadingBarangays = false; }
            },

            onBarangayChange() { this.errors.barangay = ''; },

            validate() {
                let valid = true;
                if (!this.selectedRegion)   { this.errors.region   = 'Region is required.';               valid = false; }
                if (!this.selectedProvince) { this.errors.province  = 'Province is required.';            valid = false; }
                if (!this.selectedCity)     { this.errors.city      = 'City / Municipality is required.'; valid = false; }
                if (!this.selectedBarangay) { this.errors.barangay  = 'Barangay is required.';            valid = false; }
                return valid;
            },
        };
    }
    </script>

</body>
</html>
