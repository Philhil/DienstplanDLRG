@if(\Illuminate\Support\Facades\Route::current()->getName() == 'training.edit')
    {{ html()->modelForm($training->toArray() + $positions->toArray(), 'PUT', action('TrainingController@update', $training->id))->open() }}
@else
    {{ html()->modelForm($training->toArray() + $positions->toArray(), 'POST', action('TrainingController@store', $training->id))->open() }}
@endif

<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Von:', 'date') }}
                    {{ html()->text('date', old('date', !empty($training->date) ? $training->date->format('d m Y H:i') : ''))->attributes(['id' => 'date-start', 'class' => 'date-start form-control', 'placeholder'=>'Datum auswählen...', 'required'=>"true"]) }}
                    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Bis:', 'dateEnd') }}
                    {{ html()->text('dateEnd', old('dateEnd', !empty($training->dateEnd) ? $training->dateEnd->format('d m Y H:i') : ''))->attributes(['id' => 'date-end', 'class' => 'date-end form-control', 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('dateEnd', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Titel:', 'title') }}
                    {{ html()->text('title', old('title'))->attributes(['placeholder' => "Titel...", 'class' => 'form-control no-resize', 'required'=>"true"]) }}
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
                    {{ html()->textarea('content', old('content'))->class('form-control')->id("tinymce") }}
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
                    {{ html()->label('Ort (Mit Koordinaten darstellbar auf Karte):', 'location') }}
                    {{ html()->text('location')->class('location form-control')->id('location')->attributes(['placeholder'=>'Ort eingeben...']) }}
                    {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('sendbydatetime') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Automatisch E-Mail versenden am (Leer = deaktiviert):', 'sendbydatetime') }}
                    {{ html()->text('sendbydatetime', old('sendbydatetime', !empty($training->sendbydatetime) ? $training->sendbydatetime->format('d m Y H:i') : ''))->class('sendbydatetime form-control')->id('sendbydatetime')->attributes(['placeholder'=>'Datum auswählen...']) }}
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

    <div class="body table-responsive" style="padding-bottom: 100px; overflow: auto">
        <table class="table table-striped" id="tblPositions">
            <thead>
            <tr>
                <th>Qualifikation</th>
                <th>Kommentar</th>
                <th>Punkte</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($positions) && $positions instanceof \Illuminate\Database\Eloquent\Collection)
                @foreach($positions as $position)
                    <tr pos_id="{{$position->id}}" class="strikeout">
                        <td>
                            {{html()->hidden('position[]',$position->id)}}
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
                            <input class="form-control" placeholder="Punkte..." type="number" step="0.01" name="credit[]" required="" aria-required="true" aria-invalid="true" value="{{$position->getCredit->points}}">
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
                $('#date-end').bootstrapMaterialDatePicker('setDate', moment(date).clone().add(3, 'hours'));

                $('#sendbydatetime').bootstrapMaterialDatePicker('setMaxDate', date);
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
                prot += ' <td><input class="form-control" placeholder="Punkte..." type="number" step="0.01" name="credit[]" required="" aria-required="true" aria-invalid="true" value="1"> </td>';
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
