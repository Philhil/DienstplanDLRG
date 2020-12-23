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
                    {{ Form::model($qualification, ['action' => ['QualificationController@store', $qualification->id]]) }}
                    <div class="row clearfix">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::label('name', 'Name der Qualifikation:') }}
                                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']) }}
                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('short') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::label('short', 'Abkürzung der Qualifikation:') }}
                                    {{ Form::text('short', null, ['class' => 'form-control', 'placeholder' => 'Abkürzung']) }}
                                    {!! $errors->first('short', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('isservicedefault') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::hidden('isservicedefault',0)}}
                                    {{ Form::checkbox('isservicedefault', 1, old('isservicedefault') or $qualification->isservicedefault != 0 ? true : false, ['class' => 'filled-in', 'id' => "isservicedefault"]) }}
                                    {{ Form::label('isservicedefault', 'Soll die Qualifikation beim Anlegen eines Dienstes automatisch erstellt werden?') }}
                                    {!! $errors->first('isservicedefault', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('defaultcount') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::label('defaultcount', 'Wie viele Positionen dieser Qualifikation sollen automatisch angelegt werden?:') }}
                                    {{ Form::number('defaultcount', 1, ['class' => 'form-control']) }}
                                    {!! $errors->first('defaultcount', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('defaultrequiredasposition') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::hidden('defaultrequiredasposition',0)}}
                                    {{ Form::checkbox('defaultrequiredasposition', 1, old('defaultrequiredasposition') or $qualification->defaultrequiredasposition != 0 ? true : false, ['class' => 'filled-in', 'id' => "defaultrequiredasposition"]) }}
                                    {{ Form::label('defaultrequiredasposition', 'Ist dies eine notwendige oder optionale Position?') }}
                                    {!! $errors->first('defaultrequiredasposition', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-sm-1">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Input -->
</div>
