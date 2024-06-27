<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Bill;
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

        // Simpan transaksi
        $this->saveTransaction($bank_account, $targetAccount, $request->amount);

        $bank_account->save();
        $targetAccount->save();

        return redirect()->route('bank_accounts.index')->with('success', 'Transfer successful.');
    }


    private function saveTransaction(BankAccount $sender, ?BankAccount $receiver, $amount, $description)
{
    // Simpan transaksi pengirim
    $transactionSender = new Transaction([
        'user_id' => auth()->user()->id,
        'bank_account_id' => $sender->id,
        'type' => 'withdrawal', // atau sesuaikan dengan kebutuhan Anda
        'amount' => $amount,
        'description' => $description,
    ]);
    $transactionSender->save();

    // Jika ada penerima, simpan juga transaksi penerima
    if ($receiver) {
        $transactionReceiver = new Transaction([
            'user_id' => $receiver->user_id,
            'bank_account_id' => $receiver->id,
            'type' => 'deposit', // atau sesuaikan dengan kebutuhan Anda
            'amount' => $amount,
            'description' => $description,
        ]);
        $transactionReceiver->save();
    }
}
    public function transactions(BankAccount $bank_account)
    {
        $transactions = Transaction::where('bank_account_id', $bank_account->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bank_accounts.transactions', compact('transactions', 'bank_account'));
    }
    
    public function payBillForm(BankAccount $bank_account)
{
    return view('bank_accounts.pay_bill', compact('bank_account'));
}

public function payBill(Request $request, BankAccount $bank_account)
{
    $request->validate([
        'virtual_account_number' => 'required|string',
        'amount' => 'required|numeric|min:0.01',
        'pin' => 'required|digits:4',
    ]);

    if ($request->pin !== $bank_account->pin) {
        return back()->withErrors(['pin' => 'Invalid PIN'])->withInput();
    }
    
    

    // Verifikasi Saldo Cukup
    if ($bank_account->balance < $request->amount) {
        return back()->withErrors(['amount' => 'Insufficient balance to pay this bill'])->withInput();
    }

    // Lakukan pembayaran tagihan
    $description = 'Payment for virtual account ' . $request->virtual_account_number;

    // Simpan transaksi
    $this->saveTransaction($bank_account, null, $request->amount, $description);

    // Kurangi saldo bank account
    $bank_account->balance -= $request->amount;
    $bank_account->save();

    return redirect()->route('bank_accounts.index')->with('success', 'Bill paid successfully.');
}

}
