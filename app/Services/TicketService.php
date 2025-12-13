<?php

namespace App\Services;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\Ticket_Message;

class TicketService
{
    public function __construct(
        private TicketRepositoryInterface $tickets
    ){}

    public function openTicket(array $data): Ticket
    {
        return $this->tickets->create($data);
    }

    public function addMessage(int $ticketId, array $data): Ticket_Message
    {
        return $this->tickets->addMessage($ticketId, $data);
    }

    public function changeStatus(int $ticketId, string $status): Ticket
    {
        return $this->tickets->updateStatus($ticketId, $status);
    }
}

