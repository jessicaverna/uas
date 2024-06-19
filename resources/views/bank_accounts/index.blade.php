<div class="container">
    <h2>My Bank Accounts</h2>
    <a href="{{ route('bank_accounts.create') }}" class="btn btn-primary mb-3">Add Account</a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                <tr>
                    <td>{{ $account->account_name }}</td>
                    <td>{{ $account->account_number }}</td>
                    <td>${{ number_format($account->balance, 2) }}</td>
                    <td>
                        <a href="{{ route('bank_accounts.verify_edit', $account) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('bank_accounts.show_transfer_form', $account) }}" class="btn btn-info btn-sm">Transfer</a>
                        <a href="{{ route('bank_accounts.transactions', $account) }}" class="btn btn-secondary btn-sm">Transaction History</a>
                        <form action="{{ route('bank_accounts.destroy', $account) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?')">Delete</button>
                        </form>
                    </td>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
