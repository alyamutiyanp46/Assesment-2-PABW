<?php

use Illuminate\Support\Facades\Route;
use Modules\Facility\Http\Controllers\FacilityController;

// Route dilapisi web untuk session/CSRF lalu auth untuk memastikan user login.
Route::middleware(['web', 'auth'])->prefix('facilities')->name('facilities.')->group(function () {
    Route::get('/', [FacilityController::class, 'index'])->name('index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/', [FacilityController::class, 'store'])->name('store');
        Route::put('/{facility}', [FacilityController::class, 'update'])->name('update');
        Route::delete('/{facility}', [FacilityController::class, 'destroy'])->name('destroy');
    });
});
