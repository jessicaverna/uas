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
        ]);

        $bankAccount = new BankAccount([
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'balance' => 0.00,
        ]);

        auth()->user()->bankAccounts()->save($bankAccount);

        return redirect()->route('bank_accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank_accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $bankAccount->id,
        ]);

        $bankAccount->update($request->all());

        return redirect()->route('bank_accounts.index')->with('success', 'Account updated successfully.');
    }
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank_accounts.index')->with('success', 'Account deleted successfully.');
    }
}
