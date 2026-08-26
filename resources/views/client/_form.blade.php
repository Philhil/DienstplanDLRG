@if(\Illuminate\Support\Facades\Route::current()->getName() == 'client.create')
    {{ html()->modelForm($client, 'POST', action('ClientController@store', $client->id))->open() }}
@else
    {{ html()->modelForm($client, 'PUT', action('ClientController@update', $client->id))->open() }}
@endif

<div class="col-md-12">
    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
        <div class="form-line">
            {{ html()->label('Name:', 'name') }}
            {{ html()->text('name')->class('form-control')->placeholder('Client Name') }}
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <b>{{ html()->label('Start der Saison:', 'seasonStart') }}</b>
    <div class="input-group {{ $errors->has('seasonStart') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">date_range</i>
        </span>
        <div class="form-line">
            {{ html()->text('seasonStart', isset($client['seasonStart']) ? $client['seasonStart']->format('d.m') : null)->class('form-control saison')->placeholder('z.B: 01.03') }}
            {!! $errors->first('seasonStart', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <b>{{ html()->label('Default Dienstbeginn (24 hour):', 'defaultServiceStart') }}</b>
    <div class="input-group {{ $errors->has('defaultServiceStart') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">alarm</i>
        </span>
        <div class="form-line">
            {{ html()->text('defaultServiceStart')->class('form-control time24')->placeholder('z.B. 09:30') }}
            {!! $errors->first('defaultServiceStart', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-md-4">
    <b>{{ html()->label('Default Dienstende (24 hour):', 'defaultServiceEnd') }}</b>
    <div class="input-group {{ $errors->has('defaultServiceEnd') ? 'has-error' : ''}}">
        <span class="input-group-addon">
            <i class="material-icons">alarm_off</i>
        </span>
        <div class="form-line">
            {{ html()->text('defaultServiceEnd')->class('form-control time24')->placeholder('z.B. 19:30') }}
            {!! $errors->first('defaultServiceEnd', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>


<div class="col-md-12">
    <b>{{ html()->label('Automatismen', 'weeklyServiceviewEmail') }}</b>
    <div class="input-group {{ $errors->has('weeklyServiceviewEmail')  ? 'has-error' : ''}}">
        <span class="input-group-addon pull-left">
            {{ html()->checkbox('weeklyServiceviewEmail', old('weeklyServiceviewEmail') or $client->weeklyServiceviewEmail != 0 ? true : false, 1)->class('filled-in')->id("weeklyServiceviewEmail") }}
            {{ html()->label('Wöchentliches versenden des Wachplans', 'weeklyServiceviewEmail') }}
            {!! $errors->first('weeklyServiceviewEmail', '<p class="help-block">:message</p>') !!}
        </span>
    </div>
</div>

<div class="col-md-12">
    <b>{{ html()->label('E-Mail Verteiler über Mailingliste (News und Wachpläne)', 'isMailinglistCommunication') }}</b>
    <div class="input-group {{ $errors->has('isMailinglistCommunication') || $errors->has('mailinglistAddress')  ? 'has-error' : ''}}">
        <span class="input-group-addon">
            {{ html()->checkbox('isMailinglistCommunication', old('isMailinglistCommunication') or $client->isMailinglistCommunication != 0 ? true : false, 1)->class('filled-in')->id("isMailinglistCommunication") }}
            {{ html()->label(' ','isMailinglistCommunication') }}
            {!! $errors->first('isMailinglistCommunication', '<p class="help-block">:message</p>') !!}
        </span>

        <div class="form-line">
            {{ html()->text('mailinglistAddress')->class('form-control email')->placeholder('Mailinglist E-Mail') }}
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
            {{ html()->text('mailSenderName')->class('form-control')->placeholder('Absender Name') }}
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
            {{ html()->text('mailReplyAddress')->class('form-control email')->placeholder('Antworten an E-Mail Adresse') }}
            {!! $errors->first('mailReplyAddress', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-sm-10">
    <div class="form-line">
        {{ html()->button('Speichern', 'submit')->class('form-control btn btn-success waves-effect') }}
    </div>
</div>

{{ html()->closeModelForm() }}