<?php

namespace App\Listeners;

use App\Mail\PositionAssigned;
use Illuminate\Support\Facades\Mail;

class PositionAuthorized
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
     * @param  PositionAuthorized  $event
     * @return void
     */
    public function handle(\App\Events\PositionAuthorized $event)
    {
        $position = $event->position;
        Mail::to($position->user()->get())->later(now()->addMinutes(5), new PositionAssigned($position, $event->authorizedby, $position->user_id));
    }
}
