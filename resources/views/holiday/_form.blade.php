@if(\Illuminate\Support\Facades\Route::current()->getName() == 'holiday.edit')
    {{ html()->modelForm($holiday, 'PUT', action('HolidayController@update', $holiday->id))->open() }}
@else
    {{ html()->modelForm($holiday, 'POST', action('HolidayController@store', $holiday->id))->open() }}
@endif

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    {{html()->hidden('id', $holiday->id)}}
    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('from') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Von:', 'from') }}
                    {{ html()->date('from', old('from', !empty($holiday->from) ? $holiday->from : ''))->class('form-control no-resize')->placeholder("Von...") }}
                    {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('to') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ html()->label('Bis:', 'to') }}
                    {{ html()->date('to', old('to', !empty($holiday->to) ? $holiday->to : ''))->class('form-control no-resize')->placeholder("Bis...") }}
                    {!! $errors->first('to', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-sm-1 pull-right">
        <div class="form-line">
            {{ html()->button('Speichern', 'submit')->class('form-control btn btn-success waves-effect') }}
        </div>
    </div>
</div>
{{ html()->closeModelForm() }}


@section('post_body')
    <script>
        $( document ).ready(function() {

        });
    </script>
@endsection