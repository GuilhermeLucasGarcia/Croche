<?php

namespace Database\Factories;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition(): array
    {
        return [
            'NOME' => $this->faker->name(),
            'NOME_USUARIO' => $this->faker->unique()->userName(),
            'EMAIL' => $this->faker->unique()->safeEmail(),
            'SENHA' => Hash::make('password'),
            'PERFIL' => 'user',
            'ATIVO' => true,
            'IMG_URL' => null,
            'RESET_TOKEN_HASH' => null,
            'RESET_TOKEN_EXPIRES' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'PERFIL' => 'admin',
        ]);
    }
}
