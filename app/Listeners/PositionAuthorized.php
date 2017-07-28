<?php

namespace App\Listeners;

use App\Events\PositionAuthorized;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
    public function handle(PositionAuthorized $event)
    {
        //
    }
}
