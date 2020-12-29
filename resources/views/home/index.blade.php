@extends('_layouts.application')

@section('head')
    <script>
    </script>
@endsection

@section('content')
    @if(\Illuminate\Support\Facades\Auth::user()->can('administration'))
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Admin Panel</h2>
                </div>
                <div class="body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs tab-col-deep-orange" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#action_with_icon_title" data-toggle="tab" aria-expanded="true">
                                <i class="material-icons">record_voice_over</i> Aktionen
                            </a>
                        </li>
                        <li role="presentation" class="">
                            <a href="#hilfe_with_icon_title" data-toggle="tab" aria-expanded="false">
                                <i class="material-icons">feedback</i> Hilfe
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="action_with_icon_title">
                            <b></b>
                            <div class="row clearfix">

                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="padding-left: 15px; padding-right: 15px;">
                                    <a href="{{ action('HomeController@generatePDF')}}" class="btn btn-block btn-lg bg-red waves-effect">Dienstplan (PDF) herunterladen</a>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="padding-left: 15px; padding-right: 15px;">
                                    <button type="button" class="btn btn-block btn-lg bg-red waves-effect" data-toggle="modal" data-target="#smallModal">Dienstplan E-Mail an alle Versenden</button>
                                </div>

                                <div class="modal fade" id="smallModal" tabindex="-1" role="dialog" style="display: none;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content modal-col-red">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="smallModalLabel">E-Mail Massenversand</h4>
                                            </div>
                                            <div class="modal-body">
                                                Mit dieser Aktion wird
                                                @if(Auth::user()->currentclient()->isMailinglistCommunication)
                                                    eine E-Mail an den zentralen Mailverteiler
                                                @else
                                                    an jeden Helfer eine E-Mail
                                                @endif
                                                versendet.
                                                Soll wirklich eine E-Mail an alle Helfer versendet werden?

                                                <br>
                                                <br>
                                                <small>Nur einmal klicken um Mehrfachversand zu vermeiden - auch wenn es mal etwas länger dauert</small>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ action('HomeController@sendServicePDF')}}" class="btn btn-link waves-effect">Dienstplan E-Mail an alle Versenden</a>
                                                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="hilfe_with_icon_title">
                            <b>Hilfe und Ideen</b>
                            <ul>
                                <li>Fehlt für eure Gliederung die entsprechende Funktion oder hast du eine Verbesserung?</li>
                                <li>Hast du einen Fehler in der Software entdeckt?</li>
                                <li>Benötigt ihr Hilfe oder kommt irgendwo nicht weiter? </li>
                            </ul>
                            <a href="mailto:{{env("impressum.mail")}}?subject=Dienstplan%20Anfrage&amp;body=Hallo%20{{env("impressum.name")}},">
                                <button type="button" class="btn btn-dlrg waves-effect">
                                    <i class="material-icons">contact_mail</i>
                                    <span> Schreibt mir gerne</span>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Übersicht und Statistik von {{Auth::user()->currentclient()->name}} ({{Auth::user()->currentclient()->Season()['from']->isoFormat('DD. MMMM Y')}} - {{Auth::user()->currentclient()->Season()['to']->isoFormat('DD. MMMM Y')}})</h2>
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
                        @if(\Illuminate\Support\Facades\Auth::user()->currentclient()->module_training)
                        <div class="info-box bg-grey hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">Teilgenommene Fortbildungen</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_user_past_training}}" data-speed="1000" data-fresh-interval="20">{{$positions_user_past_training}}</div>
                            </div>
                        </div>
                        @endif
                        <div class="info-box bg-blue hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">event_available</i>
                            </div>
                            <div class="content">
                                <div class="text">Geleistete Dienste aller Helfenden</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_total_past}}" data-speed="1000" data-fresh-interval="20">{{$positions_total_past}}</div>
                            </div>
                        </div>
                        @if(\Illuminate\Support\Facades\Auth::user()->currentclient()->module_training)
                        <div class="info-box bg-grey hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">event_available</i>
                            </div>
                            <div class="content">
                                <div class="text">Fortbildungen aller Helfender</div>
                                <div class="number count-to" data-from="0" data-to="{{$positions_total_past_training}}" data-speed="1000" data-fresh-interval="20">{{$positions_total_past_training}}</div>
                            </div>
                        </div>
                        @endif
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
                                        {{$news->title}} <small>{{$news->created_at->isoFormat('ddd  DD.MM.YY H:mm')}} @if(isset($news->user))von {{$news->user->first_name}} {{$news->user->name}} @endif</small>
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
