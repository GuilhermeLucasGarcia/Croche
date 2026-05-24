<?php

namespace App\Admin\Strategies;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ProductStrategy extends AbstractAdminFormStrategy
{
    public function key(): string
    {
        return 'produtos';
    }

    public function singularLabel(): string
    {
        return 'Produto';
    }

    public function pluralLabel(): string
    {
        return 'Produtos';
    }

    public function listQuery(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));

        return Product::query()
            ->with(['category'])
            ->when($q !== '', function (Builder $builder) use ($q) {
                $builder->where('CODIGO', 'like', "%{$q}%")
                    ->orWhere('DESCRICAO', 'like', "%{$q}%");
            })
            ->orderByDesc('id');
    }

    public function listColumns(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'CODIGO', 'label' => 'Nome'],
            ['key' => 'VALOR', 'label' => 'Preço'],
            ['key' => 'ESTOQUE', 'label' => 'Estoque'],
            ['key' => 'category.NOME', 'label' => 'Categoria'],
            ['key' => 'ATIVO', 'label' => 'Ativo'],
        ];
    }

    public function fields(Request $request, ?Model $model = null): array
    {
        $categories = Category::query()
            ->orderBy('NOME')
            ->pluck('NOME', 'id')
            ->all();

        return [
            [
                'name' => 'CODIGO',
                'label' => 'Nome',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Nome do produto',
            ],
            [
                'name' => 'DESCRICAO',
                'label' => 'Descrição',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Descrição do produto',
            ],
            [
                'name' => 'VALOR',
                'label' => 'Preço',
                'type' => 'number',
                'required' => true,
                'step' => '0.01',
                'min' => '0',
                'placeholder' => '0,00',
            ],
            [
                'name' => 'ESTOQUE',
                'label' => 'Quantidade em estoque',
                'type' => 'number',
                'required' => true,
                'step' => '1',
                'min' => '0',
                'placeholder' => '0',
            ],
            [
                'name' => 'CATEGORIA_ID',
                'label' => 'Categoria',
                'type' => 'select',
                'required' => true,
                'options' => $categories,
                'placeholder' => 'Selecione',
            ],
            [
                'name' => 'ATIVO',
                'label' => 'Ativo',
                'type' => 'checkbox',
                'required' => false,
            ],
        ];
    }

    protected function rules(Request $request, ?Model $model = null): array
    {
        return [
            'CODIGO' => ['required', 'string', 'max:255'],
            'DESCRICAO' => ['required', 'string', 'max:2000'],
            'VALOR' => ['required', 'numeric', 'min:0'],
            'ESTOQUE' => ['required', 'integer', 'min:0'],
            'CATEGORIA_ID' => ['required', 'integer', 'exists:CATEGORIA,id'],
            'ATIVO' => ['nullable', 'boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'CODIGO.required' => 'Informe o nome do produto.',
            'DESCRICAO.required' => 'Informe a descrição.',
            'VALOR.required' => 'Informe o preço.',
            'VALOR.numeric' => 'Informe um preço válido.',
            'ESTOQUE.required' => 'Informe a quantidade em estoque.',
            'ESTOQUE.integer' => 'Informe um estoque válido.',
            'CATEGORIA_ID.required' => 'Selecione uma categoria.',
            'CATEGORIA_ID.exists' => 'Selecione uma categoria válida.',
        ];
    }

    public function load(Request $request, string $id): Model
    {
        return Product::query()->with('category')->findOrFail($id);
    }

    public function create(Request $request, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');

        $model = new Product();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');

        $model->fill($data);
        $model->save();

        return $model;
    }
}

