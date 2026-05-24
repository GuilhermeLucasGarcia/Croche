<?php

namespace Tests\Unit\Admin\Strategies;

use App\Admin\Strategies\ProductStrategy;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductStrategyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
            $table->timestamp('DT_ALTERACAO')->nullable();
            $table->string('CODIGO')->nullable();
            $table->string('DESCRICAO')->nullable();
            $table->double('VALOR')->nullable();
            $table->integer('ESTOQUE')->default(0);
            $table->unsignedBigInteger('CATEGORIA_ID')->nullable();
            $table->unsignedBigInteger('MARCA_ID')->nullable();
            $table->boolean('ATIVO')->default(true);
        });
    }

    public function test_validate_requires_required_fields(): void
    {
        $strategy = new ProductStrategy();
        $category = Category::create(['NOME' => 'Cat', 'ATIVO' => true]);

        $request = Request::create('/admin/produtos', 'POST', [
            'DESCRICAO' => 'Desc',
            'VALOR' => 10,
            'ESTOQUE' => 1,
            'CATEGORIA_ID' => $category->id,
        ]);

        $this->expectException(ValidationException::class);
        $strategy->validateData($request, null);
    }

    public function test_realtime_validation_validates_single_field_only(): void
    {
        $strategy = new ProductStrategy();

        $request = Request::create('/admin/produtos/validar', 'POST', [
            '_validate_field' => 'CODIGO',
            'CODIGO' => '',
        ]);

        try {
            $strategy->validateData($request, null);
            $this->fail('Esperava ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('CODIGO', $e->errors());
            $this->assertArrayNotHasKey('DESCRICAO', $e->errors());
        }
    }

    public function test_create_persists_product(): void
    {
        $strategy = new ProductStrategy();
        $category = Category::create(['NOME' => 'Cat', 'ATIVO' => true]);

        $request = Request::create('/admin/produtos', 'POST', [
            'CODIGO' => 'Produto A',
            'DESCRICAO' => 'Desc',
            'VALOR' => 19.9,
            'ESTOQUE' => 3,
            'CATEGORIA_ID' => $category->id,
            'ATIVO' => '1',
        ]);

        $data = $strategy->validateData($request, null);
        $model = $strategy->create($request, $data);

        $this->assertInstanceOf(Product::class, $model);
        $this->assertDatabaseHas('PRODUTO', [
            'id' => $model->id,
            'CODIGO' => 'Produto A',
            'ESTOQUE' => 3,
            'CATEGORIA_ID' => $category->id,
            'ATIVO' => 1,
        ]);
    }

    public function test_update_persists_changes(): void
    {
        $strategy = new ProductStrategy();
        $category = Category::create(['NOME' => 'Cat', 'ATIVO' => true]);

        $product = Product::create([
            'CODIGO' => 'Antigo',
            'DESCRICAO' => 'Desc',
            'VALOR' => 10,
            'ESTOQUE' => 1,
            'CATEGORIA_ID' => $category->id,
            'ATIVO' => true,
        ]);

        $request = Request::create('/admin/produtos/'.$product->id, 'PUT', [
            'CODIGO' => 'Novo',
            'DESCRICAO' => 'Desc',
            'VALOR' => 12.5,
            'ESTOQUE' => 2,
            'CATEGORIA_ID' => $category->id,
            'ATIVO' => '0',
        ]);

        $data = $strategy->validateData($request, $product);
        $strategy->update($request, $product, $data);

        $this->assertDatabaseHas('PRODUTO', [
            'id' => $product->id,
            'CODIGO' => 'Novo',
            'ESTOQUE' => 2,
            'ATIVO' => 0,
        ]);
    }
}

