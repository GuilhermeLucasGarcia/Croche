<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CartFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('favorite_products');
        Schema::dropIfExists('PRODUTO');
        Schema::dropIfExists('CATEGORIA');

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
    }

    public function test_can_add_item_to_cart_and_render_cart_page(): void
    {
        $cat = Category::create(['NOME' => 'Amigurumi', 'ATIVO' => true]);
        $product = Product::create([
            'CATEGORIA_ID' => $cat->id,
            'CODIGO' => 'Rena',
            'VALOR' => 110.00,
            'ESTOQUE' => 10,
            'ATIVO' => true,
        ]);

        $this->post(route('cart.items.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'color' => 'azul marinho',
            'size' => 'M',
        ])->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertStatus(200)
            ->assertSee('Rena')
            ->assertSee('2');
    }
}
