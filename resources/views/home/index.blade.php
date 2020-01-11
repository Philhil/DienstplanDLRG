@extends('_layouts.application')

@section('head')
    <script>
    </script>
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Übersicht und Statistik von {{Auth::user()->currentclient()->name}} ({{Auth::user()->currentclient()->Season()['from']->formatLocalized('%d. %B %Y')}} - {{Auth::user()->currentclient()->Season()['to']->formatLocalized('%d. %B %Y')}})</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">Geleistete Dienste</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_user_past}}" data-speed="1000" data-fresh-interval="20">{{$positions_user_past}}</div>
                            </div>
                        </div>
                        <div class="info-box bg-grey hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text"> Teilgenommene Übungen</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_user_past_training}}" data-speed="1000" data-fresh-interval="20">{{$positions_user_past_training}}</div>
                            </div>
                        </div>

                        <div class="info-box bg-blue hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">event_available</i>
                            </div>
                            <div class="content">
                                <div class="text">Total Geleistete Dienste</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_total_past}}" data-speed="1000" data-fresh-interval="20">{{$positions_total_past}}</div>
                            </div>
                        </div>
                        <div class="info-box bg-pink hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">event_busy</i>
                            </div>
                            <div class="content">
                                <div class="text">Noch nicht besetzte Dienste (Mindestbesatzung)</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_free_required}}" data-speed="1000" data-fresh-interval="20">{{$positions_free_required}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                        <div class="card">
                            <div class="body bg-orange">
                                <div class="font-bold m-b--35">
                                    <i class="material-icons">stars</i> Hall of Fame
                                </div>
                                <ul class="dashboard-stat-list">
                                    @foreach($top_users as $top_pos)
                                        <li>
                                            {{substr($top_pos->user->first_name, 0, 1)}}. {{$top_pos->user->name}}
                                            <span class="pull-right"><b>{{$top_pos->aggregate}}</b> <small>Dienste</small></span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NEWS -->
                <div class="row clearfix">
                    @foreach(\App\News::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('created_at', 'DESC')->take(1)->get() as $news)
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header bg-blue-grey">
                                    <h2>
                                        {{$news->title}} <small>{{$news->created_at->format('l d.m.Y')}}</small>
                                    </h2>
                                </div>
                                <div class="body">
                                    {!! $news->content !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('post_body')
<script src="/plugins/jquery-countto/jquery.countTo.js"></script>
<script>
    $( document ).ready(function() {
        $('.count-to').countTo();
    });
</script>
@endsection
