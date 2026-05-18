<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PAI</title>
    <link rel="icon" href="/images/logoPAI.png" type="image/png">
    @vite(['resources/css/chat.css', 'resources/js/chat.js'])
</head>

<body class="light">

    <aside class="barra-lateral">
        <div class="barra-lateral-cabecera">
            <div class="logo">
                <img src="/images/logoPAI.png" alt="PAI" class="logo-img">
                <div>
                    <span class="logo-texto">PAI</span>
                    <div class="logo-subtitulo">Personal AI</div>
                </div>
            </div>
            <div class="linea-acento-barra-lateral"></div>
        </div>

        <form action="{{ route('conversaciones.store') }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" class="btn-nuevo">
                <svg class="btn-nuevo-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Nueva conversación
            </button>
        </form>

        <nav class="lista-conv">
            <div class="etiqueta-conv">Conversaciones</div>
            @forelse ($conversaciones as $conv)
                <div class="item-conv {{ isset($conversacion) && $conversacion->id === $conv->id ? 'active' : '' }}">
                    <svg class="icono-conv-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    <a href="{{ route('conversaciones.show', $conv) }}" class="titulo-conv">
                        {{ $conv->tituloConversacion }}
                    </a>
                    <form action="{{ route('conversaciones.destroy', $conv) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="eliminar-conv" title="Eliminar">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <div class="vacio-conv">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    Sin conversaciones aún
                </div>
            @endforelse
        </nav>

        <div class="barra-lateral-pie">
            <div class="avatar-usuario">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="info-usuario">
                <div class="nombre-usuario">{{ auth()->user()->name }}</div>
                <div class="email-usuario">{{ auth()->user()->email }}</div>
            </div>
            <div class="acciones-pie">
                <button class="btn-icono" id="btn-tema" onclick="alternarTema()" title="Cambiar tema">
                    <svg id="icono-sol" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"/>
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                    <svg id="icono-luna" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>
                <form action="{{ route('logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" class="btn-icono btn-iconoo-peligro" title="Cerrar sesión">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main>
        @yield('content')
    </main>

</body>

</html>