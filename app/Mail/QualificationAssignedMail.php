<?php

namespace App\Mail;

use App\Client;
use App\Qualification_user;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class QualificationAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $qualification_user;
    protected $authorizedby;
    protected $client;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Qualification_user $qualification_user, $authorizedby, Client $client)
    {
        $this->qualification_user = $qualification_user;
        $this->authorizedby = $authorizedby;
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DLRG DIENSTE🏅: Qualifikation zugewiesen')->view('email.qualification')->with([
            'user' => $this->qualification_user->user()->first(),
            'qualification' => $this->qualification_user->qualification()->first(),
            'authorizedby' => $this->authorizedby,
        ])->from($this->client->mailReplyAddress, $this->client->mailSenderName);
    }
}
