<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Factories\TransactionFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accounts()
    {
        return $this->hasMany(AccountModel::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function openAccount(string $type, array $data = [])
    {
        $accountNumber = $this->generateAccountNumber($type);

        // unset($data['opening_date']);

        $accountData = array_merge( $data,[
            'account_number' => $accountNumber,
            'user_id' => $this->id,
            'type' => $type,
            'balance' => $data['initial_balance'] ?? 0,
            'status' => 'active',
            'opening_date' => now()->format('Y-m-d'),
        ]);

        if ($type === 'composite') {
            $accountData['is_composite'] = true;
        }

        return AccountModel::create($accountData);
    }

    public function requestDeposit(AccountModel $account, float $amount, string $description = '')
    {
        if ($account->user_id !== $this->id) {
            throw new \Exception('لا تملك هذا الحساب');
        }

        $transaction = TransactionFactory::getInstance()->createTransaction('deposit', [
            'account_id' => $account->id,
            'user_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
        ]);

        $transaction->save();
        return $transaction;
    }

    public function requestWithdrawal(AccountModel $account, float $amount, string $description = '')
    {
        if ($account->user_id !== $this->id) {
            throw new \Exception('لا تملك هذا الحساب');
        }

        $transaction = TransactionFactory::getInstance()->createTransaction('withdrawal', [
            'account_id' => $account->id,
            'user_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
        ]);

        $transaction->save();
        return $transaction;
    }

    public function requestTransfer(AccountModel $fromAccount, AccountModel $toAccount, float $amount, string $description = '')
    {
        if ($fromAccount->user_id !== $this->id) {
            throw new \Exception('لا تملك الحساب المصدر');
        }

        $transaction = TransactionFactory::getInstance()->createTransaction('transfer', [
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'user_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
        ]);

        $transaction->save();
        return $transaction;
    }

    public function requestSell(AccountModel $fromAccount, AccountModel $toAccount, float $amount, string $description = '', array $metadata)
    {
        if ($fromAccount->user_id !== $this->id) {
            throw new \Exception('لا تملك هذا الحساب');
        }

        // dd('Creating Sell Transaction');
        $transaction = TransactionFactory::getInstance()->createTransaction('sellAsset', [
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'user_id' => $this->id,
            'amount' => $amount,
            'description' => $description,
            'metadata' => $metadata
        ]);
        // dd('Sell Transaction Created', $transaction->toArray());

        $transaction->save();
        // dd('Sell Transaction Saved');
        return $transaction;
    }

    private function generateAccountNumber(string $type): string
    {
        $prefix = match($type) {
            'savings' => 'SAV',
            'checking' => 'CHK',
            'loan' => 'LON',
            'investment' => 'INV',
            'composite' => 'COM',
            default => 'ACC'
        };

        $count = AccountModel::where('type', $type)->count() + 1;

        return $prefix . '-' . date('Y') . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function storeFCM_Token(string $fcm_token)
    {
        $this->update(['fcm_token' => $fcm_token]);
        return true;
    }
}
