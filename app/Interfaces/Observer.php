<?php

namespace App\Interfaces;

use App\Models\AccountModel;

interface Observer
{
    public function update(string $eventType, AccountModel $account, array $data): void;
}
