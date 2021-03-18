@if(\Illuminate\Support\Facades\Route::current()->getName() == 'service.edit')
    {{ Form::model($service->toArray() + $positions->toArray(), ['action' => ['ServiceController@update', $service->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($service->toArray() + $positions->toArray(), ['action' => ['ServiceController@store', $service->id]]) }}
@endif

<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('date', 'Datum:') }}
                    {{ Form::text('date', old('date', !empty($service->date) ? $service->date->format('d m Y H:i') : ''), ['id' => 'date-start', 'class' => 'datepicker form-control', 'placeholder'=>'Datum auswählen...', 'required'=>"true"]) }}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('dateEnd', 'Datum Ende:') }}
                    {{ Form::text('dateEnd', old('dateEnd', !empty($service->dateEnd) ? $service->dateEnd->format('d m Y H:i') : ''), ['id' => 'date-end', 'class' => 'datepicker form-control', 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('hastoauthorize') ? 'has-error' : ''}}">
                {{Form::hidden('hastoauthorize',0)}}
                {{ Form::checkbox('hastoauthorize', 1, old('hastoauthorize') or $service->hastoauthorize != 0 ? true : false, ['class' => 'filled-in', 'id' => "hastoauthorize"]) }}
                {{ Form::label('hastoauthorize', 'Muss freigegeben werden') }}
                {!! $errors->first('hastoauthorize', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('location', 'Ort (Mit Koordinaten darstellbar auf Karte):') }}
                    {{ Form::text('location', old('location', $service->location), ['id' => 'location', 'class' => 'location form-control', 'placeholder'=>'Ort eingeben...']) }}
                    {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('comment') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('comment', 'Bemerkung:') }}
                    {{ Form::textarea('comment', old('comment'), ['placeholder' => "Bemerkung...", 'rows' => 2, 'class' => 'form-control no-resize']) }}
                    {!! $errors->first('comment', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-12 text-center">
            <div class="form-group">
                <button type="button" class="btn btn-danger waves-effect btn-success" id="add_qualification"><i class="material-icons">add</i><span> Position Hinzufügen</span> </button>
            </div>
        </div>
    </div>

    <div class="body table-responsive" style="padding-bottom: 100px; overflow: auto">
        <table class="table table-striped" id="tblPositions">
            <thead>
            <tr>
                <th>Qualifikation</th>
                <th>Person</th>
                <th>Kommentar</th>
                <th>Erforderlich</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($positions) && $positions instanceof \Illuminate\Database\Eloquent\Collection)
                @foreach($positions as $position)
                    <tr pos_id="{{$position->id}}" class="strikeout">
                        <td>
                            {{Form::hidden('position[]',$position->id)}}

                            <select class="bootstrap-select show-tick" data-live-search="true" name="qualification[]" data-size="5">
                                @foreach($qualifications as $qualification)
                                    <option value="{{$qualification->id}}" @if($position->qualification_id == $qualification->id) selected @endif>{{$qualification->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="btn-group bootstrap-select form-control show-tick" data-live-search="true" name="user[]" data-size="5">
                                <option value="null">-- Bitte wählen --</option>
                                @foreach($users as $key => $user)
                                    <option @if($user->qualifications->contains('id', $position->qualification_id))class="bg-green" @endif  value="{{$user->id}}" @if($position->user_id == $user->id) selected @endif>{{substr ($user->first_name, 0, 1)}}. {{$user->name}}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input class="form-control" placeholder="Kommentar..." type="text" value="{{$position->comment}}" name="position_comment[]" >
                        </td>
                        <td>
                            <select class="form-control bootstrap-select show-tick" name="position_required[]">
                                <option value="0">Optional</option>
                                <option value="1" {{$position->requiredposition ? "selected" : ""}}>Erforderlich</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger waves-effect btn-delete delete_position">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

</div>

<div class="row clearfix">
    <div class="col-sm-1 pull-right">
        <div class="form-line">
            {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
        </div>
    </div>
</div>

<div id="delete_position"></div>
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
            var today = new Date();
            today.setHours(0,0,0,0);

            $('#date-end').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de',
                minDate : today
            });

            $('#date-start').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de',
                minDate : today
            }).on('change', function(e, date)
            {
                <!-- date holds the old value :( -->
                date = $('#date-start').bootstrapMaterialDatePicker().val();

                var dateStart = moment(date, 'DD MM YYYY hh:mm');
                {{-- Take diff of defaultStart and defaultEnd as default time gap --}}
                var dateEnd = dateStart.clone();
                dateEnd.add({{\Carbon\Carbon::parse(\Illuminate\Support\Facades\Auth::user()->currentclient()->defaultServiceStart)->diffInHours(\Illuminate\Support\Facades\Auth::user()->currentclient()->defaultServiceEnd)}}, 'hours');

                $('#date-end').bootstrapMaterialDatePicker('setMinDate', dateStart);
                $('#date-end').val(dateEnd.format('DD MM YYYY HH:mm'));
            });

            @if(\Illuminate\Support\Facades\Route::current()->getName() != 'service.edit')
            $('#date-start').val("{{\Carbon\Carbon::today()->setDateTimeFrom(\Illuminate\Support\Facades\Auth::user()->currentclient()->defaultServiceStart)->format('d m Y H:i')}}");
                @if(!empty(\Illuminate\Support\Facades\Auth::user()->currentclient()->defaultServiceEnd))
                    $('#date-end').val("{{\Carbon\Carbon::today()->setDateTimeFrom(\Illuminate\Support\Facades\Auth::user()->currentclient()->defaultServiceEnd)->format('d m Y H:i')}}");
                @endif
            @endif

            $('#add_qualification').on("click", function () {
                var date = new Date;
                var prot = '<tr pos_id="-1" >';
                prot += '<td><select class="bootstrap-select show-tick" data-live-search="true" name="qualification[]">';
                prot += '@foreach($qualifications as $qualification)<option value="{{$qualification->id}}">{{$qualification->name}}</option>@endforeach';
                prot += ' </select> </td>';
                prot += ' <td> <select class="bootstrap-select show-tick" data-live-search="true" name="user[]">';
                prot += '<option value="null">-- Bitte wählen --</option> @foreach($users as $user) <option value="{{$user->id}}">{{$user->name}}</option> @endforeach';
                prot += '</select> </td>';
                prot += ' <td> <input class="form-control" placeholder="Kommentar..." type="text" value="" name="position_comment[]" > </td>';
                prot += '<td> <select class="bootstrap-select show-tick" name="position_required[]">\n' +
                    '<option value="0">Optional</option>\n' +
                    '<option value="1">Erforderlich</option>\n' +
                    '</select> </td>';
                prot += '<td><button type="button" class="btn btn-danger waves-effect btn-delete delete_position"><i class="material-icons">delete</i></button></td>';
                prot += '</tr>';

                $("#tblPositions tbody").append(prot);

                $('.bootstrap-select').selectpicker();
            });

            $('#tblPositions').on('click', '.delete_position', function() {
                $('#delete_position').append('<input type="hidden" name="delete_position[]" value="'+$(this).closest('tr').attr('pos_id')+'" />')
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection