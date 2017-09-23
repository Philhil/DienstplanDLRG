<?php

namespace App\Listeners;

use App\Events\NewPositioncandidature;
use App\Mail\PositionCandidatureMail;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewPositioncandidatureListener
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
     * @param  NewPositioncandidature  $event
     * @return void
     */
    public function handle(NewPositioncandidature $event)
    {
        $admins = User::where('role', '=', 'admin')->select('email')->get();

        foreach ($admins as $admin)
        {
            Mail::to($admin->email)->queue(new PositionCandidatureMail($event->positionCandidature));
        }
    }
}
