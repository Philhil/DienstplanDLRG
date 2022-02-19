@if(\Illuminate\Support\Facades\Route::current()->getName() == 'survey.edit')
    {{ Form::model($survey->toArray(), ['action' => ['SurveyController@update', $survey->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($survey->toArray(), ['action' => ['SurveyController@store', $survey->id]]) }}
@endif

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

    <div class="row clearfix">
        <div class="col-sm-12">
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
        <div class="form-group {{ $errors->has('content') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('content', 'Inhalt:') }}
                {{ Form::textarea('content', old('content'), ['class' => 'form-control', 'id'=>"tinymce"]) }}
                {!! $errors->first('content', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <!-- #END# TinyMCE -->

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{$errors->has('dateStart') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{Form::label('dateStart', 'Von:')}}
                    {{Form::text('dateStart', old('date', !empty($survey->dateStart) ? $survey->dateStart->format('d m Y H:i') : ''),
                                ['id' => 'date-start', 'class' => 'date-start form-control',
                                'placeholder'=>'Datum auswählen...'])}}
                    {!! $errors->first('dateStart', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('dateEnd') ? 'has-error' : '' }}">
                <div class="form-line">
                    {{ Form::label('dateEnd', 'Bis:') }}
                    {{ Form::text('dateEnd', old('dateEnd', !empty($survey->dateEnd) ? $survey->dateEnd->format('d m Y H:i') : ''),
                                 ['id' => 'date-end', 'class' => 'date-end form-control',
                                 'placeholder'=>'Datum auswählen...']) }}
                    {!! $errors->first('dateEnd', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-12">
            <div class="input-group {{ $errors->has('mandatory')  ? 'has-error' : ''}}">
                <span class="input-group-addon pull-left">
                    {{ Form::checkbox('mandatory', 1, old('mandatory') or $survey->mandatory != 0 ? true : false, ['class' => 'filled-in', 'id' => "mandatory"]) }}
                    {{ Form::label('mandatory', 'Abfrage muss zugestimmt werden.') }}
                    {!! $errors->first('mandatory', '<p class="help-block">:message</p>') !!}
                </span>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-12">
            <div class="input-group {{ $errors->has('passwordConfirmationRequired')  ? 'has-error' : ''}}">
                <span class="input-group-addon pull-left">
                    {{ Form::checkbox('passwordConfirmationRequired', 1, old('passwordConfirmationRequired') or $survey->passwordConfirmationRequired != 0 ? true : false, ['class' => 'filled-in', 'id' => "passwordConfirmationRequired"]) }}
                    {{ Form::label('passwordConfirmationRequired', 'Nutzer müssen Passwort zum zustimmen eingeben.') }}
                    {!! $errors->first('passwordConfirmationRequired', '<p class="help-block">:message</p>') !!}
                </span>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-12">
            <div class="input-group {{ $errors->has('qualification_id')  ? 'has-error' : ''}}">
                <span class="input-group-addon pull-left">
                    {{Form::label('qualification_id', 'Nur für Nutzer mit Qualifikation:')}}
                </span>
                {{Form::select('qualification_id', $qualifications, $survey->qualification_id, ['class' => 'form-control'])}}
                {!! $errors->first('qualification_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

    </div>

    @if(isset($aUserHasAlreadyVoted) && !$aUserHasAlreadyVoted)
        <div class="alert alert-danger">
            <strong>VORSICHT!</strong> Nutzer haben sich zu dieser Abfrage bereits gemeldet. Das verändern der Abfrage kann die Integrität der Rückmeldungen verletzen.
        </div>
    @endif

</div>

<div class="row clearfix">

    <div class="col-sm-1">
        <div class="form-line">
            {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit", 'value' => "submit"]) }}
        </div>
    </div>
    @if(\Illuminate\Support\Facades\Route::current()->getName() == 'survey.edit')
        <div class="col-sm-1">
            <div class="form-line">
                <a href="{{ route('survey.destroy', $survey->id) }}" class="form-control btn btn-danger waves-effect">Löschen</a>
            </div>
        </div>
    @endif
</div>

{{ Form::close() }}


@section('post_body')
    <!-- Moment Plugin Js -->
    <script src="/plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

    <!-- TinyMCE -->
    <script src="/plugins/tinymce/tinymce.js"></script>

    <script>
        $( document ).ready(function() {

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

            $('#date-end').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de'
            });
            $('#date-start').bootstrapMaterialDatePicker({
                format: 'DD MM YYYY HH:mm',
                clearButton: true,
                weekStart: 1,
                lang : 'de'
            });

        });
    </script>
@endsection