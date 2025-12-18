<!DOCTYPE html>
<html>
<head>
    <title>Daily Transactions - {{ $date }}</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Daily Transactions - {{ $date }}</h2>
    <table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Fees</th>
            <th>Net Amount</th>
            <th>Currency</th>
            <th>Account Number</th>
            <th>From Account</th>
            <th>To Account</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Approver</th>
            <th>Requires Approval</th>
            <th>Executed At</th>
            <th>Approved At</th>
            <th>Description</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $txn)
            <tr>
                <td>{{ $txn->transaction_id }}</td>
                <td>{{ $txn->type }}</td>
                <td>{{ $txn->amount }}</td>
                <td>{{ $txn->fees ?? '0' }}</td>
                <td>{{ $txn->net_amount ?? $txn->amount }}</td>
                <td>{{ $txn->currency ?? 'N/A' }}</td>
                <td>{{ $txn->account->account_number ?? '-'  }}</td>
                <td>{{ $txn->fromAccount->account_number ?? '-'  }}</td>
                <td>{{ $txn->toAccount->account_number?? '-'  }}</td>
                <td>{{ $txn->account->balance ?? '-'  }}</td>
                <td>{{ $txn->status }}</td>
                <td>{{ $txn->customer->name ?? 'N/A' }}</td>
                <td>{{ $txn->approver->name ?? '-' }}</td>
                <td>{{ $txn->requires_approval ? 'Yes' : 'No' }}</td>
                <td>{{ $txn->executed_at ? $txn->executed_at->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $txn->approved_at ? $txn->approved_at->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $txn->description ?? '-' }}</td>
                <td>{{ $txn->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
