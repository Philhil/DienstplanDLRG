{{ html()->modelForm($user, 'PUT', action('UserController@update', $user->id))->open() }}
<div class="col-sm-10">
    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
        <div class="form-line">
            {{ html()->label('Nachname:', 'name') }}
            {{ html()->text('name')->class('form-control')->placeholder('Nachname') }}
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-sm-10">
    <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
        <div class="form-line">
            {{ html()->label('Vorname:', 'first_name') }}
            {{ html()->text('first_name')->class('form-control')->placeholder('Vorname') }}
            {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-sm-10">
    <div class="form-group {{ $errors->has('mobilenumber') ? 'has-error' : ''}}">
        <div class="form-line">
            {{ html()->label('Handy Nr.:', 'mobilenumber') }}
            {{ html()->text('mobilenumber')->class('form-control')->placeholder('Handy Nr.') }}
            {!! $errors->first('mobilenumber', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="col-sm-10">
    <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
        <div class="form-line">
            {{ html()->label('E-Mail:', 'email') }}
            {{ html()->email('email')->class('form-control')->placeholder('mail@example.org') }}
            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>



@if(\Illuminate\Support\Facades\Auth::user()->can('superadministration'))

    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('approved') ? 'has-error' : ''}}">
            {{ html()->checkbox('approved', null, 1)->class('filled-in') }}
            {{ html()->label('Freigegeben', 'approved') }}
            {!! $errors->first('approved', '<p class="help-block">:message</p>') !!}
        </div>
    </div>

    <div class="col-sm-10">
        <div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">
            <div class="form-line">
                {{ html()->label('Rolle:', 'role') }}
                {{ html()->select('role', ['benutzer' => 'benutzer', 'admin' => 'admin'], $user->role)->class('form-control bootstrap-select') }}
                {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
@endif


<div class="col-sm-10">
    <div class="form-line">
        {{ html()->submit('Speichern')->class('form-control btn btn-success waves-effect') }}

    </div>
</div>

{{ html()->closeModelForm() }}