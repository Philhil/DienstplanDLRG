<?php

namespace App\Mail;

use App\Client;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegister extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $client;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Client $client)
    {
        $this->user = $user;
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DLRG DIENSTE: New Account registration!')->view('email.new_user_register');
    }
}
