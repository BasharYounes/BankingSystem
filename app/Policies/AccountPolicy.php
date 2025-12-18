<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AccountModel;

class AccountPolicy
{
    /**
     * هل يمكن طلب السحب؟
     */
    public function withdraw(User $user, AccountModel $account): bool
    {
        return match ($user->role) {
            'customer' => $account->user_id === $user->id
                          && $account->status === 'active',

            'teller'   => $account->status === 'active',

            'manager',
            'admin'    => false,
        };
    }

    /**
     * هل يمكن طلب الإيداع؟
     */
    public function deposit(User $user, AccountModel $account): bool
    {
        return match ($user->role) {
            'customer' => $account->user_id === $user->id
                          && in_array($account->status, ['active', 'frozen']),

            'teller'   => true,

            'manager',
            'admin'    => false,
        };
    }

    public function addAccount(User $user): bool
    {
        return match ($user->role) {
            'admin' => true,
            'manager' => false,
            'teller' => false,
            'customer' => false,
        };
    }

    public function getTicket(User $user): bool
    {
        return match ($user->role) {
            'admin' => false,
            'manager' => true,
            'teller' => false,
            'customer' => false,
        };
    }

    public function openTicket(User $user): bool
    {
        return match ($user->role) {
            'admin' => false,
            'manager' => true,
            'teller' => false,
            'customer' => false,
        };
    }

    public function changeStatusTicket(User $user): bool
    {
        return match ($user->role) {
            'admin' => false,
            'manager' => true,
            'teller' => false,
            'customer' => false,
        };
    }

    public function frozenAccount(User $user): bool
    {
        return match ($user->role) {
            'admin' => false,
            'manager' => true,
            'teller' => false,
            'customer' => false,
        };
    }
}
