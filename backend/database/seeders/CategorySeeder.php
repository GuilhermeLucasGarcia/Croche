<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Amigurumi',
            'Maternidade',
            'Acessórios',
            'Decoração',
            'Outros',
            'Papelaria',
            'KIT',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => Str::slug($cat)], [
                'NOME' => $cat,
            ]);
        }
    }
}
