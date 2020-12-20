<?php

namespace App\Http\Controllers;

use App\Position;
use App\Service;
use App\Training_user;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $top_users = Position::where('user_id', '!=', null)->whereNull('positions.service_id')->with('user')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->selectRaw('user_id, count(*) as aggregate')
            ->groupBy('user_id')->limit(10)->orderby('aggregate', 'desc')->get();

        return view('home.index', compact('positions_user_past', 'positions_user_past_training', 'positions_total_past', 'positions_total_past_training', 'positions_free_required', 'top_users'));
    }

    public function mailtest(){
        $tableheader = \App\Qualification::where(['isservicedefault' => true, 'client_id' => Auth::user()->currentclient_id])->get();
        $services = Service::where([['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)], 'services.client_id' => Auth::user()->currentclient_id])->orderBy('date')->with('positions.qualification')->get();

        return view('email.serviceslist', compact('tableheader', 'services'));
    }

    public function generatePDF()
    {
        $tableheader = Auth::user()->currentclient()->Qualifications()->where('isservicedefault', true)->get();
        //get all services of next 2 month
        $services = \App\Service::where(['client_id' => Auth::user()->currentclient_id,['date','>=', DB::raw('CURDATE()')], ['date', '<=', \Carbon\Carbon::today()->addMonth(2)]])->orderBy('date')->with('positions.qualification')->get();


        $pdf = PDF::loadView('email.serviceslist', [
            'tableheader' => $tableheader,
            'services' => $services,
        ])->setPaper('a3', 'landscape');

        return $pdf->download();
    }
}
