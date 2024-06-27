<div class="container">
    <h2>Pay Bill</h2>
    <form method="POST" action="{{ route('bank_accounts.pay_bill', $bank_account) }}">
        @csrf
        <div class="form-group">
            <label for="virtual_account_number">Virtual Account Number</label>
            <input type="text" name="virtual_account_number" id="virtual_account_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="amount">Total Bill Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="pin">PIN</label>
            <input type="password" name="pin" id="pin" class="form-control" required>
            @error('pin')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

        </div>
        <button type="submit" class="btn btn-primary">Pay Bill</button>
    </form>
</div>