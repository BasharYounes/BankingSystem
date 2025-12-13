<?php

namespace App\Interfaces;

use App\Models\Ticket;
use App\Models\Ticket_Message;

interface TicketRepositoryInterface
{
    public function create(array $data): Ticket;
    public function findById(int $id): ?Ticket;
    public function addMessage(int $ticketId, array $data): Ticket_Message;
    public function updateStatus(int $ticketId, string $status): Ticket;
}
