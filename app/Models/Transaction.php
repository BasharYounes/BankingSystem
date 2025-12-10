<?php

namespace App\Models;

use App\Interfaces\Account;
use Illuminate\Database\Eloquent\Model;

abstract class Transaction extends Model
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

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!isset($this->attributes['transaction_id'])) {
            $this->attributes['transaction_id'] = 'TXN-' . date('Ymd') . '-' . uniqid();
        }

        if (!isset($this->attributes['status'])) {
            $this->attributes['status'] = self::STATUS_PENDING;
        }
    }

    abstract public function execute(): bool;
    abstract public function validate(): bool;

    public function getDetails(): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }

    public function rollback(): bool
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->save();
        return true;
    }

    protected function setStatus(string $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

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
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
