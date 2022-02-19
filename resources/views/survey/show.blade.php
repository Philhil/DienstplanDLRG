@extends('_layouts.application')

@section('head')
    <link rel="stylesheet" href="/plugins/bootstrap-table/bootstrap-table.min.css">
@endsection

@section('content')

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-green">
                <h2>{{$survey->title}}</h2>
                <small>@if(isset($survey->dateStart) || isset($survey->dateEnd)) Abfrage @endif @if(isset($survey->dateStart)) von {{$survey->dateStart->isoFormat('ddd DD.MM.YY HH:mm')}} @endif @if(isset($survey->dateEnd)) bis {{$survey->dateEnd->isoFormat('ddd DD.MM.YY HH:mm')}} @endif</small>
            </div>

            <div class="body ">
                {!! $survey->content !!}

                <hr>
                <div class="row clearfix">

                    {{-- User already voted --}}
                    @if(isset($survey_user) && !is_null($survey_user->vote))
                        <div class="col-sm-12">
                            <div class="form-group">
                                Du hast am {{$survey_user->votedAt->isoFormat('ddd DD.MM.YY HH:mm')}} @if($survey_user->vote) zugestimmt. @else abgelehnt. @endif
                            </div>
                        </div>
                    @else
                        {{ Form::model($survey_user, ['action' => ['SurveyController@vote', $survey->id], 'method' => 'POST']) }}

                        @if($survey->passwordConfirmationRequired)
                            <div class="col-sm-2 col-xs-6">
                                <div class="form-group {{$errors->has('password')  ? 'has-error' : ''}}">
                                    <div class="form-line">
                                        {{ Form::password('password', ['placeholder' => "Passwort...", 'class' => 'form-control', 'id' => "password", "autocomplete" => "off"]) }}
                                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-sm-1 col-xs-3">
                            {{ Form::button('Zustimmen', ['class' => 'btn btn-success waves-effect', 'type' => "submit", 'value' => "accept"]) }}
                        </div>

                        @if(!$survey->mandatory)
                        <div class="col-sm-1 col-xs-3">
                            {{ Form::button('Ablehnen', ['class' => 'btn btn-danger waves-effect', 'type' => "submit", 'value' => "deny"]) }}
                        </div>
                        @endif

                        {{ Form::close() }}
                    @endif
                </div>

            </div>

        </div>
    </div>

    @if($isAdmin)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Abfrage Übersicht</h2>
                    <small>Gestartet: @if(isset($survey->dateStart)) {{$survey->dateStart->isoFormat('ddd DD.MM.YY HH:mm')}} @else {{$survey->created_at->isoFormat('ddd DD.MM.YY HH:mm')}} @endif | Endet: @if(isset($survey->dateEnd)) {{$survey->dateEnd->isoFormat('ddd DD.MM.YY HH:mm')}} @else nie @endif</small>
                </div>

                <div class="body ">

                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                            <canvas class="chart chart-pie" id="chart-pie" width="50" height="50"></canvas>
                        </div>

                        <div class="col-lg-9 col-md-6 col-sm-12 col-xs-12">
                            {{-- User survey stats--}}
                            <div class="header">
                                <h2>Übersicht Rückmeldungen</h2>
                            </div>
                            <div class="body table-responsive">
                                <table id="table_usersurvey" data-toggle="table" class="table table-striped table-hover" data-show-toggle="true"
                                       data-cookie="true" data-cookie-id-table="usersurveystatView"
                                       data-filter-control="true"
                                       data-show-export="true"
                                       @if(!Browser::isDesktop())
                                       data-card-view="true"
                                        @endif
                                >
                                    <thead>
                                    <tr>
                                        <th data-sortable="true" data-field="Name" data-filter-control="input">Name</th>
                                        <th data-sortable="true" data-field="Vorname" data-filter-control="input">Vorname</th>
                                        <th data-sortable="true" data-field="Rückmeldung" data-filter-control="select">Rückmeldung</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($users_with_user_survey as $user)
                                        <tr>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->first_name}}</td>
                                            <td>
                                                @if(count($user['mySurveyUsers']) > 0)
                                                    @if($user['mySurveyUsers'][0]->vote)
                                                        <p class="bg-green">✓</p>
                                                    @else
                                                        <p class="bg-red">✗</p>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- #END# User survey stats--}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

@endsection

@section('post_body')
    <!-- Chart Plugins Js -->
    <script src="/plugins/chartjs/Chart.bundle.js"></script>

    <!-- Table -->
    <script src="/plugins/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/cookie/bootstrap-table-cookie.min.js"></script>

    <script src="/plugins/jquery-tableexport/tableExport.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/export/bootstrap-table-export.js"></script>

    <script>

        $(document).ready(function () {
            @if($isAdmin)
            $('#table_usersurvey').bootstrapTable();

            new Chart(document.getElementById("chart-pie"), {
                type: 'pie',
                data: {
                    labels: ["nicht abgestimmt", "Zugestimmt", "Abgelehnt"],
                    datasets: [{
                        label: "Abfrageresultat",
                        backgroundColor: ["#757b80", "#358333","rgba(255,0,0,0.7)"],
                        //alle user mit qualification - survey_user where voted not null
                        data: [{{$countNoVote}}, {{$countAccept}}, {{$countDeny}}]
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Abfrageresultat'
                    }
                }
            });
            @endif
        });
    </script>
@endsection