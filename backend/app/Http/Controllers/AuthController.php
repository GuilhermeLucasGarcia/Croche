<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\PessoaActivityLog;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $this->ensureBootstrapAdminExists();
        $pessoa = $this->findPessoaByEmail($email);

        // 1. Validação de credenciais e checagem de status de conta ativa
        if (!$pessoa || !Hash::check($request->password, $pessoa->SENHA)) {
            Log::warning('Falha no login: credenciais inválidas.', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_found' => (bool) $pessoa,
            ]);
            return back()->withErrors([
                'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
            ])->onlyInput('email');
        }

        if (Schema::hasTable('PESSOA') && Schema::hasColumn('PESSOA', 'ATIVO') && ! (bool) $pessoa->ATIVO) {
            Log::warning('Falha no login: conta inativa.', [
                'pessoa_id' => $pessoa->id,
                'email' => $email,
                'ip' => $request->ip(),
            ]);
            return back()->withErrors([
                'email' => 'Esta conta está bloqueada ou inativa.',
            ])->onlyInput('email');
        }

        // Autenticar na sessão
        Auth::login($pessoa);
        $request->session()->regenerate();

        $isAdmin = strtolower((string) $pessoa->PERFIL) === 'admin';

        // 2. Geração de tokens de acesso e refresh
        $scopes = $isAdmin ? ['admin:all'] : ['user:read', 'user:write'];
        try {
            $this->generateTokensForUser($pessoa, $scopes);
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar tokens de autenticação.', [
                'pessoa_id' => $pessoa->id,
                'exception' => $e,
            ]);
        }

        // 3. Registro de atividade de login
        try {
            $this->logActivity($pessoa, clone $request);
        } catch (\Throwable $e) {
            Log::error('Erro ao registrar log de atividade.', [
                'pessoa_id' => $pessoa->id,
                'exception' => $e,
            ]);
        }

        // 5. Redirecionamento personalizado baseado no perfil do usuário logado
        if ($isAdmin) {
            return redirect()->intended('/admin/produtos');
        }

        return redirect()->intended('/minha-conta');
    }

    protected function generateTokensForUser(Pessoa $pessoa, array $scopes)
    {
        if (!Schema::hasTable('personal_access_tokens')) {
            return;
        }

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
        if (!Schema::hasTable('pessoa_activity_logs')) {
            return;
        }

        PessoaActivityLog::create([
            'pessoa_id' => $pessoa->id,
            'action' => 'Login',
            'description' => 'Login realizado com sucesso pelo IP: ' . $request->ip() . ' - Agente: ' . $request->userAgent(),
        ]);
    }

    private function findPessoaByEmail(string $email): ?Pessoa
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            return Pessoa::whereRaw('LOWER("EMAIL") = ?', [$email])->first();
        }

        return Pessoa::whereRaw('LOWER(EMAIL) = ?', [$email])->first();
    }

    private function ensureBootstrapAdminExists(): void
    {
        $bootstrapEmail = strtolower((string) env('ADMIN_BOOTSTRAP_EMAIL', ''));
        $bootstrapPassword = (string) env('ADMIN_BOOTSTRAP_PASSWORD', '');

        if ($bootstrapEmail === '' || $bootstrapPassword === '') {
            return;
        }

        if (!Schema::hasTable('PESSOA')) {
            return;
        }

        try {
            $pessoa = $this->findPessoaByEmail($bootstrapEmail);

            if (!$pessoa) {
                $username = (string) env('ADMIN_BOOTSTRAP_USERNAME', 'admin');
                if ($username === '') {
                    $username = 'admin';
                }

                if (Pessoa::where('NOME_USUARIO', $username)->exists()) {
                    $username = $username . '_' . Str::lower(Str::random(6));
                }

                $payload = [
                    'NOME' => (string) env('ADMIN_BOOTSTRAP_NAME', 'Administrador'),
                    'NOME_USUARIO' => $username,
                    'SENHA' => Hash::make($bootstrapPassword),
                    'PERFIL' => 'admin',
                    'EMAIL' => $bootstrapEmail,
                ];

                if (Schema::hasColumn('PESSOA', 'ATIVO')) {
                    $payload['ATIVO'] = true;
                }

                Pessoa::create($payload);
                return;
            }

            $updates = [];

            if (strtolower((string) $pessoa->PERFIL) !== 'admin') {
                $updates['PERFIL'] = 'admin';
            }

            if (Schema::hasColumn('PESSOA', 'ATIVO') && ! (bool) $pessoa->ATIVO) {
                $updates['ATIVO'] = true;
            }

            if (!empty($updates)) {
                $pessoa->fill($updates);
                $pessoa->save();
            }
        } catch (\Throwable $e) {
            Log::error('Falha ao garantir bootstrap do admin.', ['exception' => $e]);
        }
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
