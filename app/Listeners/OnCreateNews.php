<?php

namespace App\Listeners;

use App\Mail\NewsToMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class OnCreateNews
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
     * @param  OnCreateNews  $event
     * @return void
     */
    public function handle(\App\Events\OnCreateNews $event)
    {
        $client = $event->news->client()->first();

        if ($client->isMailinglistCommunication) {
            Mail::to($client->mailinglistAddress)->queue(new NewsToMail($event->news));
        } else {
            foreach ($client->user()->get() as $user) {
                Mail::to($user->email)->queue(new NewsToMail($event->news));
            }
        }


    }
}
