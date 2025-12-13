<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket_Message extends Model
{
    protected $fillable = ['ticket_id', 'sender_id', 'message', 'sender_type'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    //========= add morph relationship ========== //
}
