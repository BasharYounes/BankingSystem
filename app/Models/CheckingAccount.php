<?php

namespace App\Models;

use App\Interfaces\Account;
class CheckingAccount extends IndividualAccount
{
    protected $attributes = [
        'type' => 'checking',
        'overdraft_limit' => 500,
    ];

    public function deposit(float $amount): void
    {
        $this->validateAmount($amount);
        $this->validateStatus();

        $this->updateBalance($this->balance + $amount);
    }

    public function withdraw(float $amount): void
    {
        $this->validateAmount($amount);
        $this->validateStatus();

        if (!$this->canWithdraw($amount)) {
            throw new \Exception('الرصيد غير كافي');
        }

        $this->updateBalance($this->balance - $amount);
    }

    public function transfer(Account $toAccount, float $amount): bool
    {
        $this->withdraw($amount);
        $toAccount->deposit($amount);
        return true;
    }

    public function getAvailableBalance(): float
    {
        return $this->balance + $this->overdraft_limit;
    }
}
