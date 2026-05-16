<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $sessionItems = $request->session()->get('favorites.items');

        if (empty($sessionItems)) {
            $products = Product::with('category')
                ->orderByDesc('DESTAQUE')
                ->orderByDesc('id')
                ->limit(4)
                ->get();

            $defaults = [
                ['color' => 'amarelo'],
                ['color' => 'azul'],
                ['color' => 'bege'],
                ['color' => 'vermelho'],
            ];

            $sessionItems = $products->values()->map(function ($product, $index) use ($defaults) {
                return [
                    'product_id' => $product->id,
                    'color' => $defaults[$index]['color'] ?? 'natural',
                ];
            })->all();

            $request->session()->put('favorites.items', $sessionItems);
        }

        $productIds = collect($sessionItems)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::with(['category', 'marca'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $favorites = collect($sessionItems)
            ->map(function ($item) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'color' => $item['color'] ?? 'natural',
                ];
            })
            ->filter()
            ->values();

        return view('favorites.index', [
            'customerName' => 'Lorena',
            'favorites' => $favorites,
        ]);
    }
}
