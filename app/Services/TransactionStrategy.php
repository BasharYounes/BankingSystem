<?php

namespace App\Services;

use App\Models\Transaction;

interface TransactionStrategy
{
    public function process(Transaction $transaction): void;
    public function calculateFees(Transaction $transaction): float;
}
