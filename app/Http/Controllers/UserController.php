<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_user;
use App\Events\UserApproved;
use App\Position;
use App\Qualification;
use App\Qualification_user;
use App\Service;
use App\Training;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        if (Auth::user()->isSuperAdmin() && Route::current()->getPrefix() == '/superadmin')
        {
            $users = User::orderBy('name')->with('clients', 'client_user', 'qualifications')->get();
        }
        else
        {
            $users = Client::findorfail(Auth::user()->currentclient_id)->user_all()->with('clients', 'client_user', 'qualifications')->get();
        }

        $qualifications = Auth::user()->currentclient()->Qualifications()->get();
        $authuser = Auth::user();

        return view('user.index', compact('users', 'authuser', 'qualifications'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(402, "Nope.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(402, "Nope.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()->isSuperAdmin() || Auth::user()->isAdmin()) {
            $user = User::findOrFail($id);
        }
        else
        {
            $user = Auth::user();
        }

        $saison = Auth::user()->currentclient()->Season();
        $fromYear = $saison["from"]->copy()->subYear(2)->year;
        $toYear = $saison["from"]->copy()->year;


        //get all qualification types where Trainings exsist and user has qualification
        $all_qualfications_where_trainings_exsist_and_user_has = Qualification::select('qualifications.id', 'qualifications.name')
            ->join('qualification_users', 'qualification_users.qualification_id', '=', 'qualifications.id')
            ->join('positions', 'positions.qualification_id', '=', 'qualifications.id')
            ->join('trainings', 'trainings.id', '=', 'positions.training_id')
            //not relevant if user participate
            ->where(['qualification_users.user_id' => $user->id, 'trainings.client_id' => Auth::user()->currentclient_id])
            ->whereNull('positions.service_id') //is a training and not a service
            ->whereBetween('trainings.date', [$saison["from"]->copy()->year($fromYear), $saison["to"]])
            ->groupBy('qualifications.id', 'qualifications.name')
            ->orderBy('qualifications.name')
            ->pluck('name', 'qualifications.id')->toArray();

        $years = range($fromYear,$toYear);
        $qualfication_credits = array();

        $debug = array();
        foreach ($years as $year) {
            $from = $saison["from"]->copy()->year($year);
            $year == $toYear ?  $to = Carbon::now() : $to = $saison["to"]->copy()->year($year+1);

            //get all qualifications of user with sum of credit points
            $qualfication_credits[$year] = Qualification::selectRaw('sum(credits.points) as sum_points, qualifications.id')
                ->join('qualification_users', 'qualification_users.qualification_id', '=', 'qualifications.id')
                ->join('positions', 'positions.qualification_id', '=', 'qualifications.id')
                ->join('training_users', 'training_users.position_id', '=', 'positions.id')
                ->join('credits', 'credits.position_id', '=', 'positions.id')
                ->join('trainings', 'trainings.id', '=', 'training_users.training_id')
                //user have to participate to get credits
                ->where(['qualification_users.user_id' => $user->id, 'training_users.user_id' => $user->id, 'trainings.client_id' => Auth::user()->currentclient_id])
                ->whereNull('positions.service_id') //is a training and not a service
                ->whereBetween('trainings.date', [$from, $to])
                ->groupBy('qualifications.id')
                ->pluck('sum_points', 'id')->toArray();
            $debug[$year] = [$from, $to, [$qualfication_credits[$year] ]];
        }

        return view('user.profile', compact('qualfication_credits', 'all_qualfications_where_trainings_exsist_and_user_has', 'years'))->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Refuse wenn 1. kein Admin oder Superadmin
        // oder
        // 2. kein Superadmin und user hat kein client_user.client mit aktuellerUser.client
        if (!(Auth::user()->isAdminOfClient(Auth::user()->currentclient_id) || Auth::user()->isSuperAdmin())
        || (empty(Client_user::where(['user_id' => $id, 'client_id' => Auth::user()->currentclient_id])->first())) && !Auth::user()->isSuperAdmin()){
            abort(402, "Nope.");
        }

        $user = User::findorFail($id);
        $qualifications_assigned = $user->qualifications()->get();
        $qualifications_notassigned = $user->qualificationsNotAssignedToUser()->get();
        return view('user.edit', compact('user', 'qualifications_assigned', 'qualifications_notassigned'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(env("IS_DEMO", false)) {
            abort(402, "Not allowed in Demo Mode.");
        }

        if (Auth::user()->isSuperAdmin() && Auth::user()->id != $id)
        {
            $user = User::findorFail($id);
            $user->fill($request->except(['id', '_token']));
            $user->save();

            return redirect(action('UserController@index'));
        }
        else if (Auth::user()->isAdmin() && Auth::user()->id != $id)
        {
            $user = User::findorFail($id);
            $user->fill($request->except(['id', '_token', 'approved', 'role']));
            $user->save();

            return redirect(action('UserController@index'));
        }
        else
        {
            $user = Auth::user();
            $user->fill($request->except(['id', '_token', 'approved', 'role']));
            $user->save();

            return redirect(action('HomeController@index'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        if(env("IS_DEMO", false)) {
            abort(402, "Not allowed in Demo Mode.");
        }

        User::findorFail($id)->delete();

        Session::flash('successmessage', 'User erfolgreich gelöscht');
        return redirect(route('superadmin.user'));
    }

    public function approve_user($id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $user = User::find($id);
        $user->approved = true;
        $user->save();

        $client_user = Client_user::where(['user_id' => $id, 'client_id' => Auth::user()->currentclient_id])->first();
        $client_user->approved = true;
        $client_user->save();

        $user = User::findorFail($id);
        event(new UserApproved($user, Client::find(Auth::user()->currentclient_id), Auth::user()));

        Session::flash('successmessage', $user->first_name .' '. $user->name .' wurde freigegeben');
        return redirect(action('UserController@index'));
    }

    public function setcurrentclient($id)
    {
        if(Auth::user()->clients()->where(['clients.id' => $id, 'client_user.approved' => 1])->count() > 0)
        {
            $user = Auth::user();
            $user->currentclient_id = $id;
            $user->save();

            return redirect()->back();
        }

        $client = Client::find($id);
        return redirect()->back()->with(['errormessage' => 'Dein User ist für ' . empty($client) ? "" : $client->name . ' noch nicht freigegeben']);
    }
}
