<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentAttendanceRequest;
use App\Incident;
use App\Incident_attendance;
use App\Station;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class IncidentAttendanceController extends Controller
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

    private function findViewableIncident($id)
    {
        $incident = Incident::findOrFail($id);

        if (!$incident->isViewableBy(Auth::user())) {
            abort(402, "Nope.");
        }

        return $incident;
    }

    private function backToIncident(Incident $incident)
    {
        return redirect(action('IncidentController@show', $incident->id));
    }

    private function lockedResponse(Incident $incident)
    {
        Session::flash('errormessage', ' Das Ereignis ist abgeschlossen. Änderungen sind gesperrt.');
        return $this->backToIncident($incident);
    }

    /**
     * Station of the request, only if it belongs to the client of the incident.
     * Clients without stations have no station field, so null is returned.
     */
    private function stationIdFor(Incident $incident, $requested)
    {
        if (empty($requested)) {
            return null;
        }

        return Station::where(['id' => $requested, 'client_id' => $incident->client_id])->value('id');
    }

    /**
     * Report himself: "on the way" or "was there". Switching the status is possible while the incident is open.
     */
    public function report(StoreIncidentAttendanceRequest $request, $incidentId)
    {
        $incident = $this->findViewableIncident($incidentId);
        $user = Auth::user();

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $attendance = Incident_attendance::where(['incident_id' => $incident->id, 'user_id' => $user->id])->first();

        if ($attendance) {
            if ($attendance->status != $request->get('status')) {
                $attendance->status = $request->get('status');
                $attendance->reported_at = Carbon::now();
                // a changed self report has to be confirmed again
                if ($attendance->source == Incident_attendance::SOURCE_SELF) {
                    $attendance->confirmed_at = null;
                }
            }
            if ($request->has('station_id')) {
                $attendance->station_id = $this->stationIdFor($incident, $request->get('station_id'));
            }
            $attendance->save();
        } else {
            $stationId = $request->has('station_id')
                ? $this->stationIdFor($incident, $request->get('station_id'))
                : $this->stationIdFor($incident, $user->homeStationId($incident->client_id));

            Incident_attendance::create([
                'incident_id' => $incident->id,
                'user_id' => $user->id,
                'station_id' => $stationId,
                'status' => $request->get('status'),
                'reported_at' => Carbon::now(),
                'source' => Incident_attendance::SOURCE_SELF,
            ]);
        }

        Session::flash('successmessage', ' Gemeldet: ' . Incident_attendance::STATUSES[$request->get('status')]);
        return $this->backToIncident($incident);
    }

    /**
     * Take back the own self report.
     */
    public function withdraw($incidentId)
    {
        $incident = $this->findViewableIncident($incidentId);

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $attendance = Incident_attendance::where(['incident_id' => $incident->id, 'user_id' => Auth::user()->id])->first();

        if ($attendance && $attendance->source == Incident_attendance::SOURCE_SELF) {
            $attendance->delete();
            Session::flash('successmessage', ' Meldung zurückgenommen');
        } elseif ($attendance) {
            Session::flash('errormessage', ' Dieser Eintrag wurde für dich erfasst. Bitte wende dich an den Einsatzleiter.');
        }

        return $this->backToIncident($incident);
    }

    /**
     * Record a helper: member with account or guest by name.
     */
    public function store(StoreIncidentAttendanceRequest $request, $incidentId)
    {
        $incident = $this->findViewableIncident($incidentId);

        if (!$incident->canRecord(Auth::user())) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $data = [
            'incident_id' => $incident->id,
            'station_id' => $this->stationIdFor($incident, $request->get('station_id')),
            'status' => $request->get('status'),
            'reported_at' => Carbon::now(),
            'source' => Incident_attendance::SOURCE_RECORDED,
            'recorded_by' => Auth::user()->id,
            'confirmed_at' => Carbon::now(),
        ];

        if (!empty($request->get('user_id'))) {
            $member = User::find($request->get('user_id'));

            if (is_null($member) || !$member->isApprovedMemberOfClient($incident->client_id)) {
                Session::flash('errormessage', ' Das Mitglied gehört nicht zu dieser Gliederung.');
                return $this->backToIncident($incident);
            }

            if (Incident_attendance::where(['incident_id' => $incident->id, 'user_id' => $member->id])->exists()) {
                Session::flash('errormessage', ' ' . $member->first_name . ' ' . $member->name . ' ist bereits eingetragen.');
                return $this->backToIncident($incident);
            }

            $data['user_id'] = $member->id;
        } else {
            $data['guest_name'] = trim($request->get('guest_name'));
        }

        Incident_attendance::create($data);

        Session::flash('successmessage', ' Helfer eingetragen');
        return $this->backToIncident($incident);
    }

    /**
     * Change status and station of an attendance.
     */
    public function update(StoreIncidentAttendanceRequest $request, $incidentId, $attendanceId)
    {
        $incident = $this->findViewableIncident($incidentId);
        $attendance = Incident_attendance::where(['incident_id' => $incident->id, 'id' => $attendanceId])->firstOrFail();

        if (!$incident->canChangeAttendance(Auth::user(), $attendance)) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $attendance->status = $request->get('status');
        $attendance->station_id = $this->stationIdFor($incident, $request->get('station_id'));
        if ($attendance->isGuest() && trim((string) $request->get('guest_name')) !== '') {
            $attendance->guest_name = trim($request->get('guest_name'));
        }
        $attendance->save();

        Session::flash('successmessage', ' Eintrag gespeichert');
        return $this->backToIncident($incident);
    }

    /**
     * Remove an attendance.
     */
    public function destroy($incidentId, $attendanceId)
    {
        $incident = $this->findViewableIncident($incidentId);
        $attendance = Incident_attendance::where(['incident_id' => $incident->id, 'id' => $attendanceId])->firstOrFail();

        if (!$incident->canChangeAttendance(Auth::user(), $attendance)) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $attendance->delete();

        Session::flash('successmessage', ' Eintrag entfernt');
        return $this->backToIncident($incident);
    }

    /**
     * Resolve open points before finalizing:
     * confirm = confirm all unconfirmed self reports, present = turn everyone "on the way" into "was there".
     */
    public function resolve(Request $request, $incidentId)
    {
        $incident = $this->findViewableIncident($incidentId);

        if (!$incident->canManage(Auth::user())) {
            abort(402, "Nope.");
        }

        if ($incident->isFinalized()) {
            return $this->lockedResponse($incident);
        }

        $request->validate(['action' => ['required', Rule::in(['confirm', 'present'])]]);

        if ($request->get('action') == 'confirm') {
            $incident->attendances()->whereNull('confirmed_at')->update(['confirmed_at' => Carbon::now()]);
            Session::flash('successmessage', ' Alle Meldungen bestätigt');
        } else {
            $incident->attendances()->where('status', '!=', Incident_attendance::STATUS_PRESENT)
                ->update(['status' => Incident_attendance::STATUS_PRESENT]);
            Session::flash('successmessage', ' Alle Helfer auf Anfahrt als anwesend gesetzt');
        }

        return $this->backToIncident($incident);
    }
}
