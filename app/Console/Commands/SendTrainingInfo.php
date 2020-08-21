<?php

namespace App\Console\Commands;

use App\Mail\TrainingToMail;
use App\Training;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendTrainingInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendTrainingInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Training Information. Sheduled as Defined in Training Object';

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
        //Trainigs in future and sendbydatetime is in past.
        $trainings = Training::whereNotNull('sendbydatetime')
            ->where([['date','>', DB::raw('NOW()')], ['sendbydatetime','<=', DB::raw('NOW()')]])
            ->whereNull('sended')->get();

        foreach($trainings as $training) {
            $client = $training->client;

            if ($client->isMailinglistCommunication) {
                Mail::to($client->mailinglistAddress)->queue(new TrainingToMail($training));
            } else {
                foreach ($client->user()->get() as $user) {
                    Mail::to($user->email)->queue(new TrainingToMail($training));
                }
            }

            //save send Time
            $training->sended = Carbon::now();
            $training->save();
        }
    }
}
