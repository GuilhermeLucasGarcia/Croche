<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\PessoaActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        /** @var Pessoa $user */
        $user = $request->user()->load(['notificationPreference', 'activityLogs']);

        $preferences = $user->notificationPreference()->firstOrCreate(
            ['pessoa_id' => $user->id],
            [
                'email_enabled' => true,
                'push_enabled' => false,
                'sms_enabled' => false,
            ]
        );

        $activities = $user->activityLogs()
            ->latest()
            ->limit(8)
            ->get();

        if ($activities->isEmpty()) {
            $activities = collect([
                (object) [
                    'created_at' => $user->DT_ALTERACAO ?? now(),
                    'action' => 'Perfil sincronizado',
                    'description' => 'Seus dados de conta foram sincronizados com a plataforma.',
                ],
                (object) [
                    'created_at' => now(),
                    'action' => 'Sessao validada',
                    'description' => 'Sua sessao ativa foi validada antes do carregamento da area Minha conta.',
                ],
            ]);
        }

        return view('account.index', [
            'user' => $user,
            'preferences' => $preferences,
            'activities' => $activities,
            'registeredAt' => $user->DT_CRIACAO ?? null,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var Pessoa $user */
        $user = $request->user();

        $validated = $request->validate([
            'NOME' => ['required', 'string', 'max:255'],
            'EMAIL' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('PESSOA', 'EMAIL')->ignore($user->id)],
            'IMG_URL' => ['nullable', 'url', 'max:2048'],
        ], [
            'NOME.required' => 'Informe seu nome completo.',
            'EMAIL.required' => 'Informe seu e-mail.',
            'EMAIL.email' => 'Informe um e-mail valido.',
            'EMAIL.unique' => 'Este e-mail ja esta em uso.',
            'IMG_URL.url' => 'Informe uma URL valida para o avatar.',
        ]);

        $validated['EMAIL'] = strtolower($validated['EMAIL']);
        $user->fill($validated);
        $user->save();

        $this->logActivity($user, 'Perfil atualizado', 'Seus dados pessoais foram atualizados com sucesso.');

        return back()->with('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Pessoa $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/',
            ],
        ], [
            'current_password.required' => 'Informe sua senha atual.',
            'new_password.required' => 'Informe a nova senha.',
            'new_password.confirmed' => 'A confirmacao da nova senha nao confere.',
            'new_password.min' => 'A nova senha deve ter no minimo 8 caracteres.',
            'new_password.regex' => 'A senha deve combinar letras, numeros e caracteres especiais.',
        ]);

        if (! $this->passwordMatches($user, $validated['current_password'])) {
            return back()->withErrors([
                'current_password' => 'A senha atual informada esta incorreta.',
            ], 'password')->withInput();
        }

        $user->SENHA_ANTERIOR_3 = $user->SENHA_ANTERIOR_2;
        $user->SENHA_ANTERIOR_2 = $user->SENHA_ANTERIOR_1;
        $user->SENHA_ANTERIOR_1 = $user->SENHA;
        $user->SENHA = Hash::make($validated['new_password']);
        $user->save();

        $this->logActivity($user, 'Senha alterada', 'Sua senha de acesso foi alterada com seguranca.');

        return back()->with('status_password', 'Senha atualizada com sucesso.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        /** @var Pessoa $user */
        $user = $request->user();

        $preferences = $user->notificationPreference()->firstOrCreate(
            ['pessoa_id' => $user->id],
            [
                'email_enabled' => true,
                'push_enabled' => false,
                'sms_enabled' => false,
            ]
        );

        $preferences->update([
            'email_enabled' => $request->boolean('email_enabled'),
            'push_enabled' => $request->boolean('push_enabled'),
            'sms_enabled' => $request->boolean('sms_enabled'),
        ]);

        $this->logActivity($user, 'Preferencias atualizadas', 'Suas preferencias de notificacao foram atualizadas.');

        return back()->with('status_notifications', 'Preferencias salvas com sucesso.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var Pessoa $user */
        $user = $request->user();

        $request->validate([
            'delete_confirmation' => ['accepted'],
            'delete_phrase' => ['required', 'in:EXCLUIR'],
        ], [
            'delete_confirmation.accepted' => 'Confirme que deseja excluir a conta.',
            'delete_phrase.required' => 'Digite EXCLUIR para confirmar.',
            'delete_phrase.in' => 'Digite exatamente EXCLUIR para concluir a exclusao.',
        ]);

        DB::transaction(function () use ($user, $request) {
            $user->notificationPreference()->delete();
            $user->activityLogs()->delete();
            $user->delete();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        });

        return redirect('/')->with('status', 'Sua conta foi excluida com sucesso.');
    }

    private function passwordMatches(Pessoa $user, string $plainPassword): bool
    {
        $storedPassword = (string) $user->SENHA;

        if ($storedPassword === '') {
            return false;
        }

        if (str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$argon2')) {
            return Hash::check($plainPassword, $storedPassword);
        }

        return hash_equals($storedPassword, $plainPassword);
    }

    private function logActivity(Pessoa $user, string $action, string $description): void
    {
        PessoaActivityLog::create([
            'pessoa_id' => $user->id,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
