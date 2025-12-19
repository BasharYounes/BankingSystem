<?php

namespace App\Services;

use App\Interfaces\TransactionContract;
interface TransactionStrategy
{
    public function process(TransactionContract $transaction): void;
    public function calculateFees(TransactionContract $transaction): float;
}
