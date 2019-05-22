<?php

namespace App\Listeners;

use App\Events\QualificationAssigned;
use App\Mail\QualificationAssignedMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class QualificationAssignedListener
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
        Mail::to($event->qualification_user->user()->first())
            ->queue(new QualificationAssignedMail($event->qualification_user, $event->authorizedby, $event->authorizedby->currentclient()));
    }
}
