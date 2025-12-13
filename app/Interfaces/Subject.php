<?php

namespace App\Interfaces;

interface Subject
{
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $eventType, array $data): void;
}
