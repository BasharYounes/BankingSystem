<?php

namespace App\Interfaces;

use App\Models\Ticket;
use App\Models\TicketMessage;

interface TicketRepositoryInterface
{
    public function create(array $data): Ticket;
    public function findById(int $id): ?Ticket;
    public function addMessage(int $ticketId, array $data): TicketMessage;
    public function updateStatus(int $ticketId, string $status): Ticket;
}
