<?php

namespace App\Services;

use App\Interfaces\TransactionContract;
use App\Models\Transaction;

class AuthorizationHandler implements TransactionHandler
{
    private ?TransactionHandler $nextHandler = null;
    private float $approvalLimit = 5000;

    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(TransactionContract $transaction): bool
    {
        // التحقق من الحاجة إلى موافقة
        if ($this->requiresApproval($transaction)) {
            $transaction->setStatus('requires_approval');
            // $transaction->status = 'requires_approval';
            // $transaction->save();
            return false; // تحتاج موافقة
        }

        // تمرير إلى المعالج التالي
        if ($this->nextHandler !== null) {
            return $this->nextHandler->handle($transaction);
        }

        return true;
    }

    private function requiresApproval(TransactionContract $transaction): bool
    {
        // معاملات كبيرة تحتاج موافقة
        if ($transaction->getAmount() > $this->approvalLimit) {
            return true;
        }

        // // تحويلات بين حسابات مختلفة الملاك
        // if ($transaction->getType() === 'transfer') {
        //     // يمكن إضافة منطق التحقق هنا
        // }

        return false;
    }
}
