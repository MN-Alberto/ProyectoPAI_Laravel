<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Verifica que el usuario autenticado sea administrador.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no está autenticado o no es admin, aborta con 403
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Acceso denegado. No tienes permisos de administrador.');
        }

        return $next($request);
    }
}
