<?php

namespace App\Http\Controllers;

use App\Position;
use App\Qualification;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->can('administration'))
        {
            $services = Service::where('date','>=', DB::raw('CURDATE()'))->orderBy('date')->with('positions.qualification')->with('positions.user')->with('positions.candidatures')->with('positions.candidatures.user')->get();
        } else
        {
            $services = Service::where('date','>=', DB::raw('CURDATE()'))->orderBy('date')->with('positions.qualification')->with('positions.user')->with('positions.candidatures')->get();
        }

        return view('service.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $service = new Service();
        $qualifications = Qualification::orderBy('name')->get();
        $positions = new \Illuminate\Database\Eloquent\Collection();

        foreach (Qualification::where(['isservicedefault' => true])->get() as $quali)
        {
            $positions->push(new Position(['qualification_id' => $quali->id]));
        }

        $users = User::orderBy('name')->get();
        return view('service.create', compact('service', 'positions', 'qualifications', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $service = new Service();
        $service->date = Carbon::createFromFormat('d m Y', $request->get('date'));
        $service->comment = $request->get('comment');
        $service->hastoauthorize = $request->get('hastoauthorize');
        $service->save();

        if ($request->get('qualification')) {
            $qualifications = $request->get('qualification');
            $users = $request->get('user');
            $position_comment = $request->get('position_comment');
            for ($i = 0; $i < count($qualifications); $i++ ) {
                $position = new Position();
                $position->service_id = $service->id;
                $position->qualification_id = $qualifications[$i];
                if ($users[$i] == "null") {
                    $position->user_id = null;
                } else {
                    $position->user_id = $users[$i];
                }
                $position->comment = $position_comment[$i];

                $position->save();
            }
        }

        return redirect(action('ServiceController@create'));
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
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $service = Service::findOrFail($id);
        $qualifications = Qualification::orderBy('name')->get();
        $positions = $service->positions()->get();
        $users = User::orderBy('name')->get();
        return view('service.create', compact('service', 'positions', 'qualifications', 'users'));
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
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $service = Service::findOrFail($id);
        $service->date = Carbon::createFromFormat('d m Y', $request->get('date'));
        $service->comment = $request->get('comment');
        $service->hastoauthorize = $request->get('hastoauthorize');
        $service->save();

        //Delete all Positions and recreate 
        Position::where('service_id', '=', $id)->forceDelete();
        if ($request->get('qualification')) {
            $qualifications = $request->get('qualification');
            $users = $request->get('user');
            $position_comment = $request->get('position_comment');
            for ($i = 0; $i < count($qualifications); $i++ ) {
                $position = new Position();
                $position->service_id = $service->id;
                $position->qualification_id = $qualifications[$i];
                if ($users[$i] == "null") {
                    $position->user_id = null;
                } else {
                    $position->user_id = $users[$i];
                }
                $position->comment = $position_comment[$i];

                $position->save();
            }
        }

        return redirect(action('ServiceController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        Service::findOrFail($id)->forceDelete();

        return redirect(action('ServiceController@index'));
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
