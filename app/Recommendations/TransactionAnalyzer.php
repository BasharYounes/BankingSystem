<?php

namespace App\Recommendations;

use App\Models\AccountModel;
use App\Recommendations\TP\TransactionProfile;

class TransactionAnalyzer
{
    public function analyze(AccountModel $account): TransactionProfile
    {
        // مثال مبسط (يمكن تعقيده لاحقًا)
        $withdrawals = $account->transactions()
            ->where('type', 'withdrawal')
            ->whereMonth('created_at', now()->month)
            ->count();

        return new TransactionProfile(
            monthlyWithdrawals: $withdrawals,
            averageBalance: $account->balance,
            feesPaid: 0,
            riskLevel: $withdrawals > 5 ? 'high' : 'low',
            hasRegularIncome: true
        );
    }
}
