<?php

namespace App\Mail;

use App\Client;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientRegister extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $client;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $client, User $user, $order)
    {
        $this->user = $user;
        $this->client = $client;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DIENSTPLAN🚨: New Client ordered!')->view('email.new_client_register', ['user' => $this->user, 'client' => $this->client, 'order' => $this->order])
            ->replyTo(env('MAIL_FROM_ADDRESS'), env("MAIL_FROM_NAME"));
    }
}
