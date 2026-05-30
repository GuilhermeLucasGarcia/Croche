<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $items = [];

        if (Auth::check()) {
            $items = DB::table('favorite_products')
                ->where('user_id', Auth::id())
                ->orderByDesc('id')
                ->get()
                ->map(fn ($row) => ['product_id' => (int) $row->product_id, 'color' => (string) ($row->color ?? 'natural')])
                ->all();
        } else {
            $sessionItems = $request->session()->get('favorites.items', []);
            $items = is_array($sessionItems) ? $sessionItems : [];
        }

        $productIds = collect($items)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::with(['category', 'marca'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $favorites = collect($items)
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
            'customerName' => Auth::check() ? (Auth::user()->name ?? 'Cliente') : 'Cliente',
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:PRODUTO,id'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $productId = (int) $data['product_id'];
        $color = trim((string) ($data['color'] ?? 'natural')) ?: 'natural';

        if (Auth::check()) {
            $exists = DB::table('favorite_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                DB::table('favorite_products')
                    ->where('user_id', Auth::id())
                    ->where('product_id', $productId)
                    ->delete();
            } else {
                DB::table('favorite_products')->insert([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'color' => $color,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return back();
        }

        $sessionItems = $request->session()->get('favorites.items', []);
        $sessionItems = is_array($sessionItems) ? $sessionItems : [];

        $index = collect($sessionItems)->search(fn ($item) => is_array($item) && (int) ($item['product_id'] ?? 0) === $productId);
        if ($index !== false) {
            unset($sessionItems[$index]);
        } else {
            $sessionItems[] = ['product_id' => $productId, 'color' => $color];
        }

        $request->session()->put('favorites.items', array_values($sessionItems));

        return back();
    }

    public function destroy(Request $request, Product $product)
    {
        if (Auth::check()) {
            DB::table('favorite_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();

            return redirect()->route('favorites.index');
        }

        $sessionItems = $request->session()->get('favorites.items', []);
        $sessionItems = is_array($sessionItems) ? $sessionItems : [];
        $sessionItems = array_values(array_filter($sessionItems, fn ($item) => (int) ($item['product_id'] ?? 0) !== (int) $product->id));
        $request->session()->put('favorites.items', $sessionItems);

        return redirect()->route('favorites.index');
    }
}
