<?php

namespace App\Admin\Strategies;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Carousel;

class CarouselStrategy extends AbstractAdminFormStrategy
{
    public function key(): string
    {
        return 'carrossel';
    }

    public function singularLabel(): string
    {
        return 'Slide';
    }

    public function pluralLabel(): string
    {
        return 'Carrossel';
    }

    public function listQuery(Request $request): Builder
    {
        return Carousel::query()->orderBy('ORDEM')->orderByDesc('id');
    }

    public function listColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID'],
            ['key' => 'TITULO', 'label' => 'Título'],
            ['key' => 'ORDEM', 'label' => 'Ordem'],
            ['key' => 'ATIVO', 'label' => 'Ativo'],
        ];
    }

    public function fields(Request $request, ?Model $model = null): array
    {
        return [
            [
                'name' => 'TITULO',
                'label' => 'Título',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Título do slide (máx 60 caracteres)',
            ],
            [
                'name' => 'DESCRICAO',
                'label' => 'Descrição',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'Descrição curta do slide (máx 120 caracteres)',
            ],
            [
                'name' => 'IMG_DESKTOP_URL',
                'label' => 'Imagem Desktop (URL)',
                'type' => 'url',
                'required' => true,
                'placeholder' => 'https://... (mínimo 1920x800px)',
            ],
            [
                'name' => 'IMG_MOBILE_URL',
                'label' => 'Imagem Mobile (URL)',
                'type' => 'url',
                'required' => true,
                'placeholder' => 'https://... (mínimo 750x1080px)',
            ],
            [
                'name' => 'LINK_DESTINO',
                'label' => 'Link de destino',
                'type' => 'url',
                'required' => true,
                'placeholder' => 'https://...',
            ],
            [
                'name' => 'ORDEM',
                'label' => 'Ordem de exibição',
                'type' => 'number',
                'required' => true,
                'placeholder' => 'Ex: 1, 2, 3...',
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
            'TITULO' => ['required', 'string', 'max:60'],
            'DESCRICAO' => ['nullable', 'string', 'max:120'],
            'IMG_DESKTOP_URL' => ['required', 'url', 'max:2000'],
            'IMG_MOBILE_URL' => ['required', 'url', 'max:2000'],
            'LINK_DESTINO' => ['required', 'url', 'max:2000'],
            'ORDEM' => ['required', 'integer'],
            'ATIVO' => ['nullable', 'boolean'],
        ];
    }

    public function load(Request $request, string $id): Model
    {
        return Carousel::findOrFail($id);
    }

    public function create(Request $request, array $data): Model
    {
        $data['ATIVO'] = isset($data['ATIVO']) ? (bool) $data['ATIVO'] : false;
        return Carousel::create($data);
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $data['ATIVO'] = isset($data['ATIVO']) ? (bool) $data['ATIVO'] : false;
        $model->update($data);
        return $model;
    }
}
