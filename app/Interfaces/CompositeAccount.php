<?php

namespace App\Interfaces;

interface CompositeAccount extends Account
{
    public function addChild(AccountComponent $account): void;
    public function removeChild(AccountComponent $account): void;
    public function getChildren(): array;
    public function getTotalBalance(): float;
}
