
// Desplaza la ventana del chat hacia abajo
window.desplazarAbajo = function () {
    // Variable que almacena el contenedor de mensajes
    const c = document.getElementById('contenedor-mensajes');
    // Si el contenedor existe, desplaza la ventana del chat hacia abajo
    if (c) c.scrollTop = c.scrollHeight;
}

// Ajusta el tamaño del área de texto
window.autoAjustar = function (el) {
    // Resetea la altura del área de texto
    el.style.height = 'auto';
    // Ajusta la altura del área de texto
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

// Maneja el envío de mensajes
window.manejarTecla = function (e) {
    // Si pulsamos la tecla enter se envia el mensaje
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        window.enviar();
    }
}

// Alterna entre modo claro y oscuro
window.alternarTema = function () {
    // Variable que almacena el estado del tema
    const esClaro = document.body.classList.toggle('light');
    // Guarda el tema en localStorage
    localStorage.setItem('pai-theme', esClaro ? 'light' : 'dark');
    // Variable para cambiar el icono del sol y la luna
    const sun = document.getElementById('icono-sol');
    const moon = document.getElementById('icono-luna');
    // Si el sol y la luna existen cambia el icono
    if (sun && moon) {
        sun.style.display = esClaro ? 'none' : 'block';
        moon.style.display = esClaro ? 'block' : 'none';
    }
}

// Escapa caracteres HTML especiales para evitar inyección XSS
function escaparHtml(t) {
    return t
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

// Establece el estado de carga
window.establecerCargando = function (state) {
    // Variable que almacena el estado de carga
    window.enviando = state;
    // Si el boton de enviar existe cambia el icono
    document.getElementById('btn-enviar').disabled = state;
    // Si el estado de carga es true muestra el icono de escribiendo
    document.getElementById('escribiendo-ui').style.display = state ? 'flex' : 'none';
    // Variable que almacena el icono del logo de typing
    const logoTyping = document.getElementById('logo-escribiendo');
    // Si el logo de typing existe cambia el icono
    if (logoTyping) {
        if (state) {
            logoTyping.classList.add('girando');
        } else {
            logoTyping.classList.remove('girando');
        }
    }
    // Si el estado de carga es true desplaza la ventana del chat hacia abajo
    if (state) window.desplazarAbajo();
}

// Agrega un mensaje al chat
function agregarMensaje(rol, contenido) {
    // Variable que almacena el contenedor de mensajes
    const container = document.getElementById('contenedor-mensajes');
    // Variable que almacena el estado vacio
    const empty = container.querySelector('.estado-vacio');
    // Si el estado vacio existe eliminarlo
    if (empty) {
        empty.remove();
        // Si el rol es usuario
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
            // Si el boton de nueva conversacion existe
            if (btnNueva) {
                // Habilita el boton de nueva conversacion
                btnNueva.disabled = false;
                // Asigna el estilo al boton de nueva conversacion
                btnNueva.style.opacity = '1';
                btnNueva.style.cursor = 'pointer';
            }
        }
    }
    // Variable que almacena el estado de escribiendo
    const typing = document.getElementById('escribiendo-ui');
    // Variable que almacena el div para crear el mensaje
    const div = document.createElement('div');
    // Si el rol es usuario
    if (rol === 'usuario') {
        // Asigna el nombre de la clase para el mensaje de usuario
        div.className = 'msg-usuario';
        // Asigna el contenido del mensaje de usuario
        div.innerHTML = '<div class="burbuja-msg-usuario">' + escaparHtml(contenido) + '</div>';
    } else {
        // Si el rol es ia
        // Asigna el nombre de la clase para el mensaje de ia
        div.className = 'msg-ia';
        // Asigna el nombre de la clase para la burbuja del mensaje de usuario
        div.innerHTML = '<div class="avatar-msg-ia"><img src="/images/logoPAI.png" alt="PAI"></div><div class="burbuja-msg-ia">' + escaparHtml(contenido) + '</div>';
    }
    // Inserta el mensaje en el contenedor
    container.insertBefore(div, typing);
    // Desplaza la ventana del chat hacia abajo
    window.desplazarAbajo();
    return div;
}

// Maneja el envío de mensajes
window.enviar = async function () {
    // Variable que almacena el input
    const input = document.getElementById('entrada-mensaje');
    // Variable que almacena el contenido del input
    const content = input.value.trim();
    // Si el contenido es nulo o la ventana se esta enviando
    if (!content || window.enviando) return;
    // Limpia el input
    input.value = '';
    // Limpia el alto del input
    input.style.height = 'auto';
    // Agrega el mensaje del usuario
    agregarMensaje('usuario', content);
    // Establece el estado de cargando
    window.establecerCargando(true);

    // Intenta realizar la peticion a la api
    try {
        // PHP guarda mensaje usuario y nos devuelve el prompt
        const res = await fetch('/conversaciones/' + window.ID_CONVERSACION + '/mensajes', {
            // Metodo de la peticion
            method: 'POST',
            // Cabeceras de la peticion
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.TOKEN_CSRF,
            },
            // Cuerpo de la peticion
            body: JSON.stringify({ content }),
        });

        // Si la respuesta no es correcta
        if (!res.ok) {
            // Lanza un error
            throw new Error('Error al guardar mensaje en el servidor (' + res.status + ')');
        }

        // Variable que almacena el prompt y el id del mensaje
        const { prompt, mensaje_id } = await res.json();

        // Streaming directo a Ollama
        const ollamaRes = await fetch('http://localhost:11434/api/generate', {
            // Metodo de la peticion
            method: 'POST',
            // Cabeceras de la peticion
            headers: { 'Content-Type': 'application/json' },
            // Cuerpo de la peticion
            body: JSON.stringify({ model: 'mistral', prompt: prompt, stream: true }),
        });
        // Si la respuesta no es correcta
        if (!ollamaRes.ok) {
            // Lanza un error
            throw new Error('Error al conectar con Ollama (' + ollamaRes.status + ')');
        }
        // Variable que almacena el lector de la respuesta
        const reader = ollamaRes.body.getReader();
        // Variable que decodifica la respuesta
        const decoder = new TextDecoder('utf-8');

        // Variable que almacena la burbuja del mensaje de ia
        let burbujaIa = null;
        // Variable que almacena la respuesta completa
        let respuestaCompleta = '';
        // Variable que almacena si es el primer token
        let primerToken = true;
        // Variable que almacena el buffer de la respuesta
        let bufferStr = '';

        // Bucle que se ejecuta mientras no se reciba la señal de fin
        while (true) {
            // Lee la respuesta
            const { done, value } = await reader.read();
            // Si se ha recibido la señal de fin
            if (done) break;
            // Añade la respuesta al buffer
            bufferStr += decoder.decode(value, { stream: true });
            // Divide la respuesta en lineas
            const lineas = bufferStr.split('\n');
            // Elimina la ultima linea
            bufferStr = lineas.pop(); // Mantener línea incompleta en el buffer
            // Itera sobre las lineas
            for (const linea of lineas) {
                // Si la linea esta vacia
                if (!linea.trim()) continue;
                // Intenta realizar la peticion a la api
                try {
                    // Variable que almacena los datos de la peticion
                    const data = JSON.parse(linea);
                    // Variable que almacena el token de la peticion
                    const token = data.response ?? '';
                    // Si el token no esta vacio
                    if (token !== '') {
                        // Si es el primer token
                        if (primerToken) {
                            // Oculta el estado de escribiendo
                            document.getElementById('escribiendo-ui').style.display = 'none';
                            // Agrega el mensaje de ia
                            burbujaIa = agregarMensaje('ia', '').querySelector('.burbuja-msg-ia');
                            // Cambia a false para que no sea el primer token
                            primerToken = false;
                        }
                        // Añade el token a la respuesta completa
                        respuestaCompleta += token;
                        // Añade el token a la burbuja del mensaje de ia
                        burbujaIa.textContent += token;
                        // Desplaza la ventana del chat hacia abajo
                        window.desplazarAbajo();
                    }
                    // Si se ha recibido la señal de fin
                    if (data.done) break;
                } catch (e) {
                    console.error('Error parseando JSON de Ollama:', e, linea);
                }
            }
        }
        // PHP guarda la respuesta completa
        const guardarRes = await fetch('/conversaciones/' + window.ID_CONVERSACION + '/mensajes/' + mensaje_id + '/guardar', {
            // Metodo de la peticion
            method: 'POST',
            // Cabeceras de la peticion
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.TOKEN_CSRF,
            },
            // Cuerpo de la peticion
            body: JSON.stringify({ contenido: respuestaCompleta }),
        });
        // Si la respuesta no es correcta
        if (!guardarRes.ok) {
            // Lanza un error
            throw new Error('Error al persistir la respuesta de la IA (' + guardarRes.status + ')');
        }

    } catch (err) {
        // Agrega el mensaje de error
        agregarMensaje('ia', 'Error de conexión: ' + err.message);
    } finally {
        // Establece el estado de cargando
        window.establecerCargando(false);
        // Enfoca el input
        document.getElementById('entrada-mensaje').focus();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Restaurar tema guardado
    const temaGuardado = localStorage.getItem('pai-theme');
    // Variable que almacena el icono del sol
    const sun = document.getElementById('icono-sol');
    // Variable que almacena el icono de la luna
    const moon = document.getElementById('icono-luna');
    // Por defecto es claro, a menos que esté guardado explícitamente como dark
    if (temaGuardado === 'dark') {
        // Elimina la clase light
        document.body.classList.remove('light');
        // Muestra el icono del sol
        if (sun) sun.style.display = 'block';
        // Oculta el icono de la luna
        if (moon) moon.style.display = 'none';
    } else {
        // Agrega la clase light
        document.body.classList.add('light');
        // Muestra el icono del sol
        if (sun) sun.style.display = 'none';
        // Oculta el icono de la luna
        if (moon) moon.style.display = 'block';
    }
    // Desplaza la ventana del chat hacia abajo
    window.desplazarAbajo();

    // Modal de perfil
    const avatar = document.querySelector('.avatar-usuario');
    const modal = document.getElementById('modal-perfil');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar-modal');

    // Cierra el modal y limpia los campos
    function cerrarYLimpiarModal() {
        // Si el modal existe
        if (modal) {
            // Oculta el modal
            modal.style.display = 'none';

            // Limpiar campos de contraseña
            const inputCurrent = document.getElementById('modal-current-password');
            const inputNew = document.getElementById('modal-password');
            const inputConfirm = document.getElementById('modal-password-confirm');

            // Si el campo existe se limpia
            if (inputCurrent) inputCurrent.value = '';
            // Si el campo existe se limpia
            if (inputNew) inputNew.value = '';
            // Si el campo existe se limpia
            if (inputConfirm) inputConfirm.value = '';

            // Variables que almacenan los mensajes de alerta de error y exito
            const alertaError = modal.querySelector('.alerta-error');
            const alertaExito = modal.querySelector('.alerta-exito');
            // Si existe se elimina la alerta de error
            if (alertaError) alertaError.remove();
            // Si existe se elimina la alerta de exito
            if (alertaExito) alertaExito.remove();
        }
    }

    // Si el avatar y el modal existen se agrega un evento de click al avatar
    if (avatar && modal) {
        // Evento que abre el modal al hacer click en el avatar
        avatar.addEventListener('click', function () {
            // Muestra el modal
            modal.style.display = 'flex';
        });
    }

    // Si el modal existe
    if (modal) {
        // Si el boton de cerrar existe se agrega un evento de click
        if (btnCerrar) {
            btnCerrar.addEventListener('click', cerrarYLimpiarModal);
        }
        // Si el boton de cancelar existe se agrega un evento de click
        if (btnCancelar) {
            btnCancelar.addEventListener('click', cerrarYLimpiarModal);
        }
        // Si el modal existe se agrega un evento de click
        modal.addEventListener('click', function (e) {
            // Si el objetivo del click es el modal
            if (e.target === modal) {
                // Se cierra y limpia el modal
                cerrarYLimpiarModal();
            }
        });
    }
});