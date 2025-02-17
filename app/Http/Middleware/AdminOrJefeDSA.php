<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrJefeDSA
{
    public function handle(Request $request, Closure $next): Response
    {
        // Permitir solo si el usuario tiene los roles correctos
        if ($request->user()->hasAnyRole(['Administrador', 'Jefe DSA'])) {
            return $next($request);
        }

        // Si no tiene permisos, devolver un error 403
        abort(403, 'Acceso denegado');
    }
}