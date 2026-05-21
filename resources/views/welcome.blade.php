<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PAI - Personal AI</title>
    <link rel="icon" href="/images/logoPAI.png" type="image/png">


    @vite(['resources/css/welcome.css'])
</head>

<body class="light">
    <script>
        // Prevenir parpadeo (FOUC) aplicando el tema inmediatamente antes de renderizar
        (function () {
            const theme = localStorage.getItem('pai-theme') || 'light';
            if (theme === 'dark') {
                document.body.classList.remove('light');
            }
        })();
    </script>

    <!-- Objetos de fondo que crean un resplandor en la página de bienvenida -->
    <div class="envoltura-resplandor">
        <div class="circulo-resplandor circulo-resplandor-1"></div>
        <div class="circulo-resplandor circulo-resplandor-2"></div>
        <div class="circulo-resplandor circulo-resplandor-3"></div>
    </div>

    <!-- Navegación -->
    <header>
        <div class="logo">
            <img src="/images/logoPAI.png" alt="PAI Logo" class="logo-imagen">
            <span class="logo-texto">PAI</span>
        </div>
        <nav style="display: flex; align-items: center;">
            <button class="btn-tema-bienvenida" id="btn-tema-bienvenida" onclick="alternarTemaBienvenida()" title="Cambiar tema">
                <svg id="icono-sol-bienvenida" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <circle cx="12" cy="12" r="5" />
                    <path
                        d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                </svg>
                <svg id="icono-luna-bienvenida" viewBox="0 0 24 24" width="18" height="18" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="display:none">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </button>
            @auth
                <a href="{{ route('conversaciones.index') }}" class="btn-cabecera">Ir al Chat</a>
            @else
                <a href="{{ route('login') }}" class="btn-enlace">Iniciar Sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-cabecera">Registrarse</a>
                @endif
            @endauth
        </nav>
    </header>

    <!-- Contenido de pantalla principal -->
    <main class="principal-bienvenida">
        <div class="columna-principal">
            <div class="etiqueta">
                <span class="punto-etiqueta"></span>
                100% Local & Privado
            </div>
            <h1>Tu Asistente de IA<br>Privado y Offline</h1>
            <p>
                PAI (Personal AI) es un chat inteligente diseñado para correr en tu ordenador. Interactúa con modelos
                avanzados como Mistral y Phi-3 de forma local, sin conexión a internet y con privacidad total.
            </p>

            <div class="mini-caracteristicas">
                <div class="mini-caracteristica">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Privacidad Total
                </div>
                <div class="mini-caracteristica">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    Modelos Flexibles
                </div>
                <div class="mini-caracteristica">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="10" y1="15" x2="10" y2="9" />
                        <line x1="14" y1="15" x2="14" y2="9" />
                    </svg>
                    Pausa y Reanudación
                </div>
                <div class="mini-caracteristica">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M8 12h8" />
                        <path d="M12 8v8" />
                    </svg>
                    Interfaz Premium
                </div>
            </div>

            <div class="acciones-principales">
                @auth
                    <a href="{{ route('conversaciones.index') }}" class="btn-primario">Abrir Aplicación</a>
                @else
                    <a href="{{ route('register') }}" class="btn-primario">Comenzar Gratis</a>
                    <a href="{{ route('login') }}" class="btn-secundario">Iniciar Sesión</a>
                @endauth
            </div>
        </div>

        <div class="columna-maqueta">
            <div class="maqueta">
                <div class="cabecera-maqueta">
                    <span class="punto-maqueta punto-maqueta-rojo"></span>
                    <span class="punto-maqueta punto-maqueta-amarillo"></span>
                    <span class="punto-maqueta punto-maqueta-verde"></span>
                    <span class="titulo-maqueta">PAI_CHAT_PREVIEW</span>
                </div>
                <div class="cuerpo-maqueta">
                    <div class="msg-maqueta msg-maqueta-usuario">
                        Hola PAI, ¿qué te hace especial?
                    </div>
                    <div class="msg-maqueta msg-maqueta-ia">
                        ¡Hola! Corro <strong>100% de forma local</strong> en tu máquina a través de Ollama. Tus datos
                        nunca salen a internet. Disfrutas de privacidad absoluta, alta velocidad y control total sobre
                        los modelos.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} PAI - Personal Artificial Intelligence. Todos los derechos reservados. Potenciado
            localmente con <a href="https://ollama.com" target="_blank">Ollama</a>.</p>
    </footer>

    <script>
        function alternarTemaBienvenida() {
            const esClaro = document.body.classList.toggle('light');
            localStorage.setItem('pai-theme', esClaro ? 'light' : 'dark');
            actualizarIconosTema(esClaro);
        }

        function actualizarIconosTema(esClaro) {
            const sun = document.getElementById('icono-sol-bienvenida');
            const moon = document.getElementById('icono-luna-bienvenida');
            if (sun && moon) {
                sun.style.display = esClaro ? 'none' : 'block';
                moon.style.display = esClaro ? 'block' : 'none';
            }
        }

        // Inicialización al cargar la página
        (function () {
            const theme = localStorage.getItem('pai-theme') || 'light';
            const esClaro = (theme === 'light');
            actualizarIconosTema(esClaro);
        })();
    </script>

</body>

</html>