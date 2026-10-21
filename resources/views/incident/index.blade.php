@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @if($canCreate)
                <a href="{{ action('IncidentController@create') }}" class="btn btn-success waves-effect m-b-15"><i class="material-icons">add</i> <span>Neues Ereignis</span></a>
            @endif
            @if($isAdmin)
                <a href="{{ action('StationController@index') }}" class="btn btn-default waves-effect m-b-15"><i class="material-icons">place</i> <span>Wachen verwalten</span></a>
            @endif
        </div>

        @if($stations->count() > 0)
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <form method="POST" action="{{ route('station.home') }}" class="form-inline">
                            @csrf
                            <label for="home_station_id">Meine Heimatwache (wird beim Melden vorausgewählt):</label>
                            <select name="station_id" id="home_station_id" class="form-control">
                                <option value="">- keine -</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}" @if($homeStationId == $station->id) selected @endif>{{ $station->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-default waves-effect">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-blue-grey"><h2>Offene Ereignisse</h2></div>
                <div class="body">
                    @if($openIncidents->count() == 0)
                        Aktuell keine offenen Ereignisse
                    @endif
                    @foreach($openIncidents as $incident)
                        @php $mine = $myAttendances->get($incident->id); @endphp
                        <div class="media">
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="{{ action('IncidentController@show', $incident->id) }}">{{ $incident->title }}</a>
                                    <span class="label label-info">{{ $incident->typeLabel() }}</span>
                                    @if($incident->isRunning())<span class="label label-danger">laufend</span>@endif
                                    @if($mine)
                                        <span class="label @if($mine->isPresent()) label-success @else label-warning @endif">Du: {{ $mine->statusLabel() }}</span>
                                    @endif
                                </h4>
                                <p>
                                    {{ $incident->start->isoFormat('ddd DD.MM.YY H:mm') }} Uhr
                                    @if($incident->location) &middot; {{ $incident->location }} @endif
                                    &middot; {{ $incident->present_count }} Helfer vor Ort
                                </p>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Abgeschlossene Ereignisse <small>die letzten 100</small></h2></div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Beginn</th>
                            <th>Art</th>
                            <th>Titel</th>
                            <th>Dauer</th>
                            <th>Helfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($finalizedIncidents as $incident)
                            <tr>
                                <td>{{ $incident->start->format('d.m.Y H:i') }}</td>
                                <td>{{ $incident->typeLabel() }}</td>
                                <td><a href="{{ action('IncidentController@show', $incident->id) }}">{{ $incident->title }}</a></td>
                                <td>@if(!is_null($incident->durationMinutes())){{ intdiv($incident->durationMinutes(), 60) }} h {{ $incident->durationMinutes() % 60 }} min @endif</td>
                                <td>{{ $incident->present_count }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
