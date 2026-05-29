<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdmin
{
    /**
     * Redirige al panel de administración si el usuario autenticado es admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return redirect()->route('admin.index');
        }

        return $next($request);
    }
}
