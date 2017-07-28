<?php

namespace App\Listeners;

use App\Events\ServiceDelete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PositionDelete
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
     * @param  ServiceDelete  $event
     * @return void
     */
    public function handle(ServiceDelete $event)
    {
        //
    }
}
