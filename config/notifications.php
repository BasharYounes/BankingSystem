<?php

return [
    'templates' => [
        'alert for loanAccount' => [
            'title' => 'Loan Alert',
            'body'  => 'Please repay a portion of the loan within a period not exceeding 5 days.'
        ],
        'deposit' => [
            'title' => 'Deposit Confirmation',
            'body'  => 'Your deposit has been successfully processed in Account that is number {{account_number}} with {{amount}}.'
        ],
        'withdrawal' => [
            'title' => 'Withdrawal Confirmation',
            'body'  => 'Your withdrawal has been successfully processed in Account that is number {{account_number}}  with {{amount}}.'
        ],
        'transfer' => [
            'title' => 'Transfer Confirmation',
            'body'  =>  'Your transfer has been successfully processed from Account that is number {{from_account_number}}
                        to account that is number {{to_account_number}} with amount {{amount}}.'
        ]
    ]
];
