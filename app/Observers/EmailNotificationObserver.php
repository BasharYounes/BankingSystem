<?php

namespace App\Observers;

use App\Events\EmailObserver;
use App\Events\GenericNotificationEvent;
use App\Interfaces\Observer;
use App\Models\AccountModel;
use App\Models\User;

class EmailNotificationObserver implements Observer
{
    public function update(string $eventType, AccountModel $account, array $data): void
    {
        $dataEmail = $data;
        $dataEmail['eventType'] = $eventType;

        $user = User::findOrFail($account->user_id);

        event(new EmailObserver(
            $user,
            $dataEmail
        ));

        event( new GenericNotificationEvent(
            $user,
            $eventType,
            $data
        ));

        if ($eventType === 'transfer')
        {
            $accountTo = AccountModel::where('account_number',$data['to_account_number'])->firstOrFail();

            event(new EmailObserver(
                $accountTo->first()->user,
                $dataEmail
            ));

            event( new GenericNotificationEvent(
                $accountTo->first()->user,
                $eventType,
                $data
            ));

        }
    }
}
