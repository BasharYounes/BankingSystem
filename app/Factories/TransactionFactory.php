<?php

namespace App\Factories;

use App\Models\SellTransaction;
use App\Models\Transaction;
use App\Models\DepositTransaction;
use App\Models\WithdrawalTransaction;
use App\Models\TransferTransaction;

class TransactionFactory
{
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createTransaction(string $type, array $data = []): Transaction
    {
        $data['type'] = $type;

        return match($type) {
            'deposit' => $this->createDepositTransaction($data),
            'withdrawal' => $this->createWithdrawalTransaction($data),
            'transfer' => $this->createTransferTransaction($data),
            'sellAsset' => $this->createSellTransaction($data),
            default => throw new \InvalidArgumentException("نوع معاملة غير معروف: {$type}")
        };
    }

    private function createDepositTransaction(array $data): DepositTransaction
    {
        return new DepositTransaction($data);
    }

    private function createWithdrawalTransaction(array $data): WithdrawalTransaction
    {
        return new WithdrawalTransaction($data);
    }

    private function createTransferTransaction(array $data): TransferTransaction
    {
        return new TransferTransaction($data);
    }

    private function createSellTransaction(array $data): SellTransaction
    {
        return new SellTransaction($data);
    }
}
