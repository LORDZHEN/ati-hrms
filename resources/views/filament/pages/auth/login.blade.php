<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/ATI XI.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(22, 163, 74, 0.55) 0%,
                rgba(15, 118, 55, 0.45) 50%,
                rgba(5, 46, 22, 0.60) 100%
            );
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.3);
        }

        .login-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto 16px;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #14532d;
            text-align: center;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #4b7a5c;
            text-align: center;
            margin-top: 4px;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
            letter-spacing: 0.4px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.35);
        }

        .login-button:active { transform: translateY(0); }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.875rem;
            color: #4b7a5c;
        }

        .register-link a {
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #16a34a;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            font-size: 0.9rem;
            opacity: 0;
            transition: opacity 0.4s ease;
            max-width: 320px;
        }

        /* ── Dark mode ── */
        @media (prefers-color-scheme: dark) {
            .login-container {
                background: rgba(17, 24, 39, 0.92);
                box-shadow: 0 8px 32px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.08);
                color: #e5e7eb;
            }

            .login-title      { color: #86efac; }
            .login-subtitle   { color: #6ee7b7; }
            .register-link    { color: #6ee7b7; }
            .register-link a  { color: #4ade80; }
            .toast            { background: #15803d; }

            .error-message {
                background: #3b0f0f;
                border-color: #b91c1c;
                color: #fca5a5;
            }

            /* ── All labels — broad catch-all ── */
            label,
            span[class*="label"],
            .fi-fo-field-wrp label,
            .fi-label,
            .fi-fo-field-wrp-label,
            [class*="fi-fo"] label,
            [class*="fi-label"],
            .fi-fo-field-wrp > div > label,
            form label,
            .login-container label {
                color: #e5e7eb !important;
            }

            /* ── All input fields ── */
            input,
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="tel"],
            input[type="number"],
            input[type="date"],
            textarea,
            select {
                background-color: rgba(255, 255, 255, 0.10) !important;
                border-color: rgba(255, 255, 255, 0.25) !important;
                color: #ffffff !important;
            }

            input::placeholder,
            textarea::placeholder {
                color: rgba(255, 255, 255, 0.4) !important;
            }

            /* ── Filament input wrappers ── */
            .fi-input-wrp,
            .fi-fo-field-wrp-input,
            [class*="fi-input"] {
                background-color: rgba(255, 255, 255, 0.10) !important;
                border-color: rgba(255, 255, 255, 0.25) !important;
            }

            /* ── Checkbox label ── */
            .fi-fo-checkbox label,
            .fi-checkbox-label {
                color: #d1d5db !important;
            }

            /* ── Hint and error text ── */
            .fi-fo-field-wrp-hint,
            .fi-hint,
            [class*="fi-hint"] {
                color: #9ca3af !important;
            }

            .fi-fo-field-wrp-error,
            .fi-error,
            [class*="fi-error"] {
                color: #fca5a5 !important;
            }

            /* ── Eye toggle icon inside password field ── */
            .fi-input-wrp button svg,
            .fi-fo-password button svg {
                color: #9ca3af !important;
            }

            /* ── Required asterisk (*) ── */
            .fi-fo-field-wrp-label sup,
            label sup,
            label span[class*="required"] {
                color: #f87171 !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body>

    {{-- Single root element required by Livewire --}}
    <div>

        <div class="login-container">

            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo" class="login-logo">
            <h1 class="login-title">HRMS Login</h1>
            <p class="login-subtitle">Welcome! Please sign in to continue.</p>

            @if ($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            <form wire:submit.prevent="authenticate">
                {{ $this->form }}
                <button type="submit" class="login-button">Sign In</button>
            </form>

            <p class="register-link">
                Don't have an account?
                <a href="{{ route('filament.hrms.auth.register') }}">Create one</a>
            </p>

        </div>

        {{-- Toast inside the single root --}}
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
