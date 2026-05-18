@extends('layouts.chat')

@section('content')

    <script>
        window.ID_CONVERSACION = {{ $conversacion->id }};
        window.TOKEN_CSRF = document.querySelector('meta[name="csrf-token"]').content;
        window.enviando = false;
    </script>

    <div id="contenedor-mensajes">

        @forelse ($mensajes as $mensaje)
            @if ($mensaje->rol === 'usuario')
                <div class="msg-usuario">
                    <div class="burbuja-msg-usuario">{{ $mensaje->contenido }}</div>
                </div>
            @else
                <div class="msg-ia">
                    <div class="avatar-msg-ia">
                        <img src="/images/logoPAI.png" alt="PAI">
                    </div>
                    <div class="burbuja-msg-ia">{{ $mensaje->contenido }}</div>
                </div>
            @endif
        @empty
            <div class="estado-vacio">
                <div class="icono-vacio">◈</div>
                <div class="titulo-vacio">Hola, {{ auth()->user()->name }}</div>
                <p class="sub-vacio">Soy PAI, tu asistente personal. Escribe un mensaje para comenzar.</p>
            </div>
        @endforelse

        <div id="escribiendo-ui" style="display:none" class="indicador-escribiendo">
            <div class="avatar-msg-ia">
                <img src="/images/logoPAI.png" alt="PAI" id="logo-escribiendo">
            </div>
            <div class="puntos-escribiendo">
                <span></span><span></span><span></span>
            </div>
        </div>

    </div>

    <div class="area-entrada">
        <div class="envoltura-entrada">
            <textarea id="entrada-mensaje" rows="1" placeholder="Escribe tu mensaje..." onkeydown="manejarTecla(event)"
                oninput="autoAjustar(this)"></textarea>
            <button class="btn-enviar" id="btn-enviar" onclick="enviar()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 2L11 13" />
                    <path d="M22 2L15 22 11 13 2 9l20-7z" />
                </svg>
            </button>
        </div>
        <div class="pista-entrada">Enter para enviar · Shift+Enter para nueva línea</div>
    </div>

@endsection