<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Pessoa;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:ensure-admin {--email=} {--password=} {--name=} {--username=}', function () {
    $email = strtolower((string) ($this->option('email') ?: env('ADMIN_BOOTSTRAP_EMAIL', 'admin@philoscroche.com.br')));
    $password = (string) ($this->option('password') ?: env('ADMIN_BOOTSTRAP_PASSWORD', 'senha123'));
    $name = (string) ($this->option('name') ?: env('ADMIN_BOOTSTRAP_NAME', 'Administrador'));
    $username = (string) ($this->option('username') ?: env('ADMIN_BOOTSTRAP_USERNAME', 'admin'));

    if (!Schema::hasTable('PESSOA')) {
        $this->error('Tabela PESSOA não existe. Rode as migrations primeiro.');
        return self::FAILURE;
    }

    if ($email === '' || $password === '') {
        $this->error('E-mail e senha são obrigatórios.');
        return self::FAILURE;
    }

    $pessoa = Pessoa::where('EMAIL', $email)->first();

    if (!$pessoa) {
        $candidateUsername = $username !== '' ? $username : 'admin';
        if (Pessoa::where('NOME_USUARIO', $candidateUsername)->exists()) {
            $candidateUsername = $candidateUsername . '_' . Str::lower(Str::random(6));
        }

        $payload = [
            'NOME' => $name !== '' ? $name : 'Administrador',
            'NOME_USUARIO' => $candidateUsername,
            'SENHA' => Hash::make($password),
            'PERFIL' => 'admin',
            'EMAIL' => $email,
        ];

        if (Schema::hasColumn('PESSOA', 'ATIVO')) {
            $payload['ATIVO'] = true;
        }

        Pessoa::create($payload);
        $this->info("Admin criado: {$email}");
        return self::SUCCESS;
    }

    $updates = [
        'PERFIL' => 'admin',
        'EMAIL' => $email,
    ];

    if ($password !== '') {
        $updates['SENHA'] = Hash::make($password);
    }

    if ($name !== '') {
        $updates['NOME'] = $name;
    }

    if ($username !== '' && $pessoa->NOME_USUARIO !== $username && !Pessoa::where('NOME_USUARIO', $username)->where('id', '!=', $pessoa->id)->exists()) {
        $updates['NOME_USUARIO'] = $username;
    }

    if (Schema::hasColumn('PESSOA', 'ATIVO')) {
        $updates['ATIVO'] = true;
    }

    $pessoa->fill($updates);
    $pessoa->save();

    $this->info("Admin atualizado: {$email}");
    return self::SUCCESS;
})->purpose('Cria/atualiza um usuário admin no banco');
