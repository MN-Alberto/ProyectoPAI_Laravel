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

    <!-- Capa oscura para cerrar barra lateral en móvil -->
    <div id="capa-oscura-lateral" class="capa-oscura-lateral"></div>

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
            <!--
            Condicion que verifica si la conversacion actual no tiene mensajes y esta es la primera
            Si es la primera conversacion y no tiene mensajes se deshabilita el boton de nueva conversacion
            y se cambia el estilo del boton
            -->
            <button type="submit" id="btn-nueva-conv" class="btn-nuevo" {{ isset($conversacion) && $conversacion->mensajes()->count() === 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' }}>
                <svg class="btn-nuevo-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Nueva conversación
            </button>
        </form>

        <nav class="lista-conv">
            <div class="etiqueta-conv">Conversaciones</div>
            <!--
            Recorre todas las conversaciones del usuario
            -->
            @forelse ($conversaciones as $conv)
                <!--
                                        Si la conversacion actual es la que se esta mostrando, se marca como active
                                        -->
                <div class="item-conv {{ isset($conversacion) && $conversacion->id === $conv->id ? 'active' : '' }}">
                    <svg class="icono-conv-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                    </svg>
                    <!--
                                            Muestra el titulo de la conversacion y su fecha de ultima actividad
                                            -->
                    <div class="info-conv">
                        <a href="{{ route('conversaciones.show', $conv) }}" class="titulo-conv">
                            {{ $conv->tituloConversacion }}
                        </a>
                        <!--
                                Muestra la fecha de ultima actividad de la conversacion
                                y se cambia la zona horaria a la de Madrid
                                -->
                        <span class="fecha-conv">
                            @if ($conv->mensajes_max_created_at)
                                {{ \Carbon\Carbon::parse($conv->mensajes_max_created_at)->setTimezone('Europe/Madrid')->format('d/m/Y H:i') }}
                            @endif
                        </span>
                    </div>
                    <!--
                                            Formulario para eliminar la conversacion
                                            -->
                    <form action="{{ route('conversaciones.destroy', $conv) }}" method="POST" style="margin:0">
                        @csrf
                        @method('DELETE')
                        <!--
                                                Boton para eliminar la conversacion
                                                -->
                        <button type="button" class="eliminar-conv btn-abrir-eliminar" title="Eliminar">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <!--
                                        Si no hay conversaciones, se muestra un mensaje
                                        -->
                <div class="vacio-conv">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                    </svg>
                    Sin conversaciones aún
                </div>
            @endforelse
        </nav>
        <!--
        Pie de la barra lateral con la información del usuario y los botones de tema y cerrar sesión
        -->
        <div class="barra-lateral-pie">
            <!--
            Avatar del usuario
            -->
            <div class="avatar-usuario">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <!--
            Información del usuario
            -->
            <div class="info-usuario">
                <div class="nombre-usuario">{{ auth()->user()->name }}</div>
                <div class="email-usuario">{{ auth()->user()->email }}</div>
            </div>
            <div class="acciones-pie">
                <button class="btn-icono" id="btn-tema" onclick="alternarTema()" title="Cambiar tema">
                    <svg id="icono-sol" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5" />
                        <path
                            d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                    </svg>
                    <svg id="icono-luna" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>
                <form action="{{ route('logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" class="btn-icono btn-iconoo-peligro" title="Cerrar sesión">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main>
        <!-- Botón menú hamburguesa en móvil -->
        <button type="button" id="btn-menu-movil" class="btn-menu-movil" title="Abrir menú">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <!--
        Contenido principal con la conversación actual
        -->
        @yield('content')
    </main>

    <!-- Modal para editar perfil -->
    <div id="modal-perfil" class="modal-overlay"
        style="display: {{ ($errors->any() || session('status') === 'profile-chat-updated') ? 'flex' : 'none' }}">
        <div class="modal-contenedor">
            <div class="modal-cabecera">
                <h3 class="modal-titulo">Editar Perfil</h3>
                <button type="button" id="btn-cerrar-modal" class="modal-cerrar-svg">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <!--
            Formulario para actualizar el perfil
            -->
            <form action="{{ route('profile.actualizar-usuario') }}" method="POST" class="modal-formulario">
                @csrf
                <!--
                Se especifica que el método es PATCH para que Laravel pueda actualizar el perfil
                -->
                @method('PATCH')

                @if ($errors->any())
                    <!--
                                    Si hay errores, se muestra un mensaje
                                    -->
                    <div class="alerta-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!--
                Si hay un mensaje de éxito, se muestra un mensaje
                -->
                @if (session('status') === 'profile-chat-updated')
                    <div class="alerta-exito">
                        ¡Perfil actualizado correctamente!
                    </div>
                @endif

                <div class="grupo-formulario">
                    <label for="modal-nombre">Nombre</label>
                    <input type="text" id="modal-nombre" name="name" value="{{ old('name', auth()->user()->name) }}"
                        required autocomplete="name">
                </div>

                <div class="grupo-formulario">
                    <label for="modal-current-password">Contraseña Actual</label>
                    <input type="password" id="modal-current-password" name="current_password"
                        placeholder="Introduce tu contraseña actual" required autocomplete="current-password">
                </div>

                <div class="grupo-formulario">
                    <label for="modal-password">Nueva Contraseña</label>
                    <input type="password" id="modal-password" name="password"
                        placeholder="Mínimo 8 caracteres (opcional)" autocomplete="new-password">
                </div>

                <div class="grupo-formulario">
                    <label for="modal-password-confirm">Confirmar Nueva Contraseña</label>
                    <input type="password" id="modal-password-confirm" name="password_confirmation"
                        placeholder="Repite la contraseña" autocomplete="new-password">
                </div>

                <div class="modal-acciones">
                    <button type="button" id="btn-cancelar-modal" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para eliminar conversación -->
    <div id="modal-eliminar" class="modal-overlay" style="display: none">
        <div class="modal-contenedor">
            <div class="modal-cabecera">
                <h3 class="modal-titulo">Eliminar conversación</h3>
                <button type="button" id="btn-cerrar-modal-eliminar" class="modal-cerrar-svg">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-formulario">
                <p style="color: var(--texto-atenuado); font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                    ¿Estás seguro de que deseas eliminar esta conversación? Todos los mensajes se perderán
                    permanentemente.
                </p>
                <div class="modal-acciones">
                    <button type="button" id="btn-cancelar-modal-eliminar" class="btn-secundario">Cancelar</button>
                    <button type="button" id="btn-confirmar-eliminar" class="btn-primario"
                        style="background: linear-gradient(135deg, #f87171, #ef4444); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>