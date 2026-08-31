<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->email === 'manuela.viana@fiocruz.br') {
            return $next($request);
        }
        abort(403, 'Acesso negado - apenas administradores.');
    }
}