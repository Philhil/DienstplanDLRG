<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_user;
use App\Events\UserRegistered;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        $clients = Client::orderBy('name')->with('user')->get();
        return view('client.index')->with('clients', $clients);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        $client = new Client();
        return view('client.create')->with('client', $client);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        $validatedData = $request->validate([
            'name' => 'required|unique:clients|max:255',
            'seasonStart' => 'required',
            'isMailinglistCommunication' => 'boolean',
            'weeklyServiceviewEmail' => 'boolean',
            'mailinglistAddress' => 'required_with:isMailinglistCommunication',
            'mailSenderName' => 'required|alpha_dash|max:255',
            'mailReplyAddress' => 'required|email|max:255',
            'defaultServiceStart' => 'required',
            'defaultServiceEnd' => 'required'
        ]);

        $client = new Client($request->all());
        $client->seasonStart = Carbon::createFromFormat('d.m', $request->get("seasonStart"));
        $client->weeklyServiceviewEmail = isset($request['weeklyServiceviewEmail']);
        $client->isMailinglistCommunication = isset($request['isMailinglistCommunication']);
        $client->save();

        $client_user = new Client_user(['user_id' => Auth::user()->id, 'client_id' => $client->id, 'isAdmin' => true]);
        $client_user->approved = true;
        $client_user->save();

        Session::flash('message', $client->name . ' erfolgreich angelegt!');
        return redirect(action('ClientController@show', [$client->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->isAdminOfClient($id) && !Auth::user()->isSuperAdmin()){
            abort(402, "Nope.");
        }

        $client = Client::findOrFail($id);
        $usersOfClient = $client->noAdmins()->get();
        $adminsOfClient = $client->Admins()->get();

        return view('client.edit')->with(['client' => $client,
            'usersOfClient' => $usersOfClient, 'adminsOfClient' => $adminsOfClient]);
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
        if (!Auth::user()->isAdminOfClient($id) && !Auth::user()->isSuperAdmin()){
            abort(402, "Nope.");
        }

        $validatedData = $request->validate([
            'seasonStart' => 'required',
            'isMailinglistCommunication' => 'boolean',
            'weeklyServiceviewEmail' => 'boolean',
            'mailinglistAddress' => 'required_with:isMailinglistCommunication',
            'mailSenderName' => 'required|max:255',
            'mailReplyAddress' => 'required|email|max:255',
            'defaultServiceStart' => 'required',
            'defaultServiceEnd' => 'required'
        ]);
        
        $client = Client::findorfail($id);
        $client->fill($request->except(['name']));
        $client->seasonStart = Carbon::createFromFormat('d.m', $request->get("seasonStart"));
        $client->weeklyServiceviewEmail = isset($request['weeklyServiceviewEmail']);
        $client->isMailinglistCommunication = isset($request['isMailinglistCommunication']);
        $client->save();

        Session::flash('message', $client->name . ' gespeichert!');
        return $this->edit($id);
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
        //Cascate deleting: user_client, news, qualifications, services (services->Position, services->Position->PositionCandidates)

        Client::findorfail($id)->Delete();

        return redirect(action('ClientController@index'));
    }

    /**
     * Set Admin rights to the specified resource and user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool as String :D
     */
    public function adminClient_User(Request $request)
    {
        $clientuser = Client_user::where(['client_id' => $request->get('client_id'), 'user_id' => $request->get('user_id')])->first();

        if ( !is_null($clientuser))
        {
            //kann nur von admins oder superadmin erfolgen.
            if(!Auth::user()->isAdminOfClient($request->get('client_id')) && !Auth::user()->isSuperAdmin()) {
                abort(402, "Nope.");
            }

            $isAdmin = $request->get('isAdmin');

            if ($isAdmin == true)
            { //set admin
                $clientuser->isAdmin = true;
                $clientuser->save();
                return "true";
            }
            else if(Client_user::where(['client_id' => $request->get('client_id'), 'isAdmin' => true, 'approved' => true])->count() > 1) //es muss immer min. 1 admin einem client zugeordnet sein
            { //remove admin
                $clientuser->isAdmin = false;
                $clientuser->save();
                return "true";
            }
        }
        return "false";
    }

    public function apply()
    {
        $clients = Client::orderBy('name')->with('client_authuser')->get();
        return view('client.apply')->with('clients', $clients);
    }

    public function applyrequest($id)
    {
        Client_user::create(['user_id' => Auth::user()->id, 'client_id' => $id]);

        event(new UserRegistered(Auth::user(), Client::find($id)));
        return redirect()->back();
    }

    public function applyrevert($id)
    {
        Client_user::where(['user_id' => Auth::user()->id, 'client_id' => $id, 'approved' => 0])->forceDelete();
        return redirect()->back();
    }
}
