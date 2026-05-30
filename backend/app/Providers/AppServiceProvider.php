<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url', '');

        if ($appUrl !== '' && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        view()->composer(['home', 'products.*', 'cart.*', 'favorites.*', 'account.*'], function ($view) {
            $topCategories = collect();
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('CATEGORIA')) {
                    $topCategories = Category::query()
                        ->where('ATIVO', true)
                        ->orderBy('NOME')
                        ->take(5)
                        ->get();
                }
            } catch (\Throwable $e) {
                $topCategories = collect();
            }

            $cartItems = session('cart.items', []);
            $cartCount = is_array($cartItems) ? count($cartItems) : 0;

            $favoriteCount = 0;
            if (Auth::check()) {
                try {
                    $favoriteCount = (int) \Illuminate\Support\Facades\DB::table('favorite_products')
                        ->where('user_id', Auth::id())
                        ->count();
                } catch (\Throwable $e) {
                    $favoriteCount = 0;
                }
            } else {
                $favorites = session('favorites.items', []);
                $favoriteCount = is_array($favorites) ? count($favorites) : 0;
            }

            $view->with([
                'topCategories' => $topCategories,
                'cartCount' => $cartCount,
                'favoriteCount' => $favoriteCount,
                'siteTagline' => 'Artesanato em crochê 100% feito à mão',
            ]);
        });
    }
}
