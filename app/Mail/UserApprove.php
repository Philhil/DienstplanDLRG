<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserApprove extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $user;
    public $authorizedby;

    /**
     * Create a new message instance.
     *
     * @param User instance
     * @param User instance of authorized by
     * @return void
     */
    public function __construct(User $user, $authorizedby)
    {
        $this->user = $user;
        $this->authorizedby = $authorizedby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DLRG DIENSTE: Account Freigeschaltet')->view('email.user_approved');
    }
}
