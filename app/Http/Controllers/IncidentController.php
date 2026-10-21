<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Incident;
use App\Incident_attendance;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->currentclient()->module_incident) {
                abort(402, "Module not activated.");
            }

            return $next($request);
        });
    }

    /**
     * Find an incident of a client the user belongs to.
     */
    private function findViewable($id)
    {
        $incident = Incident::findOrFail($id);

        if (!$incident->isViewableBy(Auth::user())) {
            abort(402, "Nope.");
        }

        return $incident;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->currentclient_id;

        $openIncidents = Incident::where('client_id', $clientId)->whereNull('finalized_at')
            ->orderBy('start', 'desc')->withCount(['attendances as present_count' => function ($query) {
                $query->where('status', Incident_attendance::STATUS_PRESENT);
            }])->get();
        $finalizedIncidents = Incident::where('client_id', $clientId)->whereNotNull('finalized_at')
            ->orderBy('start', 'desc')->limit(100)->withCount(['attendances as present_count' => function ($query) {
                $query->where('status', Incident_attendance::STATUS_PRESENT);
            }])->get();

        $myAttendances = Incident_attendance::where('user_id', $user->id)
            ->whereIn('incident_id', $openIncidents->pluck('id'))->get()->keyBy('incident_id');

        $stations = $user->currentclient()->Stations()->get();
        $homeStationId = $user->homeStationId($clientId);
        $canCreate = Incident::canCreate($user, $clientId);
        $isAdmin = $user->isAdmin();

        return view('incident.index', compact('openIncidents', 'finalizedIncidents', 'myAttendances', 'stations',
            'homeStationId', 'canCreate', 'isAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Incident::canCreate(Auth::user(), Auth::user()->currentclient_id)) {
            abort(402, "Nope.");
        }

        $incident = new Incident();
        $incident->type = Incident::TYPE_INCIDENT;
        $incident->start = Carbon::now();
        $members = Auth::user()->currentclient()->user()->get();

        return view('incident.create', compact('incident', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreIncidentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIncidentRequest $request)
    {
        $incident = new Incident($request->only(['type', 'title', 'keyword', 'location', 'dispatch_number', 'note']));
        $incident->start = Carbon::createFromFormat('d m Y H:i', $request->get('start'));
        $incident->end = empty($request->get('end')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('end'));
        $incident->leader_user_id = empty($request->get('leader_user_id')) ? null : $request->get('leader_user_id');
        $incident->client_id = Auth::user()->currentclient_id;
        $incident->created_by = Auth::user()->id;
        $incident->save();

        Session::flash('successmessage', ' Ereignis erfolgreich angelegt');
        return redirect(action('IncidentController@show', $incident->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $incident = $this->findViewable($id);

        $attendances = $incident->attendances()->with(['user', 'station', 'recorder'])->get()
            ->sortBy(function ($attendance) {
                return mb_strtolower((string) $attendance->stationName() . '|' . $attendance->displayName());
            })->values();
        $myAttendance = $attendances->firstWhere('user_id', $user->id);
        $stations = $incident->client->Stations()->get();
        $homeStationId = $user->homeStationId($incident->client_id);

        $canManage = $incident->canManage($user);
        $canRecord = $incident->canRecord($user);
        $isAdmin = $incident->isManagedByAdmin($user);
        $canSelfReport = $incident->canSelfReport($user);

        $members = collect();
        if ($canRecord && $incident->isOpen()) {
            $attendedIds = $attendances->pluck('user_id')->filter()->all();
            $members = $incident->client->user()->get()->reject(function ($member) use ($attendedIds) {
                return in_array($member->id, $attendedIds);
            });
        }

        $unconfirmedCount = $attendances->filter(function ($attendance) {
            return !$attendance->isConfirmed();
        })->count();
        $onTheWayCount = $attendances->filter(function ($attendance) {
            return !$attendance->isPresent();
        })->count();

        return view('incident.show', compact('incident', 'attendances', 'myAttendance', 'stations', 'homeStationId',
            'canManage', 'canRecord', 'isAdmin', 'canSelfReport', 'members', 'unconfirmedCount', 'onTheWayCount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $incident = $this->findViewable($id);

        if (!$incident->canManage(Auth::user())) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            Session::flash('errormessage', ' Das Ereignis ist abgeschlossen und kann nicht mehr bearbeitet werden.');
            return redirect(action('IncidentController@show', $incident->id));
        }

        $members = $incident->client->user()->get();

        return view('incident.edit', compact('incident', 'members'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreIncidentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreIncidentRequest $request, $id)
    {
        $incident = $this->findViewable($id);

        if (!$incident->canManage(Auth::user())) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            Session::flash('errormessage', ' Das Ereignis ist abgeschlossen und kann nicht mehr bearbeitet werden.');
            return redirect(action('IncidentController@show', $incident->id));
        }

        $incident->fill($request->only(['type', 'title', 'keyword', 'location', 'dispatch_number', 'note']));
        $incident->start = Carbon::createFromFormat('d m Y H:i', $request->get('start'));
        $incident->end = empty($request->get('end')) ? null : Carbon::createFromFormat('d m Y H:i', $request->get('end'));
        $incident->leader_user_id = empty($request->get('leader_user_id')) ? null : $request->get('leader_user_id');
        $incident->save();

        Session::flash('successmessage', ' Ereignis gespeichert');
        return redirect(action('IncidentController@show', $incident->id));
    }

    /**
     * Remove the specified resource from storage.
     * Attendance lists are kept for queries and insurance, so deleting is a manual admin action.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);

        if (!$incident->isManagedByAdmin(Auth::user()) && !Auth::user()->isSuperAdmin()) {
            abort(402, "Nope.");
        }

        $incident->attendances()->delete();
        $incident->delete();

        Session::flash('successmessage', ' Ereignis und Anwesenheitsliste gelöscht');
        return redirect(action('IncidentController@index'));
    }

    /**
     * Finalize the incident: the attendance list is locked.
     * Every self report has to be confirmed and nobody may be left "on the way".
     */
    public function finalize(Request $request, $id)
    {
        $incident = $this->findViewable($id);

        if (!$incident->canManage(Auth::user())) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            return redirect(action('IncidentController@show', $incident->id));
        }

        $request->validate([
            'end' => 'nullable|date_format:d m Y H:i',
        ]);

        if (!empty($request->get('end'))) {
            $end = Carbon::createFromFormat('d m Y H:i', $request->get('end'));
            if ($end->lt($incident->start)) {
                Session::flash('errormessage', ' Das Ende darf nicht vor dem Beginn liegen.');
                return redirect(action('IncidentController@show', $incident->id));
            }
            $incident->end = $end;
        }

        if (is_null($incident->end)) {
            Session::flash('errormessage', ' Bitte zuerst das Ende des Ereignisses eintragen.');
            return redirect(action('IncidentController@show', $incident->id));
        }

        $unconfirmed = $incident->attendances()->whereNull('confirmed_at')->count();
        $onTheWay = $incident->attendances()->where('status', '!=', Incident_attendance::STATUS_PRESENT)->count();
        if ($unconfirmed > 0 || $onTheWay > 0) {
            Session::flash('errormessage', ' Es gibt noch unbestätigte Meldungen oder Helfer auf Anfahrt. Bitte zuerst auflösen.');
            return redirect(action('IncidentController@show', $incident->id));
        }

        $incident->finalized_at = Carbon::now();
        $incident->finalized_by = Auth::user()->id;
        $incident->save();

        Session::flash('successmessage', ' Ereignis abgeschlossen');
        return redirect(action('IncidentController@show', $incident->id));
    }

    /**
     * Reopen a finalized incident (admin only).
     */
    public function reopen($id)
    {
        $incident = $this->findViewable($id);

        if (!$incident->isManagedByAdmin(Auth::user())) {
            abort(402, "Nope.");
        }

        $incident->finalized_at = null;
        $incident->finalized_by = null;
        $incident->save();

        Session::flash('successmessage', ' Ereignis wieder geöffnet');
        return redirect(action('IncidentController@show', $incident->id));
    }

    /**
     * Attendance list as PDF (digital Einsatzzettel).
     */
    public function pdf($id)
    {
        $incident = $this->findViewable($id);

        if (!$incident->canManage(Auth::user())) {
            abort(402, "Nope.");
        }

        $attendances = $incident->attendances()->with(['user', 'station', 'recorder'])->get()
            ->sortBy(function ($attendance) {
                return mb_strtolower((string) $attendance->stationName() . '|' . $attendance->displayName());
            })->values();
        $client = $incident->client;
        $finalizer = $incident->finalized_by ? User::find($incident->finalized_by) : null;

        $pdf = PDF::loadView('incident.pdf', compact('incident', 'attendances', 'client', 'finalizer'))->setPaper('a4');

        return $pdf->download('Einsatzzettel' . $incident->start->format('Ymd') . '_' . $incident->id . '.pdf');
    }
}
