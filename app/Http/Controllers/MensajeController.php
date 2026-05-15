<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // manejamos las peticiones http
use Illuminate\Support\Facades\Gate; // manejamos los permisos
use App\Models\Mensaje; // manejamos los mensajes
use App\Models\Conversacion; // manejamos las conversaciones

// class MensajeController extends Controller
// {
//     public function store(Request $request, Conversacion $conversacion)
//     {
//         Gate::authorize('view', $conversacion); // verificamos si el usuario tiene permiso para ver la conversación

//         $request->validate(['contenido' => 'required|string|max:10000']); // validamos que el mensaje no esté vacío y no tenga mas de 10000 caracteres

//         // Creamos el mensaje del usuario con el rol y en contenido
//         $conversacion->mensajes()->create([
//             'rol' => 'usuario',
//             'contenido' => $request->contenido
//         ]);

//         // si es el primer mensaje, actualizamos el titulo de la conversación
//         if ($conversacion->mensajes()->count() === 1) {
//             // cortamos el primer mensaje a los primeros 60 caracteres
//             $conversacion->update([
//                 'tituloConversacion' => mb_substr($request->contenido, 0, 60)
//             ]);
//         }

//         // Obtenemos todos los mensajes de la conversación ordenados por fecha
//         $mensajes = $conversacion->mensajes()->orderBy('created_at')->get();

//         // Generamos el prompt para la IA con el contexto de la conversación
//         $prompt = $this->generarPrompt($mensajes);

//         // Creamos el mensaje de la IA en la conversación con contenido vacío
//         $mensajeRespuesta = $conversacion->mensajes()->create([
//             'rol' => 'asistente',
//             'contenido' => ''
//         ]);

//         // Generamos la respuesta de la IA en streaming
//         return response()->stream(
//             function () use ($prompt, $mensajeRespuesta) {
//                 // Limpiamos el buffer de salida para evitar problemas de temporizacion y rendimiento
//                 while (ob_get_level() > 0) {
//                     ob_end_flush();
//                 }

//                 // Inicializamos la variable que almacenara la respuesta completa de la IA
//                 $respuestaCompleta = '';

//                 // Obtenemos la URL de Ollama desde el archivo de configuracion .env
//                 $hostOllama = env('OLLAMA_HOST', 'http://localhost:11434');

//                 // Preparamos el contexto de la solicitud para la API de Ollama
//                 $contexto = stream_context_create([
//                     'http' => [
//                         'method' => 'POST',
//                         'header' => "Content-Type: application/json\r\n",
//                         'content' => json_encode([
//                             'model' => 'mistral',
//                             'prompt' => $prompt,
//                             'stream' => true
//                         ]),
//                         'timeout' => 300,
//                     ]
//                 ]);

//                 // Abrimos la URL de la API de Ollama en modo lectura para recibir la respuesta en streaming
//                 // @ antes de fopen es para que no muestre un error si no se pudo conectar
//                 // si no se pudo abrir la URL, devuelve false
//                 $stream = @fopen($hostOllama . '/api/generate', 'r', false, $contexto);

//                 // si no se pudo abrir la URL, muestra un error y termina
//                 if (!$stream) {
//                     echo "data: " . json_encode(['error' => 'No se pudo conectar con la API de Ollama']) . "\n\n";
//                     // Fuerza la salida de datos en el buffer
//                     flush();
//                     return;
//                 }

//                 // mientras no se alcance el final del archivo y no se haya interrumpido la conexion
//                 while (!feof($stream) && !connection_aborted()) {
//                     // leemos una linea de la respuesta
//                     $lineaRespuesta = fgets($stream);
//                     // si la linea es nula, salimos del bucle
//                     if (empty(trim($lineaRespuesta))) {
//                         continue;
//                     }

//                     // Decodificamos la linea JSON
//                     $data = json_decode(trim($lineaRespuesta), true);

//                     // si la linea no es JSON, salimos del bucle
//                     if (!$data) {
//                         continue;
//                     }

//                     // Obtenemos el token de la respuesta
//                     $token = $data['respuesta'] ?? '';

//                     // si el token no esta vacio
//                     if ($token !== '') {
//                         $respuestaCompleta .= $token; // agregamos el token a la respuesta completa
//                         echo "data: " . json_encode(['token' => $token]) . "\n\n"; // enviamos el token en formato JSON al usuario
//                         flush(); // forzamos la salida de datos en el buffer
//                     }

//                     // si el token es nulo, salimos del bucle
//                     if ($data['done'] ?? false) {
//                         break;
//                     }
//                 }

//                 fclose($stream); // cerramos la conexion con la API de Ollama

//                 $mensajeRespuesta->update(['contenido' => $respuestaCompleta]); // actualizamos el mensaje de la IA con la respuesta completa

//                 echo "data: [DONE]\n\n"; // enviamos el token en formato JSON al usuario

//                 flush(); // forzamos la salida de datos en el buffer
//             }
//             // devolvemos la respuesta al usuario con los encabezados adecuados
//             ,
//             200,
//             [
//                 // Control de cache: desactiva el almacenamiento en cache
//                 'Cache-Control' => 'no-cache',
//                 // Tipo de contenido: es una secuencia de eventos (Server-Sent Events)
//                 'Content-Type' => 'text/event-stream',
//                 // Desactiva el buffering en servidores nginx
//                 'X-Accel-Buffering' => 'no',
//                 // Mantiene la conexion abierta
//                 'Connection' => 'keep-alive',
//             ]
//         );
//     }

//     private function generarPrompt($mensajes): string
//     {
//         // Iniciamos el prompt con el tag de inicio
//         $prompt = "<s>";
//         // Recorremos todos los mensajes de la conversación
//         foreach ($mensajes as $mensaje) {

//             // Si el mensaje es del usuario
//             if ($mensaje->rol === 'usuario') {
//                 // Agregamos el mensaje del usuario al prompt en formato [INST]
//                 $prompt .= "[INST] {$mensaje->contenido} [/INST]";
//                 // Agregamos el tag de fin de mensaje del usuario
//                 $prompt .= " </s>";

//             } elseif ($mensaje->rol === 'asistente' && !empty($mensaje->contenido)) {
//                 // Agregamos el mensaje de la IA al prompt en formato </s><s>
//                 $prompt .= " {$mensaje->contenido} </s><s>";
//             }
//         }
//         return $prompt;
//     }
// }

class MensajeController extends Controller
{
    public function store(Request $request, Conversacion $conversacion)
    {
        Gate::authorize('view', $conversacion);

        $request->validate(['contenido' => 'required|string|max:10000']);

        // Guardar mensaje del usuario
        $conversacion->mensajes()->create([
            'rol' => 'usuario',
            'contenido' => $request->contenido,
        ]);

        // Actualizar título en el primer mensaje
        if ($conversacion->mensajes()->count() === 1) {
            $conversacion->update([
                'tituloConversacion' => mb_substr($request->contenido, 0, 60),
            ]);
        }

        // Construir prompt con todo el historial
        $mensajes = $conversacion->mensajes()->orderBy('created_at')->get();
        $prompt = $this->generarPrompt($mensajes);

        $ollamaHost = env('OLLAMA_HOST', 'http://ollama:11434');

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode([
                    'model' => 'mistral',
                    'prompt' => $prompt,
                    'stream' => false,
                ]),
                'timeout' => 300,
            ],
        ]);

        $respuesta = @file_get_contents($ollamaHost . '/api/generate', false, $context);

        if (!$respuesta) {
            return response()->json(['error' => 'No se puede conectar con Ollama.'], 500);
        }

        $data = json_decode($respuesta, true);
        $contenido = $data['response'] ?? 'Sin respuesta.';

        // Guardar respuesta en BD
        $conversacion->mensajes()->create([
            'rol' => 'asistente',
            'contenido' => $contenido,
        ]);

        return response()->json(['response' => $contenido]);
    }

    private function generarPrompt($mensajes): string
    {
        $prompt = "<s>";
        foreach ($mensajes as $mensaje) {
            if ($mensaje->rol === 'usuario') {
                $prompt .= "[INST] {$mensaje->contenido} [/INST]";
            } elseif ($mensaje->rol === 'asistente' && !empty($mensaje->contenido)) {
                $prompt .= " {$mensaje->contenido}</s><s>";
            }
        }
        return $prompt;
    }
}