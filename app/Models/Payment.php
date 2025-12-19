<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $fillable = [
        'account_id',
        'payment_method_id',
        'amount',
        'transaction_id',
        'status',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(Payment_method::class);
    }

    public function account()
    {
        $this->belongsTo(AccountModel::class);
    }
}
