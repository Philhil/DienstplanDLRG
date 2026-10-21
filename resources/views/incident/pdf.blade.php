<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 15mm 12mm; }
        body { font-family: "DejaVu Sans", Verdana, sans-serif; font-size: 10pt; }
        h1 { font-size: 16pt; margin: 0 0 4pt 0; }
        h2 { font-size: 11pt; margin: 14pt 0 4pt 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #444; padding: 4pt 6pt; text-align: left; vertical-align: top; }
        th { background: #e6e6e6; }
        table.info td { border: none; padding: 1pt 6pt 1pt 0; }
        .draft { color: #b00; font-weight: bold; }
        .small { font-size: 8pt; color: #444; }
    </style>
</head>
<body>
@php $durationMinutes = $incident->durationMinutes(); @endphp
<h1>Einsatzzettel: {{ $incident->title }}</h1>
<div>{{ $client->name }} &middot; {{ $incident->typeLabel() }}
    @if(!$incident->isFinalized()) <span class="draft">ENTWURF - noch nicht abgeschlossen</span> @endif
</div>

<table class="info">
    <tr><td><b>Beginn</b></td><td>{{ $incident->start->format('d.m.Y H:i') }} Uhr</td></tr>
    <tr><td><b>Ende</b></td><td>@if($incident->end){{ $incident->end->format('d.m.Y H:i') }} Uhr @else offen @endif</td></tr>
    @if(!is_null($durationMinutes))
        <tr><td><b>Dauer</b></td><td>{{ intdiv($durationMinutes, 60) }} h {{ $durationMinutes % 60 }} min (für alle Helfer gleich)</td></tr>
    @endif
    @if($incident->location)<tr><td><b>Ort</b></td><td>{{ $incident->location }}</td></tr>@endif
    @if($incident->keyword)<tr><td><b>Stichwort</b></td><td>{{ $incident->keyword }}</td></tr>@endif
    @if($incident->dispatch_number)<tr><td><b>Leitstellen-Nr.</b></td><td>{{ $incident->dispatch_number }}</td></tr>@endif
    @if($incident->leader_user_id)
        <tr><td><b>Einsatzleiter</b></td><td>@if($incident->leader){{ $incident->leader->first_name }} {{ $incident->leader->name }}@else Gelöschter Benutzer (#{{ $incident->leader_user_id }})@endif</td></tr>
    @endif
    @if($incident->note)<tr><td><b>Bemerkung</b></td><td>{{ $incident->note }}</td></tr>@endif
</table>

<h2>Anwesenheit: {{ $attendances->where('status', 'present')->count() }} Helfer vor Ort</h2>
@php $grouped = $attendances->groupBy(function ($a) { return (string) $a->stationName(); }); @endphp
@foreach($grouped as $stationName => $stationAttendances)
    @if($stationName !== '' || $grouped->count() > 1)
        <h2>{{ $stationName !== '' ? $stationName : 'Ohne Wache' }} ({{ $stationAttendances->count() }})</h2>
    @endif
    <table>
        <thead><tr><th>Name</th><th>Account</th><th>Status</th><th>Erfasst</th><th>Bestätigt</th></tr></thead>
        <tbody>
        @foreach($stationAttendances as $attendance)
            <tr>
                <td>{{ $attendance->displayName() }}</td>
                <td>{{ $attendance->isGuest() ? 'ohne Account' : 'ja' }}</td>
                <td>{{ $attendance->statusLabel() }}</td>
                <td>
                    @if($attendance->source == \App\Incident_attendance::SOURCE_RECORDED)
                        nachgetragen von @if($attendance->recorder){{ $attendance->recorder->first_name }} {{ $attendance->recorder->name }}@else Benutzer #{{ $attendance->recorded_by }}@endif
                    @else
                        selbst gemeldet
                    @endif
                </td>
                <td>{{ $attendance->isConfirmed() ? 'ja' : 'nein' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach
@if($attendances->count() == 0)
    <p>Keine Helfer erfasst.</p>
@endif

<p class="small">
    @if($incident->isFinalized())
        Abgeschlossen am {{ $incident->finalized_at->format('d.m.Y H:i') }} Uhr
        @if($finalizer) von {{ $finalizer->first_name }} {{ $finalizer->name }} @elseif($incident->finalized_by) von Benutzer #{{ $incident->finalized_by }} @endif.
    @endif
    Erstellt am {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }} Uhr.
</p>
</body>
</html>
