<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('{{ asset('images/ATI XI.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Overlay for better readability */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            /* background: rgba(0, 0, 0, 0.35); */
            z-index: 0;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 980px;
        }

        /* Alerts */
        .success-alert,
        .error-alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .success-alert {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .error-alert {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        /* Card */
        .register-container {
            background: transparent;      /* ✅ remove white card */
            backdrop-filter: none;        /* optional */
            border-radius: 20px;
            padding: 32px;
            box-shadow: none;             /* cleaner floating look */
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header img {
            width: 96px;
            height: 96px;
            object-fit: contain;
            margin: 0 auto 12px;
            display: block;
        }

        .header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
        }

        .header p {
            font-size: 0.9rem;
            color: #374151;
        }

        /* Filament Form spacing improvements */
        .fi-fo-field-wrp {
            margin-bottom: 14px;
        }

        /* Button */
        .register-button {
            width: 100%;
            padding: 16px;
            margin-top: 20px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(34, 197, 94, 0.35);
        }

        /* Footer link */
        .footer-link {
            margin-top: 18px;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-link a {
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .register-container {
                background: rgba(17, 24, 39, 0.92);
                color: #f9fafb;
            }

            .header h1 {
                color: #f9fafb;
            }

            .header p {
                color: #d1d5db;
            }

            .footer-link a {
                color: #22c55e;
            }
        }
    </style>

    @livewireStyles
</head>
<body>
<div class="page-wrapper">

    {{-- Success message --}}
    @if($showSuccessMessage)
        <div class="success-alert">{{ $successMessage }}</div>
    @endif

    {{-- Error message --}}
    @if(session()->has('registration_error'))
        <div class="error-alert">{{ session('registration_error') }}</div>
    @endif

    <div class="register-container">
        <div class="header">
            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo">
            <h1>Employee Registration</h1>
            <p>Create your HRMS account below</p>
        </div>

        <form wire:submit.prevent="register">
            {{ $this->form }}

            <button type="submit" class="register-button">
                Register
            </button>
        </form>

        <div class="footer-link">
            <a href="{{ route('filament.hrms.auth.login') }}">
                Already have an account? Login
            </a>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
