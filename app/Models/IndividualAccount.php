<?php

namespace App\Models;

use App\Interfaces\Account;

abstract class IndividualAccount extends BaseAccount
{
    // === تنفيذ طرق Account المجردة ===

    abstract public function deposit(float $amount): void;
    abstract public function withdraw(float $amount): void;
    abstract public function transfer(Account $toAccount, float $amount): bool;

    // === طرق مساعدة ===

    public function getAvailableBalance(): float
    {
        return $this->balance;
    }

    protected function canWithdraw(float $amount): bool
    {
        return $this->getAvailableBalance() >= $amount;
    }

    
}
