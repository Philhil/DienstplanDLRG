<?php

namespace App\Listeners;

use App\Events\ClientRegistered;
use App\Mail\ClientRegister;
use App\Mail\OrderDone;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ClientRegisteredListener
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
     * @param  ClientRegistered  $event
     * @return void
     */
    public function handle(ClientRegistered $event)
    {
        // send Mail to all Superadmins
        $portalAdmins = User::where(['role' => 'admin', 'approved' => true])->get();

        foreach ($portalAdmins as $admin)
        {
            Mail::to($admin->email)->queue(new ClientRegister($event->client, $event->user, $event->order));
        }

        //inform User that Order is complete
        Mail::to($event->user->email)->queue(new OrderDone($event->client, $event->user, $event->order));
    }
}
