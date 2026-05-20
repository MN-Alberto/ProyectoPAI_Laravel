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

// Alterna entre modo claro y oscuro
window.alternarTema = function () {
    const esClaro = document.body.classList.toggle('light');
    // Guarda el tema en localStorage
    localStorage.setItem('pai-theme', esClaro ? 'light' : 'dark');

    const sun = document.getElementById('icono-sol');
    const moon = document.getElementById('icono-luna');
    // Si el sol y la luna existen cambia el icono
    if (sun && moon) {
        sun.style.display = esClaro ? 'none' : 'block';
        moon.style.display = esClaro ? 'block' : 'none';
    }
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
    if (state) window.desplazarAbajo();
}

// Agrega un mensaje al chat
export function agregarMensaje(rol, contenido) {
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
    if (rol === 'usuario') {
        div.className = 'msg-usuario';
        div.innerHTML = '<div class="burbuja-msg-usuario">' + escaparHtml(contenido) + '</div>';
    } else {
        div.className = 'msg-ia';
        div.innerHTML = '<div class="avatar-msg-ia"><img src="/images/logoPAI.png" alt="PAI"></div><div class="burbuja-msg-ia">' + escaparHtml(contenido) + '</div>';
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
