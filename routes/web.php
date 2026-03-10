<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;


Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::post('/cart/add/{id}', [CartController::class, 'add']);
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/remove/{id}', [CartController::class, 'remove']);

Route::get('/payment/check', [CartController::class, 'checkTransaction'])
    ->name('payment.check');

Route::post('/payment/pushback', [CartController::class, 'pushback']);

Route::get('/debug/tranid', [CartController::class, 'logTranId']);
Route::get('/debug/tranid-session', [CartController::class, 'logTranIdFromSession']);

