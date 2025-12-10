<?php

namespace App\Interfaces;

use App\Models\User;

interface AccountComponent
{
    public function getBalance(): float;
    public function getAccountNumber(): string;
    public function getOwner(): User;
    public function display(): array;
}
