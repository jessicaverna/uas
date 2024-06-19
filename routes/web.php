<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
use App\Models\BankAccount;


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
    
    Route::get('/bank_accounts/{bank_account}/transfer', [BankAccountController::class, 'showTransferForm'])->name('bank_accounts.show_transfer_form');
Route::post('/bank_accounts/{bank_account}/transfer', [BankAccountController::class, 'transfer'])->name('bank_accounts.transfer');
Route::post('/bank_accounts/{bank_account}/process-transfer', [BankAccountController::class, 'processTransfer'])->name('bank_accounts.process_transfer');

Route::get('bank_accounts/{bank_account}/transactions', [BankAccountController::class, 'transactions'])
->name('bank_accounts.transactions');

    Route::post('bank_accounts/{bank_account}/confirm-transfer', [BankAccountController::class, 'confirmTransfer'])->name('bank_accounts.confirm_transfer');

    Route::get('bank_accounts/{account_number}/name', function ($account_number) {
        $bankAccount = BankAccount::where('account_number', $account_number)->first();
    
        if (!$bankAccount) {
            return response()->json(['error' => 'Account number not found.'], 404);
        }
    
        return response()->json(['account_name' => $bankAccount->account_name]);
    });
    


});

require __DIR__.'/auth.php';
