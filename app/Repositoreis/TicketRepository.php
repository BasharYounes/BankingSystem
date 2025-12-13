<?php

namespace App\Repositoreis;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\Ticket_Message;

class TicketRepository implements TicketRepositoryInterface
{
    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function findById(int $id): ?Ticket
    {
        return Ticket::findOrFail($id);
    }

    public function addMessage(int $ticketId, array $data): Ticket_Message
    {
        $ticket = Ticket::findOrFail($ticketId);
        return $ticket->messages()->create($data);
    }

    public function updateStatus(int $ticketId, string $status): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->status = $status;

        if ($status === 'resolved') {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        return $ticket;
    }
}

