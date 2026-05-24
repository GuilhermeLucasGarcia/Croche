<?php

namespace App\Admin\Strategies;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserStrategy extends AbstractAdminFormStrategy
{
    public function key(): string
    {
        return 'usuarios';
    }

    public function singularLabel(): string
    {
        return 'Usuário';
    }

    public function pluralLabel(): string
    {
        return 'Usuários';
    }

    public function listQuery(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));

        return Pessoa::query()
            ->when($q !== '', function (Builder $builder) use ($q) {
                $builder->where('NOME', 'like', "%{$q}%")
                    ->orWhere('EMAIL', 'like', "%{$q}%");
            })
            ->orderByDesc('id');
    }

    public function listColumns(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'NOME', 'label' => 'Nome completo'],
            ['key' => 'EMAIL', 'label' => 'E-mail'],
            ['key' => 'PERFIL', 'label' => 'Perfil'],
            ['key' => 'ATIVO', 'label' => 'Ativo'],
        ];
    }

    public function fields(Request $request, ?Model $model = null): array
    {
        $isEdit = (bool) $model;

        return [
            [
                'name' => 'NOME',
                'label' => 'Nome completo',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Nome completo',
            ],
            [
                'name' => 'EMAIL',
                'label' => 'E-mail',
                'type' => 'email',
                'required' => true,
                'placeholder' => 'email@exemplo.com',
            ],
            [
                'name' => 'SENHA',
                'label' => 'Senha',
                'type' => 'password',
                'required' => ! $isEdit,
                'placeholder' => $isEdit ? 'Deixe em branco para manter' : 'Senha de acesso',
            ],
            [
                'name' => 'PERFIL',
                'label' => 'Perfil de acesso',
                'type' => 'select',
                'required' => true,
                'options' => [
                    'admin' => 'Admin',
                    'cliente' => 'Cliente',
                ],
                'placeholder' => 'Selecione',
            ],
            [
                'name' => 'ATIVO',
                'label' => 'Ativo',
                'type' => 'checkbox',
                'required' => false,
            ],
        ];
    }

    protected function rules(Request $request, ?Model $model = null): array
    {
        $id = $model?->getKey();
        $isEdit = (bool) $model;

        $passwordRules = [
            $isEdit ? 'nullable' : 'required',
            'min:8',
            'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/',
        ];

        return [
            'NOME' => ['required', 'string', 'max:255'],
            'EMAIL' => [
                'required',
                'email',
                'max:255',
                Rule::unique('PESSOA', 'EMAIL')->ignore($id),
            ],
            'SENHA' => $passwordRules,
            'PERFIL' => ['required', Rule::in(['admin', 'cliente'])],
            'ATIVO' => ['nullable', 'boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'NOME.required' => 'Informe o nome completo.',
            'EMAIL.required' => 'Informe o e-mail.',
            'EMAIL.email' => 'Informe um e-mail válido.',
            'EMAIL.unique' => 'Este e-mail já está em uso.',
            'SENHA.required' => 'Informe uma senha.',
            'SENHA.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'SENHA.regex' => 'A senha deve combinar letras, números e caracteres especiais.',
            'PERFIL.required' => 'Selecione um perfil.',
        ];
    }

    public function load(Request $request, string $id): Model
    {
        return Pessoa::query()->findOrFail($id);
    }

    public function create(Request $request, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');
        $data['SENHA'] = Hash::make((string) $data['SENHA']);

        $model = new Pessoa();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');

        if ($request->filled('SENHA')) {
            $data['SENHA'] = Hash::make((string) $request->input('SENHA'));
        } else {
            unset($data['SENHA']);
        }

        $model->fill($data);
        $model->save();

        return $model;
    }
}

