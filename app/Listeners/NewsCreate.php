<?php

namespace App\Listeners;

use App\Events\NewsCreate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewsCreate
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
     * @param  NewsCreate  $event
     * @return void
     */
    public function handle(NewsCreate $event)
    {
        //
    }
}
