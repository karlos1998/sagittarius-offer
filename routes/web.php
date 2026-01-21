<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\GunController;
use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/guns', [GunController::class, 'index'])->name('guns.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/toggle-club-member', [CartController::class, 'toggleClubMember'])->name('cart.toggle-club-member');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
