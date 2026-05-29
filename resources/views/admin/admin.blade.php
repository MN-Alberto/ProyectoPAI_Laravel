<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PAI - Panel de Administración</title>
    <meta name="description" content="Panel de administración de PAI - Gestión de usuarios y modelos">
    <link rel="icon" href="/images/logoPAI.png" type="image/png">
    @vite(['resources/css/admin.css', 'resources/js/admin/admin.js'])
</head>

<body class="admin-body light">

    <!-- Header -->
    <header class="admin-header">
        <div class="admin-header-izq">
            <div class="admin-logo">
                <img src="/images/logoPAI.png" alt="PAI">
                <span class="admin-logo-texto">PAI</span>
            </div>
            <span class="admin-titulo">Panel de Administración</span>
        </div>
        <div class="admin-header-der">
            <button class="admin-btn-tema" id="btn-tema-admin" onclick="alternarTemaAdmin()" title="Cambiar tema">
                <svg id="icono-sol-admin" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5" />
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                </svg>
                <svg id="icono-luna-admin" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </button>

            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="admin-btn-volver" title="Cerrar sesión">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                    </svg>
                    Salir
                </button>
            </form>
        </div>
    </header>

    <!-- Contenido principal -->
    <div class="admin-contenido">

        <!-- Stats Cards -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icono usuarios">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-numero">{{ $usuarios->count() }}</span>
                    <span class="stat-etiqueta">Usuarios totales</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icono activos">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-numero">{{ $usuarios->where('activo', true)->count() }}</span>
                    <span class="stat-etiqueta">Activos</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icono inactivos">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-numero">{{ $usuarios->where('activo', false)->count() }}</span>
                    <span class="stat-etiqueta">Dados de baja</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icono conversaciones">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                    </svg>
                </div>
                <div class="stat-info">
                    <span class="stat-numero">{{ $usuarios->sum('conversaciones_count') }}</span>
                    <span class="stat-etiqueta">Conversaciones</span>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="admin-tabla-contenedor">
            <div class="admin-tabla-header">
                <div class="admin-tabla-titulo">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    Gestión de Usuarios
                </div>
                <div class="admin-tabla-acciones-header">
                    <input type="text" class="admin-buscar" id="buscar-usuario" placeholder="Buscar usuario..." oninput="filtrarUsuarios(this.value)">
                    <button class="btn-primario btn-crear-usuario" onclick="abrirCrear()">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Añadir usuario
                    </button>
                </div>
            </div>
            <div class="admin-tabla-scroll">
                <table class="admin-tabla">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Conversaciones</th>
                            <th>Modelos Permitidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        @foreach($usuarios as $usuario)
                            <tr data-user-id="{{ $usuario->id }}"
                                data-user-name="{{ $usuario->name }}"
                                data-user-email="{{ $usuario->email }}"
                                class="fila-usuario {{ !$usuario->activo ? 'fila-inactiva' : '' }}">
                                <!-- Columna usuario -->
                                <td>
                                    <div class="celda-usuario">
                                        <div class="avatar-tabla">{{ strtoupper(substr($usuario->name, 0, 1)) }}</div>
                                        <div class="info-usuario-tabla">
                                            <span class="nombre-usuario-tabla">
                                                {{ $usuario->name }}
                                                @if($usuario->is_admin)
                                                    <span class="badge badge-admin">Admin</span>
                                                @endif
                                            </span>
                                            <span class="email-usuario-tabla">{{ $usuario->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Columna estado -->
                                <td>
                                    @if($usuario->activo)
                                        <span class="badge badge-activo">● Activo</span>
                                    @else
                                        <span class="badge badge-inactivo">● Inactivo</span>
                                    @endif
                                </td>
                                <!-- Columna conversaciones -->
                                <td>{{ $usuario->conversaciones_count }}</td>
                                <!-- Columna modelos -->
                                <td>
                                    <div class="modelos-grid">
                                        @foreach($modelosDisponibles as $clave => $nombre)
                                            @php
                                                $permitido = $usuario->modelos_permitidos === null || in_array($clave, $usuario->modelos_permitidos ?? []);
                                            @endphp
                                            <label class="modelo-check {{ $permitido ? 'checked' : '' }}"
                                                   onclick="toggleModelo(this)"
                                                   data-modelo="{{ $clave }}"
                                                   data-user-id="{{ $usuario->id }}">
                                                <input type="checkbox" {{ $permitido ? 'checked' : '' }}
                                                    {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                                                <span class="modelo-check-indicator">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                </span>
                                                {{ $nombre }}
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <!-- Columna acciones -->
                                <td>
                                    <div class="acciones-celda">
                                        <!-- Guardar modelos -->
                                        <button class="btn-accion guardar" title="Guardar modelos"
                                                onclick="guardarModelos({{ $usuario->id }})"
                                                {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                                                <polyline points="17 21 17 13 7 13 7 21" />
                                                <polyline points="7 3 7 8 15 8" />
                                            </svg>
                                        </button>
                                        <!-- Editar usuario -->
                                        <button class="btn-accion" title="Editar usuario"
                                                onclick="abrirEditar({{ $usuario->id }}, '{{ addslashes($usuario->name) }}', '{{ $usuario->email }}')"
                                                {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <!-- Toggle activo/baja -->
                                        <button class="btn-accion baja" title="{{ $usuario->activo ? 'Dar de baja' : 'Reactivar' }}"
                                                onclick="toggleActivo({{ $usuario->id }})"
                                                {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                                            @if($usuario->activo)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18.36 6.64a9 9 0 11-12.73 0" />
                                                    <line x1="12" y1="2" x2="12" y2="12" />
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="23 4 23 10 17 10" />
                                                    <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10" />
                                                </svg>
                                            @endif
                                        </button>
                                        <!-- Eliminar -->
                                        <button class="btn-accion eliminar" title="Eliminar usuario"
                                                onclick="confirmarEliminar({{ $usuario->id }}, '{{ addslashes($usuario->name) }}')"
                                                {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="admin-paginacion" id="paginacion-container"></div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminar -->
    <div id="modal-eliminar-usuario" class="admin-modal-overlay" style="display:none">
        <div class="admin-modal">
            <div class="admin-modal-header">
                <h3>Eliminar usuario</h3>
                <button class="admin-modal-cerrar" onclick="cerrarModalEliminar()">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="admin-modal-body">
                <p>¿Estás seguro de que deseas eliminar al usuario <strong id="nombre-eliminar"></strong>? Se eliminarán todas sus conversaciones y mensajes permanentemente.</p>
                <div class="admin-modal-acciones">
                    <button class="admin-modal-btn-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
                    <button class="admin-modal-btn-eliminar" id="btn-confirmar-eliminar-usuario" onclick="ejecutarEliminar()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="modal-editar-usuario" class="admin-modal-overlay" style="display:none">
        <div class="admin-modal modal-admin">
            <div class="admin-modal-header modal-header-admin">
                <div class="modal-header-info">
                    <div class="modal-icono-header editar">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3>Editar usuario</h3>
                        <p class="modal-subtitulo">Modifica los datos del usuario</p>
                    </div>
                </div>
                <button class="admin-modal-cerrar" onclick="cerrarModalEditar()">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="admin-modal-body">
                <div class="modal-campo">
                    <label for="editar-nombre">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" />
                        </svg>
                        Nombre
                    </label>
                    <input type="text" id="editar-nombre" class="modal-input-admin" placeholder="Nombre completo">
                </div>
                <div class="modal-campo">
                    <label for="editar-email">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" />
                        </svg>
                        Email
                    </label>
                    <input type="email" id="editar-email" class="modal-input-admin" placeholder="correo@ejemplo.com">
                </div>
                <div class="modal-separador"></div>
                <div class="modal-campo">
                    <label for="editar-password">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        Nueva contraseña
                    </label>
                    <span class="modal-campo-hint">Dejar vacío para mantener la actual</span>
                    <input type="password" id="editar-password" class="modal-input-admin" placeholder="Mínimo 8 caracteres">
                </div>
                <div class="admin-modal-acciones modal-acciones-admin">
                    <button class="modal-btn-cancelar-admin" onclick="cerrarModalEditar()">Cancelar</button>
                    <button class="modal-btn-guardar-admin" onclick="ejecutarEditar()">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div id="modal-crear-usuario" class="admin-modal-overlay" style="display:none">
        <div class="admin-modal modal-admin">
            <div class="admin-modal-header modal-header-admin">
                <div class="modal-header-info">
                    <div class="modal-icono-header crear">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="8.5" cy="7" r="4" />
                            <line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" />
                        </svg>
                    </div>
                    <div>
                        <h3>Añadir usuario</h3>
                        <p class="modal-subtitulo">Crea una nueva cuenta de usuario</p>
                    </div>
                </div>
                <button class="admin-modal-cerrar" onclick="cerrarModalCrear()">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="admin-modal-body">
                <div class="modal-campo">
                    <label for="crear-nombre">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" />
                        </svg>
                        Nombre
                    </label>
                    <input type="text" id="crear-nombre" class="modal-input-admin" placeholder="Nombre completo">
                </div>
                <div class="modal-campo">
                    <label for="crear-email">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" />
                        </svg>
                        Email
                    </label>
                    <input type="email" id="crear-email" class="modal-input-admin" placeholder="correo@ejemplo.com">
                </div>
                <div class="modal-campo">
                    <label for="crear-password">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        Contraseña
                    </label>
                    <input type="password" id="crear-password" class="modal-input-admin" placeholder="Mínimo 8 caracteres">
                </div>
                <div class="admin-modal-acciones modal-acciones-admin">
                    <button class="modal-btn-cancelar-admin" onclick="cerrarModalCrear()">Cancelar</button>
                    <button class="modal-btn-guardar-admin crear" onclick="ejecutarCrear()">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Crear usuario
                    </button>
                </div>
            </div>
        </div>
    </div>



</body>
</html>
