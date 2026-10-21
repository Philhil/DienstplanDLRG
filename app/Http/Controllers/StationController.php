<?php

namespace App\Http\Controllers;

use App\Client_user;
use App\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StationController extends Controller
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

    private function ownStation($id)
    {
        return Station::where(['id' => $id, 'client_id' => Auth::user()->currentclient_id])->firstOrFail();
    }

    /**
     * List and manage the stations (admin only).
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $stations = Auth::user()->currentclient()->Stations()->get();

        return view('station.index', compact('stations'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $request->validate(['name' => 'required|max:255']);

        Station::create(['client_id' => Auth::user()->currentclient_id, 'name' => trim($request->get('name'))]);

        Session::flash('successmessage', ' Wache angelegt');
        return redirect(action('StationController@index'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $request->validate(['name' => 'required|max:255']);

        $station = $this->ownStation($id);
        $station->name = trim($request->get('name'));
        $station->save();

        Session::flash('successmessage', ' Wache gespeichert');
        return redirect(action('StationController@index'));
    }

    /**
     * Delete a station. Attendances keep the station id (no cascade), so the history stays complete.
     */
    public function destroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $this->ownStation($id)->delete();

        Session::flash('successmessage', ' Wache gelöscht');
        return redirect(action('StationController@index'));
    }

    /**
     * Every member sets the own home station (preselected when reporting). Empty = none.
     */
    public function setHome(Request $request)
    {
        $request->validate(['station_id' => 'nullable|integer']);

        $stationId = null;
        if (!empty($request->get('station_id'))) {
            $stationId = Station::where(['id' => $request->get('station_id'), 'client_id' => Auth::user()->currentclient_id])->value('id');
        }

        Client_user::where(['client_id' => Auth::user()->currentclient_id, 'user_id' => Auth::user()->id])
            ->update(['home_station_id' => $stationId]);

        Session::flash('successmessage', ' Heimatwache gespeichert');
        return redirect(action('IncidentController@index'));
    }
}
