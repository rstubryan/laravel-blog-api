<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'destroy']);
Route::post('/verify-token', [AuthController::class, 'verifyToken']);

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('{post}', [PostController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('{category}', [CategoryController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'store']);
        Route::put('{post}', [PostController::class, 'update']);
        Route::patch('{post}', [PostController::class, 'update']);
        Route::delete('{post}', [PostController::class, 'destroy']);
    });
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('{category}', [CategoryController::class, 'update']);
        Route::patch('{category}', [CategoryController::class, 'update']);
        Route::delete('{category}', [CategoryController::class, 'destroy']);
    });
});
