
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
}

// Maneja el envío de mensajes
window.enviar = async function () {
    // Variable que almacena el area de entrada de mensajes
    const input = document.getElementById('entrada-mensaje');
    // Variable que almacena el contenido del mensaje
    const content = input.value.trim();
    // Si el contenido es nulo o el estado de carga es true
    if (!content || window.enviando) return;
    // Limpia el area de entrada de mensajes
    input.value = '';
    // Ajusta la altura del area de entrada de mensajes
    input.style.height = 'auto';
    // Agrega el mensaje de usuario
    agregarMensaje('usuario', content);
    // Establece el estado de carga
    window.establecerCargando(true);
    // Intentamos enviar la peticion
    try {
        // Obtenemos la respuesta de la IA
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
        // Obtenemos la respuesta de la IA
        const data = await res.json();
        // Si la respuesta es un error
        if (data.error) {
            // Agrega el mensaje de error
            agregarMensaje('ia', 'Error: ' + data.error);
        } else {
            // Si la respuesta es correcta
            agregarMensaje('ia', data.response);
        }
    } catch (err) {
        // Si hay un error de conexion
        agregarMensaje('ia', 'Error de conexión: ' + err.message);
    } finally {
        // Finalizamos el estado de carga
        window.establecerCargando(false);
        // Ponemos el foco en el area de entrada de mensajes
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
});