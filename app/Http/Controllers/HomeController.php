<?php

namespace App\Http\Controllers;

use App\Mail\WachplanToMail;
use App\Position;
use App\Service;
use App\Training_user;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saison = Auth::user()->currentclient()->Season();

        $positions_user_past = Position::where('user_id', '=', Auth::user()->id)->whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->count();
        $positions_user_past_training = Training_user::where('user_id', '=', Auth::user()->id)->join('trainings', 'training_users.training_id', '=', 'trainings.id')
            ->where('trainings.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.date', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)->count();
        $positions_total_past = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->count();
        $positions_total_past_training = Training_user::join('trainings', 'training_users.training_id', '=', 'trainings.id')
            ->where('trainings.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.date', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)->count();
        $positions_free_required = Position::where('user_id', '=', null)->whereNull('positions.training_id')
            ->where('requiredposition', '=', 1)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '>=', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->count();
        $top_users = Position::where('user_id', '!=', null)->whereNotNull('positions.service_id')->with('user')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->selectRaw('user_id, count(*) as aggregate')
            ->groupBy('user_id')->limit(10)->orderby('aggregate', 'desc')->get();

        return view('home.index', compact('positions_user_past', 'positions_user_past_training', 'positions_total_past', 'positions_total_past_training', 'positions_free_required', 'top_users'));
    }

    public function mailtest(){
        $tableheader = \App\Qualification::where(['isservicedefault' => true, 'client_id' => Auth::user()->currentclient_id])->get();
        $services = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)], 'services.client_id' => Auth::user()->currentclient_id])->orderBy('date')->with('positions.qualification')->get();
        $client = Auth::user()->currentclient();

        return view('email.serviceslist', compact('tableheader', 'services', 'client'));
    }

    public function generatePDF()
    {
        $tableheader = Auth::user()->currentclient()->Qualifications()->where('isservicedefault', true)->get();
        $services = \App\Service::where(['client_id' => Auth::user()->currentclient_id,['date','>=', DB::raw('CURDATE()')]])->orderBy('date')->with('positions.qualification')->get();
        $client = Auth::user()->currentclient();

        $pdf = PDF::loadView('email.serviceslist', [
            'tableheader' => $tableheader,
            'services' => $services,
            'client' => $client
        ])->setPaper('a3', 'landscape');

        return $pdf->download('Dienstplan'.Carbon::now()->format('Ymd').'.pdf');
    }

    public function sendServicePDF()
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $services_count = Service::where(['client_id' => Auth::user()->currentclient_id, ['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addWeek(2)]])->orderBy('date')->with('positions.qualification')->count();

        if($services_count > 0) {
            $client = Auth::user()->currentclient();
            if ($client->isMailinglistCommunication) {
                Mail::to($client->mailinglistAddress)->queue(new WachplanToMail($client));
            } else {
                foreach ($client->user()->get() as $user) {
                    Mail::to($user->email)->queue(new WachplanToMail($client));
                }
            }
        }

        Session::flash('successmessage', ' E-Mail Versand erfolgreich gestartet!');
        return back();
    }

    public function getUserGuide()
    {
        $file = base_path('docs/userguide/dlrgdienstplan.pdf');
        $headers = array(
            'Content-Type: application/pdf',
        );

        return \response()->download($file, 'dienstplan_userguide.pdf', $headers);
    }
}
