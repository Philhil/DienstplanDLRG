<?php

namespace App\Http\Controllers;

use App\Events\PositionAuthorized;
use App\Position;
use App\PositionCandidature;
use App\Qualification;
use App\Service;
use App\Training;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->isAdmin())
        {
            $services = Service::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->with('openpositions')->with('positions.qualification')->with('positions.user')
                ->with('positions.candidatures')->with('positions.candidatures.user')->get();
        } else
        {
            $services = Service::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->with('openpositions')->with('positions.qualification')->with('positions.user')
                ->with('positions.candidatures')->with(['positions.candidatures.user'=> function ($query) {
                $query->where('id', '=', Auth::user()->id);
            }])->get();
        }

        $user = User::where(['id' => Auth::user()->id])->with('qualifications')->first();
        
        return view('service.index', compact('services', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $service = new Service();
        $qualifications = Qualification::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name')->get();
        $positions = new \Illuminate\Database\Eloquent\Collection();

        foreach (Qualification::where(['client_id' => Auth::user()->currentclient_id, 'isservicedefault' => true])->get() as $quali)
        {
            for ($i = 0; $i < $quali->defaultcount; $i++)
            {
                $positions->push(new Position(['qualification_id' => $quali->id, 'requiredposition' => $quali->defaultrequiredasposition]));
            }
        }

        $users = Auth::user()->currentclient()->user()->orderBy('name')->get();
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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $service = new Service();
        $service->date = Carbon::createFromFormat('d m Y', $request->get('date'))->startOfDay();
        $service->comment = $request->get('comment');
        $service->hastoauthorize = $request->get('hastoauthorize');
        $service->client_id = Auth::user()->currentclient_id;
        $service->save();

        if ($request->has('qualification') && $request->get('qualification')) {
            $qualifications = $request->get('qualification');
            $users = $request->get('user');
            $position_comment = $request->get('position_comment');
            $position_required = $request->get('position_required');
            for ($i = 0; $i < count($qualifications); $i++ ) {

                if (Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0) {
                    $position = new Position();
                    $position->service_id = $service->id;
                    $position->qualification_id = $qualifications[$i];
                    $position->requiredposition = $position_required[$i];
                    if ($users[$i] == "null") {
                        $position->user_id = null;
                    } else {
                        $position->user_id = $users[$i];
                    }
                    $position->comment = $position_comment[$i];

                    $saved = $position->save();

                    //inform user about new pos assign
                    if($saved && !is_null($position->user_id)) {
                        event(new PositionAuthorized($position, Auth::user()));
                    }
                }
            }

            Session::flash('successmessage', ' Neuen Dienst erfolgreich angelegt');
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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }
        // if admin or headofservice (service->positions where user && position->quali headofservice)
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $service = Service::findOrFail($id);
        $qualifications = Qualification::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name')->get();
        $positions = $service->positions()->get();
        $users = Auth::user()->currentclient()->user()->with('qualifications')->orderBy('name')->get();
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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $service = Service::findOrFail($id);
        $service->date = Carbon::createFromFormat('d m Y', $request->get('date'))->startOfDay();
        $service->comment = $request->get('comment');
        $service->hastoauthorize = $request->get('hastoauthorize');
        $service->save();

        //set changes and delete potential candidatures
        if ($request->has('qualification') && $request->get('qualification')) {
            $qualifications = $request->get('qualification');
            $users = $request->get('user');
            $position_comment = $request->get('position_comment');
            $position_required = $request->get('position_required');
            $positions = $request->get('position');

            //remove deleted positions (with candidatures)
            if($request->has('delete_position')) {
                foreach ($request->get('delete_position') as $delete_position) {
                    if ($delete_position >= 0) {
                        PositionCandidature::where('position_id', $delete_position)->forceDelete();
                        Position::where('id', $delete_position)->forceDelete();
                    }
                }
            }

            //If there are more qualifications than position: User has Add new Positions
            if (count($positions) < count($qualifications))
            {
                for ($i = count($positions); $i < count($qualifications); $i++ )
                {
                    if (Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0) {
                        $newposition = new Position();
                        $newposition->service_id = $service->id;
                        $newposition->qualification_id = $qualifications[$i];
                        $newposition->requiredposition = $position_required[$i];
                        if ($users[$i] == "null") {
                            $newposition->user_id = null;
                        } else {
                            $newposition->user_id = $users[$i];
                        }
                        $newposition->comment = $position_comment[$i];

                        $saved = $newposition->save();

                        //inform user about new pos assign
                        if ($saved && !is_null($newposition->user_id)) {
                            event(new PositionAuthorized($newposition, Auth::user()));
                        }
                    }
                }
            }

            //are there changed positions
            for ($i = 0; $i < count($positions); $i++ ) {
                $pos = Position::findOrFail($positions[$i]);

                //just security reasons -> no manipulation of foreign positions
                if ($pos->service_id == $id){

                    //null and "null" compare problem :(
                    $user_id = $pos->user_id;
                    if (is_null($pos->user_id))
                    {
                        $user_id = "null";
                    }

                    //changed comment?
                    //=>Update without inform
                    if (strcmp($pos->comment, $position_comment[$i]) != 0) {
                        $pos->comment = $position_comment[$i];
                    }

                    //changed requiredposition?
                    //=>Update without inform
                    if($pos->requiredposition != $position_required[$i]) {
                        $pos->requiredposition = $position_required[$i];
                    }

                    //changed qualification and not null?
                    //=>Update and inform user
                    $informUser = false;
                    if ($pos->qualification_id != $qualifications[$i] && !empty($qualifications[$i])
                        &&  Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0)
                    {
                        $pos->qualification_id = $qualifications[$i];
                        $informUser = true;
                    }

                    //changed user?
                    //=>Update and inform user
                    if ($user_id != $users[$i])
                    {
                        $pos->user_id = $users[$i] === "null" ? null : $users[$i];
                        $informUser = true;
                    }

                    $save = $pos->save();

                    if ($informUser && $save && !is_null($pos->user_id)) {
                        //user not null? -> remove all cadidates of this pos
                        PositionCandidature::where('position_id', '=', $pos->id)->forceDelete();

                        //inform user about new pos assign
                        event(new PositionAuthorized($pos, Auth::user()));
                    }
                }
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
        if(!Auth::user()->isAdmin() || Service::where(['id' => $id, 'client_id' => Auth::user()->currentclient_id])->count() <= 0) {
            abort(402, "Nope.");
        }

        Service::findOrFail($id)->forceDelete();

        return redirect(action('ServiceController@index'));
    }

    public function delete($id)
    {
        if(!Auth::user()->isAdmin() || Service::where(['id' => $id, 'client_id' => Auth::user()->currentclient_id])->count() <= 0) {
            abort(402, "Nope.");
        }

        return $this->destroy($id);
    }
}
