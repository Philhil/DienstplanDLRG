<?php

namespace App\Listeners;

use App\Mail\NewsToMail;
use App\Mail\ServiceInformationMail;
use App\Service;
use App\ServiceInformation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OnCreateServiceInformation
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
     * @param  OnCreateServiceInformation
     * @return void
     */
    public function handle(\App\Events\OnCreateServiceInformation $event)
    {
        
        $service = $event->service;
        $serviceInformation = $event->serviceInformation;
        foreach ($service->assignpositions()->get() as $position) {
            $mail = $position->user()->first()->email;
            Mail::to($position->user()->first()->email)->queue(new ServiceInformationMail($serviceInformation, $position));
        }
    }
}
