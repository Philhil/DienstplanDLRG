<div class="container-fluid">
    <!-- Input -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Kategorie
                    </h2>
                </div>
                <div class="body">
                    {{Form::model($tag, ['action' => ['TagController@store', $tag->id]]) }}
                    {{Form::hidden('id', $tag->id)}}
                    <div class="row clearfix">
                        <div class="col-sm-4">
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                <div class="form-line">
                                    {{ Form::label('name', 'Name der Kategorie:') }}
                                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']) }}
                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('color') ? 'has-error' : ''}}">

                                    {{ Form::label('color', 'Farbe:') }}
                                    <input type="color" value="{{$tag->color}}" name="color" id="color">
                                    {!! $errors->first('color', '<p class="help-block">:message</p>') !!}
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
