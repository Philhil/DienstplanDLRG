@extends('_layouts.application')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Multi Select Css -->
    <link href="/plugins/multi-select/css/multi-select.css" rel="stylesheet">

    <script>
        function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
            if (colorName === null || colorName === '') { colorName = 'alert-success'; }
            if (text === null || text === '') { text = ''; }
            if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
            if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
            if (placementFrom === null || placementFrom === '') { placementFrom = 'top'; }
            if (placementAlign === null || placementAlign === '') { placementAlign = 'center'; }

            var allowDismiss = true;

            $.notify({
                        message: text
                    },
                    {
                        type: colorName,
                        allow_dismiss: allowDismiss,
                        newest_on_top: true,
                        timer: 1000,
                        placement: {
                            from: placementFrom,
                            align: placementAlign
                        },
                        animate: {
                            enter: animateEnter,
                            exit: animateExit
                        },
                        template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<span data-notify="icon"></span> ' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span data-notify="message">{2}</span>' +
                        '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                        '</div>' +
                        '<a href="{3}" target="{4}" data-notify="url"></a>' +
                        '</div>'
                    });
        }
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Input -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Benutzer
                        </h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
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
                                        <div class="form-group">
                                            <div class="form-line">
                                                {{ Form::button('Speichern', ['class' => 'form-control btn btn-success waves-effect', 'type' => "submit"]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{ Form::close() }}
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

                                <div class="row clearfix">
                                    <select id='user_qualification_select' class="ms" multiple='multiple' style="position: absolute; left: -9999px;">
                                        @foreach($qualifications_notassigned as $qualification_notassigned)
                                            <option value='{{$qualification_notassigned->id}}' >{{$qualification_notassigned->name}}</option>
                                        @endforeach

                                        @foreach($qualifications_assigned as $qualification_assigned)
                                            <option value='{{$qualification_assigned->id}}' selected>{{$qualification_assigned->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Input -->
    </div>
@endsection


@section('post_body')
    <!-- Multi Select Plugin Js -->
    <script src="/plugins/multi-select/js/jquery.multi-select.js"></script>

    <!-- Bootstrap Notify Plugin Js -->
    <script src="../../plugins/bootstrap-notify/bootstrap-notify.js"></script>

    <script>
        $( document ).ready(function() {

            $('#user_qualification_select').multiSelect({
                selectableHeader: "<h6>Alle Qualifikationen</h6>",
                selectionHeader: "<h6>zugeordnete Qualifikationen</div>",
                selectableFooter: "<div class='custom-header'></div>",
                selectionFooter: "<div class='custom-header'></div>",

                afterSelect: function(value){
                    $.ajax({
                        type: "POST",
                        url: '/qualification_user/create',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        'data' : {
                           'qualification_id': value[0],
                            'user_id': "{{$user->id}}"
                        },
                        success : function(data){
                            if (data == "true") {
                                showNotification("alert-success", "Qualifikation gespeichert", "top", "center", "", "");
                            } else {
                                showNotification("alert-warning", "Fehler beim speichern der Qualifikation", "top", "center", "", "");
                            }
                        }
                    });
                },
                afterDeselect: function(value){
                    $.ajax({
                        type: "POST",
                        url: '/qualification_user/delete/{{$user->id}}/'+value,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success : function(data){
                            if (data == "true") {
                                showNotification("alert-success", "Qualifikation gelöscht", "top", "center", "", "");
                            } else {
                                showNotification("alert-warning", "Fehler beim löschen der Qualifikation", "top", "center", "", "");
                            }
                        }
                    });
                }
            });

        });
    </script>
@endsection