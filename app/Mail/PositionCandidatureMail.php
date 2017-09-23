<?php

namespace App\Mail;

use App\PositionCandidature;
use App\Qualification;
use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PositionCandidatureMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $position;
    protected $user;
    protected $positionCandidature;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PositionCandidature $positionCandidature)
    {
        $this->positionCandidature = $positionCandidature;
        $this->position = $positionCandidature->position()->with('service')->with('qualification')->first();
        $this->user = $positionCandidature->user()->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('DLRG DIENSTE: New Positioncandidature')->view('email.positioncandidate')
            ->with([
                'position' => $this->position,
                'positionCandidature' => $this->positionCandidature,
                'user' => $this->user,
            ]);
    }
}
