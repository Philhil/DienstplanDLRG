<?php

namespace App\Mail;

use App\Client;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserApprove extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $user;
    public $client;
    public $authorizedby;

    /**
     * Create a new message instance.
     *
     * @param User instance
     * @param User instance of authorized by
     * @return void
     */
    public function __construct(User $user, Client $client, $authorizedby)
    {
        $this->user = $user;
        $this->client = $client;
        $this->authorizedby = $authorizedby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Dienstplan🚨: Account Freigeschaltet')->view('email.user_approved')
            ->replyTo($this->client->mailReplyAddress, $this->client->mailSenderName);
    }
}
