<div class="container">
    <h2>Top Up Form</h2>
    <form action="{{ route('bank_accounts.topup', $bank_account) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Jumlah Top Up</label>
            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" min="0.01" step="0.01" required value="{{ old('amount') }}">
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="pin">PIN</label>
            <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror" required>
            @error('pin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mr-2">Top Up</button>
        <a href="{{ route('bank_accounts.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
