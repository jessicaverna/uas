<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('bank_accounts', BankAccountController::class)->except(['show']);
    Route::get('bank_accounts/{bank_account}/verify-edit', [BankAccountController::class, 'verifyEdit'])
    ->name('bank_accounts.verify_edit');
    
    Route::put('bank_accounts/{bank_account}/check-pin', [BankAccountController::class, 'checkPIN'])
    ->name('bank_accounts.check_pin');


});

require __DIR__.'/auth.php';
