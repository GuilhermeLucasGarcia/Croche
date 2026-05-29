<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\PessoaActivityLog;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower($request->email);
        $pessoa = Pessoa::whereRaw('LOWER(EMAIL) = ?', [$email])->first();

        // 1. Validação de credenciais e checagem de status de conta ativa
        if (!$pessoa || !Hash::check($request->password, $pessoa->SENHA)) {
            return back()->withErrors([
                'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
            ])->onlyInput('email');
        }

        if (!$pessoa->ATIVO) {
            return back()->withErrors([
                'email' => 'Esta conta está bloqueada ou inativa.',
            ])->onlyInput('email');
        }

        // Autenticar na sessão
        Auth::login($pessoa);
        $request->session()->regenerate();

        $isAdmin = $pessoa->PERFIL === 'admin';

        // 2. Geração de tokens de acesso e refresh
        $scopes = $isAdmin ? ['admin:all'] : ['user:read', 'user:write'];
        $this->generateTokensForUser($pessoa, $scopes);

        // 3. Registro de atividade de login
        $this->logActivity($pessoa, clone $request);

        // 5. Redirecionamento personalizado baseado no perfil do usuário logado
        if ($isAdmin) {
            return redirect()->intended('/admin/produtos');
        }

        return redirect()->intended('/minha-conta');
    }

    protected function generateTokensForUser(Pessoa $pessoa, array $scopes)
    {
        $accessToken = Str::random(60);
        $refreshToken = Str::random(60);

        PersonalAccessToken::create([
            'tokenable_type' => Pessoa::class,
            'tokenable_id' => $pessoa->id,
            'name' => 'Web Session Token',
            'token' => hash('sha256', $accessToken),
            'refresh_token' => hash('sha256', $refreshToken),
            'abilities' => $scopes,
            'expires_at' => now()->addHours(2),
        ]);

        // Guardamos os tokens brutos na sessão se precisarmos expô-los (ex: front-end SPA)
        session(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
    }

    protected function logActivity(Pessoa $pessoa, Request $request)
    {
        PessoaActivityLog::create([
            'pessoa_id' => $pessoa->id,
            'action' => 'Login',
            'description' => 'Login realizado com sucesso pelo IP: ' . $request->ip() . ' - Agente: ' . $request->userAgent(),
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            // Registrar logout
            PessoaActivityLog::create([
                'pessoa_id' => Auth::id(),
                'action' => 'Logout',
                'description' => 'Logout realizado pelo IP: ' . $request->ip(),
            ]);

            // Remover tokens
            PersonalAccessToken::where('tokenable_id', Auth::id())->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
