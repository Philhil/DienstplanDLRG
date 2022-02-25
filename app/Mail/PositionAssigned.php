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
    protected $user_id_original;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Position $position, $authorizedby, $user_id)
    {
        $this->position = $position;
        $this->authorizedby = $authorizedby;
        $this->user_id_original = $user_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //because this is delayed -> check if position is still valid and assigned user is still the same
        $posStillValid = Position::find($this->position->id);
        if ($posStillValid != null && $posStillValid->user_id != null && $posStillValid->user_id == $this->user_id_original) {

            $client = $this->position->service()->first()->client()->first();
            $service = $this->position->service()->first();
            $this->servicepositions = $this->position->service->positions()->whereNotNull('user_id')->with('user')->with('qualification')->get();

            if (!empty($service->dateEnd)) {
                $start = new \Eluceo\iCal\Domain\ValueObject\DateTime(new \DateTime($service->date->toDateTimeString()), true);
                $end = new \Eluceo\iCal\Domain\ValueObject\DateTime(new \DateTime($service->dateEnd->toDateTimeString()), true);
                $occurrence = new \Eluceo\iCal\Domain\ValueObject\TimeSpan($start, $end);
            }
            else {
                $date = new \Eluceo\iCal\Domain\ValueObject\Date(new \DateTime($service->date->toDateString()));
                $occurrence = new \Eluceo\iCal\Domain\ValueObject\SingleDay($date);
            }

            $vEvent = new \Eluceo\iCal\Domain\Entity\Event();
            $vEvent->setOccurrence($occurrence);
            $vEvent->setSummary('Wachdienst');
            if(!empty($service->comment)) {
                $vEvent->setDescription($service->comment);
            }
            $vEvent->setLocation((new \Eluceo\iCal\Domain\ValueObject\Location($service->location, $service->location)));

            $vCalendar = new \Eluceo\iCal\Domain\Entity\Calendar([$vEvent]);
            $iCalendarComponent = (new \Eluceo\iCal\Presentation\Factory\CalendarFactory())->createCalendar($vCalendar);

            return $this->subject('Dienstplan📟: Dienst zugewiesen')->view('email.position')->with([
                'position' => $this->position,
                'servicepositions' => $this->servicepositions,
                'authorizedby' => $this->authorizedby,
            ])->replyTo($client->mailReplyAddress, $client->mailSenderName)
            ->attachData(((string) $iCalendarComponent), 'dienst'.$service->date->toDateString().'.ics', [
                'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
            ]);
        }
    }
}