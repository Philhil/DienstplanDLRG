<?php

namespace App\Mail;

use App\Client;
use App\Service;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class WachplanToMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $client;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tableheader = $this->client->Qualifications()->where('isservicedefault', true)->get();
        //get all services of next 2 month
        $services = Service::where(['client_id' => $this->client->id,['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)]])->orderBy('date')->with('positions.qualification')->get();

        if (count($services) > 0)
        {
            $pdf = \PDF::loadView('email.serviceslist', ['services'=>$services, 'tableheader'=>$tableheader])->setPaper('a3', 'landscape');

            return $this->subject('Wachplan')->view('email.serviceslist_text', ['client' => $this->client])
                ->from($this->client->mailReplyAddress, $this->client->mailSenderName)
                ->attachData($pdf->output(), 'Dienstplan'.Carbon::now()->format('Ymd').'.pdf');
        }
    }
}
