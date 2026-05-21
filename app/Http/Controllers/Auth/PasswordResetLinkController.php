<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Si no hay conexión a internet no se puede enviar el correo
        if (!$this->hasInternetConnection()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('No hay conexión a internet. No se puede recuperar la contraseña.')]);
        }

        // Enviamos el enlace de restablecimiento de contraseña
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Comprobamos si se ha enviado el enlace de restablecimiento de contraseña
        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }

    /**
     * Comprobamos que hay una conexión a internet activa
     */
    private function hasInternetConnection(): bool
    {
        // Abrimos una conexión al servidor de google
        $connected = @fsockopen("www.google.com", 80, $errno, $errstr, 2);
        // Si hay conexión devolvemos true
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }
}
