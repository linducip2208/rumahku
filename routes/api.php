<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\IdeaController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('api.v1.auth.register');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('api.v1.auth.login');

    Route::get('/properties', [PropertyController::class, 'index'])->name('api.v1.properties.index');
    Route::get('/properties/{slug}', [PropertyController::class, 'show'])->name('api.v1.properties.show');

    Route::get('/projects', [ProjectController::class, 'index'])->name('api.v1.projects.index');
    Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('api.v1.projects.show');

    Route::get('/articles', [ArticleController::class, 'index'])->name('api.v1.articles.index');
    Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('api.v1.articles.show');

    Route::post('/leads', [LeadController::class, 'store'])->name('api.v1.leads.store');

    Route::get('/ideas/features', [IdeaController::class, 'features'])->name('api.v1.ideas.features');
    Route::get('/ideas', [IdeaController::class, 'index'])->name('api.v1.ideas.index');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

        Route::get('/favorites', [FavoriteController::class, 'index'])->name('api.v1.favorites.index');
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('api.v1.favorites.toggle');

        Route::get('/orders', [OrderController::class, 'index'])->name('api.v1.orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('api.v1.orders.store');
    });
});
