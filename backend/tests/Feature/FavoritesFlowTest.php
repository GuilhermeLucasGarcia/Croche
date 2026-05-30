<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FavoritesFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('favorite_products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('PRODUTO');
        Schema::dropIfExists('CATEGORIA');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('CATEGORIA', function (Blueprint $table) {
            $table->id();
            $table->timestamp('DT_CRIACAO')->nullable();
            $table->timestamp('DT_ALTERACAO')->nullable();
            $table->string('NOME')->nullable();
            $table->string('DESCRICAO')->nullable();
            $table->string('IMG_URL')->nullable();
            $table->boolean('ATIVO')->default(true);
            $table->unsignedBigInteger('CATEGORIA_PAI_ID')->nullable();
        });

        Schema::create('PRODUTO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CATEGORIA_ID')->nullable();
            $table->string('CODIGO')->nullable();
            $table->text('DETALHES')->nullable();
            $table->text('DESCRICAO')->nullable();
            $table->decimal('VALOR', 10, 2)->default(0);
            $table->integer('ESTOQUE')->default(0);
            $table->boolean('ATIVO')->default(true);
            $table->timestamp('DT_ALTERACAO')->nullable();
        });

        Schema::create('favorite_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('color')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function test_user_can_toggle_favorite_and_it_persists(): void
    {
        $user = User::create([
            'name' => 'Lorena',
            'email' => 'lorena@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $cat = Category::create(['NOME' => 'Amigurumi', 'ATIVO' => true]);
        $product = Product::create([
            'CATEGORIA_ID' => $cat->id,
            'CODIGO' => 'Girafa',
            'VALOR' => 140.00,
            'ESTOQUE' => 10,
            'ATIVO' => true,
        ]);

        $this->actingAs($user)
            ->post(route('favorites.toggle'), ['product_id' => $product->id, 'color' => 'natural'])
            ->assertStatus(302);

        $this->assertDatabaseHas('favorite_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertStatus(200)
            ->assertSee('Girafa');

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $product))
            ->assertRedirect(route('favorites.index'));

        $this->assertDatabaseMissing('favorite_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
