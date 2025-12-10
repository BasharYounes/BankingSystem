<?php

namespace App\Strategies;

use App\Interfaces\StrategyTransaction;
use App\Interfaces\Account;
use App\Models\AccountModel;
use App\Factories\AccountFactory;

class SavingAccountStrategy extends AccountStrategy
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
        try {
            $this->validateAmount($this->amount);
            $this->validateStatus();
            // dd( $this->getBalance());
            $this->updateBalance($this->accountModel, $this->getBalance() - $this->amount);
            // dd($this->getBalance());
            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل السحب: ' . $e->getMessage());
        }
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
}
