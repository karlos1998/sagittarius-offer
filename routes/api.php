<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('assortment')->name('api.assortment.')
    ->middleware(['throttle:10,1', \App\Http\Middleware\AuthenticateAssortment::class])
    ->group(function (): void {
        Route::post('/', [\App\Http\Controllers\AssortmentController::class, 'index'])->name('index');
        Route::post('/import', [\App\Http\Controllers\AssortmentController::class, 'import'])->name('import');
        Route::post('/guns/{gun}/delete', [\App\Http\Controllers\AssortmentController::class, 'deleteGun'])->name('guns.delete');
        Route::post('/guns/{gun}/restore', [\App\Http\Controllers\AssortmentController::class, 'restoreGun'])->name('guns.restore');
        Route::post('/backups', [\App\Http\Controllers\AssortmentBackupController::class, 'store'])->name('backups.store');
        Route::post('/backups/{backup}/download', [\App\Http\Controllers\AssortmentBackupController::class, 'download'])->whereUuid('backup')->name('backups.download');
        Route::post('/backups/{backup}/restore', [\App\Http\Controllers\AssortmentBackupController::class, 'restore'])->whereUuid('backup')->name('backups.restore');
        Route::post('/guns/{gun}/photos', [\App\Http\Controllers\AssortmentController::class, 'uploadPhotos'])->name('guns.photos');
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
