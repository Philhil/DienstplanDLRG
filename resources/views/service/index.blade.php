@extends('_layouts.application')

@section('head')
@endsection

@section('content')

    @foreach($services as $service)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
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
                <div class="body">
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
                                        @elseif(\Illuminate\Support\Facades\Auth::user()->hascandidate($position->id))
                                            <button type="button" class="btn bg-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">check_circle</i>
                                                bereits gemeldet
                                            </button>
                                            {{-- Has user this qualification? and Has user already a Position at this service --}}
                                        @elseif(\Illuminate\Support\Facades\Auth::user()->hasqualification($position->qualification()->first()->id) && !$service->hasUserPositions(Auth::user()->id))
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
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {
            $('.btn-subscribe').on('click', function () {
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
                                $(tr).html('<button type="button" class="btn bg-orange waves-effect btn-subscribe" positionid="7" disabled=""><i class="material-icons">check_circle</i>bereits gemeldet</button>');
                            }
                        }
                    }
                });
            });

            @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
            $('.btn-authorize').on('click', function () {
                $(this).removeAttr('positionid');

                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('candidateid')+'/authorize',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(data){
                        if (data == "false") {
                            showNotification("alert-warning", "Fehler beim freigeben", "top", "center", "", "");
                        } else {
                            showNotification("alert-success", "Zuordnung freigegeben", "top", "center", "", "");

                            $($(".btn-authorize[positionid="+data.id+"]").closest('row')).remove();

                            $($(".btn-subscribe[positionid="+data.id+"]").parent()).find("span").removeClass('bg-orange').addClass('bg-green').find('button').remove();
                            $(".btn-subscribe[positionid="+data.id+"]").remove();
                        }
                    }
                });
            });
            @endif
        });
    </script>
@endsection