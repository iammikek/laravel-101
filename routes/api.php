<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:api');

Route::get('/items/stats/summary', [ItemController::class, 'stats']);
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{itemId}', [ItemController::class, 'show'])->whereNumber('itemId');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{categoryId}', [CategoryController::class, 'show'])->whereNumber('categoryId');

Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::post('/items', [ItemController::class, 'store']);
    Route::patch('/items/{itemId}', [ItemController::class, 'update'])->whereNumber('itemId');
    Route::delete('/items/{itemId}', [ItemController::class, 'destroy'])->whereNumber('itemId');

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::patch('/categories/{categoryId}', [CategoryController::class, 'update'])->whereNumber('categoryId');
    Route::delete('/categories/{categoryId}', [CategoryController::class, 'destroy'])->whereNumber('categoryId');
});
