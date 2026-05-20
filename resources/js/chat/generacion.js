import { agregarMensaje, guardarMensajeEnServidor, leerStreamOllama } from './utilidades.js';

// Controlador para abortar la petición a Ollama
window.controladorAborto = null;
// Variable que almacenará si la generación fue detenida por el usuario
window.generacionDetenida = false;
// Objeto que almacenará los datos de una generación pausada para poder reanudarla
window.datosGeneracionPausada = null;

// Guarda la respuesta parcial y almacena datos para poder reanudar
async function guardarYPrepararReanudacion(idMensajeIa, respuesta, promptOriginal, burbujaIa) {
    if (idMensajeIa && respuesta !== '') {
        console.log('Guardando respuesta parcial de la IA en la base de datos...');
        try {
            await guardarMensajeEnServidor(idMensajeIa, respuesta);
            console.log('Respuesta parcial guardada correctamente.');
        } catch (errorGuardado) {
            console.error('Error al guardar la respuesta parcial:', errorGuardado);
        }

        // Almacena datos para poder reanudar la generación
        window.datosGeneracionPausada = {
            promptOriginal: promptOriginal,
            idMensajeIa: idMensajeIa,
            respuestaAcumulada: respuesta,
            burbujaIa: burbujaIa,
        };
        // Muestra el botón de reanudar
        const btnReanudar = document.getElementById('btn-reanudar');
        if (btnReanudar) btnReanudar.style.display = 'flex';
        console.log('Datos de generación pausada guardados. Se puede reanudar.');
    }
}

// Limpia el estado tras finalizar generación (éxito o error)
function finalizarGeneracion() {
    window.establecerCargando(false);
    window.generacionDetenida = false;
    document.getElementById('entrada-mensaje').focus();
}

// Detiene la generación de la ia abortando la conexión con Ollama
window.detenerGeneracion = function () {
    if (window.controladorAborto) {
        console.log('Solicitando detener la generación de la IA...');
        window.generacionDetenida = true;
        window.controladorAborto.abort();
        console.log('Señal de aborto enviada con éxito.');
        window.controladorAborto = null;
    } else {
        console.log('No hay generación activa para detener.');
    }
}

// Reanuda la generación de la ia desde donde se detuvo
window.reanudarGeneracion = async function () {
    if (!window.datosGeneracionPausada) {
        console.log('No hay generación pausada para reanudar.');
        return;
    }

    // Extrae los datos de la generación pausada
    const { promptOriginal, idMensajeIa, respuestaAcumulada, burbujaIa } = window.datosGeneracionPausada;
    window.datosGeneracionPausada = null;

    // Oculta el botón de reanudar si existe
    const btnReanudar = document.getElementById('btn-reanudar');
    if (btnReanudar) btnReanudar.style.display = 'none';

    window.establecerCargando(true);
    // Oculta el indicador de escribiendo porque ya existe la burbuja
    document.getElementById('escribiendo-ui').style.display = 'none';
    window.generacionDetenida = false;

    // Acumulador que empieza con lo que ya se tenía
    let acumulador = { texto: respuestaAcumulada };

    console.log('Reanudando generación. Texto acumulado: ' + respuestaAcumulada.length + ' caracteres.');

    try {
        window.controladorAborto = new AbortController();

        // El prompt de reanudación es el prompt original más la respuesta acumulada
        const promptContinuacion = promptOriginal + '\n' + respuestaAcumulada;

        const ollamaRes = await fetch('http://localhost:11434/api/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ model: 'mistral', prompt: promptContinuacion, stream: true }),
            signal: window.controladorAborto.signal,
        });

        if (!ollamaRes.ok) {
            throw new Error('Error al conectar con Ollama (' + ollamaRes.status + ')');
        }

        const reader = ollamaRes.body.getReader();
        await leerStreamOllama(reader, burbujaIa, acumulador);

        window.controladorAborto = null;

        // Guarda la respuesta completa original más la continuación
        await guardarMensajeEnServidor(idMensajeIa, acumulador.texto);
        console.log('Respuesta reanudada guardada correctamente.');

    } catch (err) {
        if (err.name === 'AbortError' || window.generacionDetenida) {
            console.log('Generación reanudada detenida por el usuario.');
            window.controladorAborto = null;
            await guardarYPrepararReanudacion(idMensajeIa, acumulador.texto, promptOriginal, burbujaIa);
        } else {
            agregarMensaje('ia', 'Error de conexión: ' + err.message);
        }
    } finally {
        finalizarGeneracion();
    }
}

// Maneja el envío de mensajes
window.enviar = async function () {
    const input = document.getElementById('entrada-mensaje');
    const content = input.value.trim();
    if (!content || window.enviando) return;
    input.value = '';
    input.style.height = 'auto';
    agregarMensaje('usuario', content);
    window.establecerCargando(true);
    window.generacionDetenida = false;

    // Limpia datos de generación pausada anterior al enviar nuevo mensaje
    window.datosGeneracionPausada = null;
    const btnReanudar = document.getElementById('btn-reanudar');
    if (btnReanudar) btnReanudar.style.display = 'none';

    // Variables accesibles en catch para guardar estado pausado
    let idMensajeIa = null;
    let promptOriginal = '';
    let burbujaIaRef = null;
    let acumulador = { texto: '' };

    try {
        // PHP guarda mensaje usuario y nos devuelve el prompt
        const res = await fetch('/conversaciones/' + window.ID_CONVERSACION + '/mensajes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.TOKEN_CSRF,
            },
            body: JSON.stringify({ content }),
        });

        if (!res.ok) {
            throw new Error('Error al guardar mensaje en el servidor (' + res.status + ')');
        }

        const datosServidor = await res.json();
        promptOriginal = datosServidor.prompt;
        idMensajeIa = datosServidor.mensaje_id;

        window.controladorAborto = new AbortController();

        // Streaming directo a Ollama con señal de aborto
        const ollamaRes = await fetch('http://localhost:11434/api/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ model: 'mistral', prompt: promptOriginal, stream: true }),
            signal: window.controladorAborto.signal,
        });

        if (!ollamaRes.ok) {
            throw new Error('Error al conectar con Ollama (' + ollamaRes.status + ')');
        }

        const reader = ollamaRes.body.getReader();

        // Primer token: crea la burbuja de IA
        // Usamos un wrapper para detectar el primer token
        const decoder = new TextDecoder('utf-8');
        let primerToken = true;
        let bufferStr = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            bufferStr += decoder.decode(value, { stream: true });
            const lineas = bufferStr.split('\n');
            bufferStr = lineas.pop();
            for (const linea of lineas) {
                if (!linea.trim()) continue;
                try {
                    const data = JSON.parse(linea);
                    const token = data.response ?? '';
                    if (token !== '') {
                        if (primerToken) {
                            document.getElementById('escribiendo-ui').style.display = 'none';
                            burbujaIaRef = agregarMensaje('ia', '').querySelector('.burbuja-msg-ia');
                            primerToken = false;
                        }
                        acumulador.texto += token;
                        burbujaIaRef.textContent += token;
                        window.desplazarAbajo();
                    }
                    if (data.done) break;
                } catch (e) {
                    console.error('Error parseando JSON de Ollama:', e, linea);
                }
            }
        }

        window.controladorAborto = null;

        // PHP guarda la respuesta completa
        const guardarRes = await guardarMensajeEnServidor(idMensajeIa, acumulador.texto);
        if (!guardarRes.ok) {
            throw new Error('Error al persistir la respuesta de la IA (' + guardarRes.status + ')');
        }

    } catch (err) {
        if (err.name === 'AbortError' || window.generacionDetenida) {
            console.log('Capturado AbortError. La petición HTTP fue cancelada con éxito.');
            window.controladorAborto = null;
            console.log('Generación detenida de forma íntegra por el usuario.');
            await guardarYPrepararReanudacion(idMensajeIa, acumulador.texto, promptOriginal, burbujaIaRef);
        } else {
            agregarMensaje('ia', 'Error de conexión: ' + err.message);
        }
    } finally {
        finalizarGeneracion();
    }
}
