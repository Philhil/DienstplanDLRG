<?php

namespace App\Http\Controllers;

use App\Incident;
use App\Incident_attendance;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Statistic of finalized incidents: number, duration and distribution, number of helpers.
 * The duration is start to end of the incident and the same for every helper of it,
 * helper hours = duration x helpers that were there.
 */
class IncidentStatisticController extends Controller
{
    const WEEKDAYS = [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];
    const DAYTIMES = ['Nacht (0-6 Uhr)', 'Vormittag (6-12 Uhr)', 'Nachmittag (12-18 Uhr)', 'Abend (18-24 Uhr)'];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $client = Auth::user()->currentclient();

            if (!$client->module_incident) {
                abort(402, "Module not activated.");
            }

            // admins or statistic editors (the latter need the statistic module)
            if (!Auth::user()->isAdmin() && !(Auth::user()->isStatisticEditor() && $client->module_statistic)) {
                abort(402, "Nope.");
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $client = Auth::user()->currentclient();

        $from = null;
        $to = null;
        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::createFromFormat('d.m.Y', $request->input('from'))->startOfDay();
            $to = Carbon::createFromFormat('d.m.Y', $request->input('to'))->endOfDay();
        }
        if (empty($from) || empty($to)) {
            $season = $client->Season();
            $from = $season['from']->copy()->startOfDay();
            $to = $season['to']->copy()->endOfDay();
        }

        $type = array_key_exists($request->input('type'), Incident::TYPES) ? $request->input('type') : null;
        $stationId = $request->filled('station_id') ? (int) $request->input('station_id') : null;

        $stations = $client->Stations()->get();
        $stats = $this->calculate($client->id, $from, $to, $type, $stationId, $stations);

        if ($request->input('export') == 'incidents') {
            return $this->exportIncidents($stats['incidents']);
        }
        if ($request->input('export') == 'attendances') {
            return $this->exportAttendances($stats['incidents'], $stationId);
        }

        $types = Incident::TYPES;

        return view('incident.statistic', compact('stats', 'from', 'to', 'type', 'stationId', 'stations', 'types'));
    }

    /**
     * Calculates everything from the loaded rows (database independent).
     */
    public function calculate($clientId, Carbon $from, Carbon $to, $type = null, $stationId = null, $stations = null)
    {
        $query = Incident::where('client_id', $clientId)->whereNotNull('finalized_at')
            ->whereNotNull('end')->whereBetween('start', [$from, $to])->orderBy('start')
            ->with(['attendances.user']);
        if ($type) {
            $query->where('type', $type);
        }

        $incidents = $query->get();

        // station filter: only attendances of this station count, incidents without any are left out
        if ($stationId) {
            $incidents = $incidents->filter(function ($incident) use ($stationId) {
                return $incident->attendances->where('station_id', $stationId)->count() > 0;
            })->values();
        }

        $stationNames = $stations ? $stations->pluck('name', 'id') : collect();

        $totalMinutes = 0;
        $helperMinutes = 0;
        $presentTotal = 0;
        $guestTotal = 0;
        $distinctUsers = [];
        $byType = array_fill_keys(array_keys(Incident::TYPES), 0);
        $byMonth = [];
        $byWeekday = array_fill_keys(array_keys(self::WEEKDAYS), 0);
        $byDaytime = array_fill_keys(self::DAYTIMES, 0);
        $byStation = [];
        $helpers = [];

        foreach ($incidents as $incident) {
            $present = $incident->attendances->filter(function ($attendance) use ($stationId) {
                return $attendance->isPresent() && (!$stationId || $attendance->station_id == $stationId);
            });
            $minutes = $incident->durationMinutes();

            $totalMinutes += $minutes;
            $helperMinutes += $minutes * $present->count();
            $presentTotal += $present->count();
            $byType[$incident->type] = ($byType[$incident->type] ?? 0) + 1;

            $monthKey = $incident->start->format('Y-m');
            $byMonth[$monthKey] = ($byMonth[$monthKey] ?? 0) + 1;
            $byWeekday[$incident->start->isoWeekday()]++;
            $byDaytime[self::DAYTIMES[intdiv($incident->start->hour, 6)]]++;

            foreach ($present as $attendance) {
                if ($attendance->isGuest()) {
                    $guestTotal++;
                } else {
                    $distinctUsers[$attendance->user_id] = true;
                    if (!isset($helpers[$attendance->user_id])) {
                        $helpers[$attendance->user_id] = [
                            'name' => $attendance->displayName(),
                            'incidents' => 0,
                            'minutes' => 0,
                        ];
                    }
                    $helpers[$attendance->user_id]['incidents']++;
                    $helpers[$attendance->user_id]['minutes'] += $minutes;
                }

                $label = is_null($attendance->station_id)
                    ? 'Ohne Wache'
                    : ($stationNames[$attendance->station_id] ?? 'Gelöschte Wache (#' . $attendance->station_id . ')');
                $byStation[$label] = ($byStation[$label] ?? 0) + 1;
            }
        }

        ksort($byMonth);
        arsort($byStation);
        uasort($helpers, function ($a, $b) {
            return [$b['incidents'], $b['minutes']] <=> [$a['incidents'], $a['minutes']];
        });

        $count = $incidents->count();

        return [
            'incidents' => $incidents,
            'count' => $count,
            'byType' => $byType,
            'totalMinutes' => $totalMinutes,
            'avgMinutes' => $count ? (int) round($totalMinutes / $count) : 0,
            'presentTotal' => $presentTotal,
            'avgPresent' => $count ? round($presentTotal / $count, 1) : 0,
            'distinctUsers' => count($distinctUsers),
            'guestTotal' => $guestTotal,
            'helperMinutes' => $helperMinutes,
            'byMonth' => $byMonth,
            'byWeekday' => $byWeekday,
            'byDaytime' => $byDaytime,
            'byStation' => $byStation,
            'topHelpers' => array_slice($helpers, 0, 20),
        ];
    }

    private function csvResponse($filename, array $rows)
    {
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM for Excel
            foreach ($rows as $row) {
                fputcsv($out, $row, ';');
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function exportIncidents($incidents)
    {
        $rows = [['Datum', 'Typ', 'Titel', 'Stichwort', 'Ort', 'Leitstellen-Nr.', 'Beginn', 'Ende', 'Dauer (Minuten)', 'Helfer (war da)', 'davon ohne Account']];
        foreach ($incidents as $incident) {
            $present = $incident->attendances->filter->isPresent();
            $rows[] = [
                $incident->start->format('d.m.Y'), $incident->typeLabel(), $incident->title, $incident->keyword,
                $incident->location, $incident->dispatch_number, $incident->start->format('d.m.Y H:i'),
                $incident->end->format('d.m.Y H:i'), $incident->durationMinutes(), $present->count(),
                $present->filter->isGuest()->count(),
            ];
        }

        return $this->csvResponse('Einsaetze_' . Carbon::now()->format('Ymd') . '.csv', $rows);
    }

    private function exportAttendances($incidents, $stationId)
    {
        $rows = [['Datum', 'Typ', 'Titel', 'Helfer', 'Account', 'Wache', 'Status', 'Erfasst', 'Bestätigt', 'Dauer (Minuten)']];
        foreach ($incidents as $incident) {
            foreach ($incident->attendances as $attendance) {
                if ($stationId && $attendance->station_id != $stationId) {
                    continue;
                }
                $rows[] = [
                    $incident->start->format('d.m.Y'), $incident->typeLabel(), $incident->title, $attendance->displayName(),
                    $attendance->isGuest() ? 'nein' : 'ja', $attendance->stationName(), $attendance->statusLabel(),
                    $attendance->source == Incident_attendance::SOURCE_SELF ? 'selbst' : 'nachgetragen',
                    $attendance->isConfirmed() ? 'ja' : 'nein', $incident->durationMinutes(),
                ];
            }
        }

        return $this->csvResponse('Einsatz_Anwesenheit_' . Carbon::now()->format('Ymd') . '.csv', $rows);
    }
}
