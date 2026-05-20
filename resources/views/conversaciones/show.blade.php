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
        // Variable que guarda el modelo actual
        window.MODELO_ACTUAL = '{{ $conversacion->modelo }}';
    </script>

    <div id="contenedor-mensajes">
        <!--Recorre todos los mensajes de la conversación-->
        @forelse ($mensajes as $mensaje)
            <!--Si el mensaje es del usuario, se muestra como un mensaje de usuario y si no como un mensaje de ia-->
            @if ($mensaje->rol === 'usuario')
                <div class="msg-usuario">
                    <!--Si el mensaje es del usuario se muestra como un mensaje de usuario-->
                    <div class="burbuja-msg-usuario">{{ $mensaje->contenido }}</div>
                </div>
            @else
                <div class="msg-ia">
                    <!--Si el mensaje es de la ia se muestra como un mensaje de ia-->
                    <div class="avatar-msg-ia">
                        <img src="/images/logoPAI.png" alt="PAI">
                    </div>
                    <div class="envoltura-burbuja-ia">
                        @if(!empty($mensaje->modelo))
                            <div class="modelo-nombre-tag">
                                {{ $modelosDisponibles[$mensaje->modelo] ?? $mensaje->modelo }}
                            </div>
                        @endif
                        <div class="burbuja-msg-ia">{{ $mensaje->contenido }}</div>
                    </div>
                </div>
            @endif
        @empty
            <!--Si no hay mensajes en la conversación, se muestra un estado vacio-->
            <div class="estado-vacio">
                <div class="icono-vacio">
                    <img src="/images/logoPAI.png" alt="PAI">
                </div>
                <div class="titulo-vacio">Hola, {{ auth()->user()->name }}</div>
                <p class="sub-vacio">Soy PAI, tu asistente personal. Escribe un mensaje para comenzar.</p>
            </div>
        @endforelse

        <!--Si la ia esta escribiendo, se muestra un estado de escribiendo-->
        <div id="escribiendo-ui" style="display:none" class="indicador-escribiendo">
            <div class="avatar-msg-ia">
                <img src="/images/logoPAI.png" alt="PAI" id="logo-escribiendo">
            </div>
            <div class="puntos-escribiendo">
                <span></span><span></span><span></span>
            </div>
        </div>

    </div>
    <!--Área de entrada de mensajes-->
    <div class="area-entrada">
        <div class="contenedor-entrada-fila">
            {{-- Selector de modelo --}}
            <div class="selector-modelo-wrap" id="selector-modelo-wrap">
                <div class="selector-modelo" id="selector-modelo" onclick="toggleSelectorModelo()">
                    <span
                        id="modelo-nombre">{{ $modelosDisponibles[$conversacion->modelo] ?? $conversacion->modelo }}</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6" />
                    </svg>
                </div>
                <div class="dropdown-modelos" id="dropdown-modelos" style="display:none">
                    <!--Recorre todos los modelos disponibles-->
                    @foreach($modelosDisponibles as $valor => $etiqueta)
                        <div class="opcion-modelo {{ $conversacion->modelo === $valor ? 'activo' : '' }}"
                            onclick="seleccionarModelo('{{ $valor }}', '{{ $etiqueta }}')">
                            <span class="opcion-modelo-icono">
                                <!--Muestra el icono del modelo-->
                                @if($valor === 'mistral')
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z" />
                                        <path
                                            d="M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z" />
                                        <path d="M12 5v14" />
                                    </svg>
                                @elseif($valor === 'phi3')
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                    </svg>
                                @elseif($valor === 'deepseek-coder')
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 18 22 12 16 6" />
                                        <polyline points="8 6 2 12 8 18" />
                                    </svg>
                                @elseif($valor === 'tinyllama')
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="4" width="16" height="16" rx="2" ry="2" />
                                        <rect x="9" y="9" width="6" height="6" />
                                        <line x1="9" y1="1" x2="9" y2="4" />
                                        <line x1="15" y1="1" x2="15" y2="4" />
                                        <line x1="9" y1="20" x2="9" y2="23" />
                                        <line x1="15" y1="20" x2="15" y2="23" />
                                        <line x1="20" y1="9" x2="23" y2="9" />
                                        <line x1="20" y1="15" x2="23" y2="15" />
                                        <line x1="1" y1="9" x2="4" y2="9" />
                                        <line x1="1" y1="15" x2="4" y2="15" />
                                    </svg>
                                @endif
                            </span>
                            <div>
                                <!--Nombre del modelo-->
                                <div class="opcion-modelo-nombre">{{ $etiqueta }}</div>
                                <!--Descripcion del modelo-->
                                <div class="opcion-modelo-desc">
                                    @if($valor === 'mistral') General · 7B · Equilibrado
                                    @elseif($valor === 'phi3') Rápido · 3.8B · Microsoft
                                    @elseif($valor === 'deepseek-coder') Código · 6.7B · Especializado
                                    @elseif($valor === 'tinyllama') Ligero · 1.1B · Muy rápido
                                    @endif
                                </div>
                            </div>
                            <!--Si el modelo es el seleccionado-->
                            @if($conversacion->modelo === $valor)
                                <svg class="opcion-check" viewBox="0 0 24 24" width="14" height="14" fill="none"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="envoltura-entrada">
                <textarea id="entrada-mensaje" rows="1" placeholder="Escribe tu mensaje..." onkeydown="manejarTecla(event)"
                    oninput="autoAjustar(this)"></textarea>
                <button class="btn-enviar" id="btn-enviar" onclick="enviar()">
                    <!--Icono de enviar-->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M22 2L11 13" />
                        <path d="M22 2L15 22 11 13 2 9l20-7z" />
                    </svg>
                </button>
                <button class="btn-detener" id="btn-detener" style="display:none" onclick="detenerGeneracion()">
                    <!--Icono de detener-->
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <rect x="6" y="6" width="12" height="12" rx="2" />
                    </svg>
                </button>
                <button class="btn-reanudar" id="btn-reanudar" style="display:none" onclick="reanudarGeneracion()">
                    <!--Icono de reanudar-->
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="6,4 20,12 6,20" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="pista-entrada">Enter para enviar · Shift+Enter para nueva línea</div>
    </div>

@endsection