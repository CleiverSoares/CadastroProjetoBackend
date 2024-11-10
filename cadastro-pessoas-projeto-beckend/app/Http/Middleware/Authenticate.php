<?php
// app/Http/Middleware/Authenticate.php

// app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Verifica se o usuário está autenticado usando o guard 'sanctum'
        if (Auth::guard('sanctum')->guest()) {
            return response()->json([
                'error' => 'Você precisa estar logado para acessar esta rota.'
            ], 401);  // Retorna erro 401 (não autorizado)
        }

        // Se o usuário estiver autenticado, prossegue com a requisição
        return $next($request);
    }
}
