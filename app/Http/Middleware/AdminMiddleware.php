<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            abort(403, 'Acesso negado.');
        }

        $user = Auth::user();

        $isAdmin = $user instanceof \App\Models\User
            && ($user->isAdmin() || $user->email === 'manuela.viana@fiocruz.br');

        if (! $isAdmin) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }

        return $next($request);
    }
}
