<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;


Route::middleware('auth')->group(function () {

    Route::get('/', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/deposit', [TransactionController::class, 'depositTransction'])->name('transaction.deposit');
    Route::post('/deposit', [TransactionController::class, 'addDeposit'])->name('transaction.deposit');




    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
