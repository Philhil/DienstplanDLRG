@extends('_layouts.application')

@section('head')
@endsection

@php
    $hm = function ($minutes) { return intdiv($minutes, 60) . ' h ' . ($minutes % 60) . ' min'; };
    $filterQuery = ['from' => $from->format('d.m.Y'), 'to' => $to->format('d.m.Y'), 'type' => $type, 'station_id' => $stationId];
@endphp

@section('content')
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-blue-grey"><h2>Einsatz Statistik <small>nur abgeschlossene Ereignisse</small></h2></div>
                <div class="body">
                    <form method="GET" action="{{ action('IncidentStatisticController@index') }}" class="form-inline">
                        <label for="from">Zeitraum:</label>
                        <input type="text" name="from" id="from" class="form-control" value="{{ $from->format('d.m.Y') }}">
                        <label for="to">bis</label>
                        <input type="text" name="to" id="to" class="form-control" value="{{ $to->format('d.m.Y') }}">
                        <select name="type" class="form-control">
                            <option value="">Alle Arten</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" @if($type == $key) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($stations->count() > 0)
                            <select name="station_id" class="form-control">
                                <option value="">Alle Wachen</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}" @if($stationId == $station->id) selected @endif>{{ $station->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <button type="submit" class="btn btn-success waves-effect">Anzeigen</button>
                        <a href="{{ action('IncidentStatisticController@index', $filterQuery + ['export' => 'incidents']) }}" class="btn btn-default waves-effect">CSV Ereignisse</a>
                        <a href="{{ action('IncidentStatisticController@index', $filterQuery + ['export' => 'attendances']) }}" class="btn btn-default waves-effect">CSV Anwesenheit</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="card"><div class="body">
                <h3>{{ $stats['count'] }}</h3>Ereignisse
                <br><small>@foreach($types as $key => $label){{ $stats['byType'][$key] }} {{ $label }}@if(!$loop->last) &middot; @endif @endforeach</small>
            </div></div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="card"><div class="body">
                <h3>{{ $hm($stats['totalMinutes']) }}</h3>Gesamtdauer
                <br><small>&oslash; {{ $hm($stats['avgMinutes']) }} je Ereignis</small>
            </div></div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="card"><div class="body">
                <h3>{{ $stats['presentTotal'] }}</h3>Meldungen "war da"
                <br><small>&oslash; {{ number_format($stats['avgPresent'], 1, ',', '.') }} je Ereignis</small>
            </div></div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="card"><div class="body">
                <h3>{{ $stats['distinctUsers'] }}</h3>Verschiedene Helfer mit Account
                <br><small>+ {{ $stats['guestTotal'] }} Meldungen ohne Account</small>
            </div></div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card"><div class="body">
                <h3>{{ $hm($stats['helperMinutes']) }}</h3>Einsatzstunden aller Helfer
                <br><small>Dauer des Ereignisses x Helfer, die vor Ort waren. Die Dauer ist für alle Helfer eines Ereignisses gleich.</small>
            </div></div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Ereignisse pro Monat</h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        @foreach($stats['byMonth'] as $month => $count)
                            <tr><td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->isoFormat('MMMM YYYY') }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                        @if(count($stats['byMonth']) == 0)<tr><td>Keine Daten</td></tr>@endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Helfer nach Wache</h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        @foreach($stats['byStation'] as $name => $count)
                            <tr><td>{{ $name }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                        @if(count($stats['byStation']) == 0)<tr><td>Keine Daten</td></tr>@endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Ereignisse nach Wochentag und Tageszeit</h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        @foreach($stats['byWeekday'] as $day => $count)
                            <tr><td>{{ \App\Http\Controllers\IncidentStatisticController::WEEKDAYS[$day] }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                        @foreach($stats['byDaytime'] as $label => $count)
                            <tr><td>{{ $label }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Einsatzbeteiligung pro Helfer <small>nur Mitglieder mit Account, die 20 häufigsten</small></h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Name</th><th>Ereignisse</th><th>Stunden</th></tr></thead>
                        @foreach($stats['topHelpers'] as $helper)
                            <tr><td>{{ $helper['name'] }}</td><td>{{ $helper['incidents'] }}</td><td>{{ $hm($helper['minutes']) }}</td></tr>
                        @endforeach
                        @if(count($stats['topHelpers']) == 0)<tr><td colspan="3">Keine Daten</td></tr>@endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Ereignisse</h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Beginn</th><th>Art</th><th>Titel</th><th>Dauer</th><th>Helfer</th></tr></thead>
                        @foreach($stats['incidents'] as $incident)
                            <tr>
                                <td>{{ $incident->start->format('d.m.Y H:i') }}</td>
                                <td>{{ $incident->typeLabel() }}</td>
                                <td><a href="{{ action('IncidentController@show', $incident->id) }}">{{ $incident->title }}</a></td>
                                <td>{{ $hm($incident->durationMinutes()) }}</td>
                                <td>{{ $incident->attendances->filter->isPresent()->count() }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
