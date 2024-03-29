<?php

namespace App\Http\Controllers;

use App\Calendar;
use App\Http\Requests\StoreCalendarRequest;
use App\Service;
use App\Training;
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

        $calendars = Calendar::where('date','>=', DB::raw('CURDATE() -INTERVAL 1 YEAR'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->orderBy('date')->get();
        $trainings = Training::where('date','>=', DB::raw('CURDATE() -INTERVAL 1 YEAR'))->where('client_id', '=', Auth::user()->currentclient_id)
            ->orderBy('date')->get();

        //user -> user_pos -> pos -> service
        $services_of_user = Auth::user()->services()->where('services.date','>=', DB::raw('CURDATE() -INTERVAL 1 YEAR'))->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->orderBy('date')->get();

        $services = Service::where('services.date','>=', DB::raw('CURDATE() -INTERVAL 1 YEAR'))->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->orderBy('date')->get();
        $services_without_user = $services->diff($services_of_user);

        return view('calendar.index', compact('calendars', 'trainings', 'services_of_user', 'services_without_user', 'isAdmin', 'isTrainingEditor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, 'Nope');
        }

        $calendar = new Calendar();

        return view('calendar.create', compact('calendar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(StoreCalendarRequest  $request)
    {
        if (Auth::user()->isAdmin() || Auth::user()->isTrainingEditor()) {
            $calendar = new Calendar();
            $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
            $calendar->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
            $calendar->title = $request->get('title');
            $calendar->verantwortlicher = empty($request->get('verantwortlicher')) ? "" : $request->get('verantwortlicher');
            $calendar->location = $request->get('location');
            $calendar->client_id = Auth::user()->currentclient_id;
            $calendar->save();

            Session::flash('successmessage', ' Neuer Kalendereintrag erfolgreich angelegt');
            return redirect(action('CalendarController@index'));
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
        if(!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $calendar = Calendar::findOrFail($id);

        if (!Auth::user()->isAdminOfClient($calendar->client_id) && !Auth::user()->isTrainingEditorOfClient($calendar->client_id)) {
            abort(402, "Nope.");
        }

        return view('calendar.edit', compact('calendar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isTrainingEditor()) {
            abort(402, "Nope.");
        }

        $calendar = Calendar::findOrFail($id);

        if (!Auth::user()->isAdminOfClient($calendar->client_id) && !Auth::user()->isTrainingEditorOfClient($calendar->client_id)) {
            abort(402, "Nope.");
        }

        $calendar->date = Carbon::createFromFormat('d m Y H:i', $request->get('date'));
        $calendar->dateEnd = empty($request->get('dateEnd')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('dateEnd'));
        $calendar->title = $request->get('title');
        $calendar->verantwortlicher = empty($request->get('verantwortlicher')) ? "" : $request->get('verantwortlicher');
        $calendar->location = $request->get('location');
        $calendar->save();

        Session::flash('successmessage', 'Kalendereintrag gespeichert');
        return redirect(action('CalendarController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
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
        Session::flash('successmessage', 'Kalendereintrag gelöscht');
        return redirect(action('CalendarController@index'));
    }
}

