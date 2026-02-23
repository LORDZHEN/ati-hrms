<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Background image layer */
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

        /* Green overlay layer */
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

        /* Card container */
        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 560px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo & headings */
        .register-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto 16px;
        }

        .register-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #14532d;
            text-align: center;
        }

        .register-subtitle {
            font-size: 0.9rem;
            color: #4b7a5c;
            text-align: center;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        /* Alerts */
        .success-alert {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Submit button */
        .register-button {
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

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.35);
        }

        .register-button:active {
            transform: translateY(0);
        }

        /* Login link */
        .login-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.875rem;
            color: #4b7a5c;
        }

        .login-link a {
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* ── Dark mode ── */
        @media (prefers-color-scheme: dark) {
            .register-container {
                background: rgba(17, 24, 39, 0.92);
                box-shadow:
                    0 8px 32px rgba(0, 0, 0, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.08);
            }

            .register-title {
                color: #86efac;
            }

            .register-subtitle {
                color: #6ee7b7;
            }

            .success-alert {
                background: #052e16;
                border-color: #166534;
                color: #86efac;
            }

            .error-alert {
                background: #3b0f0f;
                border-color: #b91c1c;
                color: #fca5a5;
            }

            .login-link {
                color: #6ee7b7;
            }

            .login-link a {
                color: #4ade80;
            }
        }
    </style>
    @livewireStyles
</head>
<body>

    <div class="register-container">

        {{-- Logo & heading --}}
        <img
            src="{{ asset('images/ati_logo.png') }}"
            alt="ATI Logo"
            class="register-logo"
        >
        <h1 class="register-title">Employee Registration</h1>
        <p class="register-subtitle">Create your HRMS account below</p>

        {{-- Success message --}}
        @if ($showSuccessMessage)
            <div class="success-alert">{{ $successMessage }}</div>
        @endif

        {{-- Error message --}}
        @if (session()->has('registration_error'))
            <div class="error-alert">{{ session('registration_error') }}</div>
        @endif

        {{-- Registration form --}}
        <form wire:submit.prevent="register">
            {{ $this->form }}

            <button type="submit" class="register-button">
                Register
            </button>
        </form>

        {{-- Login link --}}
        <p class="login-link">
            Already have an account?
            <a href="{{ route('filament.hrms.auth.login') }}">Sign in</a>
        </p>

    </div>

    @livewireScripts
</body>
</html>
