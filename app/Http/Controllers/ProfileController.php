<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Actualiza el nombre o la contraseña del usuario
     */
    public function actualizarUsuario(Request $request): RedirectResponse
    {
        // Obtiene el usuario logueado
        $user = $request->user();

        // Reglas de validación
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['required', 'current_password'],
        ];

        // Si el campo de la contraseña está relleno se agrega la regla de validación
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        // Valida los datos
        $datos = $request->validate($rules);

        // Actualiza el nombre
        $user->name = $datos['name'];

        // Si el campo de la contraseña está relleno se actualiza la contraseña
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($datos['password']);
        }

        // Guarda los cambios en la base de datos
        $user->save();

        // Redirige a la vista anterior con un mensaje de éxito
        return back()->with('status', 'profile-chat-updated');
    }
}
