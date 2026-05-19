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

    <style>
        body {
            background: var(--fondo-oscuro);
            color: var(--texto-principal);
            font-family: 'Georgia', serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor-auth {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .brillo-fondo-auth {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(124, 106, 255, 0.1) 0%, transparent 60%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }

        .tarjeta-auth {
            background: var(--fondo-chat);
            border: 1px solid var(--borde);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
            position: relative;
            z-index: 2;
        }

        .logo-auth {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            text-decoration: none;
        }

        .logo-auth img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .texto-logo-auth {
            font-size: 28px;
            font-weight: 700;
            color: var(--texto-principal);
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .sub-logo-auth {
            font-size: 11px;
            color: var(--acento-suave);
            font-family: monospace;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .tarjeta-auth input[type="text"],
        .tarjeta-auth input[type="email"],
        .tarjeta-auth input[type="password"] {
            background: var(--fondo-entrada) !important;
            border: 1px solid var(--borde) !important;
            color: var(--texto-principal) !important;
            border-radius: 14px !important;
            padding: 14px 16px !important;
            width: 100% !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
            font-family: sans-serif !important;
            box-shadow: none !important;
        }

        .tarjeta-auth input:focus {
            border-color: var(--acento) !important;
            box-shadow: 0 0 0 3px rgba(124, 106, 255, 0.15) !important;
            outline: none !important;
        }

        .tarjeta-auth label {
            color: var(--texto-atenuado) !important;
            font-size: 13px !important;
            font-family: sans-serif !important;
            font-weight: 500 !important;
            margin-bottom: 8px !important;
            display: block !important;
        }

        .tarjeta-auth button[type="submit"] {
            background: var(--acento) !important;
            color: white !important;
            border: none !important;
            border-radius: 14px !important;
            padding: 14px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            width: 100% !important;
            margin-top: 32px !important;
            display: flex !important;
            justify-content: center !important;
            text-transform: none !important;
            letter-spacing: normal !important;
        }

        .tarjeta-auth button[type="submit"]:hover {
            background: var(--acento-suave) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(124, 106, 255, 0.25) !important;
        }

        .tarjeta-auth .underline {
            color: var(--texto-atenuado) !important;
            text-decoration: none !important;
            font-size: 13px !important;
            transition: color 0.2s !important;
            font-family: sans-serif !important;
        }

        .tarjeta-auth .underline:hover {
            color: var(--acento-suave) !important;
        }

        .tarjeta-auth .mt-4 {
            margin-top: 20px !important;
        }

        .tarjeta-auth .mt-1 {
            margin-top: 6px !important;
        }

        .tarjeta-auth input[type="checkbox"] {
            background-color: var(--fondo-entrada) !important;
            border-color: var(--borde) !important;
            border-radius: 6px !important;
            width: 18px !important;
            height: 18px !important;
            color: var(--acento) !important;
        }

        .tarjeta-auth input[type="checkbox"]:focus {
            --tw-ring-color: var(--acento) !important;
            --tw-ring-opacity: 0.3 !important;
        }

        .tarjeta-auth .text-gray-600 {
            color: var(--texto-atenuado) !important;
        }

        .tarjeta-auth .flex.items-center.justify-end {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-direction: row-reverse !important;
        }

        .tarjeta-auth .flex.items-center.justify-end button {
            margin-top: 0 !important;
            width: auto !important;
        }

        .text-sm.text-red-600 {
            color: #f87171 !important;
            font-family: sans-serif !important;
            font-size: 12px !important;
            margin-top: 6px !important;
        }
    </style>
</head>

<body class="light">
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