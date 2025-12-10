<?php

namespace App\Models;

use App\Factories\AccountFactory;
use App\Factories\StrategyFactory;
use App\Interfaces\Account;
use App\Strategies\SavingAccountStrategy;

class TransferTransaction extends Transaction
{
    protected $attributes = [
        'type' => 'transfer',
    ];

    public function execute(): bool
    {
        try {
            $this->setStatus(self::STATUS_PROCESSING);
            \Log::info('بدء تنفيذ عملية التحويل');
            \Log::info('Status'.$this->getStatus());

            if (!$this->validate()) {
                throw new \Exception('فشل التحقق من صحة المعاملة');
            }

            $fromModelAccount = AccountModel::find($this->from_account_id);
            $toModelAccount = AccountModel::find($this->to_account_id);

            if (!$fromModelAccount || !$toModelAccount) {
                throw new \Exception('الحساب غير موجود');
            }
            $strategy = StrategyFactory::getInstance()->createStrategy($fromModelAccount, $this->amount);
            // تنفيذ التحويل
            $strategy->transfer($toModelAccount);
            \Log::info('تم التحويل بنجاح من الحساب رقم: ' . $fromModelAccount->account_number . ' إلى الحساب رقم: ' . $toModelAccount->account_number);
            \Log::info('Status'.$this->getStatus());

            $this->setStatus(self::STATUS_COMPLETED);
            \Log::info('اكتملت عملية التحويل بنجاح');
            \Log::info('Status'.$this->getStatus());
            return true;

        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_FAILED);
            \Log::error('فشل التحويل: ' . $e->getMessage());
            return false;
        }
    }

    public function validate(): bool
    {
        if ($this->amount <= 0) {
            return false;
        }

        $fromAccount = AccountModel::find($this->from_account_id);
        $toAccount = AccountModel::find($this->to_account_id);

        if (!$fromAccount || !$toAccount) {
            return false;
        }

        if ($fromAccount->status !== 'active' || $toAccount->status !== 'active') {
            return false;
        }

        if ($fromAccount->account_number === $toAccount->account_number) {
            return false;
        }

        // التحقق من الرصيد الكافي
        $availableBalance = $fromAccount->balance;
        if ($this->amount > $availableBalance) {
            return false;
        }

        return true;
    }
}
