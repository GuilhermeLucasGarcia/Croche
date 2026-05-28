<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PessoaSeeder extends Seeder
{
    public function run(): void
    {
        Pessoa::firstOrCreate(
            ['EMAIL' => 'admin@philoscroche.com.br'],
            [
                'NOME' => 'Administrador',
                'NOME_USUARIO' => 'admin',
                'SENHA' => Hash::make('senha123'),
                'PERFIL' => 'admin',
                'ATIVO' => true,
            ]
        );

        Pessoa::firstOrCreate(
            ['EMAIL' => 'cliente@philoscroche.com.br'],
            [
                'NOME' => 'Cliente Teste',
                'NOME_USUARIO' => 'cliente',
                'SENHA' => Hash::make('senha123'),
                'PERFIL' => 'user',
                'ATIVO' => true,
            ]
        );
    }
}
