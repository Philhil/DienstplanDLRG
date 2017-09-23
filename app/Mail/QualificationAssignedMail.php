<?php

namespace App\Mail;

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
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Qualification_user $qualification_user, $authorizedby)
    {
        $this->qualification_user = $qualification_user;
        $this->authorizedby = $authorizedby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DLRG DIENSTE: Qualifikation zugewiesen')->view('email.qualification')->with([
            'user' => $this->qualification_user->user()->first(),
            'qualification' => $this->qualification_user->qualification()->first(),
            'authorizedby' => $this->authorizedby,
        ]);
    }
}
