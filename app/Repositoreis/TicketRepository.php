<?php

namespace App\Repositoreis;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketRepository implements TicketRepositoryInterface
{
    public function create(array $data): Ticket
    {
        $data['user_id'] = auth()->id();
        return Ticket::create($data);
    }

    public function findById(int $id): ?Ticket
    {
        return Ticket::findOrFail($id);
    }

    public function addMessage(int $ticketId, array $data): TicketMessage
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

