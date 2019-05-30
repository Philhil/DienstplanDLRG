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

        define('ICAL_FORMAT', 'Ymd');

        $icalObject = "BEGIN:VCALENDAR
                        VERSION:2.0
                        METHOD:PUBLISH
                        PRODID:-//DLRG//Dienstplan//DE\n";

            $icalObject .=
                "BEGIN:VEVENT
                DTSTART:" . date(ICAL_FORMAT, strtotime($service->toDateString())) . "
                DTEND:" . date(ICAL_FORMAT, strtotime($service->date->toDateString())) . "
                DTSTAMP:" . date(ICAL_FORMAT, strtotime($service->created_at)) . "
                SUMMARY: DLRG Dienst $service->comment
                UID:$service->id
                STATUS:CONFIRMED
                LAST-MODIFIED:" . date(ICAL_FORMAT, strtotime($service->updated_at)) . "
                LOCATION:DLRG
                END:VEVENT\n";

        // close calendar
        $icalObject .= "END:VCALENDAR";
        $icalObject = str_replace(' ', '', $icalObject);

        return $this->subject('DLRG DIENSTE: Dienst zugewiesen')->view('email.position')->with([
            'position' => $this->position,
            'servicepositions' => $this->servicepositions,
            'authorizedby' => $this->authorizedby,
        ])->from($client->mailReplyAddress, $client->mailSenderName)
        ->attachData($icalObject, 'dienst.ics', [
            'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
        ]);
    }
}