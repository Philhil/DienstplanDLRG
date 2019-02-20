@if(\Illuminate\Support\Facades\Route::current()->getName() == 'client.create')

    {{ Form::model($client, ['action' => ['ClientController@store', 'id' => $client->id]]) }}

    <div class="col-md-12">
        <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('name', 'Name:') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Client Name']) }}
                {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
@else
    {{ Form::model($client, ['action' => ['ClientController@update', 'id' => $client->id], "method" => "PUT"]) }}

    <div class="col-md-12">
        <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ Form::label('name', 'Name:') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Client Name', 'disabled', 'readonly']) }}
                {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
@endif

<div class="col-md-4">
    <b>{{ Form::label('seasonStart', 'Start der Saison:') }}</b>
    <div class="input-group {{ $errors->has('seasonStart') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">date_range</i>
        </span>
        <div class="form-line">
            {{ Form::text('seasonStart', null, ['class' => 'form-control saison', 'placeholder' => 'z.B: 01.03']) }}
            {!! $errors->first('seasonStart', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <b>{{ Form::label('defaultServiceStart', 'Default Dienstbeginn (24 hour):') }}</b>
    <div class="input-group {{ $errors->has('defaultServiceStart') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">alarm</i>
        </span>
        <div class="form-line">
            {{ Form::text('defaultServiceStart', null, ['class' => 'form-control time24', 'placeholder' => 'z.B. 09:30']) }}
            {!! $errors->first('defaultServiceStart', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <b>{{ Form::label('defaultServiceEnd', 'Default Dienstende (24 hour):') }}</b>
    <div class="input-group {{ $errors->has('defaultServiceEnd') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">alarm_off</i>
        </span>
        <div class="form-line">
            {{ Form::text('defaultServiceEnd', null, ['class' => 'form-control time24', 'placeholder' => 'z.B. 19:30']) }}
            {!! $errors->first('defaultServiceEnd', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>


<div class="col-md-12">
    <b>{{ Form::label('weeklyServiceviewEmail', 'Automatismen') }}</b>
    <div class="input-group {{ $errors->has('weeklyServiceviewEmail')  ? 'has-error' : ''}}">
        <span class="input-group-addon pull-left">
            {{ Form::checkbox('weeklyServiceviewEmail', 1, old('weeklyServiceviewEmail') or $client->weeklyServiceviewEmail != 0 ? true : false, ['class' => 'filled-in', 'id' => "weeklyServiceviewEmail"]) }}
            {{ Form::label('weeklyServiceviewEmail', 'Wöchentliches versenden des Wachplans') }}
            {!! $errors->first('weeklyServiceviewEmail', '<p class="help-block">:message</p>') !!}
        </span>
    </div>
</div>

<div class="col-md-12">
    <b>{{ Form::label('isMailinglistCommunication', 'E-Mail Verteiler über Mailingliste (News und Wachpläne)') }}</b>
    <div class="input-group {{ $errors->has('isMailinglistCommunication') || $errors->has('mailinglistAddress')  ? 'has-error' : ''}}">
        <span class="input-group-addon">
            {{ Form::checkbox('isMailinglistCommunication', 1, old('isMailinglistCommunication') or $client->isMailinglistCommunication != 0 ? true : false, ['class' => 'filled-in', 'id' => "isMailinglistCommunication"]) }}
            {{ Form::label('isMailinglistCommunication', ' ') }}
            {!! $errors->first('isMailinglistCommunication', '<p class="help-block">:message</p>') !!}
        </span>

        <div class="form-line">
            {{ Form::text('mailinglistAddress', null, ['class' => 'form-control email', 'placeholder' => 'Mailinglist E-Mail']) }}
            {!! $errors->first('mailinglistAddress', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>


<h2 class="card-inside-title">Absender Einstellungen</h2>

<div class="col-md-6">
    <div class="input-group {{ $errors->has('mailSenderName') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">contact_mail</i>
        </span>
        <div class="form-line">
            {{ Form::text('mailSenderName', null, ['class' => 'form-control', 'placeholder' => 'Absender Name']) }}
            {!! $errors->first('mailSenderName', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="input-group {{ $errors->has('mailReplyAddress') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">email</i>
        </span>
        <div class="form-line">
            {{ Form::text('mailReplyAddress', null, ['class' => 'form-control email', 'placeholder' => 'Antworten an E-Mail Adresse']) }}
            {!! $errors->first('mailReplyAddress', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-sm-10">
    <div class="form-line">
        {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
    </div>
</div>

{{ Form::close() }}