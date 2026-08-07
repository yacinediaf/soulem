<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('socialite.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('socialite.google.callback');

require __DIR__.'/settings.php';
