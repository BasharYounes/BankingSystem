<?php

namespace App\Models;

use App\Factories\StrategyFactory;
use App\Interfaces\Account;
use App\Strategies\SavingAccountStrategy;
use Log;

class DepositTransaction extends Transaction
{
    protected $attributes = [
        'type' => 'deposit',
    ];

    public function execute(): bool
    {
        try {
            $this->setStatus(self::STATUS_PROCESSING);
            Log::info('بدء تنفيذ عملية الإيداع');
            Log::info('Status'.$this->getStatus());

            if (!$this->validate()) {
                throw new \Exception('فشل التحقق من صحة المعاملة');
            }

            $accountModel  = AccountModel::find($this->account_id);
            if (!$accountModel ) {
                throw new \Exception('الحساب غير موجود');
            }
            Log::info('سيتم إنشاء الحساب');

            $accountStrategy = StrategyFactory::getInstance()->createStrategy($accountModel, $this->amount);
            // تنفيذ الإيداع
            $accountStrategy->deposit();

            $this->notify('deposit_made', [
            'amount' => $this->amount,
            'account_number' => $accountModel->account_number,
            ]);

            Log::info('تم الإيداع بنجاح في الحساب رقم: ' . $accountModel->account_number);
            Log::info('Status'.$this->getStatus());

            if ($accountStrategy->type === 'loan'){
                $accountStrategy->update([
                    'scheduled_for' => now()->addMonth(),
                    'status' => self::STATUS_PENDING
                ]);
            }else{
            $this->setStatus(self::STATUS_COMPLETED);
            Log::info('اكتملت عملية الإيداع بنجاح');
            Log::info('Status'.$this->getStatus());
            }
            return true;

        } catch (\Exception $e) {
            $this->setStatus(self::STATUS_FAILED);
            $this->notify('deposit_failed', [
                'amount' => $this->amount,
                'account_number' => $accountModel->account_number,
            ]);
            Log::error('فشل الإيداع: ' . $e->getMessage().'.'.$e->getLine().'.'.$e->getFile());
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

        return true;
    }
}
