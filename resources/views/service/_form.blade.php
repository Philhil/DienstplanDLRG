@if(\Illuminate\Support\Facades\Route::current()->getName() == 'service.edit')
    {{ html()->modelForm($service->toArray() + $positions->toArray(), 'PUT', action('ServiceController@update', $service->id))->open() }}
@else
    {{ html()->modelForm($service->toArray() + $positions->toArray(), 'POST', action('ServiceController@store', $service->id))->open() }}
@endif

<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Datum:', 'date') }}
                    {{ html()->text('date', old('date', !empty($service->date) ? $service->date->format('d m Y H:i') : ''))->class('datepicker form-control')->id('date-start')->required()->placeholder('Datum auswählen...') }}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Datum Ende:', 'dateEnd') }}
                    {{ html()->text('dateEnd', old('dateEnd', !empty($service->dateEnd) ? $service->dateEnd->format('d m Y H:i') : ''))->class('datepicker form-control')->id('date-end')->placeholder('Datum auswählen...') }}
                    {!! $errors->first('dateEnd', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('hastoauthorize') ? 'has-error' : ''}}">
                {{ html()->hidden('hastoauthorize', 0) }}
                {{ html()->checkbox('hastoauthorize', old('hastoauthorize') or $service->hastoauthorize != 0 ? true : false, 1)->class('filled-in')->id("hastoauthorize") }}
                {{ html()->label('Muss freigegeben werden', 'hastoauthorize') }}
                {!! $errors->first('hastoauthorize', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('comment') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Bemerkung:', 'comment') }}
                    {{ html()->textarea('comment')->class('form-control no-resize')->placeholder("Bemerkung...")->rows(2) }}
                    {!! $errors->first('comment', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Ort (Mit Koordinaten darstellbar auf Karte):', 'location') }}
                    {{ html()->text('location')->class('location form-control')->id('location')->placeholder('Ort eingeben...') }}
                    {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
    @if(\Illuminate\Support\Facades\Route::current()->getName() != 'service.edit')
    <div class="row clearfix">
        <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
            <div class="panel-group full-body" id="accordion_19" role="tablist" aria-multiselectable="true">
                <div class="panel panel-col-grey">
                    <div class="panel-heading" role="tab" id="heading_repeat">
                        <h6 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" href="#collapseTwo_19" aria-expanded="false" aria-controls="collapseTwo_19">
                                <i class="material-icons">repeat</i> Wiederholender Dienst
                            </a>
                        </h6>
                    </div>
                    <div id="collapseTwo_19" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_19" aria-expanded="false" style="height: 0px;">
                        <div class="panel-body">
                            <div id="calendar"></div>
                            <ol id="calendar_list" style="display:none;"></ol>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

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
                            {{html()->hidden('position[]',$position->id)}}

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
                                    <option
                                            {{-- inefficent n+1 querry - but site is not called a lot of times --}}
                                            @if(in_array($service->id, $user->services_inHolidayList()))
                                            data-icon="glyphicon glyphicon-remove"
                                            data-subtext="Abwesend"
                                            @endif
                                            @if($user->qualifications->contains('id', $position->qualification_id))class="bg-green" @endif value="{{$user->id}}" @if($position->user_id == $user->id) selected @endif>
                                        {{substr ($user->first_name, 0, 1)}}. {{$user->name}}
                                    </option>
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
            {{ html()->button('Speichern', 'submit')->class('form-control btn btn-success waves-effect') }}
        </div>
    </div>
</div>

<div id="delete_position"></div>
{{ html()->closeModelForm() }}


@section('post_body')
    <!-- Moment Plugin Js -->
    <script src="/plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <!-- Select Plugin Js -->
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <script src="/plugins/js-year-calendar/js-year-calendar.min.js"></script>

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
                prot += '<option value="null">-- Bitte wählen --</option> @foreach($users as $user) <option @if(in_array($service->id, $user->services_inHolidayList())) data-icon="glyphicon glyphicon-remove" data-subtext="Abwesend" @endif value="{{$user->id}}">{{substr ($user->first_name, 0, 1)}}. {{$user->name}}</option> @endforeach';
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

            @if(\Illuminate\Support\Facades\Route::current()->getName() != 'service.edit')
            const calendar = new Calendar('#calendar', {
                dataSource: [],
                minDate: new Date(),
                clickDay: function(e) {
                    toggleEvent(e);
                }
            });

            function toggleEvent(e) {
                var dataSource = calendar.getDataSource();
                var exsist = false;
                var id = 0;

                for(var i in dataSource) {
                    id = i;
                    if(dataSource[i].startDate == e.date.toString()) {
                        exsist = true;
                        break;
                    }
                }

                if (!exsist) {
                    var event = {
                        id: id,
                        startDate: e.date,
                        endDate: e.date,
                        color: "black"
                    };
                    dataSource.push(event);

                    $("#calendar_list").append('<input class="form-control" id="calendar_dates_'+ id +'" type="date" value="'+ moment(e.date).format("YYYY-MM-DD")+'" name="calendar_dates[]" >');
                }
                else
                {
                    dataSource.splice(id,1);
                    input_id = "#calendar_dates_" + id;
                    $(input_id).remove();
                }

                calendar.setDataSource(dataSource);
            }
            @endif
        });
    </script>
@endsection