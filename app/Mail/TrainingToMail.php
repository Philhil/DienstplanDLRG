<?php

namespace App\Mail;

use App\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrainingToMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $training;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Training $trainig)
    {
        $this->training = $trainig;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->training->title)->view('email.training', ['training' => $this->training])
            ->replyTo($this->training->client->mailReplyAddress, $this->training->client->mailSenderName);
    }
}
