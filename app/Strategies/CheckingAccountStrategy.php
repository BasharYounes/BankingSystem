<?php

namespace App\Strategies;

use App\Interfaces\StrategyTransaction;
use Illuminate\Http\Request;
use App\Models\AccountModel;

class CheckingAccountStrategy extends AccountStrategy
{
    protected AccountModel $accountModel;
    protected float $amount;
    public function __construct(AccountModel $accountModel, float $amount)
    {
        $this->accountModel = $accountModel;
        $this->amount = $amount;
    }
    public function deposit(): bool
    {
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();
            $this->updateBalance($this->accountModel, $this->getBalance() + $this->amount);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل الإيداع: ' . $e->getMessage());
        }
    }

    public function withdraw(): bool
    {
        $this->validateAmount($this->amount);
        $this->validateStatus();

        if (!$this->canWithdraw($this->amount)) {
            throw new \Exception('الرصيد غير كافي');
        }
        $this->updateBalance($this->accountModel, $this->getBalance() - $this->amount);
        return true;
    }

    public function transfer(AccountModel $toAccount): bool
    {
        try {
            $this->withdraw();
            $this->validateTargetStatus($toAccount);
            $this->updateBalance($toAccount, $toAccount->balance + $this->amount);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل التحويل: ' . $e->getMessage());
        }
    }

    public function updateBalance(AccountModel $accountModel, float $newBalance): void
    {
        $accountModel->balance = $newBalance;
        $accountModel->save();
    }

    public function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('المبلغ يجب أن يكون أكبر من الصفر');
        }
    }

     public function validateStatus(): void
    {
        if ($this->accountModel->status !== 'active') {
            throw new \Exception('الحساب غير نشط');
        }
    }

    public function getBalance(): float
    {
        return $this->accountModel->balance;
    }

    protected function canWithdraw(float $amount): bool
    {
        return $this->getBalance() >= $amount;
    }

}
