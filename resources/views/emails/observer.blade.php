<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنبيه بالحركة</title>
</head>
<body>
    <h2>تنبيه</h2>

    @php
        $eventType = $data['eventType'] ?? null;
        $accountNumber = $data['account_number'] ?? null;
        $amount = $data['amount'] ?? null;
        $fromAccountNumber = $data['from_account_number'] ?? null;
        $toAccountNumber = $data['to_account_number'] ?? null;
    @endphp

    @if($eventType === 'transfer')
        <p>
            تمت عملية تحويل.
        </p>
        @if($fromAccountNumber)
            <p>من الحساب: <strong>{{ $fromAccountNumber }}</strong></p>
        @endif
        @if($toAccountNumber)
            <p>إلى الحساب: <strong>{{ $toAccountNumber }}</strong></p>
        @endif
        @if(!is_null($amount))
            <p>المبلغ: <strong>{{ $amount }}</strong></p>
        @endif
    @else
        <p>
            تمت عملية <strong>{{ $eventType ?? 'غير معروفة' }}</strong>
            @if($accountNumber)
                على الحساب رقم <strong>{{ $accountNumber }}</strong>
            @endif
            @if(!is_null($amount))
                بقيمة <strong>{{ $amount }}</strong>
            @endif
            .
        </p>
    @endif

    <hr>
    <p style="color:#666; font-size: 12px;">هذه رسالة تلقائية.</p>
</body>
</html>


