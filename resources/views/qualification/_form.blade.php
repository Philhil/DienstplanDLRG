<div class="container-fluid">
    <!-- Input -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Qualifikation
                    </h2>
                </div>
                <div class="body">
                    {{ html()->modelForm($qualification, 'POST', action('QualificationController@store', $qualification->id))->open() }}
                    {{ html()->hidden('id', $qualification->id) }}
                    <div class="row clearfix">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ html()->label('Name der Qualifikation:', 'name') }}
                                    {{ html()->text('name')->class('form-control')->placeholder('Name') }}
                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('short') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ html()->label('Abkürzung der Qualifikation:', 'short') }}
                                    {{ html()->text('short')->class('form-control')->placeholder('Abkürzung') }}
                                    {!! $errors->first('short', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('isservicedefault') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ html()->hidden('isservicedefault', 0)->id("") }}
                                    {{ html()->checkbox('isservicedefault', old('isservicedefault') or $qualification->isservicedefault != 0 ? true : false)->class('filled-in')->id('isservicedefault') }}
                                    {{ html()->label('Soll die Qualifikation beim Anlegen eines Dienstes automatisch erstellt werden?', 'isservicedefault') }}
                                    {!! $errors->first('isservicedefault', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('defaultcount') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ html()->label('Wie viele Positionen dieser Qualifikation sollen automatisch angelegt werden?:', 'defaultcount') }}
                                    {{ html()->number('defaultcount', empty(old('defaultcount')) ? $qualification->defaultcount : old('defaultcount'))->class('form-control') }}
                                    {!! $errors->first('defaultcount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('defaultrequiredasposition') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ html()->hidden('defaultrequiredasposition',0)->id("") }}
                                    {{ html()->checkbox('defaultrequiredasposition', old('defaultrequiredasposition') or $qualification->defaultrequiredasposition != 0 ? true : false, 1)->class('filled-in')->id("defaultrequiredasposition")}}
                                    {{ html()->label('Ist dies eine notwendige oder optionale Position?', 'defaultrequiredasposition') }}
                                    {!! $errors->first('defaultrequiredasposition', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-sm-1">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ html()->button('Speichern', 'submit')->class('form-control btn btn-success waves-effect') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{ html()->closeModelForm() }}
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Input -->
</div>
