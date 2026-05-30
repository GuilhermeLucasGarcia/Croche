<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected function makeItemKey(int $productId, string $color, string $size): string
    {
        $colorKey = Str::slug($color, '_');
        $sizeKey = Str::slug($size, '_');

        return "{$productId}-{$colorKey}-{$sizeKey}";
    }

    protected function normalizeSessionItems(mixed $sessionItems): array
    {
        if (!is_array($sessionItems)) {
            return [];
        }

        $isAssoc = array_keys($sessionItems) !== range(0, max(count($sessionItems) - 1, 0));

        if ($isAssoc) {
            return $sessionItems;
        }

        $normalized = [];
        foreach ($sessionItems as $item) {
            if (!is_array($item) || empty($item['product_id'])) {
                continue;
            }

            $productId = (int) $item['product_id'];
            $color = (string) ($item['color'] ?? 'natural');
            $size = (string) ($item['size'] ?? 'unico');
            $key = $this->makeItemKey($productId, $color, $size);
            $normalized[$key] = [
                'product_id' => $productId,
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                'color' => $color,
                'size' => $size,
            ];
        }

        return $normalized;
    }

    public function index(Request $request)
    {
        $sessionItems = $this->normalizeSessionItems($request->session()->get('cart.items', []));
        $request->session()->put('cart.items', $sessionItems);

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
            ->map(function ($item, $itemKey) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $price = (float) ($product->VALOR ?? 0);
                $subtotal = $price * $quantity;

                return [
                    'key' => $itemKey,
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:PRODUTO,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'color' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:50'],
        ]);

        $productId = (int) $data['product_id'];
        $quantity = (int) ($data['quantity'] ?? 1);
        $color = trim((string) ($data['color'] ?? 'natural')) ?: 'natural';
        $size = trim((string) ($data['size'] ?? 'unico')) ?: 'unico';

        $key = $this->makeItemKey($productId, $color, $size);
        $items = $this->normalizeSessionItems($request->session()->get('cart.items', []));

        if (isset($items[$key])) {
            $items[$key]['quantity'] = min(99, (int) $items[$key]['quantity'] + $quantity);
        } else {
            $items[$key] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'color' => $color,
                'size' => $size,
            ];
        }

        $request->session()->put('cart.items', $items);

        return redirect()->route('cart.index');
    }

    public function update(Request $request, string $itemKey)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $items = $this->normalizeSessionItems($request->session()->get('cart.items', []));

        if (!isset($items[$itemKey])) {
            return redirect()->route('cart.index');
        }

        $items[$itemKey]['quantity'] = (int) $data['quantity'];
        $request->session()->put('cart.items', $items);

        return redirect()->route('cart.index');
    }

    public function destroy(Request $request, string $itemKey)
    {
        $items = $this->normalizeSessionItems($request->session()->get('cart.items', []));
        unset($items[$itemKey]);
        $request->session()->put('cart.items', $items);

        return redirect()->route('cart.index');
    }
}
