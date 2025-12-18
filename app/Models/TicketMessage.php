<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'sender_id', 'message', 'sender_type','is_read'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    //========= add morph relationship ========== //
}
