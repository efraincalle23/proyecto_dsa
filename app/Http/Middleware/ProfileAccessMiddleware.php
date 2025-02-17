<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $requestedId = $request->route('id');

        // Verificar si es Administrador o Jefe DSA
        /* if ($user->hasAnyRole(['Administrador', 'Jefe DSA'])) {
             return $next($request);
         }*/

        // Verificar si el usuario autenticado está accediendo a su propio perfil
        if ($user->id == $requestedId) {
            return $next($request);
        }

        // Si no cumple ninguna condición, redirigir o devolver error
        abort(403, 'Acceso denegado');
    }
}