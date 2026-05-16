<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $sessionItems = $request->session()->get('cart.items');

        if (empty($sessionItems)) {
            $products = Product::with('category')
                ->orderByDesc('DESTAQUE')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $defaults = [
                ['quantity' => 1, 'color' => 'azul marinho', 'size' => 'unico'],
                ['quantity' => 2, 'color' => 'azul claro', 'size' => 'unico'],
                ['quantity' => 1, 'color' => 'laranja', 'size' => 'M'],
            ];

            $sessionItems = $products->values()->map(function ($product, $index) use ($defaults) {
                $preset = $defaults[$index] ?? ['quantity' => 1, 'color' => 'natural', 'size' => 'unico'];

                return [
                    'product_id' => $product->id,
                    'quantity' => $preset['quantity'],
                    'color' => $preset['color'],
                    'size' => $preset['size'],
                ];
            })->all();

            $request->session()->put('cart.items', $sessionItems);
        }

        $productIds = collect($sessionItems)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::with('category')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cartItems = collect($sessionItems)
            ->map(function ($item) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $price = (float) ($product->VALOR ?? 0);
                $subtotal = $price * $quantity;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'color' => $item['color'] ?? 'natural',
                    'size' => $item['size'] ?? 'unico',
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'shipping' => 0.0,
                ];
            })
            ->filter()
            ->values();

        $subtotal = (float) $cartItems->sum('subtotal');
        $shipping = (float) $cartItems->sum('shipping');
        $total = $subtotal + $shipping;

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }
}
