<div class="container">
    <h2>Transfer Funds</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bank_accounts.transfer', $bank_account) }}">
        @csrf
        <div class="form-group">
            <label for="target_account_number">Target Account Number:</label>
            <input type="text" id="target_account_number" name="target_account_number" class="form-control" value="{{ old('target_account_number') }}" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" class="form-control" value="{{ old('amount') }}" required step="0.01">
        </div>
        <button type="submit" class="btn btn-primary">Transfer</button>
        <a href="{{ route('bank_accounts.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>


<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
