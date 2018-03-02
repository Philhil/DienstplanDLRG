@extends('_layouts.application')

@section('head')

@endsection

@section('content')

    @foreach($services as $service)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <span class="anchor" id="service{{$service->id}}"></span>
            <div class="card">
                <div class="header">
                    <h2 data-toggle="collapse" data-target="#card_{{$service->id}}">
                        @if(Browser::isDesktop())
                            <span class="glyphicon glyphicon-collapse-up float-left"></span>
                        @else
                            <span class="glyphicon glyphicon-collapse-down float-left"></span>
                        @endif
                        {{$service->date->format('l d m Y')}} <small>{{$service->comment}}</small>
                    </h2>

                    @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="{{action('ServiceController@edit', $service->id) }}" class=" waves-effect waves-block"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                    <li><a href="{{action('ServiceController@delete', $service->id) }}" class=" waves-effect waves-block"><i class="material-icons">delete</i> Löschen</a></li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="body collapse @if(Browser::isDesktop()) in @endif" id="card_{{$service->id}}">

                    @if(Browser::isDesktop())
{{-- Start Desktop --}}
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <tr>
                                    @foreach($service->positions as $position)
                                        <th>{{$position->qualification->name}}</th>
                                    @endforeach
                                </tr>
                                <tr>

                                    @foreach($service->positions as $position)
                                        <td>
                                            {{-- show candidates if nobody is approved --}}
                                            @if(!isset($position->user))
                                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                                                    @foreach($position->candidatures as $candidate)
                                                        <row>
                                                            <div class="col-md-12">
                                                                <span class="badge bg-orange">
                                                                    {{substr ($candidate->user->first_name, 0, 1)}}. {{$candidate->user->name}}
                                                                    <button type="button" class="btn btn-xs bg-green btn-authorize" positionid="{{$position->id}}" candidateid="{{$candidate->id}}"><i class="material-icons">check</i></button>
                                                                </span>
                                                            </div>
                                                        </row>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-orange">{{count($position->candidatures)}}</span>
                                                @endif
                                            @endif
                                            @if(isset($position->user))
                                                <span class="badge @if($position->user->id == \Illuminate\Support\Facades\Auth::user()->id) bg-light-green @else bg-green @endif">
                                                    {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                                </span>
                                                {{-- user has a Candidature for that pos --}}
                                            @elseif($position->candidatures->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-orange waves-effect btn-unsubscribe" positionid="{{$position->id}}"><i class="material-icons">check_circle</i>
                                                    Meldung zurückziehen
                                                </button>
                                                {{-- Has user this qualification? and Has user NOT already a Position at this service --}}
                                            @elseif($user->qualifications->contains('id', $position->qualification->id) && !$service->positions->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}"><i class="material-icons">touch_app</i>
                                                    @if($service->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">touch_app</i>
                                                    @if($service->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @endif
                                            @if($position->comment)
                                                <br>
                                                <smal>({{$position->comment}})</smal>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </div>
{{-- End Desktop--}}
                    @else
{{-- Start Mobile--}}
                        <div class="row display-flex container-fluid">

                            @foreach($service->positions as $position)
                                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                    <div class="panel panel-default">

                                        <b>{{$position->qualification->name}}:</b>
                                        <div class="panel-body">

                                            {{-- show candidates if nobody is approved --}}
                                            @if(!isset($position->user))
                                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                                                    @foreach($position->candidatures as $candidate)
                                                        <row>
                                                            <div class="col-md-12">
                                                                <span class="badge bg-orange">
                                                                    {{substr ($candidate->user->first_name, 0, 1)}}. {{$candidate->user->name}}
                                                                    <button type="button" class="btn btn-xs bg-green btn-authorize" positionid="{{$position->id}}" candidateid="{{$candidate->id}}"><i class="material-icons">check</i></button>
                                                                </span>
                                                            </div>
                                                        </row>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-orange">{{count($position->candidatures)}}</span>
                                                @endif
                                            @endif
                                            @if(isset($position->user))
                                                <span class="badge @if($position->user->id == \Illuminate\Support\Facades\Auth::user()->id) bg-light-green @else bg-green @endif">
                                                    {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                                </span>
                                                {{-- user has a Candidature for that pos --}}
                                            @elseif($position->candidatures->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-orange waves-effect btn-unsubscribe" positionid="{{$position->id}}"><i class="material-icons">check_circle</i>
                                                    Meldung zurückziehen
                                                </button>
                                                {{-- Has user this qualification? and Has user NOT already a Position at this service --}}
                                            @elseif($user->qualifications->contains('id', $position->qualification->id) && !$service->positions->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}"><i class="material-icons">touch_app</i>
                                                    @if($service->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">touch_app</i>
                                                    @if($service->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @endif
                                            @if($position->comment)
                                                <br>
                                                <smal>({{$position->comment}})</smal>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @endforeach

                        </div>
{{-- end mobile --}}
                    @endif
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {
            $('.card').on('click', '.btn-subscribe', function () {
                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('positionid')+'/subscribe',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(data){
                        if (data == "false") {
                            showNotification("alert-warning", "Fehler beim speichern der Zuordnung", "top", "center", "", "");
                        } else {
                            showNotification("alert-success", "Zuordnung gespeichert", "top", "center", "", "");

                            tr = $(".btn-subscribe[positionid="+data.id+"]").parent();
                            $(".btn-subscribe[positionid="+data.id+"]").remove();

                            if (data.user_id == "null") {
                                $(tr).html('<span class="badge bg-light-green">{{substr(\Illuminate\Support\Facades\Auth::user()->first_name, 0, 1)}}. {{\Illuminate\Support\Facades\Auth::user()->name}}</span>');
                            } else {
                                $(tr).html('<button type="button" class="btn bg-orange waves-effect btn-unsubscribe" positionid="'+data.id+'"><i class="material-icons">check_circle</i>Meldung zurückziehen</button>');
                            }
                        }
                    }
                });
            });

            $('.card').on('click', '.btn-unsubscribe', function () {
                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('positionid')+'/unsubscribe',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(pos_id){
                        if (pos_id == "false") {
                            showNotification("alert-warning", "Fehler beim speichern der Zuordnung", "top", "center", "", "");
                        } else {
                            showNotification("alert-success", "Meldung zurückgenommen", "top", "center", "", "");

                            tr = $(".btn-unsubscribe[positionid="+pos_id+"]").parent();
                            $(".btn-unsubscribe[positionid="+pos_id+"]").remove();

                            $(tr).html('<button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="'+pos_id+'"><i class="material-icons">touch_app</i>Melden</button>');
                        }
                    }
                });
            });

            @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
            $('.btn-authorize').on('click', function () {
                $(this).parent().attr("positionid", $(this).attr('positionid'));
                $(this).remove();

                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('candidateid')+'/authorize',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(data){
                        if (data == "false") {
                            showNotification("alert-warning", "Fehler beim freigeben", "top", "center", "", "");
                            $("span[positionid="+data.id+"]").removeAttr('positionid')
                        } else {
                            showNotification("alert-success", "Zuordnung freigegeben", "top", "center", "", "");

                            //remove unsubscripe btn (if admin is a candidate)
                            $(".btn-unsubscribe[positionid="+data.id+"]").remove();

                            //remove all authorize
                            $($(".btn-authorize[positionid="+data.id+"]").closest('row')).remove();

                            //remove all btn-subscribe
                            $(".btn-subscribe[positionid="+data.id+"]").remove();

                            //set authorized green
                            if(data.user_id == {{\Illuminate\Support\Facades\Auth::user()->id}}) {
                                $("span[positionid="+data.id+"]").removeClass('bg-orange').addClass('bg-light-green');
                            } else {
                                $("span[positionid="+data.id+"]").removeClass('bg-orange').addClass('bg-green');
                            }
                        }
                    }
                });
            });
            @endif
        });


        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-collapse-down").removeClass("glyphicon-collapse-down").addClass("glyphicon-collapse-up");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-collapse-up").removeClass("glyphicon-collapse-up").addClass("glyphicon-collapse-down");
        });
    </script>
@endsection