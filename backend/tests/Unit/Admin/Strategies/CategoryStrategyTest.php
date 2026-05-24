<?php

namespace Tests\Unit\Admin\Strategies;

use App\Admin\Strategies\CategoryStrategy;
use App\Models\Category;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CategoryStrategyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_name_must_be_unique(): void
    {
        $strategy = new CategoryStrategy();
        Category::create(['NOME' => 'A', 'ATIVO' => true]);

        $request = Request::create('/admin/categorias', 'POST', [
            'NOME' => 'A',
            'ATIVO' => '1',
        ]);

        $this->expectException(ValidationException::class);
        $strategy->validateData($request, null);
    }

    public function test_parent_cannot_be_self_on_edit(): void
    {
        $strategy = new CategoryStrategy();
        $cat = Category::create(['NOME' => 'A', 'ATIVO' => true]);

        $request = Request::create('/admin/categorias/'.$cat->id, 'PUT', [
            'NOME' => 'A',
            'CATEGORIA_PAI_ID' => $cat->id,
            'ATIVO' => '1',
        ]);

        $this->expectException(ValidationException::class);
        $strategy->validateData($request, $cat);
    }

    public function test_create_persists_category(): void
    {
        $strategy = new CategoryStrategy();
        $parent = Category::create(['NOME' => 'Pai', 'ATIVO' => true]);

        $request = Request::create('/admin/categorias', 'POST', [
            'NOME' => 'Filha',
            'DESCRICAO' => 'Desc',
            'CATEGORIA_PAI_ID' => $parent->id,
            'ATIVO' => '0',
        ]);

        $data = $strategy->validateData($request, null);
        $model = $strategy->create($request, $data);

        $this->assertDatabaseHas('CATEGORIA', [
            'id' => $model->id,
            'NOME' => 'Filha',
            'CATEGORIA_PAI_ID' => $parent->id,
            'ATIVO' => 0,
        ]);
    }
}

