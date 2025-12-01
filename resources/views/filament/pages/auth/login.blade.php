<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - HRMS Portal</title>
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
        }

        .login-button {
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

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34,197,94,0.3);
        }

        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        a { color: #16a34a; font-weight: 600; }
        .error-message { color: #dc2626; margin-bottom: 1rem; text-align: center; font-weight: 600; }
    </style>
    @livewireStyles
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo"
                 style="width: 100px; height: 100px; object-fit: contain; margin: 0 auto 16px; display: block;">
            <h1 class="text-2xl font-bold">HRMS Login</h1>
            <p>Welcome! Please login to continue.</p>
        </div>

        <!-- Display login error -->
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form wire:submit.prevent="authenticate">
            {{ $this->form }}

            <button type="submit" class="login-button">
                Sign In
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('filament.hrms.auth.register') }}">
                Create an account
            </a>
        </div>
    </div>
@if(session('registration_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const message = "{{ session('registration_success') }}";

            const toast = document.createElement('div');
            toast.innerText = message;
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.backgroundColor = '#16a34a';
            toast.style.color = 'white';
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            toast.style.zIndex = '9999';
            toast.style.fontWeight = '500';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s';

            document.body.appendChild(toast);

            // Fade in
            setTimeout(() => toast.style.opacity = '1', 100);

            // Fade out after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
    </script>
@endif

    @livewireScripts
</body>
</html>
