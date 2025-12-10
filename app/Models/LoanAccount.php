<?php

namespace App\Models;

use App\Interfaces\Account;

class LoanAccount extends IndividualAccount
{
    protected $attributes = [
        'type' => 'loan',
    ];

    public function deposit(float $amount): void
    {
        $this->validateAmount($amount);
        $this->validateStatus();

        $this->updateBalance($this->balance - $amount);
    }

    public function withdraw(float $amount): void
    {
        throw new \Exception('لا يمكن سحب الأموال من حساب القرض');
    }

    public function transfer(Account $toAccount, float $amount): bool
    {
        throw new \Exception('لا يمكن تحويل الأموال من حساب القرض');
    }

    public function calculateInterest(): float
    {
        return $this->balance * ($this->interest_rate / 100);
    }

    public function getOutstandingBalance(): float
    {
        return max(0, $this->balance);
    }
}
