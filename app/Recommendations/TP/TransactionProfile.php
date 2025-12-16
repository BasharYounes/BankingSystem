<?php

namespace App\Recommendations\TP;

class TransactionProfile
{
    public function __construct(
        public int $monthlyWithdrawals,
        public float $averageBalance,
        public float $feesPaid,
        public string $riskLevel,
        public bool $hasRegularIncome
    ) {}
}
