<!-- 
    Muestra la conversación actual
-->
@extends('layouts.chat')
<!-- 
    Contenido del área de mensajes
-->
@section('content')

    <script>
        // Variable que guarda el id de la conversación actual
        window.ID_CONVERSACION = {{ $conversacion->id }};
        // Variable que guarda el token csrf para enviar peticiones
        window.TOKEN_CSRF = document.querySelector('meta[name="csrf-token"]').content;
        // Variable que guarda el estado de si se esta enviando un mensaje
        window.enviando = false;
    </script>

    <div id="contenedor-mensajes">
        <!--
            Recorre todos los mensajes de la conversación
            -->
        @forelse ($mensajes as $mensaje)
            <!--
                    Si el mensaje es del usuario, se muestra como un mensaje de usuario y si no como un mensaje de ia
                    -->
            @if ($mensaje->rol === 'usuario')
                <div class="msg-usuario">
                    <!--
                                Si el mensaje es del usuario se muestra como un mensaje de usuario
                                -->
                    <div class="burbuja-msg-usuario">{{ $mensaje->contenido }}</div>
                </div>
            @else
                <div class="msg-ia">
                    <!--
                                Si el mensaje es de la ia se muestra como un mensaje de ia
                                -->
                    <div class="avatar-msg-ia">
                        <img src="/images/logoPAI.png" alt="PAI">
                    </div>
                    <div class="burbuja-msg-ia">{{ $mensaje->contenido }}</div>
                </div>
            @endif
        @empty
            <!--
                    Si no hay mensajes en la conversación, se muestra un estado vacio
                    -->
            <div class="estado-vacio">
                <div class="icono-vacio">
                    <img src="/images/logoPAI.png" alt="PAI">
                </div>
                <div class="titulo-vacio">Hola, {{ auth()->user()->name }}</div>
                <p class="sub-vacio">Soy PAI, tu asistente personal. Escribe un mensaje para comenzar.</p>
            </div>
        @endforelse

        <!--
            Si la ia esta escribiendo, se muestra un estado de escribiendo
            -->
        <div id="escribiendo-ui" style="display:none" class="indicador-escribiendo">
            <div class="avatar-msg-ia">
                <img src="/images/logoPAI.png" alt="PAI" id="logo-escribiendo">
            </div>
            <div class="puntos-escribiendo">
                <span></span><span></span><span></span>
            </div>
        </div>

    </div>
    <!--
        Área de entrada de mensajes
        -->
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