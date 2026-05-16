<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'marca']);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('NOME', $request->category);
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(12);
        $categories = Category::where('ATIVO', true)->get();

        return view('products.index', [
            'title' => 'Catálogo',
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'marca']);

        $similarProducts = Product::with(['category', 'marca'])
            ->where('id', '!=', $product->id)
            ->when($product->CATEGORIA_ID, function ($query) use ($product) {
                $query->where('CATEGORIA_ID', $product->CATEGORIA_ID);
            })
            ->orderByDesc('DESTAQUE')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        if ($similarProducts->count() < 4) {
            $existingIds = $similarProducts->pluck('id')->push($product->id);

            $fallbackProducts = Product::with(['category', 'marca'])
                ->whereNotIn('id', $existingIds)
                ->orderByDesc('DESTAQUE')
                ->orderByDesc('id')
                ->limit(4 - $similarProducts->count())
                ->get();

            $similarProducts = $similarProducts->concat($fallbackProducts);
        }

        return view('products.show', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }
}
