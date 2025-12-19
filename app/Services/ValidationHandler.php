<?php

namespace App\Services;

use App\Interfaces\TransactionContract;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ValidationHandler implements TransactionHandler
{
    private ?TransactionHandler $nextHandler = null;

    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(TransactionContract $transaction): bool
    {
        // التحقق الأساسي
        if (!$this->validateTransaction($transaction)) {
            return false;
        }
        // dd('Validation passed in ValidationHandler');
        // تمرير إلى المعالج التالي
        if ($this->nextHandler !== null) {
            return $this->nextHandler->handle($transaction);
        }

        return true;
    }

    private function validateTransaction(TransactionContract $transaction): bool
    {
        // التحقق من المبلغ
        if ($transaction->getAmount() <= 0) {
            return false;
        }
        // التحقق من التكرار
        // if ($this->isDuplicateTransaction($transaction)) {
        //     return false;
        // }
        // التحقق من الحالة
        if ($transaction->getStatus() !== Transaction::STATUS_PENDING) {
            return false;
        }

        return true;
    }

    public function isDuplicateTransaction(Transaction $transaction): bool
    {
        // التحقق من وجود معاملة مماثلة حديثة باستخدام استعلام مباشر على الجدول
        $recent = DB::table('transactions')
            ->where('amount', $transaction->amount)
            ->where('type', $transaction->type)
            ->where('created_at', '>', now()->subMinutes(10))
            ->exists();

        return $recent;
    }
}
