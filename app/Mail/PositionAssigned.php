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
        $client = $this->position->service()->first()->client()->first();
        $service = $this->position->service()->first();


        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.dlrgdienstplan.de');
        $vEvent = new \Eluceo\iCal\Component\Event();
        $vEvent
            ->setDtStart(new \DateTime($service->toDateString()))
            ->setDtEnd(new \DateTime($service->toDateString()))
            ->setNoTime(true)
            ->setSummary('DLRG Dienst' . $service->comment);

        $vCalendar->addComponent($vEvent);

        return $this->subject('DLRG DIENSTE: Dienst zugewiesen')->view('email.position')->with([
            'position' => $this->position,
            'servicepositions' => $this->servicepositions,
            'authorizedby' => $this->authorizedby,
        ])->from($client->mailReplyAddress, $client->mailSenderName)
        ->attachData($vCalendar->render(), 'dienst.ics', [
            'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
        ]);
    }
}