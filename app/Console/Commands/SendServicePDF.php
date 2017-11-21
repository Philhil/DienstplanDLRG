<?php

namespace App\Console\Commands;

use App\Mail\ServicesList;
use App\Service;
use Illuminate\Console\Command;
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
        $services_count = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)]])->orderBy('date')->with('positions.qualification')->count();

        if($services_count > 0) {
            Mail::to(env('MAIL_LIST'))->queue(new ServicesList());
        }
    }
}
