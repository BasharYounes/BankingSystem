<?php

namespace App\Services;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Repositoreis\TicketRepository;

class TicketService
{
    public function __construct(
        private TicketRepository $tickets
    ){}

    public function openTicket(array $data): Ticket
    {
        return $this->tickets->create($data);
    }

    public function addMessage(int $ticketId, array $data): TicketMessage
    {
        return $this->tickets->addMessage($ticketId, $data);
    }

    public function changeStatus(int $ticketId, string $status): Ticket
    {
        return $this->tickets->updateStatus($ticketId, $status);
    }
}

