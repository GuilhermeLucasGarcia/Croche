<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Carousel;

class HomeController extends Controller
{
    public function index()
    {
        // Load active categories
        $categories = Category::where('ATIVO', true)
            ->orderBy('NOME')
            ->get();

        // Load active products for 'Em destaque'
        $featuredProducts = Product::with(['category'])
            ->where('ATIVO', true)
            ->orderByDesc('DESTAQUE')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        // Load active carousel slides
        $slides = Carousel::where('ATIVO', true)
            ->orderBy('ORDEM')
            ->get();

        return view('home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'slides' => $slides
        ]);
    }
}
