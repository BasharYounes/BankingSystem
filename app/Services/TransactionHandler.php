<?php

namespace App\Services;

use App\Models\Transaction;
use App\Interfaces\TransactionContract;

interface TransactionHandler
{
    public function setNext(TransactionHandler $handler): TransactionHandler;
    public function handle(TransactionContract $transaction): bool;
}
