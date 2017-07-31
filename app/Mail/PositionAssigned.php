<?php

namespace App\Mail;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PositionAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $position;
    protected $servicepositions;
    protected $authorizedby;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Position $position, $authorizedby)
    {
        $this->position = $position;
        $this->servicepositions = $this->position->service->positions()->whereNotNull('user_id')->with('user')->with('qualification')->get();
        $this->authorizedby = $authorizedby;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.position')->with([
            'position' => $this->position,
            'servicepositions' => $this->servicepositions,
            'authorizedby' => $this->authorizedby,
        ]);
    }
}