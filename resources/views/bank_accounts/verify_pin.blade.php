<div class="container">
    <h2>Verify PIN</h2>
    <form method="POST" action="{{ route('bank_accounts.check_pin', $bankAccount) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="pin">Enter PIN</label>
            <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror" required>
            @error('pin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Verify and Edit Account</button>
    </form>
</div>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
