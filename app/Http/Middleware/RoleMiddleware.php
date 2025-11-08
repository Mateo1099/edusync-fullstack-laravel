<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware para verificar el rol del usuario autenticado.
 * Permite escalar para múltiples roles y permisos.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        // Verifica si el usuario tiene el rol requerido
        if (!$user || $user->role !== $role) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return $next($request);
    }
}
