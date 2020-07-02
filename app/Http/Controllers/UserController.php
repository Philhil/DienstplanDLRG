<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_user;
use App\Events\UserApproved;
use App\Position;
use App\Qualification_user;
use App\Service;
use App\Training;
use App\User;
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
        if(!Auth::user()->isAdmin() && !Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        if (Auth::user()->isSuperAdmin() && Route::current()->getPrefix() == '/superadmin')
        {
            $users = User::orderBy('name')->get();
        }
        else
        {
            $users = Client::findorfail(Auth::user()->currentclient_id)->user_all()->get();
        }

        return view('user.index')->with('users', $users);

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
        $user =  Auth::user();

        $saison = Auth::user()->currentclient()->Season();

       $wrd1 = Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '3')
            ->sum('points');
        $wrd2 = Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '6')
            ->sum('points');
        $wrd3 = Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '7')
            ->sum('points');
        $wrd4 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '3')
            ->sum('points');
        $wrd5 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '6')
            ->sum('points');
        $wrd6 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '7')
            ->sum('points');
        $wrd7 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '9')
            ->sum('points');
        $wrd8 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '10')
            ->sum('points');
        $wrd9 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '11')
            ->sum('points');

        $wrd = $wrd1 + $wrd2 + $wrd3 + $wrd4 + $wrd5 + $wrd6 + $wrd7 + $wrd8 + $wrd9;

        $boot1 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '5')
            ->sum('points');
        $boot2 =  Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '5')
            ->sum('points');

        $boot = $boot1 + $boot2;

        $sr1 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '8')
            ->sum('points');
        $sr2 = Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '8')
            ->sum('points');

        $sr = $sr1 + $sr2;

        $rd1 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '4')
            ->sum('points');
        $rd2 = Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '4')
            ->sum('points');

        $rd = $rd1 + $rd2;

        $f1 = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '1')
            ->sum('points');
        $f2 =  Position::where('user_id', '=', Auth::user()->id)
            ->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))
            ->whereBetween('services.date', [$saison["from"], $saison["to"]])
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '1')
            ->sum('points');

        $f = $f1 + $f2;

        $rsr = Position::where('user_id', '=', Auth::user()->id)
            ->join('trainings', 'positions.training_id', '=', 'trainings.id')
            ->where('trainings.dateEnd', '<', DB::raw('CURDATE()'))
            ->whereBetween('trainings.dateEnd', [$saison["from"], $saison["to"]])
            ->where('trainings.client_id', '=', Auth::user()->currentclient_id)
            ->join('credits', 'credits.position_id', '=', 'positions.id')
            ->where('credits.qualification_id', '=', '12')
            ->sum('points');

        return view('user.profile', compact('wrd', 'boot', 'sr', 'rd', 'f', 'rsr'))->with('user', $user);
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

        return redirect()->back()->with(['errormessage' => 'Dein User ist für ' . Client::find($id)->name . ' noch nicht freigegeben']);
    }
}
