<?php

namespace App\Console\Commands;

use App\Client;
use App\Mail\WachplanToMail;
use App\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendServicePDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendServicePDF';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Service Sheldue in PDF Format';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Client::all() as $client)
        {
            if ($client->weeklyServiceviewEmail)
            {
                $services_count = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addWeek(2)]])->orderBy('date')->with('positions.qualification')->count();

                if($services_count > 0) {
                    if ($client->isMailinglistCommunication) {
                        Mail::to($client->mailinglistAddress)->queue(new WachplanToMail($client));
                    } else {
                        foreach ($client->user()->get() as $user) {
                            Mail::to($user->email)->queue(new WachplanToMail($client));
                        }
                    }
                }
            }

        }
    }
}
