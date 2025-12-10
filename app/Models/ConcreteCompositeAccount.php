<?php

namespace App\Models;

use App\Interfaces\CompositeAccount;
use App\Interfaces\AccountComponent;
use App\Interfaces\Account;

class ConcreteCompositeAccount extends BaseAccount implements CompositeAccount
{
    protected $attributes = [
        'type' => 'composite',
        'is_composite' => true,
    ];

    // === تنفيذ Account ===

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

        $totalBalance = $this->getTotalBalance();
        if ($amount > $totalBalance) {
            throw new \Exception('الرصيد الإجمالي غير كافي');
        }

        // سحب من الرصيد الرئيسي أولاً
        $remaining = $amount;
        if ($this->balance >= $remaining) {
            $this->balance -= $remaining;
            $remaining = 0;
        } else {
            $remaining -= $this->balance;
            $this->balance = 0;
        }

        // سحب من الأبناء إذا لزم
        if ($remaining > 0) {
            $this->withdrawFromChildren($remaining);
        }

        $this->save();
    }

    public function transfer(Account $toAccount, float $amount): bool
    {
        $this->withdraw($amount);
        $toAccount->deposit($amount);
        return true;
    }

    // === تنفيذ CompositeAccount ===

    public function addChild(AccountComponent $account): void
    {
        if ($account instanceof BaseAccount) {
            $account->parent_id = $this->id;
            $account->save();
        }
    }

    public function removeChild(AccountComponent $account): void
    {
        if ($account instanceof BaseAccount && $account->parent_id === $this->id) {
            $account->parent_id = null;
            $account->save();
        }
    }

    public function getChildren(): array
    {
        return $this->children()->get()->all();
    }

    public function getTotalBalance(): float
    {
        $total = $this->balance;
        foreach ($this->getChildren() as $child) {
            $total += $child->getBalance();
        }
        return $total;
    }

    // === طرق خاصة ===

    private function withdrawFromChildren(float $amount): void
    {
        $children = $this->getChildren();

        foreach ($children as $child) {
            if ($amount <= 0) break;

            $childBalance = $child->getBalance();
            if ($childBalance > 0) {
                $withdrawAmount = min($amount, $childBalance);
                $child->withdraw($withdrawAmount);
                $amount -= $withdrawAmount;
            }
        }

        if ($amount > 0) {
            throw new \Exception('لا يمكن سحب المبلغ المطلوب');
        }
    }
}
