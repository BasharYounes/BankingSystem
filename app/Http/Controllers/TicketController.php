<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $service) {}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category' => 'sometimes|string',
            'account_id' => 'required|exists:accounts,id',
        ]);
        $ticket = $this->service->openTicket($request->all());
        return response()->json($ticket, 201);
    }

    public function addMessage(Request $request, int $ticketId)
    {
        $message = $this->service->addMessage($ticketId, [
            'sender_id' => auth()->id(),
            'sender_type' => 'customer',
            'message' => $request->message
        ]);

        return response()->json($message, 201);
    }

    public function updateStatus(Request $request, int $ticketId)
    {
        $ticket = $this->service->changeStatus($ticketId, $request->status);
        return response()->json($ticket);
    }
}
