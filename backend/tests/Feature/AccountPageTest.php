<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class AccountPageTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('PESSOA', function (Blueprint $table) {
            $table->id();
            $table->timestamp('DT_ALTERACAO')->nullable();
            $table->string('IMG_URL')->nullable();
            $table->string('NOME')->nullable();
            $table->string('NOME_USUARIO')->nullable();
            $table->string('SENHA')->nullable();
            $table->string('PERFIL')->nullable();
            $table->string('EMAIL')->nullable();
            $table->string('RESET_TOKEN_HASH')->nullable();
            $table->timestamp('RESET_TOKEN_EXPIRES')->nullable();
            $table->string('SENHA_ANTERIOR_1')->nullable();
            $table->string('SENHA_ANTERIOR_2')->nullable();
            $table->string('SENHA_ANTERIOR_3')->nullable();
        });

        Schema::create('pessoa_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pessoa_id')->unique();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('push_enabled')->default(false);
            $table->boolean('sms_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('pessoa_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pessoa_id');
            $table->string('action');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function test_guest_is_redirected_when_trying_to_access_account_page(): void
    {
        $response = $this->get('/minha-conta');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_see_account_page(): void
    {
        $user = $this->makePessoa();

        $response = $this->actingAs($user)->get('/minha-conta');

        $response->assertOk();
        $response->assertSee('Minha conta');
        $response->assertSee($user->EMAIL);
    }

    public function test_profile_update_requires_valid_email(): void
    {
        $user = $this->makePessoa();

        $response = $this->actingAs($user)->patch('/minha-conta/perfil', [
            'NOME' => 'Lorena Teste',
            'EMAIL' => 'email-invalido',
            'IMG_URL' => 'https://example.com/avatar.png',
        ]);

        $response->assertSessionHasErrors('EMAIL');
    }

    public function test_password_update_enforces_strength_rules(): void
    {
        $user = $this->makePessoa([
            'SENHA' => Hash::make('Senha@123'),
        ]);

        $response = $this->actingAs($user)->patch('/minha-conta/senha', [
            'current_password' => 'Senha@123',
            'new_password' => 'fraca',
            'new_password_confirmation' => 'fraca',
        ]);

        $response->assertSessionHasErrors('new_password');
    }

    public function test_notification_preferences_are_persisted(): void
    {
        $user = $this->makePessoa();

        $response = $this->actingAs($user)->patch('/minha-conta/notificacoes', [
            'email_enabled' => '1',
            'push_enabled' => '1',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pessoa_notification_preferences', [
            'pessoa_id' => $user->id,
            'email_enabled' => 1,
            'push_enabled' => 1,
            'sms_enabled' => 0,
        ]);
    }

    private function makePessoa(array $attributes = []): Pessoa
    {
        return Pessoa::create(array_merge([
            'NOME' => 'Lorena de Teste',
            'EMAIL' => 'lorena@example.com',
            'SENHA' => Hash::make('Senha@123'),
            'PERFIL' => 'cliente',
        ], $attributes));
    }
}
