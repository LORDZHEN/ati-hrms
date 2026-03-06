<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── CSS Variables ── */
        :root {
            --g1: #14532d;
            --g2: #166534;
            --g3: #16a34a;
            --g4: #22c55e;
            --g5: #4ade80;

            --text-primary: #0f172a;
            --text-muted:   #64748b;
            --border:       #e2e8f0;
            --white:        #ffffff;
            --surface:      #f8fafc;

            --radius-card:  22px;
            --radius-input: 10px;
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        html {
            height: 100%;
            font-family: var(--font);
            background-image: url('{{ asset("images/ATI XI.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        body {
            min-height: 100vh;
            height: 100%;
            font-family: var(--font);
            background: transparent;
            position: relative;
            margin: 0;
            padding: 0;
        }

        /* Green overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                150deg,
                rgba(20, 83, 45,  0.82) 0%,
                rgba(22, 163, 74, 0.72) 40%,
                rgba(5,  46,  22, 0.88) 100%
            );
            z-index: 0;
            pointer-events: none;
        }

        /* ── Force ALL Filament shell wrappers transparent and reset ── */
        .fi-body,
        .fi-layout,
        .fi-layout-simple,
        .fi-simple-layout,
        .fi-simple-main,
        .fi-simple-main-ctn,
        .fi-simple-page,
        .fi-page,
        .fi-main,
        [class^="fi-simple"],
        [class^="fi-layout"],
        [class^="fi-page"] {
            all: unset !important;
            display: block !important;
        }

        body > div,
        body > div > div,
        body > div > main,
        body > div > div > main {
            all: unset !important;
            display: block !important;
        }

        /* ── ROOT WRAPPER — fixed to viewport, guaranteed center ── */
        .login-root {
            position: fixed;
            inset: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
            overflow-y: auto;
        }

        /* ── CARD ── */
        .card {
            width: 100%;
            max-width: 820px;
            min-width: unset;
            background: var(--white);
            border-radius: var(--radius-card);
            display: grid;
            grid-template-columns: 300px 1fr;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.12),
                0 24px 60px rgba(0,0,0,0.35),
                0 6px 20px rgba(0,0,0,0.20);
            animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)   scale(1); }
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 44px 28px;
            overflow: hidden;
            background: linear-gradient(160deg, #0d3320, #14532d, #166534, #16a34a, #22c55e, #16a34a, #166534, #14532d);
            background-size: 400% 400%;
            animation: gradientShift 9s ease infinite;
        }

        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating orbs */
        .panel-left::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            top: -80px; left: -80px;
            animation: orb 9s ease-in-out infinite;
        }

        .panel-left::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -50px; right: -50px;
            animation: orb 7s ease-in-out 2s infinite reverse;
        }

        @keyframes orb {
            0%, 100% { transform: translate(0,0) scale(1); }
            50%       { transform: translate(14px,-18px) scale(1.07); }
        }

        .deco-ring {
            position: absolute;
            width: 160px; height: 160px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.10);
            bottom: 56px; left: -36px;
            animation: orb 11s ease-in-out infinite reverse;
        }

        .deco-ring-2 {
            position: absolute;
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.10);
            top: 36px; right: -18px;
            animation: orb 7s ease-in-out 1s infinite;
        }

        /* Logo wrapper with solid white circle */
        .seal-wrap {
            position: relative;
            z-index: 1;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow:
                0 0 0 6px rgba(255,255,255,0.20),
                0 8px 32px rgba(0,0,0,0.25);
            animation: sealIn 0.75s 0.15s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        /* Logo */
        .seal {
            position: relative;
            z-index: 1;
            width: 168px;
            height: 168px;
            object-fit: contain;
            border-radius: 50%;
            background: transparent;
            padding: 0;
            margin-bottom: 0;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.10)) brightness(1.05);
            animation: none;
        }

        @keyframes sealIn {
            from { opacity: 0; transform: scale(0.82); }
            to   { opacity: 1; transform: scale(1); }
        }

        .brand-name {
            position: relative; z-index: 1;
            font-size: 1.375rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.22);
        }

        .brand-org {
            position: relative; z-index: 1;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.82);
            text-align: center;
            margin-top: 6px;
            line-height: 1.6;
            font-weight: 400;
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            padding: 44px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: var(--white);
        }

        .welcome-eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.625rem;
            font-weight: 600;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: var(--g3);
            margin-bottom: 8px;
            width: 100%;
        }

        .welcome-eyebrow::before {
            content: '';
            display: block;
            width: 16px; height: 2px;
            background: var(--g4);
            border-radius: 2px;
        }

        .welcome-heading {
            font-size: 1.625rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            line-height: 1.15;
            width: 100%;
        }

        .welcome-sub {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 5px;
            margin-bottom: 22px;
            font-weight: 400;
            width: 100%;
        }

        /* Make the form full width */
        .panel-right form,
        .panel-right .forgot-wrap,
        .panel-right .register-line,
        .panel-right .form-divider,
        .panel-right .error-banner {
            width: 100%;
            text-align: left;
        }

        .panel-right .register-line {
            text-align: center;
        }

        /* ── Error banner ── */
        .error-banner {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 0.84rem;
            font-weight: 600;
        }

        /* ── Filament label overrides – light mode ── */
        label,
        .fi-label,
        .fi-fo-field-wrp label,
        [class*="fi-fo"] label,
        [class*="fi-label"],
        .fi-fo-checkbox label,
        .fi-checkbox-label,
        .fi-fo-field-wrp-label {
            font-family: var(--font) !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
        }

        label sup,
        .fi-fo-field-wrp-label-required-indicator { color: #ef4444 !important; }

        .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] {
            color: var(--text-muted) !important;
            font-size: 0.75rem !important;
        }

        .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] {
            color: #dc2626 !important;
            font-size: 0.75rem !important;
        }

        /* ── Input fields ── */
        .fi-fo-field-wrp,
        .fi-input-wrp,
        .fi-fo-text-input-wrp,
        [class*="fi-input"],
        [class*="fi-fo"] { border-radius: var(--radius-input) !important; }

        .fi-input-wrp input,
        .fi-fo-field-wrp input[type="text"],
        .fi-fo-field-wrp input[type="email"],
        .fi-fo-field-wrp input[type="password"],
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            border-radius: var(--radius-input) !important;
            border: 1.5px solid var(--border) !important;
            padding: 11px 16px !important;
            font-size: 0.875rem !important;
            font-family: var(--font) !important;
            color: var(--text-primary) !important;
            background: var(--surface) !important;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            width: 100%;
        }

        .fi-input-wrp input:focus,
        .fi-fo-field-wrp input:focus {
            outline: none !important;
            border-color: var(--g3) !important;
            background: var(--white) !important;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.16) !important;
        }

        /* Password wrapper */
        .fi-fo-password .fi-input-wrp,
        .fi-input-wrp[data-has-suffix] {
            border-radius: var(--radius-input) !important;
            border: 1.5px solid var(--border) !important;
            background: var(--surface) !important;
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
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .fi-input-wrp button svg,
        .fi-input-wrp button svg * {
            color: #94a3b8 !important;
            stroke: #94a3b8 !important;
            width: 19px !important;
            height: 19px !important;
        }

        .fi-input-wrp button:hover svg,
        .fi-input-wrp button:hover svg * {
            color: var(--g3) !important;
            stroke: var(--g3) !important;
        }

        /* ── Forgot password ── */
        .forgot-wrap {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 18px;
        }

        .forgot-wrap a {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--g3);
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-wrap a:hover { color: var(--g2); text-decoration: underline; }

        /* ── Login button ── */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--g4) 0%, var(--g3) 60%, var(--g2) 100%);
            background-size: 200% 100%;
            color: #fff;
            border: none;
            border-radius: var(--radius-input);
            font-size: 0.9375rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-position 0.4s ease;
            box-shadow: 0 4px 16px rgba(22,163,74,0.38);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(22,163,74,0.48);
            background-position: right center;
        }

        .btn-login:active { transform: translateY(0); }

        /* ── Register link ── */
        .register-line {
            text-align: center;
            margin-top: 16px;
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .register-line a {
            color: var(--g3);
            font-weight: 700;
            text-decoration: none;
        }

        .register-line a:hover { color: var(--g2); text-decoration: underline; }

        /* ── Divider ── */
        .form-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 20px 0;
        }

        /* ── Toast ── */
        .toast {
            position: fixed;
            top: 20px; right: 20px;
            background: var(--g3);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            font-size: 0.875rem;
            opacity: 0;
            transition: opacity 0.4s ease;
            max-width: 320px;
        }

        /* ── Responsive ── */
        @media (max-width: 760px) {
            .card {
                grid-template-columns: 1fr;
                max-width: 440px;
                min-width: unset;
            }

            .panel-left {
                padding: 32px 24px 26px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .seal { width: 110px; height: 110px; margin-bottom: 14px; }
            .brand-name { font-size: 1.25rem; }
            .brand-org  { font-size: 0.75rem; }
            .panel-right { padding: 32px 28px 40px; }
            .welcome-heading { font-size: 1.5rem; }
        }

        /* ══════════════════════════════════════
           DARK MODE  –  Filament class toggle
        ══════════════════════════════════════ */
        /* ── Dark mode: Filament .dark class on <html> ── */
        .dark .card {
            background: #0b1a12;
            box-shadow: 0 0 0 1px rgba(34,197,94,0.10), 0 32px 80px rgba(0,0,0,0.60);
        }

        .dark .panel-right          { background: #0b1a12; }
        .dark .welcome-eyebrow      { color: #4ade80; }
        .dark .welcome-eyebrow::before { background: #4ade80; }
        .dark .welcome-heading      { color: #f0fdf4 !important; }
        .dark .welcome-sub          { color: #86efac !important; }
        .dark .register-line        { color: #6ee7b7 !important; }
        .dark .register-line a      { color: #4ade80 !important; }
        .dark .forgot-wrap a        { color: #4ade80 !important; }

        .dark label,
        .dark .fi-label,
        .dark .fi-fo-field-wrp label,
        .dark [class*="fi-fo"] label,
        .dark [class*="fi-label"],
        .dark .fi-fo-checkbox label,
        .dark .fi-fo-field-wrp-label { color: #d1fae5 !important; }

        .dark .fi-input-wrp input,
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="text"] {
            background: rgba(255,255,255,0.05) !important;
            border-color: rgba(255,255,255,0.10) !important;
            color: #f0fdf4 !important;
        }

        .dark .fi-fo-password .fi-input-wrp,
        .dark .fi-input-wrp[data-has-suffix] {
            background: rgba(255,255,255,0.05) !important;
            border-color: rgba(255,255,255,0.10) !important;
        }

        .dark .fi-input-wrp button svg,
        .dark .fi-input-wrp button svg * { color: #4ade80 !important; stroke: #4ade80 !important; }

        /* ── LIGHT MODE: force when Filament sets .light on <html> ── */
        .light .card { background: #ffffff !important; }
        .light .panel-right { background: #ffffff !important; }
        .light .welcome-eyebrow { color: var(--g3) !important; }
        .light .welcome-eyebrow::before { background: var(--g3) !important; }
        .light .welcome-heading { color: var(--text-primary) !important; }
        .light .welcome-sub { color: var(--text-muted) !important; }
        .light .register-line { color: var(--text-muted) !important; }
        .light .register-line a { color: var(--g3) !important; }
        .light .forgot-wrap a { color: var(--g3) !important; }
        .light label,
        .light .fi-label,
        .light .fi-fo-field-wrp label,
        .light [class*="fi-fo"] label,
        .light [class*="fi-label"],
        .light .fi-fo-checkbox label,
        .light .fi-fo-field-wrp-label { color: var(--text-primary) !important; }
        .light .fi-input-wrp input,
        .light input[type="email"],
        .light input[type="password"],
        .light input[type="text"] {
            background: var(--surface) !important;
            border-color: var(--border) !important;
            color: var(--text-primary) !important;
        }
        .light .fi-fo-password .fi-input-wrp,
        .light .fi-input-wrp[data-has-suffix] {
            background: var(--surface) !important;
            border-color: var(--border) !important;
        }
        .light .fi-input-wrp button svg,
        .light .fi-input-wrp button svg * { color: #94a3b8 !important; stroke: #94a3b8 !important; }
        .light .error-banner { background: #fef2f2 !important; border-color: #fca5a5 !important; color: #dc2626 !important; }
    </style>
    @livewireStyles
</head>
<body>

<div class="login-root">
    <div class="card">

        {{-- ── LEFT PANEL ── --}}
        <div class="panel-left">
            <div class="deco-ring"></div>
            <div class="deco-ring-2"></div>
            <div class="seal-wrap">
                <img
                    src="{{ asset('images/ati_logo.png') }}"
                    alt="ATI Logo"
                    class="seal"
                >
            </div>
            <span class="brand-name">ATI HRMS</span>
            <p class="brand-org">
                Agricultural Training Institute<br>
                Region XI
            </p>
        </div>

        {{-- ── RIGHT PANEL ── --}}
        <div class="panel-right">
            <span class="welcome-eyebrow">HRMS Portal</span>
            <h1 class="welcome-heading">Welcome Back</h1>
            <p class="welcome-sub">Sign in to access your dashboard</p>

            @if ($errors->any())
                <div class="error-banner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;flex-shrink:0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form wire:submit.prevent="authenticate">
                {{ $this->form }}

                @if (filament()->hasPasswordReset())
                    <div class="forgot-wrap">
                        <a href="{{ filament()->getRequestPasswordResetUrl() }}">
                            Forgot password?
                        </a>
                    </div>
                @endif

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="form-divider"></div>

            <p class="register-line">
                Don't have an account?
                <a href="{{ route('filament.hrms.auth.register') }}">Create Account</a>
            </p>
        </div>

    </div>

    {{-- Toast notification --}}
    @if (session('registration_success'))
        <div class="toast" id="reg-toast">
            {{ session('registration_success') }}
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toast = document.getElementById('reg-toast');
                setTimeout(() => toast.style.opacity = '1', 100);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 400);
                }, 5000);
            });
        </script>
    @endif
</div>

@livewireScripts
</body>
</html>
