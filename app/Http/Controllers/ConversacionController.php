<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversacion;
use Illuminate\Support\Facades\Gate;

class ConversacionController extends Controller
{
    public function index()
    {
        $conversaciones = auth()->user()->conversaciones()->latest()->get();

        if ($conversaciones->isEmpty()) {
            $conversacion = auth()->user()->conversaciones()->create([
                'titulo' => 'Nuevo chat',
            ]);
            return redirect()->route('conversaciones.show', $conversacion->id);
        }
        return redirect()->route('conversaciones.show', $conversaciones->first());
    }

    public function store()
    {
        $conversacion = auth()->user()->conversaciones()->create([
            'titulo' => 'Nuevo chat',
        ]);
        return redirect()->route('conversaciones.show', $conversacion);
    }

    public function show(Conversacion $conversacion)
    {
        Gate::authorize('view', $conversacion);

        $conversaciones = auth()->user()->conversaciones()->latest()->get();
        $mensajes = $conversacion->mensajes()->orderBy('created_at')->get();

        return view('conversaciones.show', compact('conversacion', 'conversaciones', 'mensajes'));
    }

    public function destroy(Conversacion $conversacion)
    {
        Gate::authorize('delete', $conversacion);

        $conversacion->delete();

        return redirect()->route('conversaciones.index');
    }
}
