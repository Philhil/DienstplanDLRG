@if(\Illuminate\Support\Facades\Route::current()->getName() == 'training.edit')
    {{ Form::model($training->toArray() + $positions->toArray(), ['action' => ['TrainingController@update', 'id' => $training->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($training->toArray() + $positions->toArray(), ['action' => ['TrainingController@store', 'id' => $training->id]]) }}
@endif

<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('date', 'Von:') }}
                    {{ Form::text('date', old('date', !empty($training->date) ? $training->date->format('d m Y H:m') : ''), ['id' => 'date-start', 'class' => 'date-start form-control', 'placeholder'=>'Datum auswählen...', 'required'=>"true"]) }}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('dateEnd', 'Bis:') }}
                    {{ Form::text('dateEnd', old('dateEnd', !empty($training->dateEnd) ? $training->dateEnd->format('d m Y H:m') : ''), ['id' => 'date-end', 'class' => 'date-end form-control', 'placeholder'=>'Datum auswählen...']) }}
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
            <div class="form-group {{ $errors->has('content') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::textarea('content', old('content'), ['class' => 'form-control', 'id'=>"tinymce"]) }}
                    {!! $errors->first('content', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <!-- #END# TinyMCE -->

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('location') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('location', 'Ort (Mit Koordniaten darstellbar auf Karte):') }}
                    {{ Form::text('location', old('location', $training->location), ['id' => 'location', 'class' => 'location form-control', 'placeholder'=>'Ort eingeben...']) }}
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
                    {{ Form::text('sendbydatetime', old('sendbydatetime', !empty($training->sendbydatetime) ? $training->sendbydatetime->format('d m Y H:m') : ''), ['id' => 'sendbydatetime', 'class' => 'sendbydatetime form-control', 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('sendbydatetime', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-12 text-center">
            <div class="form-group">
                <button type="button" class="btn btn-danger waves-effect btn-success" id="add_qualification"><i class="material-icons">add</i><span> Position Hinzufügen</span> </button>
            </div>
        </div>
    </div>

    <div class="body table-responsive" style="padding-bottom: 100px">
        <table class="table table-striped" id="tblPositions">
            <thead>
            <tr>
                <th>Qualifikation</th>
                <th>Kommentar</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($positions) && $positions instanceof \Illuminate\Database\Eloquent\Collection)
                @foreach($positions as $position)
                    <tr pos_id="{{$position->id}}" class="strikeout">
                        <td>
                            {{Form::hidden('position[]',$position->id)}}
                            <select class="bootstrap-select show-tick" data-live-search="true" name="qualification[]">
                                @foreach($qualifications as $qualification)
                                    <option value="{{$qualification->id}}" @if($position->qualification_id == $qualification->id) selected @endif>{{$qualification->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" placeholder="Kommentar..." type="text" value="{{$position->comment}}" name="position_comment[]" >
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

@if(\Illuminate\Support\Facades\Route::current()->getName() == 'training.edit')
<!--
    <div class="body table-responsive" style="padding-bottom: 100px">
        <label for="tblTrainingusers">Teilnehmende:</label>
        <table class="table table-striped" id="tblTrainingusers">
            <thead>
            <tr>
                <th>User</th>
                <th>Position</th>
                <th>Kommentar</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($positions) && $positions instanceof \Illuminate\Database\Eloquent\Collection)
                @foreach($training_users as $training_user)
                    <tr training_user_id="{{$training_user->id}}" class="strikeout">
                        <td>
                            {{Form::hidden('training_user[]',$training_user->id)}}
                            {{$training_user->user->first_name . " " . $training_user->user->name}}
                        </td>
                        <td>
                            {{$training_user->position->qualification->name}}
                        </td>
                        <td>
                            <input class="form-control" placeholder="Kommentar..." type="text" value="{{$training_user->comment}}" name="position_comment[]" >
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger waves-effect btn-delete delete_training_user">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
-->
@endif
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

    <!-- TinyMCE -->
    <script src="/plugins/tinymce/tinymce.js"></script>

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
                $('#date-end').bootstrapMaterialDatePicker('setDate', moment(date).add(3, 'hours'));

                $('#sendbydatetime').bootstrapMaterialDatePicker('setMaxDate', date);
                $('#sendbydatetime').bootstrapMaterialDatePicker('setDate', moment(date).subtract(7, 'day').set({hour:8,minute:0,second:0}));
            });

            //TinyMCE
            tinymce.init({
                selector: "textarea#tinymce",
                theme: "modern",
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: 'print preview media | forecolor backcolor emoticons',
                image_advtab: true
            });
            tinymce.suffix = ".min";
            tinyMCE.baseURL = '/plugins/tinymce';

            $('#add_qualification').on("click", function () {

                var prot = '<tr pos_id="-1" >';
                prot += '<td><select class="bootstrap-select show-tick" data-live-search="true" name="qualification[]">';
                prot += '@foreach($qualifications as $qualification)<option value="{{$qualification->id}}">{{$qualification->name}}</option>@endforeach';
                prot += ' </select> </td>';
                prot += ' <td> <input class="form-control" placeholder="Kommentar..." type="text" value="" name="position_comment[]" > </td>';
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