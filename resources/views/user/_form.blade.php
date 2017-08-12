{{ Form::model($user, ['action' => ['UserController@update', 'id' => $user->id], "method" => "PUT"]) }}
<div class="row clearfix">
    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('name', 'Nachname:') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nachname']) }}
                {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('first_name', 'Vorname:') }}
                {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'Vorname']) }}
                {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('mobilenumber') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('mobilenumber', 'Handy Nr.:') }}
                {{ Form::text('mobilenumber', null, ['class' => 'form-control', 'placeholder' => 'Handy Nr.']) }}
                {!! $errors->first('mobilenumber', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('email', 'E-Mail:') }}
                {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'mail@dlrg-stuttgart.de']) }}
                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('password', 'Passwort:') }}
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Passwort']) }}
                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

@if(\Illuminate\Support\Facades\Auth::user()->can('administration'))

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('approved') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::checkbox('approved', 1, null, ['class' => 'filled-in']) }}
                    {{ Form::label('approved', 'Freigegeben') }}
                    {!! $errors->first('approved', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">
                <div class="form-line">
                    {{ Form::label('role', 'Rolle:') }}
                    {{ Form::select('role', ['benutzer' => 'benutzer', 'admin' => 'admin'], $user->role, ['class' => 'form-control bootstrap-select']) }}
                    {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
@endif


<div class="row clearfix">
    <div class="col-sm-2">
        <div class="form-line">
            {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
        </div>
    </div>
</div>

{{ Form::close() }}