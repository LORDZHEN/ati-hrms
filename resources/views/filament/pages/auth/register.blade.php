<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --g1: #14532d; --g2: #166534; --g3: #16a34a; --g4: #22c55e; --g5: #4ade80;
            --text-primary: #0f172a; --text-muted: #64748b;
            --border: #e2e8f0; --white: #ffffff; --surface: #f8fafc;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --radius: 22px; --radius-input: 10px;
        }

        html {
            font-family: var(--font);
            background-image: url('{{ asset("images/ATI XI.jpg") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
        }

        body { min-height: 100vh; font-family: var(--font); background: transparent !important; margin: 0; padding: 0; overflow-y: auto; }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(150deg, rgba(20,83,45,0.82) 0%, rgba(22,163,74,0.72) 40%, rgba(5,46,22,0.88) 100%);
            z-index: 0;
            pointer-events: none;
        }

        /* Nuke Filament wrappers */
        .fi-body, .fi-layout, .fi-layout-simple, .fi-simple-layout,
        .fi-simple-main, .fi-simple-main-ctn, .fi-simple-page,
        .fi-page, .fi-main, [class^="fi-simple"], [class^="fi-layout"], [class^="fi-page"],
        body > div, body > div > div, body > div > main, body > div > div > main {
            all: unset !important;
            display: block !important;
        }

        .reg-root {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 20px 48px;
        }

        .reg-card {
            position: relative;
            width: 100%;
            max-width: 660px;
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.12), 0 24px 64px rgba(0,0,0,0.35), 0 6px 20px rgba(0,0,0,0.20);
            animation: cardIn 0.6s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .reg-top-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--g1), var(--g3), var(--g4), var(--g5), var(--g3), var(--g1));
            background-size: 300% 100%;
            animation: barShimmer 4s linear infinite;
        }

        @keyframes barShimmer {
            0%   { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        .reg-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 32px 18px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%);
        }

        .reg-logo-wrap {
            flex-shrink: 0;
            width: 110px; height: 110px;
            border-radius: 50%;
            background: #ffffff !important;
            border: 3px solid #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 0 6px rgba(255,255,255,0.18),
                0 6px 24px rgba(22,163,74,0.28);
        }

        .reg-logo { width: 92px; height: 92px; object-fit: contain; filter: brightness(1.05); }

        .reg-header-text { flex: 1; text-align: left; }

        .reg-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #dcfce7;
            border: 1px solid rgba(22,163,74,0.25);
            color: var(--g2);
            font-family: var(--mono);
            font-size: 0.58rem;
            font-weight: 500;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 999px;
            margin-bottom: 4px;
        }

        .reg-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--g3);
            animation: blink 1.8s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        .reg-title { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.4px; line-height: 1.2; }
        .reg-subtitle { font-size: 0.76rem; color: var(--text-muted); margin-top: 2px; }
        .reg-body { padding: 24px 32px 28px; }

        .alert-success, .alert-error {
            display: flex; align-items: flex-start; gap: 10px;
            border-radius: 10px; padding: 11px 14px; margin-bottom: 18px;
            font-size: 0.82rem; font-weight: 600;
        }

        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }

        .fi-section, .fi-section-content, .fi-section-header, [class*="fi-section"] {
            background: transparent !important; box-shadow: none !important; border: none !important;
        }

        .fi-section-header-heading, [class*="fi-section"] h3 {
            font-family: var(--mono) !important; font-size: 0.62rem !important;
            font-weight: 500 !important; letter-spacing: 0.12em !important;
            text-transform: uppercase !important; color: var(--g3) !important;
        }

        label, .fi-label, .fi-fo-field-wrp label, [class*="fi-fo"] label,
        [class*="fi-label"], .fi-fo-checkbox label, .fi-checkbox-label,
        .fi-fo-field-wrp-label, .address-field label {
            font-family: var(--font) !important; font-size: 0.8rem !important;
            font-weight: 600 !important; color: var(--text-primary) !important;
        }

        label sup, .fi-fo-field-wrp-label-required-indicator { color: #ef4444 !important; }
        .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] { font-size: 0.72rem !important; color: var(--text-muted) !important; }
        .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] { font-size: 0.72rem !important; color: #dc2626 !important; }

        .fi-input-wrp, .fi-fo-text-input-wrp, [class*="fi-input"] { border-radius: var(--radius-input) !important; }

        .fi-input-wrp input, .fi-fo-field-wrp input[type="text"],
        .fi-fo-field-wrp input[type="email"], .fi-fo-field-wrp input[type="password"],
        input[type="email"], input[type="password"], input[type="text"],
        input[type="tel"], input[type="date"] {
            font-family: var(--font) !important; font-size: 0.875rem !important;
            border-radius: var(--radius-input) !important; border: 1.5px solid var(--border) !important;
            padding: 10px 14px !important; color: var(--text-primary) !important;
            background: var(--surface) !important;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            width: 100%;
        }

        .fi-input-wrp input:focus, input:focus {
            outline: none !important; border-color: var(--g3) !important;
            background: var(--white) !important; box-shadow: 0 0 0 3px rgba(34,197,94,0.14) !important;
        }

        .fi-fo-password .fi-input-wrp, .fi-input-wrp[data-has-suffix] {
            border-radius: var(--radius-input) !important; border: 1.5px solid var(--border) !important;
            background: var(--surface) !important; display: flex; align-items: center; overflow: hidden;
        }

        .fi-fo-password .fi-input-wrp input { border: none !important; box-shadow: none !important; background: transparent !important; }

        .fi-input-wrp button { background: transparent !important; border: none !important; border-radius: 0 !important; padding: 0 12px 0 4px; cursor: pointer; flex-shrink: 0; }
        .fi-input-wrp button svg, .fi-input-wrp button svg * { color: #94a3b8 !important; stroke: #94a3b8 !important; width: 17px !important; height: 17px !important; }
        .fi-input-wrp button:hover svg, .fi-input-wrp button:hover svg * { color: var(--g3) !important; stroke: var(--g3) !important; }

        .address-block { margin-top: 8px; }
        .address-label-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }

        .address-label-icon {
            width: 24px; height: 24px; background: #dcfce7; border-radius: 6px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .address-label-icon svg { width: 13px; height: 13px; color: var(--g3); }

        .address-label-text {
            font-family: var(--mono); font-size: 0.6rem; font-weight: 500;
            letter-spacing: 0.12em; text-transform: uppercase; color: var(--g3);
        }

        .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .address-field { display: flex; flex-direction: column; gap: 4px; }
        .address-field label .req { color: #ef4444; margin-left: 2px; }

        .address-select {
            width: 100%; padding: 10px 32px 10px 12px;
            border: 1.5px solid var(--border); border-radius: var(--radius-input);
            font-family: var(--font); font-size: 0.875rem;
            color: var(--text-primary); background-color: var(--surface);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; background-size: 15px;
            appearance: none; cursor: pointer; transition: border-color 0.18s, box-shadow 0.18s;
        }

        .address-select:focus { outline: none; border-color: var(--g3); background-color: var(--white); box-shadow: 0 0 0 3px rgba(34,197,94,0.14); }
        .address-select:disabled { background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed; opacity: 0.7; }
        .address-select.loading { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' stroke='%2316a34a' stroke-width='2' opacity='0.25'/%3E%3Cpath fill='%2316a34a' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z' opacity='0.75'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='0.8s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E"); background-size: 17px; }
        .address-error { font-size: 0.72rem; color: #dc2626; display: none; margin-top: 1px; }
        .address-error.visible { display: block; }

        .btn-register {
            width: 100%; margin-top: 20px; padding: 13px;
            background: linear-gradient(135deg, var(--g4), var(--g3), var(--g2));
            background-size: 200% 100%; color: #fff; border: none;
            border-radius: var(--radius-input); font-family: var(--font);
            font-size: 0.9375rem; font-weight: 700; cursor: pointer; letter-spacing: 0.3px;
            transition: transform 0.2s, box-shadow 0.2s, background-position 0.4s;
            box-shadow: 0 5px 18px rgba(22,163,74,0.38);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(22,163,74,0.48); background-position: right center; }
        .btn-register:active { transform: translateY(0); }
        .btn-register svg { width: 17px; height: 17px; }

        .reg-footer {
            padding: 14px 32px 18px; border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center; gap: 6px;
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
        }

        .reg-footer-text { font-size: 0.82rem; color: var(--text-muted); }

        .reg-footer-link {
            font-size: 0.82rem; font-weight: 700; color: var(--g3);
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: color 0.15s;
        }

        .reg-footer-link:hover { color: var(--g2); text-decoration: underline; }

        .dark .reg-card { background: #0b1a12; box-shadow: 0 0 0 1px rgba(34,197,94,0.10), 0 24px 64px rgba(0,0,0,0.55); }
        .dark .reg-header { background: linear-gradient(135deg, #0d2218, #0b1a12); border-bottom-color: #1a3a22; }
        .dark .reg-logo-wrap { background: #ffffff !important; border-color: rgba(34,197,94,0.30); box-shadow: 0 0 0 5px rgba(255,255,255,0.10), 0 4px 20px rgba(0,0,0,0.30); }
        .dark .reg-badge { background: #052e16; border-color: rgba(34,197,94,0.20); color: #4ade80; }
        .dark .reg-title { color: #f0fdf4 !important; }
        .dark .reg-subtitle { color: #86efac !important; }
        .dark .reg-body { background: #0b1a12; }
        .dark .reg-footer { background: linear-gradient(135deg, #0d2218, #0b1a12); border-top-color: #1a3a22; }
        .dark .reg-footer-text { color: #6ee7b7; }
        .dark .reg-footer-link { color: #4ade80; }
        .dark label, .dark .fi-label, .dark .fi-fo-field-wrp label, .dark [class*="fi-fo"] label,
        .dark [class*="fi-label"], .dark .fi-fo-field-wrp-label, .dark .address-field label { color: #d1fae5 !important; }
        .dark .fi-input-wrp input, .dark input[type="email"], .dark input[type="password"], .dark input[type="text"] { background: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.09) !important; color: #f0fdf4 !important; }
        .dark .fi-fo-password .fi-input-wrp, .dark .fi-input-wrp[data-has-suffix] { background: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.09) !important; }
        .dark .fi-input-wrp button svg, .dark .fi-input-wrp button svg * { color: #4ade80 !important; stroke: #4ade80 !important; }
        .dark .address-select { background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.09); color: #f0fdf4; }
        .dark .address-label-icon { background: #052e16; }
        .dark .address-label-text { color: #4ade80 !important; }
        .dark .reg-badge { background: #052e16; }

        /* ── LIGHT MODE: force when Filament sets .light on <html> ── */
        .light .reg-card { background: #ffffff !important; box-shadow: 0 0 0 1px rgba(22,163,74,0.12), 0 24px 64px rgba(0,0,0,0.10) !important; }
        .light .reg-header { background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%) !important; border-bottom-color: #e2e8f0 !important; }
        .light .reg-logo-wrap { background: #ffffff !important; border-color: #dcfce7 !important; }
        .light .reg-badge { background: #dcfce7 !important; border-color: rgba(22,163,74,0.25) !important; color: #166534 !important; }
        .light .reg-title { color: #0f172a !important; }
        .light .reg-subtitle { color: #64748b !important; }
        .light .reg-body { background: #ffffff !important; }
        .light .reg-footer { background: linear-gradient(135deg, #f0fdf4, #ffffff) !important; border-top-color: #e2e8f0 !important; }
        .light .reg-footer-text { color: #64748b !important; }
        .light .reg-footer-link { color: #16a34a !important; }
        .light label,
        .light .fi-label,
        .light .fi-fo-field-wrp label,
        .light [class*="fi-fo"] label,
        .light [class*="fi-label"],
        .light .fi-fo-checkbox label,
        .light .fi-fo-field-wrp-label,
        .light .address-field label { color: #0f172a !important; }
        .light .fi-section-header-heading, .light [class*="fi-section"] h3 { color: #16a34a !important; }
        .light .address-label-text { color: #16a34a !important; }
        .light .address-label-icon { background: #dcfce7 !important; }
        .light .fi-input-wrp input,
        .light input[type="email"],
        .light input[type="password"],
        .light input[type="text"],
        .light input[type="tel"],
        .light input[type="date"] {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .light input::placeholder { color: #94a3b8 !important; }
        .light .fi-fo-password .fi-input-wrp,
        .light .fi-input-wrp[data-has-suffix] { background: #f8fafc !important; border-color: #e2e8f0 !important; }
        .light .fi-input-wrp button svg,
        .light .fi-input-wrp button svg * { color: #94a3b8 !important; stroke: #94a3b8 !important; }
        .light .address-select { background-color: #f8fafc !important; border-color: #e2e8f0 !important; color: #0f172a !important; }
        .light label sup, .light .fi-fo-field-wrp-label-required-indicator { color: #ef4444 !important; }
        .light .fi-fo-field-wrp-hint, .light .fi-hint { color: #64748b !important; }
        .light .fi-fo-field-wrp-error, .light .fi-error { color: #dc2626 !important; }
        .light .alert-success { background: #f0fdf4 !important; border-color: #86efac !important; color: #166534 !important; }
        .light .alert-error { background: #fef2f2 !important; border-color: #fca5a5 !important; color: #dc2626 !important; }
    </style>
    @livewireStyles
</head>
<body>

<div class="reg-root">
    <div class="reg-card">

        <div class="reg-top-bar"></div>

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

        <div class="reg-body">

            @if ($showSuccessMessage)
                <div class="alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $successMessage }}
                </div>
            @endif

            @if (session()->has('registration_error'))
                <div class="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ session('registration_error') }}
                </div>
            @endif

            <form wire:submit.prevent="register">

                {{ $this->form }}

                <div class="address-block" wire:ignore x-data="psgcAddressPicker()" x-init="init()">
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
                                <template x-for="r in regions" :key="r.code"><option :value="r.code" x-text="r.name"></option></template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.region }" x-text="errors.region"></span>
                        </div>
                        <div class="address-field">
                            <label for="pick_province">Province <span class="req">*</span></label>
                            <select id="pick_province" class="address-select" :class="{ loading: loadingProvinces }" :disabled="!selectedRegion || loadingProvinces" x-model="selectedProvince" @change="onProvinceChange()">
                                <option value="">-- Select Province --</option>
                                <template x-for="p in provinces" :key="p.code"><option :value="p.code" x-text="p.name"></option></template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.province }" x-text="errors.province"></span>
                        </div>
                        <div class="address-field">
                            <label for="pick_city">City / Municipality <span class="req">*</span></label>
                            <select id="pick_city" class="address-select" :class="{ loading: loadingCities }" :disabled="!selectedProvince || loadingCities" x-model="selectedCity" @change="onCityChange()">
                                <option value="">-- Select City --</option>
                                <template x-for="c in cities" :key="c.code"><option :value="c.code" x-text="c.name"></option></template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.city }" x-text="errors.city"></span>
                        </div>
                        <div class="address-field">
                            <label for="pick_barangay">Barangay <span class="req">*</span></label>
                            <select id="pick_barangay" class="address-select" :class="{ loading: loadingBarangays }" :disabled="!selectedCity || loadingBarangays" x-model="selectedBarangay" @change="onBarangayChange()">
                                <option value="">-- Select Barangay --</option>
                                <template x-for="b in barangays" :key="b.code"><option :value="b.code" x-text="b.name"></option></template>
                            </select>
                            <span class="address-error" :class="{ visible: errors.barangay }" x-text="errors.barangay"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                    Create Account
                </button>

            </form>
        </div>

        <div class="reg-footer">
            <span class="reg-footer-text">Already have an account?</span>
            <a href="{{ route('filament.hrms.auth.login') }}" class="reg-footer-link">
                Sign in
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>

    </div>
</div>

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
            if (!res.ok) throw new Error('PSGC API error: ' + res.status);
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
                const data = await this.fetchJSON(BASE + '/regions/');
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
                const data = await this.fetchJSON(BASE + '/regions/' + this.selectedRegion + '/provinces/');
                this.provinces = this.sortByName(data);
                if (this.provinces.length === 0) {
                    this.loadingCities = true;
                    const cities = await this.fetchJSON(BASE + '/regions/' + this.selectedRegion + '/cities-municipalities/');
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
                const data = await this.fetchJSON(BASE + '/provinces/' + this.selectedProvince + '/cities-municipalities/');
                this.cities = this.sortByName(data);
            } catch (e) { console.error('Failed to load cities:', e); }
            finally { this.loadingCities = false; }
        },

        async onCityChange() {
            this.selectedBarangay = ''; this.barangays = []; this.errors.city = '';
            if (!this.selectedCity) return;
            this.loadingBarangays = true;
            try {
                const data = await this.fetchJSON(BASE + '/cities-municipalities/' + this.selectedCity + '/barangays/');
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
