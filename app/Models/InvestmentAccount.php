<?php

namespace App\Models;

use App\Interfaces\Account;

class InvestmentAccount extends IndividualAccount
{
    protected $attributes = [
        'type' => 'investment'
    ];
    public function deposit(float $amount): void
    {
        // تنفيذ إيداع في حساب الاستثمار
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        // تنفيذ سحب من حساب الاستثمار
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
        } else {
            throw new \Exception('رصيد غير كافٍ للسحب');
        }
    }
    public function transfer(Account $toAccount, float $amount): bool
    {
         return true;
    }

    public function calculateReturns(): float
    {
        // حساب العوائد على استثمار معين
        return $this->balance * 0.05; // مثال: عائد 5%
    }

    public function getMaturityDate(): string
    {
        // إرجاع تاريخ الاستحقاق للاستثمار
        return date('Y-m-d', strtotime($this->created_at . ' +1 year')); // مثال: استثمار لمدة سنة
    }

    public function getAvailableBalance(): float
    {
        return $this->balance;
    }

    

}
