<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Bem-vindo de volta');
    }

    public function test_active_user_can_login_with_correct_credentials()
    {
        $user = Pessoa::factory()->create([
            'EMAIL' => 'user@example.com',
            'SENHA' => Hash::make('password123'),
            'PERFIL' => 'user',
            'ATIVO' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/minha-conta');
        $this->assertAuthenticatedAs($user);
        
        $this->assertDatabaseHas('pessoa_activity_logs', [
            'pessoa_id' => $user->id,
            'action' => 'Login',
        ]);
        
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => Pessoa::class,
        ]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = Pessoa::factory()->create([
            'EMAIL' => 'user@example.com',
            'SENHA' => Hash::make('password123'),
            'PERFIL' => 'user',
            'ATIVO' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login()
    {
        $user = Pessoa::factory()->create([
            'EMAIL' => 'user@example.com',
            'SENHA' => Hash::make('password123'),
            'PERFIL' => 'user',
            'ATIVO' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_login_with_correct_credentials_and_redirects_to_admin_panel()
    {
        $admin = Pessoa::factory()->create([
            'EMAIL' => 'admin@example.com',
            'SENHA' => Hash::make('password123'),
            'PERFIL' => 'admin',
            'ATIVO' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/produtos');
        $this->assertAuthenticatedAs($admin);
        
        $this->assertDatabaseHas('pessoa_activity_logs', [
            'pessoa_id' => $admin->id,
            'action' => 'Login',
        ]);
        
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $admin->id,
            'tokenable_type' => Pessoa::class,
        ]);
    }
}
