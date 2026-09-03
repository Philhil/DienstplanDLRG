<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceInformationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $serviceInformation;
    protected $position;
    protected $user;

    /**
     * Create a new mail instance.
     *
     * @return void
     */
    public function __construct(\App\ServiceInformation $serviceInformation, \App\Position $position)
    {
        $this->serviceInformation = $serviceInformation;
        $this->position = $position;    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client = $this->serviceInformation->client()->first();
        $informedby = $this->serviceInformation->user()->first();
        $user = $this->position->user()->first();
        return $this->subject('Dienstplan📢: Informationen zu deinem Dienst am '. $this->serviceInformation->service()->first()->date->isoFormat('ddd  DD.MM.YY H:mm'))->view('email.service_information')->with([
            'information' => $this->serviceInformation,
            'position' => $this->position,
            'user' => $user,
            'informedby' => $informedby
        ])->replyTo($informedby->email, "$informedby->first_name $informedby->name");
    }
}
