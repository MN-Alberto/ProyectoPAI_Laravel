import { agregarMensaje, guardarMensajeEnServidor } from './utilidades.js';

// Controlador para abortar la petición a Ollama
window.controladorAborto = null;
// Variable que almacenará si la generación fue detenida por el usuario
window.generacionDetenida = false;
// Objeto que almacenará los datos de una generación pausada para poder reanudarla
window.datosGeneracionPausada = null;


// Libera el bloqueo en el servidor
function liberarBloqueoServidor() {
    return fetch('/modelo/liberar-bloqueo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // Token para autenticación
            'X-CSRF-TOKEN': window.TOKEN_CSRF,
        }
    }).catch(e => console.error('Error al liberar bloqueo:', e));
}

// Maneja las notificaciones de error del servidor
async function manejarErrorRespuestaServidor(res, errorMsgDefault) {
    // 423: El modelo está ocupado y se muestra una notificación de advertencia
    if (res.status === 423) {
        try {
            const data = await res.json();
            window.mostrarNotificacion(data.message || 'El modelo está ocupado, por favor espere.', 'warning');
        } catch (e) {
            window.mostrarNotificacion('El modelo está ocupado, por favor espere.', 'warning');
        }
        // 419: La sesión ha expirado o se ha cambiado de cuenta en otra pestaña
    } else if (res.status === 419) {
        window.mostrarNotificacion('La sesión ha expirado o se ha cambiado de cuenta en otra pestaña. Por favor, recarga la página.', 'error');
        // Cualquier otro error se muestra como error
    } else {
        window.mostrarNotificacion(`${errorMsgDefault} (código ${res.status}).`, 'error');
    }
}

// Verifica si el modelo está cargado en memoria en Ollama y muestra indicador de carga si no lo está
async function verificarYMostrarCargaModelo(modelName) {
    let modelLoaded = false;
    try {
        console.log('Verificando si el modelo ' + modelName + ' está cargado en memoria...');
        const psRes = await fetch('http://localhost:11434/api/ps');
        if (psRes.ok) {
            const psData = await psRes.json();
            modelLoaded = psData.models && psData.models.some(m => m.name.startsWith(modelName));
        }
    } catch (e) {
        console.warn('Error al verificar modelo en memoria:', e);
        modelLoaded = true;
    }

    if (!modelLoaded) {
        console.log('El modelo no está cargado. Mostrando mensaje de carga...');
        const escribiendoUi = document.getElementById('escribiendo-ui');
        if (escribiendoUi) {
            escribiendoUi.style.display = 'flex';
            const puntosDiv = escribiendoUi.querySelector('.puntos-escribiendo');
            if (puntosDiv) {
                puntosDiv.innerHTML = '<div class="texto-cargando-modelo">Cargando el modelo en memoria, por favor espere...</div>';
            }
        }
    } else {
        console.log('El modelo ya está en memoria.');
    }
}

// Helper: Realiza la petición de generación a Ollama
async function solicitarGeneracionOllama(modelName, prompt, signal) {
    const res = await fetch('http://localhost:11434/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ model: modelName, prompt: prompt, stream: true }),
        signal: signal,
    });

    if (!res.ok) {
        throw new Error('Error al conectar con Ollama (' + res.status + ')');
    }
    return res;
}

// Versión definitiva y funcional de esta función, por fin funciona correctamente
// Lee la respuesta progresiva de Ollama y ejecuta acciones específicas al recibir los datos
async function leerStreamOllamaConCallbacks(reader, acumulador, onFirstToken, onToken) {
    // Convierte los datos recibidos en texto legible
    const decoder = new TextDecoder('utf-8');
    let bufferStr = '';
    let primerToken = true;

    // Bucle para leer la respuesta de la IA paso a paso
    while (true) {
        // Lee una parte del flujo de datos
        const { done, value } = await reader.read();
        if (done) break; // Si ya no hay más datos, termina el bucle

        // Convierte esta parte a texto y la añade al texto temporal sin procesar
        bufferStr += decoder.decode(value, { stream: true });

        // Las respuestas de Ollama vienen separadas por saltos de línea.
        // Dividimos el texto acumulado en líneas para procesarlas una a una.
        const lineas = bufferStr.split('\n');

        // El último elemento puede estar incompleto, así que lo guardamos para completarlo en la siguiente lectura
        bufferStr = lineas.pop();

        for (const linea of lineas) {
            // Ignora líneas que estén vacías
            if (!linea.trim()) continue;
            try {
                // Cada línea contiene información en formato JSON. La convertimos a un objeto de JavaScript.
                const data = JSON.parse(linea);
                // Extrae el token generado por la IA
                const token = data.response ?? '';

                // Solo procesamos tokens si existen
                if (token !== '') {
                    // Si es el primer token que recibimos de la IA
                    if (primerToken) {
                        // Oculta el mensaje indicador de "Escribiendo..."
                        const escribiendoUi = document.getElementById('escribiendo-ui');
                        if (escribiendoUi) escribiendoUi.style.display = 'none';

                        // Restablece los puntos de animación del indicador visual
                        const puntosDiv = document.querySelector('.puntos-escribiendo');
                        if (puntosDiv) {
                            puntosDiv.innerHTML = '<span></span><span></span><span></span>';
                        }

                        // Ejecuta la acción personalizada para el primer fragmento (como crear la burbuja de chat de la IA)
                        if (onFirstToken) {
                            onFirstToken();
                        }
                        primerToken = false; // Marca que ya se recibió el primer fragmento
                    }

                    // Guarda el fragmento en el texto final acumulado
                    acumulador.texto += token;

                    // Ejecuta la acción personalizada para pintar este fragmento en la pantalla
                    if (onToken) {
                        onToken(token);
                    }

                    // Desplaza la conversación hacia abajo para que el nuevo texto sea visible
                    window.desplazarAbajo();
                }

                // Si la IA indica que ha terminado de responder, salimos del bucle
                if (data.done) break;
            } catch (e) {
                console.error('Error parseando JSON de Ollama:', e, linea);
            }
        }
    }
}

// Maneja los errores ocurridos durante la generación
async function manejarErrorGeneracion(err, idMensajeIa, textoAcumulado, promptOriginal, burbujaIa) {
    // Si la generación fue detenida por el usuario
    if (err.name === 'AbortError' || window.generacionDetenida) {
        console.log('Generación detenida por el usuario.');
        window.controladorAborto = null;
        // Si el texto de la burbuja no está vacío, lo guarda y prepara la reanudación
        if (textoAcumulado !== '' && burbujaIa) {
            await guardarYPrepararReanudacion(idMensajeIa, textoAcumulado, promptOriginal, burbujaIa);
        }
    } else {
        agregarMensaje('ia', 'Error de conexión: ' + err.message);
        await liberarBloqueoServidor();
    }
}

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
    const entradaMensaje = document.getElementById('entrada-mensaje');
    if (entradaMensaje) entradaMensaje.focus();
}

// Detiene la generación de la ia abortando la conexión con Ollama
window.detenerGeneracion = function () {
    if (window.controladorAborto) {
        console.log('Solicitando detener la generación de la IA...');
        window.generacionDetenida = true;
        window.controladorAborto.abort();
        console.log('Señal de aborto enviada con éxito.');
        window.controladorAborto = null;

        // Liberar bloqueo en el servidor
        liberarBloqueoServidor();
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

    // Oculta el botón de reanudar si existe
    const btnReanudar = document.getElementById('btn-reanudar');
    if (btnReanudar) btnReanudar.style.display = 'none';

    window.establecerCargando(true);

    try {
        // Intentar adquirir el bloqueo en el servidor
        const lockRes = await fetch('/modelo/adquirir-bloqueo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.TOKEN_CSRF,
            }
        });

        // Comprobar que se adquirió el bloqueo
        if (!lockRes.ok) {
            await manejarErrorRespuestaServidor(lockRes, 'Error al reanudar la generación');
            if (btnReanudar) btnReanudar.style.display = 'flex';
            finalizarGeneracion();
            return;
        }

        // Ya adquirimos el bloqueo, podemos quitar la referencia de datosGeneracionPausada
        window.datosGeneracionPausada = null;

        // Oculta el indicador de escribiendo porque ya existe la burbuja, a menos que el modelo se esté cargando
        const escribiendoUi = document.getElementById('escribiendo-ui');
        if (escribiendoUi) escribiendoUi.style.display = 'none';
        window.generacionDetenida = false;

        // Acumulador que empieza con lo que ya se tenía
        let acumulador = { texto: respuestaAcumulada };

        console.log('Reanudando generación. Texto acumulado: ' + respuestaAcumulada.length + ' caracteres.');

        window.controladorAborto = new AbortController();

        const modelName = window.MODELO_ACTUAL || 'mistral';
        await verificarYMostrarCargaModelo(modelName);

        // El prompt de reanudación es el prompt original más la respuesta acumulada
        const promptContinuacion = promptOriginal + '\n' + respuestaAcumulada;

        const ollamaRes = await solicitarGeneracionOllama(modelName, promptContinuacion, window.controladorAborto.signal);
        const reader = ollamaRes.body.getReader();

        await leerStreamOllamaConCallbacks(
            reader,
            acumulador,
            null,
            (token) => {
                burbujaIa.textContent += token;
            }
        );

        window.controladorAborto = null;

        // Guarda la respuesta completa original más la continuación
        await guardarMensajeEnServidor(idMensajeIa, acumulador.texto);
        console.log('Respuesta reanudada guardada correctamente.');

    } catch (err) {
        await manejarErrorGeneracion(err, idMensajeIa, acumulador.texto, promptOriginal, burbujaIa);
    } finally {
        finalizarGeneracion();
    }
}

// Maneja el envío de mensajes
window.enviar = async function () {
    const input = document.getElementById('entrada-mensaje');
    if (!input) return;
    const content = input.value.trim();
    if (!content || window.enviando) return;
    input.value = '';
    input.style.height = 'auto';

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
            await manejarErrorRespuestaServidor(res, 'Error al enviar el mensaje');
            input.value = content; // Restaurar texto
            finalizarGeneracion();
            return;
        }

        // Si se guardó correctamente, agregamos el mensaje del usuario a la interfaz
        agregarMensaje('usuario', content);

        // Obtenemos la respuesta de PHP con el prompt y el id del mensaje
        const datosServidor = await res.json();
        promptOriginal = datosServidor.prompt;
        idMensajeIa = datosServidor.mensaje_id;

        window.controladorAborto = new AbortController();

        // Obtenemos el modelo actual, por defecto mistral
        const modelName = window.MODELO_ACTUAL || 'mistral';

        // Verificamos si el modelo está cargado en memoria y mostramos aviso si no
        await verificarYMostrarCargaModelo(modelName);

        // Streaming directo a Ollama con señal de aborto
        const ollamaRes = await solicitarGeneracionOllama(modelName, promptOriginal, window.controladorAborto.signal);
        const reader = ollamaRes.body.getReader();

        await leerStreamOllamaConCallbacks(
            reader,
            acumulador,
            () => {
                const nuevaBurbuja = agregarMensaje('ia', '');
                if (nuevaBurbuja) {
                    burbujaIaRef = nuevaBurbuja.querySelector('.burbuja-msg-ia');
                }
            },
            (token) => {
                if (burbujaIaRef) {
                    burbujaIaRef.textContent += token;
                }
            }
        );

        window.controladorAborto = null;

        // PHP guarda la respuesta completa
        const guardarRes = await guardarMensajeEnServidor(idMensajeIa, acumulador.texto);
        if (!guardarRes.ok) {
            throw new Error('Error al persistir la respuesta de la IA (' + guardarRes.status + ')');
        }

    } catch (err) {
        await manejarErrorGeneracion(err, idMensajeIa, acumulador.texto, promptOriginal, burbujaIaRef);
    } finally {
        finalizarGeneracion();
    }
}
