<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('assortment')->name('api.assortment.')
    ->middleware(['throttle:10,1', \App\Http\Middleware\AuthenticateAssortment::class])
    ->group(function (): void {
        Route::post('/', [\App\Http\Controllers\AssortmentController::class, 'index'])->name('index');
        Route::post('/import', [\App\Http\Controllers\AssortmentController::class, 'import'])->name('import');
        Route::post('/guns/{gun}/photos', [\App\Http\Controllers\AssortmentController::class, 'uploadPhotos'])->name('guns.photos');
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
