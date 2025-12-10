<?php

namespace App\Interfaces;

interface Account extends AccountComponent
{
    public function deposit(float $amount): void;
    public function withdraw(float $amount): void;
    public function transfer(Account $toAccount, float $amount): bool;
    public function getType(): string;
    public function getStatus(): string;
    public function setStatus(string $status): void;
}
