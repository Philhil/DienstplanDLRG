@if(\Illuminate\Support\Facades\Route::current()->getName() == 'calendar.edit')
    {{ Form::model($calendar->toArray(), ['action' => ['CalendarController@update', $calendar->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($calendar->toArray(), ['action' => ['CalendarController@store', $calendar->id]]) }}
@endif

<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{$errors->has('date') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{Form::label('date', 'Von:')}}
                    {{Form::text('date', old('date', !empty($calendar->date) ? $calendar->date
                        ->format('d m Y H:i') : ''), ['id' => 'date-start', 'class' => 'date-start form-control', 'placeholder'=>'Datum auswählen...', 'required'=>"true"])}}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : '' }}">
                <div class="form-line">
                    {{ Form::label('dateEnd', 'Bis:') }}
                    {{ Form::text('dateEnd', old('dateEnd', !empty($calendar->dateEnd) ? $calendar->dateEnd->format('d m Y H:i') : ''), ['id' => 'date-end', 'class' => 'date-end form-control', 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('dateEnd', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('title', 'Titel:') }}
                    {{ Form::text('title', old('title'), ['placeholder' => "Titel...", 'class' => 'form-control no-resize', 'required'=>"true"]) }}
                    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <!-- TinyMCE -->
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('verantwortlicher') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('verantwortlicher', 'Verantwortlicher:') }}
                    {{ Form::text('verantwortlicher', old('verantwortlicher'), ['placeholder' => "Verantwortlicher...", 'class' => 'form-control no-resize', 'required'=>"true"]) }}
                    {!! $errors->first('verantwortlicher', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <!-- #END# TinyMCE -->

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('location', 'Ort (Mit Koordinaten darstellbar auf Karte):') }}
                    {{ Form::text('location', old('location', $calendar->location), ['id' => 'location', 'class' => 'location form-control', 'placeholder'=>'Ort eingeben...']) }}
                    {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('sendbydatetime') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('sendbydatetime', 'Automatisch E-Mail versenden am (Leer = deaktiviert):') }}
                    {{ Form::text('sendbydatetime', old('sendbydatetime', !empty($calendar->sendbydatetime) ? $calendar->sendbydatetime->format('d m Y H:i') : ''), ['id' => 'sendbydatetime', 'class' => 'sendbydatetime form-control', 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('sendbydatetime', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
</div>

<div class="row clearfix">
    <div class="col-sm-1 pull-right">
        <div class="form-line">
            {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
        </div>
    </div>
</div>

{{ Form::close() }}


@section('post_body')
    <!-- Moment Plugin Js -->
    <script src="/plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <!-- Select Plugin Js -->
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <script>
        $( document ).ready(function() {

            $('#date-end').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de',
                minDate : new Date()
            });
            $('#sendbydatetime').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de',
                minDate : new Date()
            });
            $('#date-start').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de',
                minDate : new Date()
            }).on('change', function(e, date)
            {
                $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
                $('#date-end').bootstrapMaterialDatePicker('setDate', moment(date).clone().add(3, 'hours'));

                $('#sendbydatetime').bootstrapMaterialDatePicker('setMaxDate', date);
            });

        });
    </script>
@endsection