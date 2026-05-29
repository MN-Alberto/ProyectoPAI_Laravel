<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Modelos disponibles
    const MODELOS = [
        'mistral' => 'Mistral 7B',
        'phi3' => 'Phi-3 Mini',
        'deepseek-coder' => 'DeepSeek Coder',
        'tinyllama' => 'TinyLlama',
    ];

    /**
     * Muestra el panel de administración con todos los usuarios
     */
    public function index()
    {
        // Obtiene todos los usuarios con el conteo de conversaciones
        $usuarios = User::withCount('conversaciones')->get();

        return view('admin.admin', [
            'usuarios' => $usuarios,
            'modelosDisponibles' => self::MODELOS,
        ]);
    }

    /**
     * Actualiza los modelos permitidos de un usuario por AJAX
     */
    public function actualizarModelos(Request $request, User $user)
    {
        // Valida que los modelos enviados sean válidos
        $request->validate([
            'modelos' => 'nullable|array',
            'modelos.*' => 'string|in:' . implode(',', array_keys(self::MODELOS)),
        ]);

        $modelos = $request->input('modelos');

        // Si se envían todos los modelos o ninguno, se guarda null (acceso a todos)
        if (empty($modelos) || count($modelos) === count(self::MODELOS)) {
            $user->modelos_permitidos = null;
        } else {
            $user->modelos_permitidos = $modelos;
        }

        $user->save();

        return response()->json([
            'ok' => true,
            'message' => 'Modelos actualizados correctamente',
            'modelos_permitidos' => $user->modelos_permitidos,
        ]);
    }

    /**
     * Actualiza el nombre, email y/o contraseña de un usuario por AJAX
     */
    public function actualizarUsuario(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $datos = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->input('password'));
        }

        $user->update($datos);

        return response()->json([
            'ok' => true,
            'message' => 'Usuario actualizado correctamente',
        ]);
    }

    /**
     * Crea un nuevo usuario manualmente por AJAX
     */
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'activo' => true,
            'is_admin' => false,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Usuario creado correctamente',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'activo' => $user->activo,
                'is_admin' => $user->is_admin,
                'conversaciones_count' => 0,
            ]
        ]);
    }

    /**
     * Elimina un usuario del sistema
     */
    public function eliminarUsuario(User $user)
    {
        // No permite eliminar al propio admin
        if ($user->id === auth()->id()) {
            return response()->json([
                'ok' => false,
                'message' => 'No puedes eliminarte a ti mismo',
            ], 422);
        }

        // Elimina conversaciones y mensajes del usuario
        foreach ($user->conversaciones as $conversacion) {
            $conversacion->mensajes()->delete();
        }
        $user->conversaciones()->delete();
        $user->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    /**
     * Activa o desactiva un usuario (dar de baja / dar de alta)
     */
    public function toggleActivo(User $user)
    {
        // No permite desactivarse a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json([
                'ok' => false,
                'message' => 'No puedes desactivarte a ti mismo',
            ], 422);
        }

        $user->activo = !$user->activo;
        $user->save();

        return response()->json([
            'ok' => true,
            'message' => $user->activo ? 'Usuario activado' : 'Usuario dado de baja',
            'activo' => $user->activo,
        ]);
    }
}
