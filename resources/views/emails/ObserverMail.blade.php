<!DOCTYPE html>
<html>
<head>
    <title>تنبيه للمراقبة</title>
</head>
<body>
    <h1>Hello!</h1>
    <p if="{{ $data['evntType'] }} !== transfer">
        There is a {{ $data['evntType'] }} in your account that is number {{ $data['account_number'] }} with {{ $data['amount'] }}
    </p>
    <P if="{{ $data['evntType'] }} === transferfrom">
        There is a transfer in your account to {{ $data['to_account_number'] }} with {{ $data['amount'] }}
    </P>
    <P if="{{ $data['evntType'] }} === transferto">
        There is a transfer in your account from {{ $data['from_account_number'] }} with {{ $data['amount'] }}
    </P>

</body>
</html>
