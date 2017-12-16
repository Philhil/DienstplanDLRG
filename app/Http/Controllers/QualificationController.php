<?php

namespace App\Http\Controllers;

use App\Events\QualificationAssigned;
use App\Http\Requests\StoreQualification;
use App\Qualification;
use App\Qualification_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class QualificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!Auth::user()->can('administration')) {
                abort(402, "Nope.");
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
        $qualifications = \App\Qualification::orderBy('name')->get();
        return view('qualification.index')->with('qualifications', $qualifications);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $qualification = new Qualification();
        return view('qualification.create', compact('qualification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQualification $request)
    {
        if($request->has('id')) {
            $quali = Qualification::findOrFail($request->only('id'))->first();
            $quali->fill($request->except(['id']));
            $quali->save();
        } else {
            $quali = new Qualification($request->except(['id']));
            $quali->save();
        }

        return redirect(action('QualificationController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $qualification = Qualification::findorFail($id);
        return view('qualification.create', compact('qualification'));
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Qualification::findOrFail($id)->delete();
        return redirect(action('QualificationController@index'));
    }

    public function createQualification_User(Request $request)
    {
        $qualuser = Qualification_user::create($request->all());

        event(new QualificationAssigned($qualuser, Auth::user()));
        return "true";
    }

    public function deleteQualification_User(Request $request, $user_id, $qualification_id)
    {
        Qualification_user::where(['user_id' => $user_id, 'qualification_id' => $qualification_id])->delete();
        return "true";
    }
}
