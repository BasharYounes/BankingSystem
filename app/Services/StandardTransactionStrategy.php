<?php

namespace App\Services;

use App\Interfaces\TransactionContract;
use App\Models\Transaction;

class StandardTransactionStrategy implements TransactionStrategy
{
    public function process(TransactionContract $transaction): void
    {
        // رسوم ثابتة للمعاملات العادية
        $fees = $this->calculateFees($transaction);

        if ($transaction->getAmount() > 0) {
            // يمكن تخزين الرسوم في قاعدة البيانات
            \Log::info("رسوم المعاملة: {$fees} للمعاملة {$transaction->getTransactionId()}");
        }

        // store fees in account bank...
    }

    public function calculateFees(TransactionContract $transaction): float
    {
        $baseFee = 0;

        switch ($transaction->getType()) {
            case 'deposit':
                $baseFee = 0;
                break;
            case 'withdraw':
                $baseFee = 2;
                break;
            case 'transfer':
                $baseFee = 5;
                break;
        }

        // نسبة من المبلغ
        $percentageFee = $transaction->getAmount() * 0.01; // 1%

        return $baseFee + $percentageFee;
    }
}
