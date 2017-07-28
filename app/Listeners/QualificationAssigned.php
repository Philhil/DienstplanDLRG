<?php

namespace App\Listeners;

use App\Events\QualificationAssigned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QualificationAssigned
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
     * @param  QualificationAssigned  $event
     * @return void
     */
    public function handle(QualificationAssigned $event)
    {
        //
    }
}
