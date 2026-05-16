<?php

namespace App\Http\Middleware;

use App\Models\Pessoa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePessoaSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        if (app()->environment('local')) {
            $pessoa = Pessoa::query()->first();

            if ($pessoa) {
                Auth::login($pessoa);
                $request->session()->regenerate();

                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sua sessao expirou. Faça login novamente para acessar sua conta.',
            ], 401);
        }

        return redirect('/')
            ->with('account_error', 'Sua sessao expirou. Faça login novamente para acessar sua conta.');
    }
}
