<?php

namespace App\Http\Controllers;

use App\Qualification;
use App\Survey;
use App\Survey_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!Auth::user()->currentclient()->module_survey) {
                abort(402, "Module not activated.");
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $isAdmin = Auth::user()->isAdmin();

        if($isAdmin) {
            $surveys = Survey::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('created_at', 'ASC')->with('qualification')->paginate(30);
            return view('survey.index', compact('surveys', 'isAdmin'));
        }

        abort(402, "Nope.");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $isAdmin = Auth::user()->isAdmin();

        if($isAdmin) {
            $survey = new Survey();
            $qualifications = Auth::user()->currentclient()->qualifications()->pluck('name', 'id');
            $qualifications->prepend('-Alle Nutzer-', -1);
            return view('survey.create', compact('survey', 'isAdmin', 'qualifications'));
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
        if(Auth::user()->isAdmin()) {
            $survey = new Survey();

            $survey->client_id = Auth::user()->currentclient_id;
            $survey->title = $request->get('title');
            $survey->content = $request->get('content');
            $survey->dateStart = empty($request->get('dateStart')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateStart'));
            $survey->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
            $survey->mandatory = isset($request['mandatory']);
            $survey->passwordConfirmationRequired = isset($request['passwordConfirmationRequired']);
            $survey->qualification_id = (!empty($request->get('qualification_id')) && ((int)$request->get('qualification_id') >= 0)) ? (int)$request->get('qualification_id') : null;

            $survey->save();

            Session::flash('successmessage', 'Abfrage erfolgreich erstellt');
            return redirect(action('SurveyController@index'));
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
        $isAdmin = Auth::user()->isAdmin();
        $survey = Survey::with('users', 'survey_user')->findOrFail($id);

        //survey is in a client of the user AND (survey qualification == NULL OR user has the survey qualification)
        if(Auth::user()->clients()->get()->contains($survey->client_id)
            && (Auth::user()->isAdmin() || $survey->qualification_id == NULL || Auth::user()->hasqualification($survey->qualification_id))) {

            //show survey only if dateStart <= now <= dateEnd
            $isInDateRange = $isAdmin;
            if (!$isInDateRange) {
                //start and end
                if(isset($survey->dateStart) && isset($survey->dateEnd)) {
                    $isInDateRange = Carbon::now()->between($survey->dateStart, $survey->dateEnd);
                }
                //only start
                elseif(isset($survey->dateStart)) {
                    $isInDateRange = Carbon::now()->gte($survey->dateStart);
                }
                //only end
                elseif(isset($survey->dateEnd)) {
                    $isInDateRange = Carbon::now()->lte($survey->dateEnd);
                }
                //no date is set
                else
                {
                    $isInDateRange = true;
                }
            }

            if($isInDateRange) {
                $survey_user = Auth::user()->mySurveyUser($survey->id)->first();

                //Statistics
                $countAccept = $survey->survey_user()->where('vote', true)->count();
                $countDeny = $survey->survey_user()->where('vote', false)->count();
                if ($survey->qualification_id == NULL) {
                    $countNoVote = Auth::user()->currentclient()->user()->count();
                    $users_with_user_survey = Auth::user()->currentclient()->user()->with('mySurveyUsers', function ($query) use($survey) {
                        $query->where('survey_id','=',$survey->id);
                    })->get();
                } else {
                    $countNoVote = Qualification::findOrFail($survey->qualification_id)->users()->count();
                    $users_with_user_survey = Qualification::findOrFail($survey->qualification_id)->users()->with('mySurveyUsers', function ($query) use($survey) {
                        $query->where('survey_id', $survey->id);
                    })->get();
                }
                $countNoVote -= $survey->survey_user()->whereNotNull('vote')->count();
                return view('survey.show', compact('survey', 'isAdmin', 'survey_user', 'countAccept', 'countDeny', 'countNoVote', 'users_with_user_survey'));
            }
        }

        abort(402, "Nope.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $isAdmin = Auth::user()->isAdmin();

        if($isAdmin) {
            $survey = Survey::findOrFail($id);
            $qualifications = Auth::user()->currentclient()->qualifications()->pluck('name', 'id');
            $qualifications->prepend('-Alle Nutzer-', -1);
            $aUserHasAlreadyVoted = Survey_user::where('survey_id', $survey->id)->exists();

            return view('survey.edit', compact('survey', 'isAdmin', 'qualifications', 'aUserHasAlreadyVoted'));
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
        if(Auth::user()->isAdmin()) {
            $survey = Survey::findOrFail($id);
            $survey->title = $request->get('title');
            $survey->content = $request->get('content');
            $survey->dateStart = empty($request->get('dateStart')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateStart'));
            $survey->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
            $survey->mandatory = isset($request['mandatory']);
            $survey->passwordConfirmationRequired = isset($request['passwordConfirmationRequired']);
            $survey->qualification_id = (!empty($request->get('qualification_id')) && ((int)$request->get('qualification_id') >= 0)) ? (int)$request->get('qualification_id') : null;

            $survey->save();

            Session::flash('successmessage', 'Abfrage erfolgreich gespeichert');
            return redirect(action('SurveyController@index'));
        }

        abort(402, "Nope.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->isAdmin()) {
            $survey = Survey::findOrFail($id);
            $survey->delete();

            Session::flash('successmessage', 'Abfrage erfolgreich gelöscht');
            return redirect(action('SurveyController@index'));
        }

        abort(402, "Nope.");
    }

    /**
     * Store a survey_user in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function vote(Request $request, $survey_id)
    {
        //check password
        $survey = Survey::findOrFail($survey_id);
        if ($survey->passwordConfirmationRequired)
        {
            $this->validate($request, [
                'password' => 'required',
            ]);

            if(!Hash::check(request('password'), Auth::user()->password)) {
                //password not correct
                return back()->withInput()->withErrors(['password' => 'Ungültiges Passwort']);
            }
        }

        //findorcreate
        $survey_user =  Survey_user::firstOrNew(array('survey_id' => $survey_id, 'user_id' => Auth::user()->id));
        $survey_user->survey_id = $survey_id;
        $survey_user->user_id = Auth::user()->id;
        if(!$survey->mandatory) {
            $survey_user->vote = $request->get('submit') === 'accept' ? true : false;
        }
        else {
            $survey_user->vote = true;
        }
        $survey_user->votedAt = Carbon::now();
        $survey_user->rememberAt = null;
        $survey_user->save();

        return redirect()->action(
            [SurveyController::class, 'show'], [$survey_id]
        );
    }

    //remember_tomorrow
    public function postpone(Request $request, $survey_id) {
        $survey_user =  Survey_user::firstOrNew(array('survey_id' => $survey_id, 'user_id' => Auth::user()->id));

        if ($survey_user->rememberCount < 3) {
            $survey_user->survey_id = $survey_id;
            $survey_user->user_id = Auth::user()->id;
            $survey_user->rememberAt = Carbon::tomorrow();
            $survey_user->rememberCount +=1;
            $survey_user->save();

            return back();
        }

        Session::flash('errormessage', 'Die Abfrage kann nicht nochmal verschoben werden.');
        return redirect()->action(
            [SurveyController::class, 'show'], [$survey_id]
        );
    }
}
