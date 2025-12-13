<?php

namespace App\Listeners;

use App\Events\EmailObserver;
use App\Mail\ObserverEmail;
use Mail;

class SendEmailObserver
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EmailObserver $event)
    {
        Mail::to($event->user->email)
        ->send(new ObserverEmail($event->data));
    }
}
