<?php

namespace App\Http\Controllers;

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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $users =  User::orderBy('name')->get();
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
        if (Auth::user()->isAdmin() && Auth::user()->id != $id)
        {
            $user = User::findorFail($id);
            $user->fill($request->except(['id', '_token', 'password']));
            $user->save();

            return redirect(action('UserController@index'));
        }
        else
        {
            $user = Auth::user();
            $user->fill($request->except(['id', '_token', 'approved', 'role', 'password']));

            if ( !empty($request->get('password')))
            {
                $user->password = bcrypt($request->get('password'));
            }

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
        if(!Auth::user()->isAdmin()) {
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

        $user = User::findorFail($id);
        $user->approved = true;
        $user->save();

        event(new UserApproved($user, Auth::user()));

        return redirect(action('UserController@index'));
    }
}
