<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\Account;
use App\Interfaces\AccountComponent;

abstract class BaseAccount extends AccountModel implements Account
{
    protected $fillable = [
        'account_number',
        'balance',
        'user_id',
        'type',
        'status',
        'interest_rate',
        'overdraft_limit',
        'parent_id',
        'is_composite',
    ];

    protected $casts = [
        'balance' => 'float',
        'interest_rate' => 'float',
        'overdraft_limit' => 'float',
        'is_composite' => 'boolean',
    ];

    // === تنفيذ AccountComponent ===

    public function getBalance(): float
    {
        return (float) $this->balance;
    }

    public function getAccountNumber(): string
    {
        return $this->account_number;
    }

    public function getOwner(): User
    {
        return $this->customer;
    }

    public function display(): array
    {
        return [
            'account_number' => $this->account_number,
            'type' => $this->type,
            'balance' => $this->balance,
            'status' => $this->status,
            'owner' => $this->customer->name,
        ];
    }

    // === تنفيذ Account ===

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->save();
    }


    // === طرق مشتركة ===

    public function canClose(): bool
    {
        return $this->balance == 0 && $this->status !== 'closed';
    }

    public function close(): void
    {
        if ($this->canClose()) {
            $this->setStatus('closed');
        } else {
            throw new \Exception('لا يمكن إغلاق الحساب');
        }
    }

    protected function updateBalance(float $newBalance): void
    {
        $this->balance = $newBalance;
        $this->save();
    }

    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('المبلغ يجب أن يكون أكبر من الصفر');
        }
    }

    protected function validateStatus(): void
    {
        if ($this->status !== 'active') {
            throw new \Exception('الحساب غير نشط');
        }
    }
}
