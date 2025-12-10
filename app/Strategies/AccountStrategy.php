<?php

namespace App\Strategies;

use App\Interfaces\StrategyTransaction;
use App\Models\AccountModel;

abstract class AccountStrategy implements StrategyTransaction
{
    protected AccountModel $accountModel;
    protected float $amount;

    public function __construct(AccountModel $accountModel, float $amount)
    {
        $this->accountModel = $accountModel;
        $this->amount = $amount;
    }

    abstract public function deposit(): bool;
    abstract public function withdraw(): bool;
    abstract public function transfer(AccountModel $toAccount): bool;

    public function validateTargetStatus(AccountModel $toAccount): void
    {
        if ($toAccount->status !== 'active') {
            throw new \Exception('الحساب المستهدف غير نشط');
        }
    }

    public function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \Exception('المبلغ غير صالح');
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

    
    public function updateBalance(AccountModel $accountModel, float $newBalance): void
    {
        $accountModel->balance = $newBalance;
        $accountModel->save();
    }

}
