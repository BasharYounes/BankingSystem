<?php

namespace App\Services;

use App\Models\Transaction;

class StandardTransactionStrategy implements TransactionStrategy
{
    public function process(Transaction $transaction): void
    {
        // رسوم ثابتة للمعاملات العادية
        $fees = $this->calculateFees($transaction);

        if ($transaction->amount > 0) {
            // يمكن تخزين الرسوم في قاعدة البيانات
            \Log::info("رسوم المعاملة: {$fees} للمعاملة {$transaction->transaction_id}");
        }
    }

    public function calculateFees(Transaction $transaction): float
    {
        $baseFee = 0;

        switch ($transaction->type) {
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
        $percentageFee = $transaction->amount * 0.01; // 1%

        return $baseFee + $percentageFee;
    }
}
