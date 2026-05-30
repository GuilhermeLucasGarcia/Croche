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
                'name' => 'IMG_URL',
                'label' => 'Imagem Principal (URL)',
                'type' => 'url',
                'required' => false,
                'placeholder' => 'https://...',
            ],
            [
                'name' => 'IMAGENS',
                'label' => 'Imagens do Produto',
                'type' => 'images',
                'required' => false,
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
            'IMG_URL' => ['nullable', 'url', 'max:255'],
            'IMAGENS' => ['nullable', 'array'],
            'IMAGENS.*' => ['nullable', 'string'],
            'IMAGENS_UPLOAD' => ['nullable', 'array'],
            'IMAGENS_UPLOAD.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
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
        
        $this->handleImageUploads($request, $data);

        $model = new Product();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $data['ATIVO'] = $request->boolean('ATIVO');

        $this->handleImageUploads($request, $data);

        $model->fill($data);
        $model->save();

        return $model;
    }

    protected function handleImageUploads(Request $request, array &$data)
    {
        // Se houver imagens antigas mantidas (vem como array de URLs)
        $imagens = $request->input('IMAGENS', []);
        if (!is_array($imagens)) {
            $imagens = [];
        }

        // Se houver novos uploads
        if ($request->hasFile('IMAGENS_UPLOAD')) {
            \Illuminate\Support\Facades\Log::info('Has file IMAGENS_UPLOAD', ['files' => $request->file('IMAGENS_UPLOAD')]);
            foreach ($request->file('IMAGENS_UPLOAD') as $file) {
                if ($file->isValid()) {
                    $filename = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Upload to Supabase using REST API
                    $supabaseUrl = env('SUPABASE_URL', 'https://qcpdmmnalmbzlgqccmih.supabase.co');
                    $supabaseKey = env('SUPABASE_SERVICE_ROLE_KEY'); // Will use the one from toolcall
                    
                    if ($supabaseUrl && $supabaseKey) {
                        $bucket = 'produtos-imagens';
                        $url = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}";
                        
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                            'Authorization' => "Bearer {$supabaseKey}",
                            'apikey' => $supabaseKey,
                            'Content-Type' => $file->getMimeType(),
                            'x-upsert' => 'true',
                        ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                        ->put($url);
                        
                        \Illuminate\Support\Facades\Log::info('Supabase upload response', ['status' => $response->status(), 'body' => $response->body()]);

                        if ($response->successful()) {
                            $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                            $imagens[] = $publicUrl;
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::error('Missing supabase keys');
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('File not valid');
                }
            }
        } else {
            \Illuminate\Support\Facades\Log::info('No IMAGENS_UPLOAD file');
        }
        
        $data['IMAGENS'] = array_values(array_filter($imagens));
    }
}
