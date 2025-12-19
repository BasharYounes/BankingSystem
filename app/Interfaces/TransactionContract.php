<?php

namespace App\Interfaces;

interface TransactionContract
{
    public function execute(): bool;
    public function getFromAccountId(): ?int;
    public function getTransactionId(): string;
    public function getAmount(): float;
    public function getType():string;
    public function getStatus():string;
    public function setStatus(string $status):void;
}

