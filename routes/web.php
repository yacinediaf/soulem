<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreNameCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::get('/dashboard', function () {
    return inertia('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.google.redirect');

Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.google.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [StoreController::class, 'store'])->name('stores.store');
    Route::get('/api/store/check', StoreNameCheckController::class)->name('api.store.check');
});

require __DIR__.'/settings.php';
