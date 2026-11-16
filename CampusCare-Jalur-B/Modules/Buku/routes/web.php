<?php

use Illuminate\Support\Facades\Route;
use Modules\Buku\Http\Controllers\BukuController;

Route::middleware('auth')->prefix('buku')->name('buku.')->group(function () {
    Route::get('/', [BukuController::class, 'index'])->name('index');
    Route::get('/data', [BukuController::class, 'data'])->name('data');
    Route::middleware('role:admin,petugas')->group(function () {
        Route::post('/', [BukuController::class, 'store'])->name('store');
    });
    Route::middleware('role:admin')->group(function () {
        Route::put('/{buku}', [BukuController::class, 'update'])->name('update');
        Route::delete('/{buku}', [BukuController::class, 'destroy'])->name('destroy');
    });
});
