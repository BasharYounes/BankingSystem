<?php

namespace App\Services;

use App\Models\Transaction;

interface TransactionHandler
{
    public function setNext(TransactionHandler $handler): TransactionHandler;
    public function handle(Transaction $transaction): bool;
}
