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
            position: relative;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
    </style>
    @livewireStyles
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo"
         style="width: 100px; height: 100px; object-fit: contain; margin: 0 auto 16px; display: block;">
            <h1 class="text-2xl font-bold">HRMS Login</h1>
            <p>Welcome! please login to continue.</p>
        </div>

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

    @livewireScripts
</body>
</html>
