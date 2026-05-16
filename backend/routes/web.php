<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('home');
});

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
