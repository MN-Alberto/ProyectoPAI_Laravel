<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Mensaje;
use App\Models\Conversacion;

class MensajeController extends Controller
{
    public function store(Request $request, Conversacion $conversacion)
    {
        // Verifica que el usuario tenga permiso para acceder a la conversacion
        Gate::authorize('view', $conversacion);
        // Valida que el contenido del mensaje sea correcto
        $request->validate(['content' => 'required|string|max:10000']);

        // Crea el mensaje en la base de datos
        $conversacion->mensajes()->create([
            'rol' => 'usuario',
            'contenido' => $request->content,
        ]);

        // Si es el primer mensaje se actualiza el titulo de la conversacion
        if ($conversacion->mensajes()->count() === 1) {
            $conversacion->update([
                'tituloConversacion' => mb_substr($request->content, 0, 60),
            ]);
        }

        // Obtiene todos los mensajes ordenados por fecha
        $mensajes = $conversacion->mensajes()->orderBy('created_at')->get();
        // Genera el prompt para la ia
        $prompt = $this->generarPrompt($mensajes);

        // Crea el mensaje de respuesta en la base de datos
        $mensajeRespuesta = $conversacion->mensajes()->create([
            'rol' => 'ia',
            'contenido' => ''
        ]);
        // Devuelve el prompt y el id del mensaje de respuesta
        return response()->json([
            'prompt' => $prompt,
            'mensaje_id' => $mensajeRespuesta->id,
        ]);
    }

    public function guardarRespuesta(Request $request, Conversacion $conversacion, Mensaje $mensaje)
    {
        // Verifica que el usuario tenga permiso para acceder a la conversacion
        Gate::authorize('view', $conversacion);
        // Valida que el contenido del mensaje sea correcto
        $request->validate(['contenido' => 'required|string']);
        // Actualiza el contenido del mensaje
        $mensaje->update(['contenido' => $request->contenido]);
        // Devuelve que todo ha ido bien
        return response()->json(['ok' => true]);
    }

    private function generarPrompt($mensajes): string
    {
        // Genera el prompt para la ia
        $prompt = "<s>[INST] <<SYS>>\nYou are a helpful assistant. Always respond in the same language the user uses. If the user writes in Spanish, respond in Spanish only. If the user writes in English, respond in English only. Never mix languages or add translations in parentheses.\n<</SYS>>\n\n";

        // Variable para saber si es el primer mensaje
        $primera = true;
        // Itera sobre los mensajes
        foreach ($mensajes as $mensaje) {
            // Si el mensaje es del usuario
            if ($mensaje->rol === 'usuario') {
                // Si es el primer mensaje
                if ($primera) {
                    // Asigna el contenido del mensaje de usuario al prompt
                    $prompt .= "{$mensaje->contenido} [/INST]";
                    // Cambia a false para que no sea el primer mensaje
                    $primera = false;
                } else {
                    // Asigna el contenido del mensaje de usuario al prompt
                    $prompt .= "[INST] {$mensaje->contenido} [/INST]";
                }
                // Si el mensaje es de ia y no esta vacio
            } elseif ($mensaje->rol === 'ia' && !empty($mensaje->contenido)) {
                // Asigna el contenido del mensaje de ia al prompt
                $prompt .= " {$mensaje->contenido}</s><s>";
            }
        }
        // Devuelve el prompt
        return $prompt;
    }
}