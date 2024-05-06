<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/deposit', [TransactionController::class, 'depositTransction'])->name('transaction.deposit');
    Route::post('/deposit', [TransactionController::class, 'addDeposit'])->name('transaction.deposit');
    Route::get('/withdrawal', [TransactionController::class, 'withdrawTransction'])->name('transaction.withdraw');
    Route::post('/withdrawal', [TransactionController::class, 'addWithdraw'])->name('transaction.withdraw');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
