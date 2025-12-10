<?php

namespace App\Interfaces;

use App\Models\AccountModel;

interface StrategyTransaction
{
    public function deposit(): bool;

    public function withdraw(): bool;

    public function transfer(AccountModel $toAccount): bool;
}
