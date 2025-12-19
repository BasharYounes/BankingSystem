<?php

namespace App\Models;

use App\Interfaces\Observer;
use App\Interfaces\Subject;
use Illuminate\Database\Eloquent\Model;

class AccountModel extends Model implements Subject
{
    protected $table = 'accounts';

    protected static array $observers = [];

    protected $fillable = [
        'account_number',
        'balance',
        'user_id',
        'type',
        'status',
        'component_type',
        'parent_id',
        'is_composite',
        'opening_date',
        'closing_date',
        'interest_rate',
        'overdraft_limit',
        'loan_amount',
        'loan_term_months',
        'risk_level',
        'minimum_balance',
    ];

    protected $casts = [
        'balance' => 'float',
        'interest_rate' => 'float',
        'overdraft_limit' => 'float',
        'loan_amount' => 'float',
        'minimum_balance' => 'float',
        'is_composite' => 'boolean',
        'opening_date' => 'date',
        'closing_date' => 'date',
    ];

    /* ===========================
     ✅ العلاقات (Relationships)
     =========================== */

    // 🔹 الحساب يتبع لمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 الحساب الأب (في حالة Composite)
    public function parent()
    {
        return $this->belongsTo(AccountModel::class, 'parent_id');
    }

    // 🔹 الحسابات الأبناء (في حالة Composite)
    public function children()
    {
        return $this->hasMany(AccountModel::class, 'parent_id');
    }

    // 🔹 جميع الحسابات الفرعية لو كان الحساب مركب
    public function components()
    {
        return $this->children();
    }

    // 🔹 إن كان الحساب مركبًا فعلاً
    public function isComposite(): bool
    {
        return $this->is_composite === true;
    }

    // 🔹 الحسابات المرتبطة بمحفظة الأصول
    public function assetPortfolios()
    {
        return $this->hasMany(ِAsset_Protfolisos::class, 'account_id');
    }

    public function transactions()
    {
        return $this->hasMany(TransactionRecord::class, 'account_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // ===========================
    // ✅ نمط المراقب (Observer Pattern)
    // ===========================
    public function attach(Observer $observer): void
    {
        static::$observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        static::$observers = array_filter(static::$observers, fn($obs) => $obs !== $observer);
    }

    public function notify(string $eventType, array $data): void
    {
        foreach (static::$observers as $observer) {
            $observer->update($eventType, $this, $data);
        }
    }
}
