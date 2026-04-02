<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GunController;
use App\Http\Controllers\HomeController;
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

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/toggle-club-member', [CartController::class, 'toggleClubMember'])->name('cart.toggle-club-member');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('checkout.store');
    Route::post('/resend-code', [CheckoutController::class, 'resendCode'])
        ->middleware('throttle:3,1')
        ->name('checkout.resend-code');
    Route::post('/verify', [CheckoutController::class, 'verify'])
        ->middleware('throttle:10,1')
        ->name('checkout.verify');
});

Route::get('/orders/{order}/pdf', [CheckoutController::class, 'downloadPdf'])
    ->name('orders.download-pdf');
