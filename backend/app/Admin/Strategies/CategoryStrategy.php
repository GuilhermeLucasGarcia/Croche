<?php

namespace App\Admin\Strategies;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryStrategy extends AbstractAdminFormStrategy
{
    public function key(): string
    {
        return 'categorias';
    }

    public function singularLabel(): string
    {
        return 'Categoria';
    }

    public function pluralLabel(): string
    {
        return 'Categorias';
    }

    public function listQuery(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));

        return Category::query()
            ->with(['parent'])
            ->when($q !== '', function (Builder $builder) use ($q) {
                $builder->where('NOME', 'like', "%{$q}%");
            })
            ->orderBy('NOME');
    }

    public function listColumns(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'NOME', 'label' => 'Nome'],
            ['key' => 'parent.NOME', 'label' => 'Categoria pai'],
            ['key' => 'ATIVO', 'label' => 'Ativo'],
        ];
    }

    public function fields(Request $request, ?Model $model = null): array
    {
        $parentQuery = Category::query()->orderBy('NOME');

        if ($model) {
            $parentQuery->where('id', '!=', $model->getKey());
        }

        $parents = $parentQuery->pluck('NOME', 'id')->all();

        return [
            [
                'name' => 'NOME',
                'label' => 'Nome',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Nome da categoria',
            ],
            [
                'name' => 'DESCRICAO',
                'label' => 'Descrição',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Descrição da categoria (opcional)',
            ],
            [
                'name' => 'IMG_URL',
                'label' => 'Imagem da Categoria',
                'type' => 'image',
                'required' => false,
            ],
            [
                'name' => 'CATEGORIA_PAI_ID',
                'label' => 'Categoria pai',
                'type' => 'select',
                'required' => false,
                'options' => $parents,
                'placeholder' => 'Nenhuma',
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
        $id = $model?->getKey();

        $parentRules = [
            'nullable',
            'integer',
            'exists:CATEGORIA,id',
        ];

        if ($id) {
            $parentRules[] = "not_in:{$id}";
        }

        return [
            'NOME' => [
                'required',
                'string',
                'max:255',
                Rule::unique('CATEGORIA', 'NOME')->ignore($id),
            ],
            'DESCRICAO' => ['nullable', 'string', 'max:2000'],
            'IMG_URL' => ['nullable', 'string', 'max:2000'],
            'IMG_URL_UPLOAD' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'CATEGORIA_PAI_ID' => $parentRules,
            'ATIVO' => ['nullable', 'boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'NOME.required' => 'Informe o nome da categoria.',
            'NOME.unique' => 'Já existe uma categoria com este nome.',
            'CATEGORIA_PAI_ID.exists' => 'Selecione uma categoria pai válida.',
        ];
    }

    public function load(Request $request, string $id): Model
    {
        return Category::query()->with('parent')->findOrFail($id);
    }

    public function create(Request $request, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');
        $data['CATEGORIA_PAI_ID'] = $request->filled('CATEGORIA_PAI_ID') ? (int) $request->input('CATEGORIA_PAI_ID') : null;

        $this->handleImageUpload($request, $data);

        $model = new Category();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');
        $data['CATEGORIA_PAI_ID'] = $request->filled('CATEGORIA_PAI_ID') ? (int) $request->input('CATEGORIA_PAI_ID') : null;

        $this->handleImageUpload($request, $data);

        $model->fill($data);
        $model->save();

        return $model;
    }

    protected function handleImageUpload(Request $request, array &$data)
    {
        // Se a imagem for removida e não enviada uma nova, isso virá nulo/vazio.
        if (!$request->filled('IMG_URL')) {
            $data['IMG_URL'] = null;
        }

        if ($request->hasFile('IMG_URL_UPLOAD')) {
            $file = $request->file('IMG_URL_UPLOAD');
            if ($file && $file->isValid()) {
                $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                
                $supabaseUrl = env('SUPABASE_URL');
                $supabaseKey = env('SUPABASE_SERVICE_ROLE_KEY');
                
                if ($supabaseUrl && $supabaseKey) {
                    $bucket = 'produtos-imagens'; // Usando o mesmo bucket de imagens
                    $url = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}";
                    
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                        'Authorization' => "Bearer {$supabaseKey}",
                        'Content-Type' => $file->getMimeType(),
                    ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                    ->post($url);
                    
                    if ($response->successful()) {
                        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                        $data['IMG_URL'] = $publicUrl;
                    }
                }
            }
        }
    }
}
