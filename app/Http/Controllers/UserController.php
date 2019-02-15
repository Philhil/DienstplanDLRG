<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_user;
use App\Events\UserApproved;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::user()->isSuperAdmin())
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
        return view('user.profile')->with('user', $user);
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
        return redirect(action('UserController@index'));
    }

    public function approve_user($id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $client_user = Client_user::where(['user_id' => $id, 'client_id' => Auth::user()->currentclient_id])->first();
        $client_user->approved = true;
        $client_user->save();

        $user = User::findorFail($id);
        event(new UserApproved($user, Client::find(Auth::user()->currentclient_id), Auth::user()));

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
