<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversacion;
use Illuminate\Support\Facades\Gate;

class ConversacionController extends Controller
{
    public function index()
    {
        $conversaciones = auth()->user()->conversaciones()->latest()->get(); // Obtiene las conversaciones del usuario ordenadas por fecha

        // Si el usuario no tiene conversaciones, crea una nueva llamada "Nuevo chat" y redirige a ella
        if ($conversaciones->isEmpty()) {
            $conversacion = auth()->user()->conversaciones()->create([
                'tituloConversacion' => 'Nuevo chat',
            ]);
            // redirige a la conversación nueva
            return redirect()->route('conversaciones.show', $conversacion->id);
        }

        // Si tiene conversaciones, redirige a la última conversación
        return redirect()->route('conversaciones.show', $conversaciones->first());
    }

    // Crear una nueva conversación
    public function store()
    {
        // Si ya hay una conversación vacía, redirigimos a esa en vez de crear otra
        // Con esto evitamos que un usuario cree un monton de conversaciones vacias con el boton de nueva conversacion
        $conversacionVacia = auth()->user()->conversaciones()->doesntHave('mensajes')->first();
        // Si hay una conversacion vacia
        if ($conversacionVacia) {
            // redirige a la conversación vacia existente
            return redirect()->route('conversaciones.show', $conversacionVacia);
        }

        // Crear una nueva conversación llamada "Nuevo chat"
        $conversacion = auth()->user()->conversaciones()->create([
            'tituloConversacion' => 'Nuevo chat',
        ]);

        // redirige a la conversación nueva
        return redirect()->route('conversaciones.show', $conversacion);
    }

    // Mostrar una conversación específica
    public function show(Conversacion $conversacion)
    {
        // Verificar si el usuario tiene permiso para ver la conversación
        Gate::authorize('view', $conversacion);

        // Obtiene todas las conversaciones del usuario ordenadas por fecha
        $conversaciones = auth()->user()->conversaciones()->latest()->get();
        // Obtiene todos los mensajes de la conversación ordenados por fecha
        $mensajes = $conversacion->mensajes()->orderBy('created_at')->get();

        // Muestra la conversación
        return view('conversaciones.show', compact('conversacion', 'conversaciones', 'mensajes'));
    }

    // Eliminar una conversación
    public function destroy(Conversacion $conversacion)
    {
        // Verificar si el usuario tiene permiso para eliminar la conversación
        Gate::authorize('delete', $conversacion);

        // Elimina la conversación
        $conversacion->delete();

        // redirige a la lista de conversaciones
        return redirect()->route('conversaciones.index');
    }
}
