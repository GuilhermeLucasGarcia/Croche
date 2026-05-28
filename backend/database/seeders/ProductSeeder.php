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
            ['name' => 'Kit porta vinho', 'category' => 'Outros', 'price_cents' => 13000],
            ['name' => 'Capa para notebook', 'category' => 'Papelaria', 'price_cents' => 10990],
            ['name' => 'Estojo', 'category' => 'Papelaria', 'price_cents' => 6000],
            ['name' => 'Rena', 'category' => 'Amigurumi', 'price_cents' => 11000],
            ['name' => 'Prendedor de cortina', 'category' => 'Amigurumi', 'price_cents' => 8000],
            ['name' => 'Kit higiene', 'category' => 'KIT', 'price_cents' => 7000],
            ['name' => 'Girafa', 'category' => 'Amigurumi', 'price_cents' => 14000],
            ['name' => 'Porta acessório coração', 'category' => 'Acessórios', 'price_cents' => 3500],
            ['name' => 'Luci', 'category' => 'Amigurumi', 'price_cents' => 11500],
        ];

        foreach ($products as $p) {
            $cat = Category::where('slug', Str::slug($p['category']))->first();
            
            if ($cat) {
                Product::firstOrCreate([
                    'CODIGO' => $p['name']
                ], [
                    'CATEGORIA_ID' => $cat->id,
                    'VALOR' => $p['price_cents'] / 100,
                    'ESTOQUE' => 10,
                ]);
            }
        }
    }
}
