<?php

namespace App\Listeners;

use App\Mail\UserApprove;
use Illuminate\Support\Facades\Mail;

class UserApproved
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserApproved  $event
     * @return void
     */
    public function handle(\App\Events\UserApproved $event)
    {
        Mail::to($event->user)->queue(new UserApprove($event->user, $event->client, $event->authorizedby));
    }
}
