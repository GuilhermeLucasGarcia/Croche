<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Kit porta vinho',
                'category' => 'Outros',
                'price_cents' => 13000,
                'description' => 'Kit artesanal em crochê para porta vinho, ideal para presentear e decorar.',
                'details' => 'Produzido manualmente com fios selecionados. Indicado para garrafas padrão de vinho (750ml).',
            ],
            [
                'name' => 'Capa para notebook',
                'category' => 'Papelaria',
                'price_cents' => 10990,
                'description' => 'Capa em crochê para proteger seu notebook no dia a dia com estilo e delicadeza.',
                'details' => 'Acabamento reforçado e textura macia. Verifique as medidas antes da compra para melhor encaixe.',
            ],
            [
                'name' => 'Estojo',
                'category' => 'Papelaria',
                'price_cents' => 6000,
                'description' => 'Estojo artesanal em crochê para organizar itens de papelaria e acessórios.',
                'details' => 'Feito à mão, leve e resistente. Ideal para canetas, lápis e pequenos objetos.',
            ],
            [
                'name' => 'Rena',
                'category' => 'Amigurumi',
                'price_cents' => 11000,
                'description' => 'Amigurumi em formato de rena feito em crochê, perfeito para decoração e coleção.',
                'details' => 'Produção artesanal com acabamento cuidadoso. Opção de personalização sob encomenda.',
            ],
            [
                'name' => 'Prendedor de cortina',
                'category' => 'Amigurumi',
                'price_cents' => 8000,
                'description' => 'Prendedor de cortina em crochê para deixar o ambiente mais acolhedor e organizado.',
                'details' => 'Peça artesanal com boa fixação. Combina com diferentes estilos de decoração.',
            ],
            [
                'name' => 'Kit higiene',
                'category' => 'KIT',
                'price_cents' => 7000,
                'description' => 'Kit higiene em crochê para organizar itens do bebê com charme e praticidade.',
                'details' => 'Confeccionado à mão, com pontos firmes e acabamento delicado. Ideal para maternidade.',
            ],
            [
                'name' => 'Girafa',
                'category' => 'Amigurumi',
                'price_cents' => 14000,
                'description' => 'Amigurumi de girafa em crochê, uma peça encantadora para presentear.',
                'details' => 'Feito manualmente com fios macios. Pode ser personalizado em cores sob encomenda.',
            ],
            [
                'name' => 'Porta acessório coração',
                'category' => 'Acessórios',
                'price_cents' => 3500,
                'description' => 'Porta acessórios em formato de coração para organizar joias e pequenos itens.',
                'details' => 'Peça artesanal em crochê, ótima para decorar e manter tudo no lugar.',
            ],
            [
                'name' => 'Luci',
                'category' => 'Amigurumi',
                'price_cents' => 11500,
                'description' => 'Amigurumi Luci feito em crochê com detalhes delicados e acabamento premium.',
                'details' => 'Produção manual com atenção aos detalhes. Personalização e variação de cores sob encomenda.',
            ],
        ];

        foreach ($products as $p) {
            $cat = Category::where('slug', Str::slug($p['category']))->first();
            
            if ($cat) {
                Product::updateOrCreate([
                    'CODIGO' => $p['name'],
                ], [
                    'CATEGORIA_ID' => $cat->id,
                    'VALOR' => $p['price_cents'] / 100,
                    'ESTOQUE' => 10,
                    'DESCRICAO' => $p['description'] ?? null,
                    'DETALHES' => $p['details'] ?? null,
                ]);
            }
        }
    }
}
