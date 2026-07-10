<?php

use App\Http\Controllers\Shop\ShopAuthController;
use App\Http\Controllers\Shop\ShopHomeController;
use App\Http\Controllers\Shop\ShopItemController;
use Illuminate\Support\Facades\Route;

Route::get('/shop', [ShopHomeController::class, 'home'])->name('shop.home');

Route::match(['get', 'post'], '/shop/login', [ShopAuthController::class, 'login'])->name('shop.login');
Route::post('/shop/logout', [ShopAuthController::class, 'logout'])->name('shop.logout');
Route::match(['get', 'post'], '/shop/register', [ShopAuthController::class, 'register'])->name('shop.register');

Route::get('/shop/items', [ShopItemController::class, 'index'])->name('shop.items.index');
Route::get('/shop/items/new', [ShopItemController::class, 'create'])->middleware('auth')->name('shop.items.create');
Route::post('/shop/items/new', [ShopItemController::class, 'create'])->middleware('auth');
Route::get('/shop/items/{id}', [ShopItemController::class, 'show'])->whereNumber('id')->name('shop.items.show');
