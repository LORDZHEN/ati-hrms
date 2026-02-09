<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personal Data Sheet')</title>
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> {{-- optional --}}
</head>
<body>
    <div class="container mx-auto p-4">
        @yield('content')
    </div>

    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script> {{-- optional --}}
</body>
</html>
