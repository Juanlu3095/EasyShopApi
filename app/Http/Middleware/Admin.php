<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rol = $request->user()->role_id; // Obtengo el rol a partir del token de Authorization

        if($rol !== 1) { // Comprobamos que el rol de la request es de Admin
            abort(403);
        } 
        return $next($request);
    }
}
