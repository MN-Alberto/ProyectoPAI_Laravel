window.desplazarAbajo = function () {
    const c = document.getElementById('contenedor-mensajes');
    c.scrollTop = c.scrollHeight;
}

window.autoAjustar = function (el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

window.manejarTecla = function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        window.enviar();
    }
}

window.alternarTema = function () {
    const esClaro = document.body.classList.toggle('light');
    localStorage.setItem('pai-theme', esClaro ? 'light' : 'dark');
    const sun = document.getElementById('icono-sol');
    const moon = document.getElementById('icono-luna');
    if (sun && moon) {
        sun.style.display = esClaro ? 'none' : 'block';
        moon.style.display = esClaro ? 'block' : 'none';
    }
}

function escaparHtml(t) {
    return t
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

window.establecerCargando = function (state) {
    window.enviando = state;
    document.getElementById('btn-enviar').disabled = state;
    document.getElementById('escribiendo-ui').style.display = state ? 'flex' : 'none';
    const logoTyping = document.getElementById('logo-escribiendo');
    if (logoTyping) {
        if (state) {
            logoTyping.classList.add('girando');
        } else {
            logoTyping.classList.remove('girando');
        }
    }
    if (state) window.desplazarAbajo();
}

function agregarMensaje(rol, contenido) {
    const container = document.getElementById('contenedor-mensajes');
    const empty = container.querySelector('.estado-vacio');
    if (empty) empty.remove();
    const typing = document.getElementById('escribiendo-ui');
    const div = document.createElement('div');
    if (rol === 'usuario') {
        div.className = 'msg-usuario';
        div.innerHTML = '<div class="burbuja-msg-usuario">' + escaparHtml(contenido) + '</div>';
    } else {
        div.className = 'msg-ia';
        div.innerHTML = '<div class="avatar-msg-ia"><img src="/images/logoPAI.png" alt="PAI"></div><div class="burbuja-msg-ia">' + escaparHtml(contenido) + '</div>';
    }
    container.insertBefore(div, typing);
    window.desplazarAbajo();
}

window.enviar = async function () {
    const input = document.getElementById('entrada-mensaje');
    const content = input.value.trim();
    if (!content || window.enviando) return;

    input.value = '';
    input.style.height = 'auto';
    agregarMensaje('usuario', content);
    window.establecerCargando(true);

    try {
        const res = await fetch('/conversaciones/' + window.ID_CONVERSACION + '/mensajes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.TOKEN_CSRF,
            },
            body: JSON.stringify({ content }),
        });

        const data = await res.json();

        if (data.error) {
            agregarMensaje('ia', 'Error: ' + data.error);
        } else {
            agregarMensaje('ia', data.response);
        }

    } catch (err) {
        agregarMensaje('ia', 'Error de conexión: ' + err.message);
    } finally {
        window.establecerCargando(false);
        document.getElementById('entrada-mensaje').focus();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Restaurar tema guardado
    const temaGuardado = localStorage.getItem('pai-theme');
    const sun = document.getElementById('icono-sol');
    const moon = document.getElementById('icono-luna');
    if (temaGuardado === 'light') {
        document.body.classList.add('light');
        if (sun) sun.style.display = 'none';
        if (moon) moon.style.display = 'block';
    } else {
        if (sun) sun.style.display = 'block';
        if (moon) moon.style.display = 'none';
    }
    window.desplazarAbajo();
});