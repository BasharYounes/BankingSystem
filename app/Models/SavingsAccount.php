<?php

namespace App\Models;

use App\Interfaces\Account;

class SavingsAccount extends IndividualAccount
{
    protected $attributes = [
        'type' => 'savings',
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
        try {
            $this->withdraw($amount);
            $toAccount->deposit($amount);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('فشل التحويل: ' . $e->getMessage());
        }
    }

    public function calculateInterest(): float
    {
        return $this->balance * ($this->interest_rate / 100);
    }

    public function getAvailableBalance(): float
    {
        $minBalance = 100; // حد أدنى افتراضي
        return max(0, $this->balance - $minBalance);
    }
}
