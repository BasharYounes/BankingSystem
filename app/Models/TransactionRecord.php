<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\TransactionContract;

/**
 * Concrete Eloquent model for reading/querying the `transactions` table.
 *
 * We keep `App\Models\Transaction` abstract for the domain/behavior layer
 * (execute/validate implemented by subclasses), but Eloquent relationships
 * need a concrete model to instantiate.
 */
class TransactionRecord extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'type',
        'amount',
        'fees',
        'net_amount',
        'currency',
        'status',
        'account_id',
        'from_account_id',
        'to_account_id',
        'user_id',
        'description',
        'metadata',
        'reference_number',
        'priority',
        'requires_approval',
        'approved_by',
        'approved_at',
        'executed_at',
        'scheduled_for',
        'processed_at',
        'is_suspicious',
        'fraud_check_notes',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'metadata' => 'array',
        'requires_approval' => 'boolean',
        'is_suspicious' => 'boolean',
        'approved_at' => 'datetime',
        'executed_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }

    public function fromAccount()
    {
        return $this->belongsTo(AccountModel::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(AccountModel::class, 'to_account_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
        public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}


