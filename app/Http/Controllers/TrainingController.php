<?php

namespace App\Http\Controllers;

use App\Credit;
use App\Position;
use App\Qualification;
use App\Training;
use App\Training_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!Auth::user()->currentclient()->module_training) {
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
        $isAdmin = Auth::user()->isAdmin();
        $isTrainingEditor = Auth::user()->isTrainingEditor();

        if($isAdmin || $isTrainingEditor) {
            $trainings = Training::where('date', '>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->with('openpositions')->with('positions.qualification')->with('positions.user')
                ->with('positions.candidatures')->with('positions.candidatures.user')
                ->with('training_users')->with('training_users.user')->get();
        } else
        {
            $trainings = Training::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->with('openpositions')->with('positions.qualification')->with('positions.user')
                ->with('positions.candidatures')->with(['positions.candidatures.user'=> function ($query) {
                    $query->where('id', '=', Auth::user()->id);
                }])->get();
        }

        $user = User::where(['id' => Auth::user()->id])->with('qualifications')->first();

        return view('training.index', compact('trainings', 'user', 'isAdmin', 'isTrainingEditor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->isAdmin() || Auth::user()->isTrainingEditor()) {
            $training = new Training();
            $qualifications = Qualification::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name')->get();
            $positions = new \Illuminate\Database\Eloquent\Collection(); //initial no positions
            $training_users = new \Illuminate\Database\Eloquent\Collection(); //initial no training_users
            $credit = new \Illuminate\Database\Eloquent\Collection(); //initial no credit

            return view('training.create', compact('training', 'positions', 'qualifications', 'training_users', 'credit'));
        }

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

        if(Auth::user()->isAdmin()  || Auth::user()->isTrainingEditor()) {
            $training = new Training();
            $training->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
            $training->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
            $training->title = $request->get('title');
            $training->content = empty($request->get('content')) ? "" : $request->get('content');
            $training->sendbydatetime = empty($request->get('sendbydatetime')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('sendbydatetime'));
            $training->location = $request->get('location');
            $training->client_id = Auth::user()->currentclient_id;
            $training->save();

            //add positions of training
            if ($request->has('qualification') && $request->get('qualification')) {
                $qualifications = $request->get('qualification');
                $position_comment = $request->get('position_comment');
                $credits = $request->input('credit');

                for ($i = 0; $i < count($qualifications); $i++ ) {

                    if (Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0) {
                        $position = new Position();
                        $position->training_id = $training->id;
                        $position->service_id = null;
                        $position->qualification_id = $qualifications[$i];
                        $position->requiredposition = false;
                        $position->user_id = null;
                        $position->comment = $position_comment[$i];
                        $position->save();

                        //add credit
                        $credit = new Credit();
                        $credit->position_id = $position->id;
                        $credit->qualification_id = $position->qualification_id;
                        $credit->points = !is_numeric($credits[$i]) ? 1 : $credits[$i];
                        $credit->save();
                    }
                }
            }

            Session::flash('successmessage', ' Neue Fortbildung erfolgreich angelegt');
            return redirect(action('TrainingController@create'));
        }

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
        if(!Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        $training = Training::findOrFail($id);
        return view('email.training', compact('training'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->isAdmin() || Auth::user()->isTrainingEditor()) {
            $training = Training::findOrFail($id);
            $qualifications = Qualification::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name')->get();
            $positions = $training->positions()->get();
            $training_users = $training->training_users()->get();

            return view('training.create', compact('training', 'positions', 'qualifications', 'training_users'));
        }

        abort(402, "Nope.");
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
        if(!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $training = Training::findOrFail($id);

        if(!Auth::user()->isAdminOfClient($training->client_id) && !Auth::user()->isTrainingEditorOfClient($training->client_id)) {
            abort(402, "Nope.");
        }

        $training->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
        $training->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
        $training->title = $request->get('title');
        $training->content = empty($request->get('content')) ? "" : $request->get('content');
        $training->sendbydatetime = empty($request->get('sendbydatetime')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('sendbydatetime'));
        $training->location = $request->get('location');
        $training->save();

        //set changes and delete position
        if ($request->has('qualification') && $request->get('qualification')) {
            $qualifications = $request->get('qualification');
            $position_comment = $request->get('position_comment');
            $positions = $request->get('position');
            $credits = $request->get('credit');

            //remove deleted positions (with candidatures)
            if($request->has('delete_position')) {
                foreach ($request->get('delete_position') as $delete_position) {
                    if ($delete_position >= 0) {
                        Position::where('id', $delete_position)->delete();
                    }
                }
            }

            //If there are more qualifications than position: User has Add new Positions
            $poscount = empty($positions) ? 0 : count($positions);
            if ($poscount < count($qualifications))
            {
                for ($i = $poscount; $i < count($qualifications); $i++ )
                {
                    if (Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0) {
                        $position = new Position();
                        $position->training_id = $training->id;
                        $position->service_id = null;
                        $position->qualification_id = $qualifications[$i];
                        $position->requiredposition = false;
                        $position->user_id = null;
                        $position->comment = $position_comment[$i];
                        $position->save();

                        //add credit
                        $credit = new Credit();
                        $credit->position_id = $position->id;
                        $credit->qualification_id = $position->qualification_id;
                        $credit->points = !is_numeric($credits[$i]) ? 1 : $credits[$i];
                        $credit->save();
                    }
                }
            }

            //are there changed positions
            for ($i = 0; $i < $poscount; $i++ ) {
                $pos = Position::findOrFail($positions[$i]);
                $credit = $pos->getCredit;

                //just security reasons -> no manipulation of foreign positions and credits
                if ($pos->training_id == $id && $credit->position_id == $pos->id){
                    $pos->comment = $position_comment[$i];

                    //changed to qualification of own client and not null?
                    if (!empty($qualifications[$i]) && Qualification::where(['id' => $qualifications[$i], 'client_id' => Auth::user()->currentclient_id])->count() > 0)
                    {
                        $pos->qualification_id = $qualifications[$i];
                        $credit->qualification_id = $qualifications[$i];
                    }
                    $pos->save();

                    $credit->points = !is_numeric($credits[$i]) ? 1 : $credits[$i];
                    $credit->save();
                }
            }
        }

        return redirect(action('TrainingController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $training = Training::findOrFail($id);
        //check if Training is of current client
        if(!Auth::user()->isAdminOfClient($training->client_id) && !Auth::user()->isTrainingEditorOfClient($training->client_id)) {
            abort(402, "Nope.");
        }

        $training->forceDelete();
        return redirect(action('TrainingController@index'));
    }

    /**
     * Remove the training_user resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_training_user($id)
    {
        $training_user = Training_user::findOrFail($id);

        if(Auth::user()->isAdminOfClient($training_user->training->client_id) || Auth::user()->isTrainingEditorOfClient($training_user->training->client_id) ||
            (Training_user::where(['id' => $id, 'user_id' => Auth::user()->id])->count() > 0 && !(Training_user::findOrFail($id)->training->date)->isToday())) {

            $posid = $training_user->position_id;
            $training_user->forceDelete();

            Session::flash('successmessage', 'Meldung der Fortbildung erfolgreich zurückgezogen');

            if(Session::has('redirect')) {
                return $posid;
            }
            return redirect(action('TrainingController@index'));
        }

        abort(402, "Nope.");
    }

}
