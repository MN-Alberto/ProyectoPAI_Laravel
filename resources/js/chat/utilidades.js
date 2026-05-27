// Desplaza la ventana del chat hacia abajo
window.desplazarAbajo = function () {
    const c = document.getElementById('contenedor-mensajes');
    // Si el contenedor existe desplaza la ventana del chat hacia abajo
    if (c) c.scrollTop = c.scrollHeight;
}

// Ajusta el tamaño del área de texto
window.autoAjustar = function (el) {
    // Resetea la altura del área de texto
    el.style.height = 'auto';
    // Ajusta la altura del área de texto
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

// Maneja el envío de mensajes con la tecla enter
window.manejarTecla = function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        window.enviar();
    }
}

// Aplica el tema claro o oscuro en la interfaz y actualiza los iconos
window.aplicarTema = function (tema) {
    const esClaro = tema !== 'dark';
    if (esClaro) {
        document.body.classList.add('light');
    } else {
        document.body.classList.remove('light');
    }

    // Alterna los iconos del sol y la luna según el tema
    const sun = document.getElementById('icono-sol');
    const moon = document.getElementById('icono-luna');
    if (sun && moon) {
        sun.style.display = esClaro ? 'none' : 'block';
        moon.style.display = esClaro ? 'block' : 'none';
    }
}

// Inicializa el tema desde localStorage
window.inicializarTema = function () {
    // Si no existe el tema en localStorage se establece el tema oscuro como predeterminado
    const temaGuardado = localStorage.getItem('pai-theme') || 'dark';
    window.aplicarTema(temaGuardado);
}

// Alterna entre modo claro y oscuro
window.alternarTema = function () {
    // Determina si el tema actual es claro u oscuro
    const esClaro = !document.body.classList.contains('light');
    const nuevoTema = esClaro ? 'light' : 'dark';
    // Guarda el tema en localStorage
    localStorage.setItem('pai-theme', nuevoTema);
    // Aplica el tema
    window.aplicarTema(nuevoTema);
}

// Escapa caracteres HTML especiales para evitar inyección XSS
export function escaparHtml(t) {
    return t
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

// Establece el estado de carga en la interfaz
window.establecerCargando = function (state) {
    window.enviando = state;
    document.getElementById('btn-enviar').disabled = state;
    document.getElementById('escribiendo-ui').style.display = state ? 'flex' : 'none';
    const logoTyping = document.getElementById('logo-escribiendo');
    // Si el logo de typing existe cambia el icono
    if (logoTyping) {
        if (state) {
            logoTyping.classList.add('girando');
        } else {
            logoTyping.classList.remove('girando');
        }
    }
    // Muestra o oculta el botón de detener y el de enviar
    const btnDetener = document.getElementById('btn-detener');
    const btnEnviar = document.getElementById('btn-enviar');
    if (btnDetener && btnEnviar) {
        // Si está cargando muestra el botón de detener y oculta el de enviar
        btnDetener.style.display = state ? 'flex' : 'none';
        btnEnviar.style.display = state ? 'none' : 'flex';
    }
    // Si no está cargando reinicia los puntos de escribiendo
    if (!state) {
        const puntosDiv = document.querySelector('.puntos-escribiendo');
        if (puntosDiv) {
            puntosDiv.innerHTML = '<span></span><span></span><span></span>';
        }
    }
    if (state) window.desplazarAbajo();
}

// Agrega un mensaje al chat
export function agregarMensaje(rol, contenido) {
    // Actualizar la fecha/hora de la última actividad reactivamente en la barra lateral sin recargar
    const activeFecha = document.querySelector('.item-conv.active .fecha-conv') || document.querySelector('.item-conv.activo .fecha-conv');
    // Si la fecha existe actualiza la fecha/hora de la última actividad
    if (activeFecha) {
        const ahora = new Date();
        const dia = String(ahora.getDate()).padStart(2, '0');
        const mes = String(ahora.getMonth() + 1).padStart(2, '0');
        const anio = ahora.getFullYear();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        // Asigna la fecha/hora de la última actividad
        activeFecha.textContent = `${dia}/${mes}/${anio} ${horas}:${minutos}`;
    }

    const container = document.getElementById('contenedor-mensajes');
    const empty = container.querySelector('.estado-vacio');
    if (empty) {
        empty.remove();
        if (rol === 'usuario') {
            // Variable que almacena el titulo de la conversacion
            const activeConv = document.querySelector('.item-conv.active .titulo-conv') || document.querySelector('.item-conv.activo .titulo-conv');
            // Si el titulo de la conversacion existe
            if (activeConv) {
                let texto = contenido.substring(0, 60);
                // Si el contenido es mayor a 60 caracteres
                if (contenido.length > 60) texto += '...';
                // Asigna el texto al titulo de la conversacion
                activeConv.textContent = texto;
            }
            // Reactiva el botón de nueva conversación ya que la actual ya no está vacía
            const btnNueva = document.getElementById('btn-nueva-conv');
            if (btnNueva) {
                // Habilita el boton de nueva conversacion
                btnNueva.disabled = false;
                btnNueva.style.opacity = '1';
                btnNueva.style.cursor = 'pointer';
            }
        }
    }

    const typing = document.getElementById('escribiendo-ui');
    const div = document.createElement('div');
    
    // Obtener la hora actual en formato local HH:mm
    const ahora = new Date();
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const horaFormateada = `${horas}:${minutos}`;

    if (rol === 'usuario') {
        div.className = 'msg-usuario';
        div.innerHTML = '<div class="envoltura-burbuja-usuario"><div class="burbuja-msg-usuario">' + escaparHtml(contenido) + '</div><span class="hora-mensaje">' + horaFormateada + '</span></div>';
    } else {
        div.className = 'msg-ia';
        const nombresModelos = {
            'mistral': 'Mistral 7B',
            'phi3': 'Phi-3 3.8B',
            'deepseek-coder': 'DeepSeek Coder 6.7B',
            'tinyllama': 'TinyLlama 1.1B'
        };
        const modelKey = window.MODELO_ACTUAL || 'mistral';
        const modelLabel = nombresModelos[modelKey] || modelKey;
        div.innerHTML = '<div class="avatar-msg-ia"><img src="/images/logoPAI.png" alt="PAI"></div><div class="envoltura-burbuja-ia"><div class="modelo-nombre-tag">' + modelLabel + '</div><div class="burbuja-msg-ia">' + escaparHtml(contenido) + '</div><span class="hora-mensaje">' + horaFormateada + '</span></div>';
    }
    // Inserta el mensaje en el contenedor
    container.insertBefore(div, typing);
    // Desplaza la ventana del chat hacia abajo
    window.desplazarAbajo();
    // Devuelve el div para poder modificarlo mas tarde
    return div;
}

// Guarda un mensaje de la IA en el servidor
export async function guardarMensajeEnServidor(idMensajeIa, contenido) {
    return fetch('/conversaciones/' + window.ID_CONVERSACION + '/mensajes/' + idMensajeIa + '/guardar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.TOKEN_CSRF,
        },
        body: JSON.stringify({ contenido: contenido }),
    });
}

// Lee el stream de Ollama y va agregando tokens a la burbuja
export async function leerStreamOllama(reader, burbuja, acumulador) {
    const decoder = new TextDecoder('utf-8');
    let bufferStr = '';
    let primerToken = true;

    while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        bufferStr += decoder.decode(value, { stream: true });
        // Separa el buffer en lineas
        const lineas = bufferStr.split('\n');
        // Guarda la ultima linea en el buffer
        bufferStr = lineas.pop();
        // Recorre todas las lineas
        for (const linea of lineas) {
            // Si la linea no esta vacia
            if (!linea.trim()) continue;
            // Intenta parsear la linea
            try {
                const data = JSON.parse(linea);
                const token = data.response ?? '';
                // Si el token no esta vacio
                if (token !== '') {
                    // Si es el primer token, ocultamos el indicador de escribiendo
                    if (primerToken) {
                        const escribiendoUi = document.getElementById('escribiendo-ui');
                        if (escribiendoUi) escribiendoUi.style.display = 'none';
                        const puntosDiv = document.querySelector('.puntos-escribiendo');
                        // Si existe la burbuja de IA mostramos los puntos de escribiendo
                        if (puntosDiv) {
                            puntosDiv.innerHTML = '<span></span><span></span><span></span>';
                        }
                        primerToken = false;
                    }
                    // Agrega el token al acumulador
                    acumulador.texto += token;
                    // Agrega el token a la burbuja
                    burbuja.textContent += token;
                    window.desplazarAbajo();
                }
                // Si la respuesta de ollama termina, rompe el bucle
                if (data.done) break;
            } catch (e) {
                console.error('Error parseando JSON de Ollama:', e, linea);
            }
        }
    }
}


window.toggleSelectorModelo = function () {
    const dropdown = document.getElementById('dropdown-modelos');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

window.seleccionarModelo = function (valor, etiqueta) {
    // Cerrar dropdown
    document.getElementById('dropdown-modelos').style.display = 'none';

    // Actualizar nombre visible
    document.getElementById('modelo-nombre').textContent = etiqueta;

    // Actualizar variable global
    window.MODELO_ACTUAL = valor;

    // Actualizar clase activo en opciones
    document.querySelectorAll('.opcion-modelo').forEach(op => {
        op.classList.remove('activo');
        const check = op.querySelector('.opcion-check');
        if (check) check.remove();
    });
    const opcionActiva = document.querySelector('[onclick*="' + valor + '"]');
    if (opcionActiva) {
        opcionActiva.classList.add('activo');
    }

    // Guardar en servidor
    fetch('/conversaciones/' + window.ID_CONVERSACION + '/modelo', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.TOKEN_CSRF,
        },
        body: JSON.stringify({ modelo: valor }),
    });
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', function (e) {
    const wrap = document.getElementById('selector-modelo-wrap');
    const dropdown = document.getElementById('dropdown-modelos');
    if (wrap && dropdown && !wrap.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// Sistema de notificaciones flotantes (toasts)
window.mostrarNotificacion = function (mensaje, tipo = 'warning') {
    let container = document.getElementById('pai-notification-container');
    if (!container) {
        // Crear contenedor de notificaciones
        container = document.createElement('div');
        container.id = 'pai-notification-container';
        container.className = 'pai-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `pai-toast pai-toast-${tipo}`;

    // Mostrar icono según el tipo
    let icon = '⚠️';
    if (tipo === 'error') icon = '❌';
    if (tipo === 'info') icon = 'ℹ️';

    toast.innerHTML = `<span class="pai-toast-icon">${icon}</span><span>${mensaje}</span>`;
    container.appendChild(toast);

    // Mostrar toast
    setTimeout(() => {
        toast.classList.add('mostrar');
    }, 10);

    // Ocultar toast después de 5 segundos
    setTimeout(() => {
        toast.classList.remove('mostrar');
        // Eliminar toast después de 0.4 segundos
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 5000);
}