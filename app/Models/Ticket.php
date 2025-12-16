<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
        protected $fillable = [
        'account_id', 'user_id', 'title', 'category',
        'priority', 'status'
    ];

    public function account() {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages() {
        return $this->hasMany(TicketMessage::class);
    }
}
