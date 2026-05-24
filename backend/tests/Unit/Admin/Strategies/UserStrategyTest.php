<?php

namespace Tests\Unit\Admin\Strategies;

use App\Admin\Strategies\UserStrategy;
use App\Models\Pessoa;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserStrategyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('PESSOA');

        Schema::create('PESSOA', function (Blueprint $table) {
            $table->id();
            $table->timestamp('DT_ALTERACAO')->nullable();
            $table->string('IMG_URL')->nullable();
            $table->string('NOME')->nullable();
            $table->string('NOME_USUARIO')->nullable();
            $table->string('SENHA')->nullable();
            $table->string('PERFIL')->nullable();
            $table->string('EMAIL')->nullable();
            $table->boolean('ATIVO')->default(true);
            $table->string('RESET_TOKEN_HASH')->nullable();
            $table->timestamp('RESET_TOKEN_EXPIRES')->nullable();
            $table->string('SENHA_ANTERIOR_1')->nullable();
            $table->string('SENHA_ANTERIOR_2')->nullable();
            $table->string('SENHA_ANTERIOR_3')->nullable();
        });
    }

    public function test_create_hashes_password(): void
    {
        $strategy = new UserStrategy();
        $request = Request::create('/admin/usuarios', 'POST', [
            'NOME' => 'Teste',
            'EMAIL' => 'teste@example.com',
            'SENHA' => 'Senha@123',
            'PERFIL' => 'admin',
            'ATIVO' => '1',
        ]);

        $data = $strategy->validateData($request, null);
        $model = $strategy->create($request, $data);

        $saved = Pessoa::findOrFail($model->id);
        $this->assertNotSame('Senha@123', (string) $saved->SENHA);
        $this->assertTrue(Hash::check('Senha@123', (string) $saved->SENHA));
    }

    public function test_update_keeps_password_when_blank(): void
    {
        $strategy = new UserStrategy();
        $pessoa = Pessoa::create([
            'NOME' => 'Teste',
            'EMAIL' => 'teste@example.com',
            'SENHA' => Hash::make('Senha@123'),
            'PERFIL' => 'cliente',
            'ATIVO' => true,
        ]);

        $oldHash = (string) $pessoa->SENHA;

        $request = Request::create('/admin/usuarios/'.$pessoa->id, 'PUT', [
            'NOME' => 'Teste 2',
            'EMAIL' => 'teste@example.com',
            'SENHA' => '',
            'PERFIL' => 'cliente',
            'ATIVO' => '1',
        ]);

        $data = $strategy->validateData($request, $pessoa);
        $strategy->update($request, $pessoa, $data);

        $saved = Pessoa::findOrFail($pessoa->id);
        $this->assertSame($oldHash, (string) $saved->SENHA);
        $this->assertSame('Teste 2', (string) $saved->NOME);
    }

    public function test_email_must_be_unique(): void
    {
        $strategy = new UserStrategy();
        Pessoa::create([
            'NOME' => 'A',
            'EMAIL' => 'dup@example.com',
            'SENHA' => Hash::make('Senha@123'),
            'PERFIL' => 'cliente',
            'ATIVO' => true,
        ]);

        $request = Request::create('/admin/usuarios', 'POST', [
            'NOME' => 'B',
            'EMAIL' => 'dup@example.com',
            'SENHA' => 'Senha@123',
            'PERFIL' => 'admin',
            'ATIVO' => '1',
        ]);

        $this->expectException(ValidationException::class);
        $strategy->validateData($request, null);
    }
}

