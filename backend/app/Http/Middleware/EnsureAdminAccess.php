<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && isset($user->PERFIL) && strtolower((string) $user->PERFIL) === 'admin') {
            return $next($request);
        }

        if (app()->environment('local')) {
            return $next($request);
        }

        abort(403, 'Acesso restrito.');
    }
}
