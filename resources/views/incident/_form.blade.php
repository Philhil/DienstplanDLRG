@php
    $isEdit = $incident->exists;
    $action = $isEdit ? action('IncidentController@update', $incident->id) : action('IncidentController@store');
@endphp
<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row clearfix">
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('type') ? 'has-error' : ''}}">
                <label for="type">Art des Ereignisses *</label>
                <select name="type" id="type" class="form-control show-tick" required>
                    @foreach(\App\Incident::TYPES as $key => $label)
                        <option value="{{ $key }}" @if(old('type', $incident->type) == $key) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
                {!! $errors->first('type', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="title">Titel *</label>
                    <input type="text" name="title" id="title" class="form-control" maxlength="255" required placeholder="Titel..." value="{{ old('title', $incident->title) }}">
                </div>
                {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('start') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="date-start">Beginn *</label>
                    <input type="text" name="start" id="date-start" class="form-control" required placeholder="Datum auswählen..." value="{{ old('start', $incident->start ? $incident->start->format('d m Y H:i') : '') }}">
                </div>
                {!! $errors->first('start', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('end') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="date-end">Ende (Dauer ist für alle Helfer gleich)</label>
                    <input type="text" name="end" id="date-end" class="form-control" placeholder="Datum auswählen..." value="{{ old('end', $incident->end ? $incident->end->format('d m Y H:i') : '') }}">
                </div>
                {!! $errors->first('end', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('leader_user_id') ? 'has-error' : ''}}">
                <label for="leader_user_id">Einsatzleiter</label>
                <select name="leader_user_id" id="leader_user_id" class="form-control show-tick" data-live-search="true">
                    <option value="">- kein Einsatzleiter -</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" @if(old('leader_user_id', $incident->leader_user_id) == $member->id) selected @endif>{{ $member->first_name }} {{ $member->name }}</option>
                    @endforeach
                </select>
                {!! $errors->first('leader_user_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('keyword') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="keyword">Stichwort</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" maxlength="255" value="{{ old('keyword', $incident->keyword) }}">
                </div>
                {!! $errors->first('keyword', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="form-group {{ $errors->has('dispatch_number') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="dispatch_number">Leitstellen-Nr.</label>
                    <input type="text" name="dispatch_number" id="dispatch_number" class="form-control" maxlength="255" value="{{ old('dispatch_number', $incident->dispatch_number) }}">
                </div>
                {!! $errors->first('dispatch_number', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-12 col-md-8">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="location">Ort</label>
                    <input type="text" name="location" id="location" class="form-control" maxlength="1000" value="{{ old('location', $incident->location) }}">
                </div>
                {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-12 col-md-8">
            <div class="form-group {{ $errors->has('note') ? 'has-error' : ''}}">
                <div class="form-line">
                    <label for="note">Bemerkung</label>
                    <textarea name="note" id="note" rows="3" class="form-control no-resize" maxlength="5000">{{ old('note', $incident->note) }}</textarea>
                </div>
                {!! $errors->first('note', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-success waves-effect">Speichern</button>
            <a href="{{ $isEdit ? action('IncidentController@show', $incident->id) : action('IncidentController@index') }}" class="btn btn-default waves-effect">Abbrechen</a>
        </div>
    </div>
</form>

@section('post_body')
    <!-- Moment Plugin Js -->
    <script src="/plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <script>
        $( document ).ready(function() {
            $('#date-end').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de'
            });
            $('#date-start').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                weekStart: 1,
                lang : 'de'
            }).on('change', function(e, date)
            {
                $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
            });
        });
    </script>
@endsection
