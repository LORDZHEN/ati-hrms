<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark:   #166534;
            --green-mid:    #16a34a;
            --green-light:  #22c55e;
            --green-pale:   #dcfce7;
            --text-primary: #111827;
            --text-muted:   #6b7280;
            --border:       #e5e7eb;
            --white:        #ffffff;
            --radius-card:  24px;
            --radius-input: 10px;
            --font:         'Plus Jakarta Sans', sans-serif;
        }

        html, body {
            height: 100%;
            font-family: var(--font);
        }

        /* ── Full-page green background ── */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ asset("images/ATI XI.jpg") }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(22, 163, 74, 0.80) 0%,
                rgba(15, 118, 55, 0.75) 50%,
                rgba(5, 46, 22, 0.85) 100%
            );
            z-index: 1;
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1000px;
            min-width: 720px;
            background: var(--white);
            border-radius: var(--radius-card);
            display: grid;
            grid-template-columns: 380px 1fr;
            overflow: hidden;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.30),
                0 0 0 1px rgba(255,255,255,0.15);
            animation: cardIn 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Left panel ── */
        .panel-left {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            gap: 0;
            overflow: hidden;
            background: linear-gradient(160deg, #14532d, #166534, #16a34a, #22c55e, #16a34a, #166534);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .panel-left::before,
        .panel-left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            animation: floatBubble 8s ease-in-out infinite;
        }

        .panel-left::before {
            width: 320px;
            height: 320px;
            top: -80px;
            left: -80px;
            animation-delay: 0s;
        }

        .panel-left::after {
            width: 220px;
            height: 220px;
            bottom: -60px;
            right: -60px;
            animation-delay: 3s;
        }

        @keyframes floatBubble {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(12px, -16px) scale(1.05); }
        }

        .deco-ring {
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.10);
            bottom: 60px;
            left: -40px;
            animation: floatBubble 10s ease-in-out infinite reverse;
        }

        .deco-ring-2 {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.10);
            top: 40px;
            right: -20px;
            animation: floatBubble 7s ease-in-out 1.5s infinite;
        }

        .seal {
            width: 190px;
            height: 190px;
            object-fit: contain;
            margin-bottom: 24px;
            filter: drop-shadow(0 4px 20px rgba(0,0,0,0.35)) brightness(1.05);
            animation: sealFadeIn 0.7s 0.2s cubic-bezier(0.22, 1, 0.36, 1) both;
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            padding: 12px;
        }

        @keyframes sealFadeIn {
            from { opacity: 0; transform: scale(0.85); }
            to   { opacity: 1; transform: scale(1); }
        }

        .brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }

        .brand-org {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.80);
            text-align: center;
            margin-top: 6px;
            line-height: 1.55;
            position: relative;
            z-index: 1;
        }

        /* ── Right panel ── */
        .panel-right {
            padding: 60px 56px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .welcome-heading {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            line-height: 1.15;
        }

        .welcome-sub {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 6px;
            margin-bottom: 32px;
        }

        /* ── Error banner ── */
        .error-banner {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 12px;
            padding: 11px 16px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* ── Field label ── */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        /* ── Force Filament labels visible in light mode ── */
        label,
        .fi-label,
        .fi-fo-field-wrp label,
        [class*="fi-fo"] label,
        [class*="fi-label"],
        .fi-fo-checkbox label,
        .fi-checkbox-label,
        .fi-fo-field-wrp-label {
            color: var(--text-primary) !important;
        }

        label sup,
        .fi-fo-field-wrp-label-required-indicator {
            color: #ef4444 !important;
        }

        .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] {
            color: var(--text-muted) !important;
        }

        .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] {
            color: #dc2626 !important;
        }

        /* ── Filament form override – normal rounded inputs ── */
        .fi-fo-field-wrp,
        .fi-input-wrp,
        .fi-fo-text-input-wrp,
        .fi-fo-field-wrp *,
        .fi-input-wrp *,
        [class*="fi-input"],
        [class*="fi-fo"] {
            border-radius: 10px !important;
        }

        /* Target the actual <input> rendered by Filament */
        .fi-input-wrp input,
        .fi-fo-field-wrp input[type="text"],
        .fi-fo-field-wrp input[type="email"],
        .fi-fo-field-wrp input[type="password"],
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            border-radius: 10px !important;
            border: 1.5px solid var(--border) !important;
            padding: 14px 22px !important;
            font-size: 0.9rem !important;
            font-family: var(--font) !important;
            color: var(--text-primary) !important;
            background: var(--white) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .fi-input-wrp input:focus,
        .fi-fo-field-wrp input:focus {
            outline: none !important;
            border-color: var(--green-mid) !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18) !important;
        }

        /* Password wrapper */
        .fi-fo-password .fi-input-wrp,
        .fi-input-wrp[data-has-suffix] {
            border-radius: 10px !important;
            border: 1.5px solid var(--border);
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .fi-fo-password .fi-input-wrp input {
            border: none !important;
            box-shadow: none !important;
        }

        .fi-input-wrp button {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 16px 0 4px;
            cursor: pointer;
            color: #374151 !important;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .fi-input-wrp button svg,
        .fi-input-wrp button svg * {
            color: #374151 !important;
            stroke: #374151 !important;
            width: 20px !important;
            height: 20px !important;
        }

        .fi-input-wrp button:hover svg,
        .fi-input-wrp button:hover svg * {
            color: var(--green-mid) !important;
            stroke: var(--green-mid) !important;
        }

        /* Forgot password link */
        .forgot-wrap {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 28px;
        }

        .forgot-wrap a {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--green-mid);
            text-decoration: none;
        }

        .forgot-wrap a:hover { text-decoration: underline; }

        /* ── Login button ── */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--green-light), var(--green-mid));
            color: #fff;
            border: none;
            border-radius: var(--radius-input);
            font-size: 1rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.35);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(34, 197, 94, 0.45);
            filter: brightness(1.05);
        }

        .btn-login:active { transform: translateY(0); }

        /* ── Register link ── */
        .register-line {
            text-align: center;
            margin-top: 22px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .register-line a {
            color: var(--green-mid);
            font-weight: 700;
            text-decoration: none;
        }

        .register-line a:hover { text-decoration: underline; }

        /* ── Toast ── */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--green-mid);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            font-size: 0.9rem;
            opacity: 0;
            transition: opacity 0.4s ease;
            max-width: 320px;
        }

        /* ── Responsive ── */
        @media (max-width: 780px) {
            .card {
                grid-template-columns: 1fr;
                max-width: 460px;
                min-width: unset;
            }

            .panel-left {
                border-right: none;
                border-bottom: 1px solid #d1fae5;
                padding: 36px 24px 28px;
            }

            .seal { width: 120px; height: 120px; }

            .panel-right { padding: 36px 32px 44px; }

            .welcome-heading { font-size: 1.6rem; }
        }

        /* ── Dark mode ── */
        @media (prefers-color-scheme: dark) {

            /* Card shell */
            .card {
                background: #0f172a;
                box-shadow: 0 25px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.06);
            }

            /* Left panel — keep the animated green, just ensure text stays white */
            .panel-left { /* animated gradient preserved */ }

            .brand-name {
                color: #ffffff !important;
                text-shadow: 0 2px 10px rgba(0,0,0,0.4);
            }

            .brand-org { color: rgba(255,255,255,0.80) !important; }

            /* Right panel */
            .panel-right { background: #0f172a; }

            .welcome-heading { color: #f1f5f9 !important; }
            .welcome-sub     { color: #94a3b8 !important; }

            /* All Filament labels */
            label,
            .fi-label,
            .fi-fo-field-wrp label,
            [class*="fi-fo"] label,
            [class*="fi-label"],
            .fi-fo-checkbox label,
            .fi-checkbox-label,
            .fi-fo-field-wrp-label,
            .field label {
                color: #e2e8f0 !important;
            }

            /* Required asterisk */
            label sup,
            .fi-fo-field-wrp-label-required-indicator {
                color: #f87171 !important;
            }

            /* Hint & error text */
            .fi-fo-field-wrp-hint, .fi-hint, [class*="fi-hint"] {
                color: #94a3b8 !important;
            }
            .fi-fo-field-wrp-error, .fi-error, [class*="fi-fo-field-wrp-validation"] {
                color: #f87171 !important;
            }

            /* Input fields */
            .fi-input-wrp input,
            .fi-fo-field-wrp input[type="text"],
            .fi-fo-field-wrp input[type="email"],
            .fi-fo-field-wrp input[type="password"],
            input[type="email"],
            input[type="password"],
            input[type="text"] {
                background: rgba(255,255,255,0.06) !important;
                border-color: rgba(255,255,255,0.12) !important;
                color: #f1f5f9 !important;
            }

            .fi-input-wrp input::placeholder,
            input::placeholder {
                color: rgba(255,255,255,0.30) !important;
            }

            /* Password wrapper */
            .fi-fo-password .fi-input-wrp,
            .fi-input-wrp[data-has-suffix] {
                border-color: rgba(255,255,255,0.12) !important;
                background: rgba(255,255,255,0.06) !important;
            }

            /* Eye icon */
            .fi-input-wrp button,
            .fi-input-wrp button svg,
            .fi-input-wrp button svg * {
                color: #94a3b8 !important;
                stroke: #94a3b8 !important;
            }

            /* Checkbox (Remember me) */
            input[type="checkbox"] {
                accent-color: #22c55e;
            }

            /* Error banner */
            .error-banner {
                background: rgba(220,38,38,0.15) !important;
                border-color: rgba(220,38,38,0.4) !important;
                color: #fca5a5 !important;
            }

            /* Register link */
            .register-line         { color: #94a3b8 !important; }
            .register-line a       { color: #4ade80 !important; }

            /* Forgot password */
            .forgot-wrap a         { color: #4ade80 !important; }
        }
    </style>
    @livewireStyles
</head>
<body>

<div>
    <div class="card">

        {{-- ── LEFT PANEL ── --}}
        <div class="panel-left">
            <div class="deco-ring"></div>
            <div class="deco-ring-2"></div>
            <img
                src="{{ asset('images/ati_logo.png') }}"
                alt="ATI Logo"
                class="seal"
            >
            <span class="brand-name">ATI HRMS</span>
            <p class="brand-org">
                Agricultural Training Institute<br>
                Region XI
            </p>
        </div>

        {{-- ── RIGHT PANEL ── --}}
        <div class="panel-right">
            <h1 class="welcome-heading">Welcome Back</h1>
            <p class="welcome-sub">Login to access your dashboard</p>

            @if ($errors->any())
                <div class="error-banner">{{ $errors->first() }}</div>
            @endif

            <form wire:submit.prevent="authenticate">
                {{ $this->form }}

                {{-- Forgot password — rendered below the Filament form fields --}}
                @if (filament()->hasPasswordReset())
                    <div class="forgot-wrap">
                        <a href="{{ filament()->getRequestPasswordResetUrl() }}">
                            Forgot password?
                        </a>
                    </div>
                @endif

                <button type="submit" class="btn-login">Login</button>
            </form>

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
