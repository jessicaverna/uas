<div class="container">
    <h2>Confirm Transfer</h2>

    @if($errors->has('pin'))
        <div class="alert alert-danger">
            {{ $errors->first('pin') }}
        </div>
    @endif

    <form method="POST" action="{{ route('bank_accounts.process_transfer', $bank_account) }}">
        @csrf
        <input type="hidden" name="target_account_number" value="{{ $target_account->account_number }}">
        <input type="hidden" name="amount" value="{{ $amount }}">

        <div class="form-group">
            <label for="target_account_number">Target Account Number:</label>
            <p class="form-control-static">{{ $target_account->account_number }}</p>
        </div>

        <div class="form-group">
            <label for="target_account_name">Target Account Name:</label>
            <p class="form-control-static">{{ $target_account->account_name }}</p>
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <p class="form-control-static">${{ number_format($amount, 2) }}</p>
        </div>

        <div class="form-group">
            <label for="pin">Enter PIN</label>
            <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror" required>
            @error('pin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Confirm Transfer</button>
        <a href="{{ route('bank_accounts.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>