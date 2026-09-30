<?php

use App\Http\Controllers\Shop\ShopAuthController;
use App\Http\Controllers\Shop\ShopHomeController;
use App\Http\Controllers\Shop\ShopItemController;
use Illuminate\Support\Facades\Route;

Route::get('/shop', [ShopHomeController::class, 'home'])->name('shop.home');

Route::get('/shop/login', [ShopAuthController::class, 'showLogin'])->name('shop.login');
Route::post('/shop/login', [ShopAuthController::class, 'login']);
Route::post('/shop/logout', [ShopAuthController::class, 'logout'])->name('shop.logout');
Route::get('/shop/register', [ShopAuthController::class, 'showRegister'])->name('shop.register');
Route::post('/shop/register', [ShopAuthController::class, 'register']);

Route::get('/shop/items', [ShopItemController::class, 'index'])->name('shop.items.index');
Route::get('/shop/items/new', [ShopItemController::class, 'create'])->middleware('auth')->name('shop.items.create');
Route::post('/shop/items/new', [ShopItemController::class, 'store'])->middleware('auth')->name('shop.items.store');
Route::get('/shop/items/{id}', [ShopItemController::class, 'show'])->whereNumber('id')->name('shop.items.show');
