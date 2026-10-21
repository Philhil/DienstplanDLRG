@extends('_layouts.application')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
@endsection

@section('content')
    @php
        $hasStations = $stations->count() > 0;
        $grouped = $attendances->groupBy(function ($attendance) { return (string) $attendance->stationName(); });
        $mineStation = $myAttendance ? $myAttendance->station_id : ($stations->contains('id', $homeStationId) ? $homeStationId : null);
        $durationMinutes = $incident->durationMinutes();
    @endphp
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-blue-grey">
                    <h2>
                        {{ $incident->title }}
                        <small>{{ $incident->typeLabel() }} @if($incident->isFinalized()) &middot; abgeschlossen @endif</small>
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><b>Beginn:</b> {{ $incident->start->isoFormat('ddd DD.MM.YY H:mm') }} Uhr</p>
                            <p><b>Ende:</b> @if($incident->end) {{ $incident->end->isoFormat('ddd DD.MM.YY H:mm') }} Uhr @else offen @endif</p>
                            @if(!is_null($durationMinutes))
                                <p><b>Dauer:</b> {{ intdiv($durationMinutes, 60) }} h {{ $durationMinutes % 60 }} min (für alle Helfer gleich)</p>
                            @endif
                            @if($incident->location)<p><b>Ort:</b> {{ $incident->location }}</p>@endif
                        </div>
                        <div class="col-md-6">
                            @if($incident->keyword)<p><b>Stichwort:</b> {{ $incident->keyword }}</p>@endif
                            @if($incident->dispatch_number)<p><b>Leitstellen-Nr.:</b> {{ $incident->dispatch_number }}</p>@endif
                            @if($incident->leader_user_id)
                                <p><b>Einsatzleiter:</b>
                                    @if($incident->leader) {{ $incident->leader->first_name }} {{ $incident->leader->name }} @else Gelöschter Benutzer (#{{ $incident->leader_user_id }}) @endif
                                </p>
                            @endif
                            @if($incident->note)<p><b>Bemerkung:</b> {{ $incident->note }}</p>@endif
                        </div>
                    </div>

                    @if($canManage)
                        <a href="{{ action('IncidentController@pdf', $incident->id) }}" class="btn btn-default waves-effect"><i class="material-icons">picture_as_pdf</i> PDF Einsatzzettel</a>
                        @if($incident->isOpen())
                            <a href="{{ action('IncidentController@edit', $incident->id) }}" class="btn btn-warning waves-effect"><i class="material-icons">mode_edit</i> Bearbeiten</a>
                        @endif
                    @endif
                    @if($isAdmin && $incident->isFinalized())
                        <form method="POST" action="{{ action('IncidentController@reopen', $incident->id) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-default waves-effect"><i class="material-icons">lock_open</i> Wieder öffnen</button>
                        </form>
                    @endif
                    @if($isAdmin)
                        <form method="POST" action="{{ action('IncidentController@destroy', $incident->id) }}" style="display:inline" onsubmit="return confirm('Ereignis samt Anwesenheitsliste endgültig löschen? Anwesenheitslisten werden normalerweise aufbewahrt.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger waves-effect"><i class="material-icons">delete</i> Löschen</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if($canSelfReport)
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header"><h2>Meine Meldung</h2></div>
                    <div class="body">
                        @if($myAttendance)
                            <p>Gemeldet als: <span class="label @if($myAttendance->isPresent()) label-success @else label-warning @endif">{{ $myAttendance->statusLabel() }}</span>
                                @if($myAttendance->stationName()) &middot; {{ $myAttendance->stationName() }} @endif</p>
                        @endif
                        <form method="POST" action="{{ route('incident.report', $incident->id) }}" class="form-inline">
                            @csrf
                            @if($hasStations)
                                <select name="station_id" class="form-control">
                                    <option value="">- keine Wache -</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}" @if($mineStation == $station->id) selected @endif>{{ $station->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <button type="submit" name="status" value="{{ \App\Incident_attendance::STATUS_ON_THE_WAY }}" class="btn btn-warning waves-effect btn-lg">Ich bin auf Anfahrt</button>
                            <button type="submit" name="status" value="{{ \App\Incident_attendance::STATUS_PRESENT }}" class="btn btn-success waves-effect btn-lg">Ich war da</button>
                        </form>
                        @if($myAttendance && $myAttendance->source == \App\Incident_attendance::SOURCE_SELF)
                            <form method="POST" action="{{ route('incident.withdraw', $incident->id) }}" class="m-t-15">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-default waves-effect">Meldung zurücknehmen</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header"><h2>Anwesenheit <small>{{ $attendances->where('status', 'present')->count() }} Helfer vor Ort, {{ $attendances->count() }} gemeldet</small></h2></div>
                <div class="body table-responsive">
                    @if($attendances->count() == 0)
                        Noch niemand gemeldet
                    @endif
                    @foreach($grouped as $stationName => $stationAttendances)
                        @if($hasStations || $stationName !== '')
                            <h5>{{ $stationName !== '' ? $stationName : 'Ohne Wache' }} <small>{{ $stationAttendances->count() }}</small></h5>
                        @endif
                        <table class="table table-striped">
                            <tbody>
                            @foreach($stationAttendances as $attendance)
                                @php $canChange = $incident->isOpen() && $incident->canChangeAttendance(Auth::user(), $attendance); @endphp
                                <tr>
                                    <td>
                                        {{ $attendance->displayName() }}
                                        @if($attendance->isGuest()) <span class="label label-default">ohne Account</span> @endif
                                        @if($attendance->user_id == Auth::user()->id) <b>(Du)</b> @endif
                                    </td>
                                    <td>
                                        <span class="label @if($attendance->isPresent()) label-success @else label-warning @endif">{{ $attendance->statusLabel() }}</span>
                                        @if(!$attendance->isConfirmed()) <span class="label label-default">unbestätigt</span> @endif
                                    </td>
                                    <td>
                                        @if($attendance->source == \App\Incident_attendance::SOURCE_RECORDED)
                                            <small>erfasst von @if($attendance->recorder) {{ $attendance->recorder->first_name }} {{ $attendance->recorder->name }} @else Benutzer #{{ $attendance->recorded_by }} @endif</small>
                                        @else
                                            <small>selbst gemeldet</small>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($canChange)
                                            <form method="POST" action="{{ route('incident.attendance.update', [$incident->id, $attendance->id]) }}" class="form-inline" style="display:inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-control">
                                                    @foreach(\App\Incident_attendance::STATUSES as $key => $label)
                                                        <option value="{{ $key }}" @if($attendance->status == $key) selected @endif>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @if($hasStations)
                                                    <select name="station_id" class="form-control">
                                                        <option value="">- keine Wache -</option>
                                                        @foreach($stations as $station)
                                                            <option value="{{ $station->id }}" @if($attendance->station_id == $station->id) selected @endif>{{ $station->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <button type="submit" class="btn btn-success waves-effect"><i class="material-icons">save</i></button>
                                            </form>
                                            <form method="POST" action="{{ route('incident.attendance.destroy', [$incident->id, $attendance->id]) }}" style="display:inline" onsubmit="return confirm('Eintrag entfernen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger waves-effect"><i class="material-icons">delete</i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>

        @if($canRecord && $incident->isOpen())
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header"><h2>Helfer hinzufügen <small>Mitglied oder Helfer ohne Account (Name)</small></h2></div>
                    <div class="body">
                        <form method="POST" action="{{ route('incident.attendance.store', $incident->id) }}">
                            @csrf
                            <div class="row clearfix">
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
                                        <label for="user_id">Mitglied</label>
                                        <select name="user_id" id="user_id" class="form-control show-tick" data-live-search="true">
                                            <option value="">- Mitglied wählen -</option>
                                            @foreach($members as $member)
                                                <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('guest_name') ? 'has-error' : ''}}">
                                        <div class="form-line">
                                            <label for="guest_name">oder Name ohne Account</label>
                                            <input type="text" name="guest_name" id="guest_name" class="form-control" maxlength="255" placeholder="Name...">
                                        </div>
                                        {!! $errors->first('guest_name', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-4">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach(\App\Incident_attendance::STATUSES as $key => $label)
                                            <option value="{{ $key }}" @if($key == \App\Incident_attendance::STATUS_PRESENT) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($hasStations)
                                    <div class="col-sm-4">
                                        <label for="station_id">Wache</label>
                                        <select name="station_id" id="station_id" class="form-control">
                                            <option value="">- keine Wache -</option>
                                            @foreach($stations as $station)
                                                <option value="{{ $station->id }}">{{ $station->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success waves-effect m-t-15"><i class="material-icons">add</i> Hinzufügen</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($canManage && $incident->isOpen())
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header"><h2>Ereignis abschließen</h2></div>
                    <div class="body">
                        <p>{{ $attendances->where('status', 'present')->count() }} Helfer vor Ort,
                            {{ $attendances->filter(function ($a) { return $a->isGuest(); })->count() }} davon ohne Account.</p>
                        @if($unconfirmedCount > 0)
                            <form method="POST" action="{{ route('incident.attendance.resolve', $incident->id) }}" class="m-b-15">
                                @csrf
                                <input type="hidden" name="action" value="confirm">
                                {{ $unconfirmedCount }} unbestätigte Meldungen
                                <button type="submit" class="btn btn-default waves-effect">Alle bestätigen</button>
                            </form>
                        @endif
                        @if($onTheWayCount > 0)
                            <form method="POST" action="{{ route('incident.attendance.resolve', $incident->id) }}" class="m-b-15">
                                @csrf
                                <input type="hidden" name="action" value="present">
                                {{ $onTheWayCount }} Helfer noch auf Anfahrt
                                <button type="submit" class="btn btn-default waves-effect">Alle auf "War da" setzen</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('incident.finalize', $incident->id) }}" class="form-inline">
                            @csrf
                            <label for="date-end">Ende:</label>
                            <input type="text" name="end" id="date-end" class="form-control" placeholder="Datum auswählen..." value="{{ old('end', $incident->end ? $incident->end->format('d m Y H:i') : \Carbon\Carbon::now()->format('d m Y H:i')) }}">
                            <button type="submit" class="btn btn-danger waves-effect" @if($unconfirmedCount > 0 || $onTheWayCount > 0) disabled @endif>Abschließen und sperren</button>
                        </form>
                        @if($unconfirmedCount > 0 || $onTheWayCount > 0)
                            <p class="m-t-15"><small>Abschließen ist erst möglich, wenn alles aufgelöst ist.</small></p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('post_body')
    <!-- Moment Plugin Js -->
    <script src="/plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <script>
        $( document ).ready(function() {
            $('#date-end').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                weekStart: 1,
                lang : 'de'
            });
        });
    </script>
@endsection
