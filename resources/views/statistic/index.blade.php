@extends('_layouts.application')

@section('head')
    <script>
    </script>
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

                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="info-box-4 bg-deep-orange hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">error_outline</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl pflicht Positionen</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['positions_required']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['positions_required']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="info-box-4 bg-red hover-zoom-effect">
                                <div class="icon">
                                    <i class="material-icons">verified_user</i>
                                </div>
                                <div class="content">
                                    <div class="text">Anzahl besetzte pflicht Positionen</div>
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['positions_assigned_required']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['positions_assigned_required']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="info-box-3 bg-deep-orange">
                                <div class="icon">
                                    <div class="chart chart-pie">30, 35, 25, 8</div>
                                </div>
                                <div class="content">
                                    <div class="text">Quote pflicht Positionen</div>
                                    <div class="font-8">Besetzte pflicht Positionen / Pflicht Positionen * 100</div>
                                    <div class="number">@if($overviewStats['positions_required'] > 0) {{round($overviewStats['positions_assigned_required'] / $overviewStats['positions_required'] * 100, 2)}} @endif %</div>
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
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['positions_optional']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['positions_optional']}}</div>
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
                                    <div class="number count-to" data-from="0" data-to="{{$overviewStats['positions_assigned_optional']}}" data-speed="1000" data-fresh-interval="20">{{$overviewStats['positions_assigned_optional']}}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <div class="info-box-3 bg-light-green">
                                <div class="icon">
                                    <div class="chart chart-pie">30, 35, 25, 8</div>
                                </div>
                                <div class="content">
                                    <div class="text">Quote pflicht Positionen</div>
                                    <div class="font-8">Besetzte pflicht Positionen / Pflicht Positionen * 100</div>
                                    <div class="number">@if($overviewStats['positions_optional'] > 0) {{round($overviewStats['positions_assigned_optional'] / $overviewStats['positions_optional'] * 100, 2)}} @endif %</div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Statistik über Zeitraum</h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">


                            neue user pro Monat
                            Anzahl User mit min 1 Dienst im Zeitraum

                            dienste pro Monat / Count Services in Daterange
                            besetzte optionale pos vs besetzte pflicht pos vs nicht besetzte optionale pos vs nicht besetzte pflicht pos Zeitraum
                            AVG Zusage zu Diensten
                            Ehrenamtlich geleistete Stunden in Daterange (Aktuell noch: Anz besetzten Positionen * x Stunden)
                            Anzahl an Diensten nach Position in Zeitraum X


                            Welche Pos ist am schwersten zu besetzen?
                                avg nach quali
                                besetzt/nicht besetzt nach quali
                            {{Auth::user()->currentclient()->name}} ({{Auth::user()->currentclient()->Season()['from']->isoFormat('DD. MMMM Y')}} - {{Auth::user()->currentclient()->Season()['to']->isoFormat('DD. MMMM Y')}})
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Vergleich Saison</h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">


                            neue user pro Monat
                            dienste pro Monat
                            besetzte positionen vs optionale pos vs pflicht pos in Zeitraum
                            AVG Zusage zu Diensten
                            Ehrenamtlich geleistete Stunden in Daterange (Aktuell noch: Anz besetzten Positionen * x Stunden)
                            Anz. Dienste pro Helfer in Daterange
                            Anz. Dienste nach Helfer in Daterange

                            {{Auth::user()->currentclient()->name}} ({{Auth::user()->currentclient()->Season()['from']->isoFormat('DD. MMMM Y')}} - {{Auth::user()->currentclient()->Season()['to']->isoFormat('DD. MMMM Y')}})
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('post_body')
<script src="/plugins/jquery-countto/jquery.countTo.js"></script>
<script src="/plugins/jquery-sparkline/jquery.sparkline.js"></script>
<script>
    $( document ).ready(function() {
        $('.count-to').countTo();

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
