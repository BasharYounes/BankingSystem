<?php

namespace App\Models;

use App\Factories\AccountFactory;
use App\Factories\StrategyFactory;
use App\Strategies\CheckingAccountStrategy;
use Str;

class WithdrawalTransaction extends Transaction
{
    protected $attributes = [
        'type' => 'withdrawal',
    ];

    public function execute(): bool
    {
        try {
            $this->setStatus(self::STATUS_PROCESSING);
            \Log::info('بدء تنفيذ عملية السحب');
            \Log::info('Status'.$this->getStatus());

            $accountModel  = AccountModel::find($this->account_id);
            if (!$accountModel ) {
                throw new \Exception('الحساب غير موجود');
            }

            if (!$this->validate() && $accountModel->type !== 'loan') {
                throw new \Exception('فشل التحقق من صحة المعاملة');
            }

            $accountStrategy = StrategyFactory::getInstance()->createStrategy($accountModel, $this->amount);
            // تنفيذ السحب
            $accountStrategy->withdraw();

            \Log::info('تم السحب بنجاح من الحساب رقم: ' . $accountModel->account_number);
            \Log::info('Status'.$this->getStatus());

            $accountModel->notify('withdrawal_made', [
                'amount' => $this->amount,
                'account_number' => $accountModel->account_number,
                'total_balance' => $accountModel->balance,
            ]);

            $this->setStatus(self::STATUS_COMPLETED);
            \Log::info('اكتملت عملية السحب بنجاح');
            \Log::info('Status'.$this->getStatus());
            return true;

        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_FAILED);
            $accountModel->notify('withdrawal_failed', [
                'amount' => $this->amount,
                'account_number' => $accountModel->account_number,
            ]);
            \Log::error('فشل السحب: ' . $e->getMessage());
            return false;
        }
    }

    public function validate(): bool
    {
        if ($this->amount <= 0) {
            return false;
        }

        $account = AccountModel::find($this->account_id);
        if (!$account || $account->status !== 'active') {
            return false;
        }

        // التحقق من الرصيد الكافي
        if ($account->balance < $this->amount) {
            return false;
        }

        return true;
    }
}
