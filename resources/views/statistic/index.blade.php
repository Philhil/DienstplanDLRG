@extends('_layouts.application')

@section('head')
    <link rel="stylesheet" href="/plugins/bootstrap-table/bootstrap-table.min.css">
@endsection

@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Gesamt Statistik</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-indigo hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">face</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl Benutzer</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['users']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['users']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-grey hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">assignment</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl Dienste</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['services']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['services']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-blue-grey hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">library_books</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl Fortbildungen</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['trainings']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['trainings']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-amber hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">forum</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl Nachrichten</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['news']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['news']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                            <div class="info-box-4 bg-orange hover-zoom-effect">
                                <div class="icon">
                                        <div class="chart chart-bar">4,6,-3,-1,2,-2,4,6</div>
                                </div>
                                <div class="content">
                                    <div class="text">Durchschnittliche Zusage</div>
                                    <div class="font-8">Zeitspanne in Tagen von Zusage bis Dienstdatum</div>
                                    <div class="number">{{round($overviewStats['service_avg_inDays'],2)}} Tage</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="block-header">
                        <h2>Anzahl Helfer nach Qualifikationen</h2>
                    </div>

                    <div class="row">
                        @foreach($overviewStats['qualifications_users_count'] as $quali)
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-indigo hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">local_activity</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl {{$quali->short}}</div>
                                    <div class="number count-to" data-from="0" data-to="{{$quali->users_count}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['users']}}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Statistik über Zeitraum: </h2>
                    <form action="{{ action('StatisticController@index') }}" method="post">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group input-daterange input-group" id="bs_datepicker_range_container">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Von..." name="from" value="{{$from->format('d.m.Y')}}">
                                    </div>
                                    <span class="input-group-addon">- </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Bis..." name="to" value="{{$to->format('d.m.Y')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button type="submit" class="btn btn-primary btn-lg m-l-15 waves-effect"><i class="material-icons">replay</i>aktualisieren</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        {{-- <!-- neue user pro Monat --> --}}
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="">
                                <div class="body">
                                    <canvas id="line_chart_newusers" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- #END# neue user pro Monat --> --}}

                        {{-- <!-- Services --> --}}
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                            <div class="info-box-4 bg-grey hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">assignment</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl Dienste</div>
                                    <div class="number count-to" data-from="0" data-to="{{$timespanStats['services']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['services']}}</div>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- #END# Services --> --}}

                        {{-- <!-- Positions --> --}}
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                <div class="info-box-4 bg-deep-orange hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">error_outline</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl pflicht Positionen</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['positions_required']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['positions_required']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                <div class="info-box-4 bg-red hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">verified_user</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl besetzte pflicht Positionen</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['positions_assigned_required']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['positions_assigned_required']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                <div class="info-box-3 bg-deep-orange">
                                    <div class="icon">
                                        <div class="chart chart-pie">30, 35, 25, 8</div>
                                    </div>
                                    <div class="content">
                                        <div class="text">Quote pflicht Positionen</div>
                                        <div class="font-8">Besetzte pflicht Positionen / Pflicht Positionen * 100</div>
                                        <div class="number">@if($timespanStats['positions_required'] > 0) {{round($timespanStats['positions_assigned_required'] / $timespanStats['positions_required'] * 100, 2)}} @endif %</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row center">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="info-box-4 bg-light-green hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">verified_user</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl optionale Positionen</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['positions_optional']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['positions_optional']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="info-box-4 bg-green hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">error</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl besetzte optionale Positionen</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['positions_assigned_optional']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['positions_assigned_optional']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="info-box-3 bg-light-green">
                                    <div class="icon">
                                        <div class="chart chart-pie">30, 35, 25, 8</div>
                                    </div>
                                    <div class="content">
                                        <div class="text">Quote optionale Positionen</div>
                                        <div class="font-8">Besetzte optionale Positionen / Optionale Positionen * 100</div>
                                        <div class="number">@if($timespanStats['positions_optional'] > 0) {{round($timespanStats['positions_assigned_optional'] / $timespanStats['positions_optional'] * 100, 2)}} @endif %</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- #END# Positions --> --}}

                        {{-- <!-- Stats relatet to users --> --}}
                        <div class="row center">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="info-box-3 bg-grey hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">assignment</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Ø Anzahl Dienste pro Helfer</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['positions_per_user_avg']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['positions_per_user_avg']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="info-box-3 bg-indigo hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">face</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl User mit min. einen Dienst</div>
                                        <div class="number count-to" data-from="0" data-to="{{$timespanStats['users_min1pos']}}" data-speed="1000" data-fresh-interval="20">{{$timespanStats['users_min1pos']}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="info-box-3 bg-orange hover-zoom-effect">
                                    <div class="icon">
                                        <div class="chart chart-bar">4,6,-3,-1,2,-2,4,6</div>
                                    </div>
                                    <div class="content">
                                        <div class="text">Durchschnittliche Zusage</div>
                                        <div class="font-8">Zeitspanne in Tagen von Zusage bis Dienstdatum</div>
                                        <div class="number">{{round($timespanStats['service_avg_inDays'],2)}} Tage</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="info-box-3 bg-deep-purple hover-zoom-effect">
                                    <div class="icon">
                                        <i class="material-icons">favorite</i>
                                    </div>
                                    <div class="content">
                                        <div class="text">Anzahl Helferstunden</div>
                                        <div class="number">{{round($timespanStats['totalservice_hours'],2)}} Stunden</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- #END# Stats relatet to users --> --}}

                        {{-- <!-- Anzahl an Diensten nach Position in Zeitraum X  --> --}}
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header">
                                <h2>Anzahl Positionen nach Qualifikationen über Monate</h2>
                            </div>
                            <div class="">
                                <div class="body">
                                    <canvas id="line_chart_positions_quali" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- #END# Anzahl an Diensten nach Position in Zeitraum X  --> --}}

                        {{-- Positions Stats--}}
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header">
                                <h2>Anzahl Positionen und Ø Zusage nach Qualifikationen im gewählten Zeitraum</h2>
                            </div>
                            <div class="body table-responsive">
                                <table id="table_posquali" data-toggle="table" class="table table-striped table-hover" data-show-toggle="true"
                                       data-search="true" data-search-highlight="true" data-show-search-clear-button="true"
                                       data-cookie="true" data-cookie-id-table="posqualistatView"
                                       data-filter-control="true"
                                       data-show-export="true"
                                       @if(!Browser::isDesktop())
                                       data-card-view="true"
                                        @endif
                                >
                                    <thead>
                                    <tr>
                                        <th data-sortable="true" data-field="Qualifikation">Qualifikation</th>
                                        <th data-sortable="true" data-field="anzposrequire">Anz. alle (Erforderlich)</th>
                                        <th data-sortable="true" data-field="anzposrequireassign">Anz. besetzte (Erforderlich)</th>
                                        <th data-sortable="true" data-field="anzposrequirequote">Quote (Erforderlich)</th>
                                        <th data-sortable="true" data-field="anzposoptional">Anz. alle (Optinal)</th>
                                        <th data-sortable="true" data-field="anzposoptionalassign">Anz. besetzte (Optinal)</th>
                                        <th data-sortable="true" data-field="anzposoptionalquote">Quote (Optinal)</th>
                                        <th data-sortable="true" data-field="avgapply">Ø Zusage in Tagen</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($timespanStats['qualifications'] as $quali)
                                        <tr>
                                            <td>{{$quali->short}}</td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_all_required'][$quali->id]))
                                                    {{$timespanStats['posquali_all_required'][$quali->id]}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_assigned_required'][$quali->id]))
                                                    {{$timespanStats['posquali_assigned_required'][$quali->id]}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_all_required'][$quali->id])
                                                    && !empty($timespanStats['posquali_assigned_required'][$quali->id])
                                                    && $timespanStats['posquali_all_required'][$quali->id] > 0)
                                                    {{round($timespanStats['posquali_assigned_required'][$quali->id] / $timespanStats['posquali_all_required'][$quali->id] * 100, 2)}} %
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_all_optional'][$quali->id]))
                                                    {{$timespanStats['posquali_all_optional'][$quali->id]}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_assigned_optional'][$quali->id]))
                                                    {{$timespanStats['posquali_assigned_optional'][$quali->id]}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['posquali_all_optional'][$quali->id])
                                                    && !empty($timespanStats['posquali_assigned_optional'][$quali->id])
                                                    && $timespanStats['posquali_all_optional'][$quali->id] > 0)
                                                    {{round($timespanStats['posquali_assigned_optional'][$quali->id] / $timespanStats['posquali_all_optional'][$quali->id] * 100, 2)}} %
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($timespanStats['service_avg_byquali_inDays'][$quali->id]))
                                                    {{round($timespanStats['service_avg_byquali_inDays'][$quali->id],2)}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- #END# Positions Stats--}}

                        {{-- User positions stats--}}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="header">
                                    <h2>Anzahl Positionen nach Helfern und Qualifikationen im gewählten Zeitraum</h2>
                                </div>
                                <div class="body table-responsive">
                                    <table id="table_userposquali" data-toggle="table" class="table table-striped table-hover" data-show-toggle="true"
                                           data-show-columns="true"
                                           data-search="true" data-search-highlight="true" data-show-search-clear-button="true"
                                           data-cookie="true" data-cookie-id-table="userqualistatView"
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
                                                <th data-sortable="true" data-field="Summe" data-filter-control="select">Summe</th>
                                                @foreach($timespanStats['qualifications'] as $qualification)
                                                <th data-field="quali_{{$qualification->short}}" data-filter-control="select">{{$qualification->short}}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($timespanStats['users'] as $user)
                                            <tr>
                                                <td>{{$user->name}}</td>
                                                <td>{{$user->first_name}}</td>
                                                <td>
                                                    @if(!empty($timespanStats['users_countpos'][$user->id]))
                                                        {{$timespanStats['users_countpos'][$user->id]}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                @foreach($timespanStats['qualifications'] as $qualification)
                                                <td>
                                                    @if(!empty($timespanStats['users_countposByQuali'][$user->id])
                                                        && !empty($timespanStats['users_countposByQuali'][$user->id][$qualification->id]))
                                                        {{$timespanStats['users_countposByQuali'][$user->id][$qualification->id]}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                @endforeach

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- #END# User positions stats--}}

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('post_body')
<script src="/plugins/jquery-countto/jquery.countTo.js"></script>
<script src="/plugins/jquery-sparkline/jquery.sparkline.js"></script>
<!-- Chart Plugins Js -->
<script src="/plugins/chartjs/Chart.bundle.js"></script>

<!-- Table -->
<script src="/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/plugins/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="/plugins/bootstrap-table/extensions/cookie/bootstrap-table-cookie.min.js"></script>

<script src="/plugins/jquery-tableexport/tableExport.min.js"></script>
<script src="/plugins/bootstrap-table/extensions/export/bootstrap-table-export.js"></script>

<!-- Bootstrap Datepicker Plugin Js -->
<script src="/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script>
    function getChartJs(type) {
        var config = null;

        if (type === 'line') {
            config = {
                type: 'line',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: 'rgba(0, 188, 212, 0.75)',
                        backgroundColor: 'rgba(0, 188, 212, 0.3)',
                        pointBorderColor: 'rgba(0, 188, 212, 0)',
                        pointBackgroundColor: 'rgba(0, 188, 212, 0.9)',
                        pointBorderWidth: 1
                    }, {
                        label: "My Second dataset",
                        data: [28, 48, 40, 19, 86, 27, 90],
                        borderColor: 'rgba(233, 30, 99, 0.75)',
                        backgroundColor: 'rgba(233, 30, 99, 0.3)',
                        pointBorderColor: 'rgba(233, 30, 99, 0)',
                        pointBackgroundColor: 'rgba(233, 30, 99, 0.9)',
                        pointBorderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'bar') {
            config = {
                type: 'bar',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        backgroundColor: 'rgba(0, 188, 212, 0.8)'
                    }, {
                        label: "My Second dataset",
                        data: [28, 48, 40, 19, 86, 27, 90],
                        backgroundColor: 'rgba(233, 30, 99, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'radar') {
            config = {
                type: 'radar',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        data: [65, 25, 90, 81, 56, 55, 40],
                        borderColor: 'rgba(0, 188, 212, 0.8)',
                        backgroundColor: 'rgba(0, 188, 212, 0.5)',
                        pointBorderColor: 'rgba(0, 188, 212, 0)',
                        pointBackgroundColor: 'rgba(0, 188, 212, 0.8)',
                        pointBorderWidth: 1
                    }, {
                        label: "My Second dataset",
                        data: [72, 48, 40, 19, 96, 27, 100],
                        borderColor: 'rgba(233, 30, 99, 0.8)',
                        backgroundColor: 'rgba(233, 30, 99, 0.5)',
                        pointBorderColor: 'rgba(233, 30, 99, 0)',
                        pointBackgroundColor: 'rgba(233, 30, 99, 0.8)',
                        pointBorderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'pie') {
            config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [225, 50, 100, 40],
                        backgroundColor: [
                            "rgb(233, 30, 99)",
                            "rgb(255, 193, 7)",
                            "rgb(0, 188, 212)",
                            "rgb(139, 195, 74)"
                        ],
                    }],
                    labels: [
                        "Pink",
                        "Amber",
                        "Cyan",
                        "Light Green"
                    ]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        return config;
    }

    const labels = [{!! $timespanlabel !!}];
    const data_newusers = {
        labels: labels,
        datasets: [{
            label: 'Anz neue User pro Monat',
            backgroundColor: '#3F51B5',
            borderColor: '#3F51B5',
            data: [{{implode(',', array_values($timespanStats['users_createedByMonth']))}}],
        }]
    };

    const data_ositions_quali = {
        labels: labels,
        datasets: [
            @foreach($timespanStats['qualifications'] as $qualification)
            {
                label: 'Anz {{$qualification->short}} Positionen',
                fill: false,
                borderColor: 'rgb({{$timespanStats["quali_colors"][$qualification->id]}})',
                data: [{{implode(',', array_values($timespanStats['quali_byMonth_pos'][$qualification->id]))}}],
            },
            @endforeach
            ]
    };

    $( document ).ready(function() {
        $('#bs_datepicker_range_container').datepicker({
            autoclose: true,
            container: '#bs_datepicker_range_container',
            format: "dd.mm.yyyy",
            language: "de",
            calendarWeeks: true,
        });

        $('.count-to').countTo();

        $('#table_posquali').bootstrapTable();
        $('#table_userposquali').bootstrapTable();

        const config_newusers = {
            type: 'line',
            data: data_newusers,
            options: {}
        };

        const config_positions_quali= {
            type: 'line',
            data: data_ositions_quali,
            options: {}
        };

        new Chart(document.getElementById('line_chart_newusers'),config_newusers);
        new Chart(document.getElementById('line_chart_positions_quali'),config_positions_quali);



        //Chart Bar
        $('.chart.chart-bar:not(.reverse)').sparkline(undefined, {
            type: 'bar',
            barColor: 'rgba(0, 0, 0, 0.15)',
            negBarColor: 'rgba(0, 0, 0, 0.15)',
            barWidth: '8px',
            height: '34px'
        });
        //Chart Bar Reverse
        $('.chart.chart-bar.reverse').sparkline(undefined, {
            type: 'bar',
            barColor: 'rgba(255, 255, 255, 0.15)',
            negBarColor: 'rgba(255, 255, 255, 0.15)',
            barWidth: '8px',
            height: '34px'
        });
        //Chart Pie
        $('.chart.chart-pie').sparkline(undefined, {
            type: 'pie',
            height: '50px',
            sliceColors: ['rgba(0,0,0,0.10)', 'rgba(0,0,0,0.15)', 'rgba(0,0,0,0.20)', 'rgba(0,0,0,0.25)']
        });
        //Chart Line
        $('.chart.chart-line').sparkline(undefined, {
            type: 'line',
            width: '60px',
            height: '45px',
            lineColor: 'rgba(0, 0, 0, 0.15)',
            lineWidth: 2,
            fillColor: 'rgba(0,0,0,0)',
            spotColor: 'rgba(0, 0, 0, 0.15)',
            maxSpotColor: 'rgba(0, 0, 0, 0.15)',
            minSpotColor: 'rgba(0, 0, 0, 0.15)',
            spotRadius: 3,
            highlightSpotColor: 'rgba(0, 0, 0, 0.15)'
        });
    });
</script>
@endsection