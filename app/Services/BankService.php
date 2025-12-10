<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountModel;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\DepositTransaction;
use App\Models\TransferTransaction;
use App\Models\User;

class BankService
{
    private TransactionProcessor $processor;

    public function __construct()
    {
        $this->processor = new TransactionProcessor();
    }

    public function createAccount(User $user, string $type, array $data = []): AccountModel
    {
        return $user->openAccount($type, $data);
    }

    public function processTransaction(Transaction $transaction)
    {
        return $this->processor->process($transaction);
    }

    public function getAccount(string $accountNumber): ?AccountModel
    {
        return  AccountModel::where('account_number', $accountNumber)->first();
    }

    public function getCustomerAccounts(User $user)
    {
        return $user->accounts;
    }
}
