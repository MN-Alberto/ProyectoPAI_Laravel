<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAI') }}</title>
    <link rel="icon" href="/images/logoPAI.png" type="image/png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/chat.css', 'resources/js/app.js', 'resources/js/chat.js'])

    @if(request()->routeIs('login'))
        @vite(['resources/css/auth/login.css'])
    @elseif(request()->routeIs('register'))
        @vite(['resources/css/auth/register.css'])
    @elseif(request()->routeIs('password.request') || request()->routeIs('password.email') || request()->routeIs('password.reset'))
        @vite(['resources/css/auth/forgot-password.css'])
    @else
        @vite(['resources/css/auth/login.css'])
    @endif
</head>

<body class="light">
    <div style="position: absolute; top: 20px; left: 20px; z-index: 10;">
        <a href="/" class="btn-volver-auth">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver al inicio
        </a>
    </div>
    <div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
        <button class="btn-icono" onclick="alternarTema()">
            <svg id="icono-sol" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
            <svg id="icono-luna" style="display:block;" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
    </div>
    <div class="contenedor-auth">
        <div class="brillo-fondo-auth"></div>

        <div class="tarjeta-auth">
            <a href="/" class="logo-auth">
                <img src="/images/logoPAI.png" alt="PAI Logo">
                <div class="texto-logo-auth">PAI</div>
                <div class="sub-logo-auth">Personal AI</div>
            </a>

            {{ $slot }}
        </div>
    </div>
</body>

</html>