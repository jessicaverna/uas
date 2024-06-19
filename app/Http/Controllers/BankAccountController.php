<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = auth()->user()->bankAccounts;
        return view('bank_accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank_accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number',
            'pin' => 'required|digits:4',
        ]);

        $bankAccount = new BankAccount([
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'pin' => $request->pin,
            'balance' => 0.00,
        ]);

        auth()->user()->bankAccounts()->save($bankAccount);

        return redirect()->route('bank_accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(BankAccount $bank_account)
    {
        // Verifikasi PIN
        if (auth()->user()->id !== $bank_account->user_id) {
            abort(403); // Unauthorized
        }

        return view('bank_accounts.edit', compact('bank_account'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        // Verifikasi PIN
        if (auth()->user()->id !== $bankAccount->user_id) {
            abort(403); // Unauthorized
        }

        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $bankAccount->id,
        ]);

        $bankAccount->update([
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('bank_accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        // Verifikasi PIN
        if (auth()->user()->id !== $bankAccount->user_id) {
            abort(403); // Unauthorized
        }

        $bankAccount->delete();

        return redirect()->route('bank_accounts.index')->with('success', 'Account deleted successfully.');
    }

    public function verifyEdit(BankAccount $bankAccount)
    {
        return view('bank_accounts.verify_pin', compact('bankAccount'));
    }

    public function checkPIN(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Verifikasi PIN
        if ($request->pin !== $bankAccount->pin) {
            return back()->withErrors(['pin' => 'Invalid PIN'])->withInput();
        }

        // Redirect ke halaman edit setelah PIN diverifikasi
        return redirect()->route('bank_accounts.edit', $bankAccount);
    }

    public function showTransferForm(BankAccount $bank_account)
    {
        return view('bank_accounts.transfer', compact('bank_account'));
    }

    public function transfer(Request $request, BankAccount $bank_account)
    {
        $request->validate([
            'target_account_number' => 'required|exists:bank_accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $targetAccount = BankAccount::where('account_number', $request->target_account_number)->first();

        if (!$targetAccount) {
            return back()->withErrors(['target_account_number' => 'Target account number not found.'])->withInput();
        }

        return view('bank_accounts.confirm_transfer', [
            'bank_account' => $bank_account,
            'target_account' => $targetAccount,
            'amount' => $request->amount,
        ]);
    }

    public function confirmTransfer(Request $request, BankAccount $bank_account)
    {
        $request->validate([
            'target_account_number' => 'required|exists:bank_accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
            'pin' => 'required|digits:4',
        ]);

        $targetAccount = BankAccount::where('account_number', $request->target_account_number)->first();

        if (!$targetAccount) {
            return back()->withErrors(['target_account_number' => 'Target account number not found.'])->withInput();
        }

        if ($bank_account->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.'])->withInput();
        }

        return view('bank_accounts.confirm_transfer', [
            'bank_account' => $bank_account,
            'target_account' => $targetAccount,
            'amount' => $request->amount,
            'pin' => $request->pin, // Untuk verifikasi PIN
        ]);
    }

    public function processTransfer(Request $request, BankAccount $bank_account)
    {
        $request->validate([
            'target_account_number' => 'required|exists:bank_accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
            'pin' => 'required|digits:4',
        ]);

        $targetAccount = BankAccount::where('account_number', $request->target_account_number)->first();

        if (!$targetAccount) {
            return back()->withErrors(['target_account_number' => 'Target account number not found.'])->withInput();
        }

        if ($bank_account->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.'])->withInput();
        }

        // Verifikasi PIN
        if ($request->pin !== $bank_account->pin) {
            return back()->withErrors(['pin' => 'Invalid PIN.'])->withInput();
        }

        // Proses transfer
        $bank_account->balance -= $request->amount;
        $targetAccount->balance += $request->amount;

        $bank_account->save();
        $targetAccount->save();

        return redirect()->route('bank_accounts.index')->with('success', 'Transfer successful.');
    }



}
