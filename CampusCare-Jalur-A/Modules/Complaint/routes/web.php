<?php

use Illuminate\Support\Facades\Route;
use Modules\Complaint\Http\Controllers\ComplaintController;

Route::middleware(['web', 'auth'])->prefix('complaints')->name('complaints.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index');
    Route::post('/', [ComplaintController::class, 'store'])->name('store');
    Route::put('/{complaint}', [ComplaintController::class, 'update'])->name('update');
    Route::patch('/{complaint}/status', [ComplaintController::class, 'updateStatus'])->middleware('role:admin')->name('status');
    Route::delete('/{complaint}', [ComplaintController::class, 'destroy'])->name('destroy');
    Route::get('/{complaint}/photo', [ComplaintController::class, 'photo'])->name('photo');
});
