<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_user;
use App\Events\ClientRegistered;
use App\Events\UserRegistered;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            if(!env("OFFER.DIENSTPLAN", true)) {
                abort(404, "Not Supported");
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($package)
    {
        if(!collect(["basic", "module", "support"])->contains($package)) {
            abort(402, "Nope.");
        }
        return view('order.order', ['package' => $package] );
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if ($data['zip'] != "spamprevention" || !empty($data['street']))
        {
            return back()->withErrors('No.');
        }

        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'client_name' => 'required|string|min:4|unique:clients,name',
            'client_billing' => 'required|string|email|min:9|max:255|regex:/(.*)dlrg\.de$/i',
            'client_billing_address' => 'required|string',

            'captcha' => 'required|captcha',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $package)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect('/order/create/' . $package)
                ->withErrors($validator)
                ->withInput();
        }

        $client = Client::create([
            'name' => $request->get('client_name'),
            'seasonStart' => "2000-01-01",
            'isMailinglistCommunication' => false,
            'weeklyServiceviewEmail' => true,
            'mailinglistAddress' => null,
            'mailSenderName' => "DLRG Dienstplan",
            'mailReplyAddress' => $request->get('email'),
            'module_training' => $request->has('module_training') ? true : false,
            'module_training_credit' => $request->has('module_training') ? true : false
        ]);

        $user = User::create([
            'name' => $request->get('name'),
            'first_name' => $request->get('first_name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'currentclient_id' => $client->id
        ]);

        //add all superadmins as user
        $portalAdmins = User::where(['role' => 'admin', 'approved' => true])->get();
        foreach ($portalAdmins as $admin)
        {
            $client_user = Client_user::create([
                'client_id' => $client->id,
                'user_id' => $admin->id,
                'isAdmin' => 1
            ]);
            $client_user->approved = true;
            $client_user->save();
        }

        Client_user::create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'isAdmin' => 1,
            'approved' => 0
        ]);

        $order = $request->except('_token', 'captcha', 'zip', 'street', 'password', 'password_confirmation');
        $order["package"] = $package;

        event(new ClientRegistered($client, $user, $order));
        return view('order.success', compact('order'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
