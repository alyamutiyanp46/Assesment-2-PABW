<?php
use Illuminate\Support\Facades\Route;
use Modules\Pustaka\Http\Controllers\BookController;
use Modules\Pustaka\Http\Controllers\LoanController;
Route::middleware('auth')->group(function(){
    Route::get('/books',[BookController::class,'index'])->name('books.index');
    Route::middleware('role:admin')->group(function(){Route::post('/books',[BookController::class,'store'])->name('books.store');Route::put('/books/{book}',[BookController::class,'update'])->name('books.update');Route::delete('/books/{book}',[BookController::class,'destroy'])->name('books.destroy');});
    Route::get('/loans',[LoanController::class,'index'])->name('loans.index');Route::post('/loans',[LoanController::class,'store'])->name('loans.store');
    Route::patch('/loans/{loan}',[LoanController::class,'update'])->middleware('role:admin')->name('loans.update');Route::delete('/loans/{loan}',[LoanController::class,'destroy'])->name('loans.destroy');
});
