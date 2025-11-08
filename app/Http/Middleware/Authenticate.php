<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Override para evitar redirección a ruta nombrada 'login' en API.
     * Si la petición espera JSON devolvemos 401 directamente.
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null; // Fuerza respuesta 401 JSON sin intentar usar Route::has('login')
        }
        return route('login'); // Para peticiones web (si existiera la ruta)
    }
}
