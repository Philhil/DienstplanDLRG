<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Http\Requests\StoreCalendarRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CalendarController extends Controller
{
    public function index()
    {
        $isAdmin = Auth::user()->isAdmin();
        $isTrainingEditor = Auth::user()->isTrainingEditor();

        if($isAdmin || $isTrainingEditor) {
            $calendars = Calendar::where('date', '>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->get();
        } else {
            $calendars = Calendar::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->get();
        }

        $user = User::where(['id' => Auth::user()->id])->with('qualifications')->first();

        return view('calendar.index', compact('calendars', 'user', 'isAdmin', 'isTrainingEditor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(402, 'Nope');
        }

        $calendar = new Calendar();

        return view('calendar.create', compact('calendar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCalendarRequest  $request)
    {
        if (Auth::user()->isAdmin() || Auth::user()->isTrainingEditor()) {
            $calendar = new Calendar();
            $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
            $calendar->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
            $calendar->title = $request->get('title');
            $calendar->verantwortlicher = empty($request->get('verantwortlicher')) ? "" : $request->get('verantwortlicher');
            $calendar->sendbydatetime = empty($request->get('sendbydatetime')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('sendbydatetime'));
            $calendar->location = $request->get('location');
            $calendar->client_id = Auth::user()->currentclient_id;
            $calendar->save();

            Session::flash('successmessage', ' Neuer Kalender erfolgreich angelegt');
            return redirect(action('CalendarController@create'));
        }

        abort(402, "Nope.");
    }

    /**
     * Store a newly created resource in storage. Helper function of store()
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function store_create(Request $request) {
        $calendar = new Calendar();
        $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
        $dateEnd =  empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
        $calendar->dateEnd = ($dateEnd == null || $dateEnd->lessThanOrEqualTo($calendar->date)) ? null : $dateEnd;
        $calendar->verantwortlicher = $request->get('verantwortlicher');
        $calendar->location = $request->get('location');
        $calendar->client_id = Auth::user()->currentclient_id;
        $calendar->save();

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

        $calendar = Calendar::findOrFail($id);

        if(!Auth::user()->isAdminOfClient($calendar->client_id) && !Auth::user()->isTrainingEditorOfClient($calendar->client_id)) {
            abort(402, "Nope.");
        }

        $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
        $calendar->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
        $calendar->title = $request->get('title');
        $calendar->verantwortlicher = empty($request->get('verantwortlicher')) ? "" : $request->get('verantwortlicher');
        $calendar->sendbydatetime = empty($request->get('sendbydatetime')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('sendbydatetime'));
        $calendar->location = $request->get('location');
        $calendar->save();
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
        if (!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $calendar = Calendar::findOrFail($id);

        return view('calendar.edit', compact('calendar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function udate(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $calendar = Calendar::findOrFail($id);

        if (!Auth::user()->isAdminOfClient($calendar->client_id) && !Auth::user()->isTrainingEditorOfClient($calendar->client_id)) {
            abort(402, "Nope.");
        }

        echo('TestTestTest');

        $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
        $calendar->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
        $calendar->title = $request->get('title');
        $calendar->verantwortlicher = empty($request->get('verantwortlicher')) ? "" : $request->get('verantwortlicher');
        $calendar->sendbydatetime = empty($request->get('sendbydatetime')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('sendbydatetime'));
        $calendar->location = $request->get('location');
        $calendar->save();

        return redirect(action('CalendarController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $calendar = Calendar::findOrFail($id);

        if (!Auth::user()->isAdminOfClient($calendar->client_id)  && !Auth::user()->isTrainingEditorOfClient($calendar->client_id)) {
            abort(402, "Nope.");
        }

        $calendar->forceDelete();
        return redirect(action('CalendarController@index'));
    }
}
