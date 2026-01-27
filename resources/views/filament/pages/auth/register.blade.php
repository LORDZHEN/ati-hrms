<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

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
            padding: 20px;
            position: relative;
            transition: background-color 0.3s;
        }

        .register-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34,197,94,0.3);
        }

        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        a { color: #16a34a; font-weight: 600; }

        input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 4px;
            background-color: #fff;
            color: #111;
            transition: background-color 0.3s, color 0.3s;
        }

        .success-alert {
            background-color: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #10b981;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .error-alert {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #f87171;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .register-container {
            background: rgba(255,255,255,0.9);
            padding: 30px;
            border-radius: 12px;
            transition: background-color 0.3s, color 0.3s;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1f2937;
            }
            .register-container {
                background: rgba(31, 41, 55, 0.9);
                color: #f9fafb;
            }
            input {
                background-color: #374151;
                color: #f9fafb;
                border: 1px solid #4b5563;
            }
            a { color: #22c55e; }
            .success-alert {
                background-color: #065f46;
                color: #d1fae5;
                border-color: #10b981;
            }
            .error-alert {
                background-color: #991b1b;
                color: #fee2e2;
                border-color: #f87171;
            }
        }
    </style>

    @livewireStyles
</head>
<body>
<div>
    <div style="width: 400px;">

        {{-- Success message --}}
        @if($showSuccessMessage)
            <div class="success-alert">{{ $successMessage }}</div>
        @endif

        {{-- Error message from session --}}
        @if(session()->has('registration_error'))
            <div class="error-alert">{{ session('registration_error') }}</div>
        @endif

        <div class="register-container">
            <div class="text-center mb-4">
                <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo"
                     style="width: 100px; height: 100px; object-fit: contain; margin: 0 auto 16px; display: block;">
                <h1 class="text-2xl font-bold">Employee Registration</h1>
                <p>Create your HRMS account below.</p>
            </div>

            {{-- Bind the form to the $data property for nested state --}}
            <form wire:submit.prevent="register">
                {{ $this->form }}
                <button type="submit" class="register-button">Register</button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('filament.hrms.auth.login') }}">Already have an account? Login</a>
            </div>
        </div>
    </div>

    @livewireScripts
</div>
</body>
</html>
