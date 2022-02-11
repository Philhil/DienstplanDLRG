<?php

namespace App\Console\Commands;

use App\Calendar;
use App\Client;
use App\Client_user;
use App\Credit;
use App\Holiday;
use App\News;
use App\Position;
use App\Qualification;
use App\Qualification_user;
use App\Service;
use App\Training;
use App\Training_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDemoClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:createDemoClient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Demo Client with Demo Data';

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
        //Perform only when DEMO is active & for safety: no client or only one specific demo client exists
        if(env("IS_DEMO", false) &&
            (Client::all()->count() == 0 || (Client::all()->count() == 1 && Client::first()->name == "DemoGliederung"))
        ) {
            //No we are save that maximum one Demo Client exists. First we have to delete the old Data
            if(Client::all()->count() >= 1)
            {
                foreach(News::all() as $news) {$news->Delete();}
                foreach(User::all() as $user) {$user->Delete();}
                Client::first()->Delete();
            }

            //create Client
            $client = Client::create([
                'name' => "DemoGliederung",
                'seasonStart' => "2000-01-01",
                'isMailinglistCommunication' => false,
                'weeklyServiceviewEmail' => true,
                'mailinglistAddress' => null,
                'mailSenderName' => env("MAIL_FROM_NAME","Dienstplan"),
                'mailReplyAddress' => env("MAIL_FROM_ADDRESS","demodienstplan@philhil.de"),
                'module_training' => true,
                'module_training_credit' => true,
                'module_statistic' => true
            ]);

            //create Users
            $user = User::create([
                'name' => "User",
                'first_name' => "User",
                'email' => "user.demodienstplan@philhil.de",
                'password' => bcrypt("user"),
                'currentclient_id' => $client->id
            ]);
            $user->approved = 1;
            $user->save();

            $admin = User::create([
                'name' => "Admin",
                'first_name' => "Admin",
                'email' => "admin.demodienstplan@philhil.de",
                'password' => bcrypt("admin"),
                'currentclient_id' => $client->id
            ]);
            $admin->approved = 1;
            $admin->save();

            //assign Users to client
            $client_user = Client_user::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'isAdmin' => 0,
                'isTrainingEditor' => 0
            ]);
            $client_user->approved = true;
            $client_user->save();

            $client_user = Client_user::create([
                'client_id' => $client->id,
                'user_id' => $admin->id,
                'isAdmin' => 1,
                'isTrainingEditor' => 0
            ]);
            $client_user->approved = true;
            $client_user->save();

            //create Qualifications
            $bf = Qualification::create([
                'name' => "Bootsführer",
                'short' => "Bf",
                'isservicedefault' => true,
                'defaultcount' => "1",
                'defaultrequiredasposition' => true
            ]);
            $bf->client_id = $client->id;
            $bf->save();

            $rs = Qualification::create([
                'name' => "Rettungsschwimmer",
                'short' => "RS",
                'isservicedefault' => true,
                'defaultcount' => "1",
                'defaultrequiredasposition' => true
            ]);
            $rs->client_id = $client->id;
            $rs->save();

            $prakti = Qualification::create([
                'name' => "Praktikant",
                'short' => "Prakti",
                'isservicedefault' => false,
                'defaultcount' => "0",
                'defaultrequiredasposition' => false
            ]);
            $prakti->client_id = $client->id;
            $prakti->save();

            Qualification_user::create(['qualification_id' => $bf->id, 'user_id' => $user->id]);
            Qualification_user::create(['qualification_id' => $rs->id, 'user_id' => $user->id]);
            Qualification_user::create(['qualification_id' => $prakti->id, 'user_id' => $user->id]);

            Qualification_user::create(['qualification_id' => $rs->id, 'user_id' => $admin->id]);
            Qualification_user::create(['qualification_id' => $prakti->id, 'user_id' => $admin->id]);

            //create Services & Positions (in past and future)
            $service_yesterday = new Service();
            $service_yesterday->date = Carbon::yesterday()->startOfDay();
            $service_yesterday->dateEnd = Carbon::yesterday()->endOfDay();
            $service_yesterday->comment = "Das ist ein Kommentar";
            $service_yesterday->hastoauthorize = true;
            $service_yesterday->client_id = $client->id;
            $service_yesterday->save();

            $pos_yesterday_1 = new Position();
            $pos_yesterday_1->service_id = $service_yesterday->id;
            $pos_yesterday_1->qualification_id = $rs->id;
            $pos_yesterday_1->requiredposition = true;
            $pos_yesterday_1->user_id = $user->id;
            $pos_yesterday_1->comment = "";
            $pos_yesterday_1->save();

            $pos_yesterday_2 = new Position();
            $pos_yesterday_2->service_id = $service_yesterday->id;
            $pos_yesterday_2->qualification_id = $prakti->id;
            $pos_yesterday_2->requiredposition = true;
            $pos_yesterday_2->user_id = $admin->id;
            $pos_yesterday_2->comment = "";
            $pos_yesterday_2->save();


            $service_tomorrow = new Service();
            $service_tomorrow->date = Carbon::tomorrow()->startOfDay();
            $service_tomorrow->dateEnd = Carbon::tomorrow()->endOfDay();
            $service_tomorrow->comment = "Das ist ein Kommentar";
            $service_tomorrow->hastoauthorize = true;
            $service_tomorrow->client_id = $client->id;
            $service_tomorrow->save();

            $pos_tomorrow_1 = new Position();
            $pos_tomorrow_1->service_id = $service_tomorrow->id;
            $pos_tomorrow_1->qualification_id = $bf->id;
            $pos_tomorrow_1->requiredposition = true;
            $pos_tomorrow_1->user_id = null;
            $pos_tomorrow_1->comment = "";
            $pos_tomorrow_1->save();

            $pos_tomorrow_2 = new Position();
            $pos_tomorrow_2->service_id = $service_tomorrow->id;
            $pos_tomorrow_2->qualification_id = $rs->id;
            $pos_tomorrow_2->requiredposition = true;
            $pos_tomorrow_2->user_id = null;
            $pos_tomorrow_2->comment = "";
            $pos_tomorrow_2->save();

            $pos_tomorrow_3 = new Position();
            $pos_tomorrow_3->service_id = $service_tomorrow->id;
            $pos_tomorrow_3->qualification_id = $prakti->id;
            $pos_tomorrow_3->requiredposition = true;
            $pos_tomorrow_3->user_id = $admin->id;
            $pos_tomorrow_3->comment = "";
            $pos_tomorrow_3->save();


            $service_nextweek = new Service();
            $service_nextweek->date = Carbon::today()->addWeek()->startOfDay();
            $service_nextweek->dateEnd = Carbon::today()->addWeek()->endOfDay();
            $service_nextweek->comment = "Das ist ein Kommentar";
            $service_nextweek->hastoauthorize = true;
            $service_nextweek->client_id = $client->id;
            $service_nextweek->save();

            $pos_yesterday_1 = new Position();
            $pos_yesterday_1->service_id = $service_nextweek->id;
            $pos_yesterday_1->qualification_id = $bf->id;
            $pos_yesterday_1->requiredposition = true;
            $pos_yesterday_1->user_id = null;
            $pos_yesterday_1->comment = "";
            $pos_yesterday_1->save();

            $pos_yesterday_2 = new Position();
            $pos_yesterday_2->service_id = $service_nextweek->id;
            $pos_yesterday_2->qualification_id = $rs->id;
            $pos_yesterday_2->requiredposition = true;
            $pos_yesterday_2->user_id = null;
            $pos_yesterday_2->comment = "";
            $pos_yesterday_2->save();

            //create Trainings & Positions
            $training = Training::create([
                'title' => "Demo Training",
                'client_id' => $client->id,
                'content' => "<p>Demo</p>",
                'date' => Carbon::today()->addWeek()->hour(19)->minute(0)->second(0),
                'dateEnd' => Carbon::today()->addWeek()->hour(21)->minute(0)->second(0),
                'location' => "An der Demo-Wachstation",
                'sendbydatetime' => null,
            ]);

            $pos_training = new Position();
            $pos_training->training_id = $training->id;
            $pos_training->qualification_id = $rs->id;
            $pos_training->user_id = null;
            $pos_training->save();

            Credit::create([
                'position_id' => $pos_training->id,
                'qualification_id' => $rs->id,
                'points' => 1
            ]);

            Training_user::create([
                'training_id' => $training->id,
                'user_id' => $admin->id,
                'position_id' => $pos_training->id
            ]);

            //create news
            $news = new News();
            $news->title = "Demo News";
            $news->content = "<p>Test der Demo News</p>";
            $news->user_id = $admin->id;
            $news->client_id = $client->id;
            $news->save();

            //create holiday
            $holiday = new Holiday();
            $holiday->from = Carbon::tomorrow()->startOfDay();
            $holiday->to = Carbon::tomorrow()->endOfDay();
            $holiday->user_id = $user->id;
            $holiday->save();

            //create Calendar
            $calendar = Calendar::create([
                'title' => "Demo Kalendereintrag",
                'client_id' => $client->id,
                'Verantwortlicher' => $user->Name,
                'date' => Carbon::today()->addWeek()->hour(19)->minute(0)->second(0),
                'dateEnd' => Carbon::today()->addWeek()->hour(21)->minute(0)->second(0),
                'location' => "An der Demo-Wachstation",
            ]);
        }
        else
        {
            $this->error('Not in Demo mode or more than demodata database');
        }
    }
}
