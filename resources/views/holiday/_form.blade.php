@if(\Illuminate\Support\Facades\Route::current()->getName() == 'holiday.edit')
    {{ Form::model($holiday, ['action' => ['HolidayController@update', $holiday->id], 'method' => 'PUT']) }}
@else
    {{ Form::model($holiday, ['action' => ['HolidayController@store', $holiday->id]]) }}
@endif

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    {{Form::hidden('id', $holiday->id)}}
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('from') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('from', 'Von:') }}
                    {{ Form::date('from', old('from', !empty($holiday->from) ? $holiday->from : ''), ['placeholder' => "Von...", 'class' => 'form-control no-resize']) }}
                    {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('to') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('to', 'Von:') }}
                    {{ Form::date('to', old('to', !empty($holiday->to) ? $holiday->to : ''), ['placeholder' => "Bis...", 'class' => 'form-control no-resize']) }}
                    {!! $errors->first('to', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
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
    <script>
        $( document ).ready(function() {

        });
    </script>
@endsection