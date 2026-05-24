<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminEntityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('account.session')->group(function () {
    Route::get('/minha-conta', [AccountController::class, 'show'])->name('account.index');
    Route::patch('/minha-conta/perfil', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/minha-conta/senha', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::patch('/minha-conta/notificacoes', [AccountController::class, 'updateNotifications'])->name('account.notifications.update');
    Route::delete('/minha-conta', [AccountController::class, 'destroy'])->name('account.destroy');
});

Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::get('/favoritos', [FavoriteController::class, 'index'])->name('favorites.index');
Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
Route::get('/produtos/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/test-carousel', function () {
    try {
        $s = app(\App\Admin\Strategies\CarouselStrategy::class);
        $s->create(request(), [
            'TITULO' => 'Slide Teste',
            'DESCRICAO' => 'Descrição do slide',
            'IMG_DESKTOP_URL' => 'https://via.placeholder.com/1920x800',
            'IMG_MOBILE_URL' => 'https://via.placeholder.com/750x1080',
            'LINK_DESTINO' => 'http://localhost:8000',
            'ORDEM' => 1,
            'ATIVO' => true
        ]);
        return 'OK';
    } catch (\Throwable $e) {
        return $e->getMessage() . ' - ' . $e->getTraceAsString();
    }
});

Route::middleware(['account.session', 'admin.access'])->prefix('admin')->group(function () {
    Route::redirect('/', '/admin/produtos')->name('admin.home');

    Route::get('/{entity}', [AdminEntityController::class, 'index'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.index');

    Route::get('/{entity}/novo', [AdminEntityController::class, 'create'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.create');

    Route::get('/{entity}/{id}/editar', [AdminEntityController::class, 'edit'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.edit');

    Route::post('/{entity}', [AdminEntityController::class, 'store'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.store');

    Route::put('/{entity}/{id}', [AdminEntityController::class, 'update'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.update');

    Route::delete('/{entity}/{id}', [AdminEntityController::class, 'destroy'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.destroy');

    Route::post('/{entity}/validar', [AdminEntityController::class, 'validateField'])
        ->whereIn('entity', ['produtos', 'categorias', 'usuarios', 'carrossel'])
        ->name('admin.validate');
});
