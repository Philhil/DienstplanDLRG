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
                                    <li><a href="service/{{$service->id}}/edit" class=" waves-effect waves-block"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons">delete</i> Löschen</a></li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                @foreach($service->positions as $position)
                                    <th>{{$position->qualification->name}}</th>
                                @endforeach
                            </tr>
                            <tr>

                                @foreach($service->positions as $position)
                                    <td>
                                        @if(isset($position->user))
                                            <span class="badge @if(!$position->isauthorized) bg-orange @elseif($position->user->id == \Illuminate\Support\Facades\Auth::user()->id) bg-light-green @else bg-green @endif">
                                                {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin() && !$position->isauthorized)
                                                    <button type="button" class="btn btn-xs bg-green btn-authorize" positionid="{{$position->id}}"><i class="material-icons">check</i></button>
                                                @endif
                                            </span>
                                        @elseif(\Illuminate\Support\Facades\Auth::user()->hasqualification($position->qualification()->first()->id) && !$service->hasUserPositions(Auth::user()->id))
                                            <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}"><i class="material-icons">touch_app</i> Eintragen</button></button>
                                        @else
                                            <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">touch_app</i> Eintragen</button></button>
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

                            if (data.isauthorized == "true" || data.isauthorized == true) {
                                $(tr).html('<span class="badge bg-light-green">{{substr(\Illuminate\Support\Facades\Auth::user()->first_name, 0, 1)}}. {{\Illuminate\Support\Facades\Auth::user()->name}}</span>');
                            } else {
                                $(tr).html('<span class="badge bg-orange">{{substr(\Illuminate\Support\Facades\Auth::user()->first_name, 0, 1)}}. {{\Illuminate\Support\Facades\Auth::user()->name}}</span>');
                            }
                        }
                    }
                });
            });

            $('.btn-authorize').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('positionid')+'/authorize',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(data){
                        if (data == "false") {
                            showNotification("alert-warning", "Fehler beim freigeben", "top", "center", "", "");
                        } else {
                            showNotification("alert-success", "Zuordnung freigegeben", "top", "center", "", "");

                            span = $(".btn-authorize[positionid="+data.id+"]").parent();
                            $(".btn-authorize[positionid="+data.id+"]").remove();

                            $(span).removeClass('bg-orange');
                            $(span).addClass('bg-green');
                        }
                    }
                });
            });
        });
    </script>
@endsection