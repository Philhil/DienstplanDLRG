<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\UserRegister;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class UserRegisteredListener
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
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $admins = $event->client->Admins()->select('email')->get();

        foreach ($admins as $admin)
        {
            Mail::to($admin->email)->queue(new UserRegister($event->user, $event->client));
        }
    }
}
