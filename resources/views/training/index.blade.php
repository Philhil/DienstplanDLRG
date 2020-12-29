@extends('_layouts.application')

@section('head')

@endsection

@section('content')

    @foreach($trainings as $training)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <span class="anchor" id="training{{$training->id}}"></span>
            <div class="card">
                <div class="header bg-blue-grey">
                    <h2 data-toggle="collapse" data-target="#card_{{$training->id}}">
                        <span class="glyphicon glyphicon-collapse-up float-left"></span>
                        {{$training->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr <small>{{$training->title}}</small>
                    </h2>

                    @if($isAdmin || $isTrainingEditor)
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="{{action('TrainingController@edit', $training->id) }}" class="btn-warning waves-effect"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                    <li><a href="{{action('TrainingController@destroy', $training->id) }}" class="btn-danger waves-effect"><i class="material-icons">delete</i> Löschen</a></li>
                                </ul>
                            </li>
                        </ul>
                    @endif

                </div>
                <div class="body collapse @if(Browser::isDesktop()) in @endif" id="card_{{$training->id}}">

                    @if(Browser::isDesktop())
{{-- Start Desktop --}}
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <tr>
                                    @foreach($training->positions as $position)
                                        <th @if($position->requiredposition) class="font-underline"@endif>@if(!empty($position->qualification)){{$position->qualification->name}}@endif</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    {{-- List all Positions --}}
                                    @foreach($training->positions as $position)
                                        {{-- show positions desc and list all teilnehmer --}}
                                        <td>
                                            {{-- Has user this qualification? and has user NOT already a Position at this training --}}
                                            @if((empty($position->qualification) || $user->qualifications->contains('id', $position->qualification->id)) && !$training->training_users->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}"><i class="material-icons">touch_app</i>
                                                    @if($training->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">touch_app</i>
                                                    @if($training->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @endif
                                            @if($isAdmin || $isTrainingEditor)
                                                {{ Form::open(['url' => '/position/'. $position->id .'/position_user/', 'method' => 'get', 'style'=>'display:inline-block']) }}
                                                <button type="submit" class="btn btn-xs bg-deep-orange waves-effect btn-delete">
                                                    <i class="material-icons">playlist_add</i>
                                                </button>
                                                {{ Form::close() }}
                                            @endif

                                            @if($position->comment)
                                                <br>
                                                <smal>({{$position->comment}})</smal>
                                            @endif

                                            {{-- List all training_user_pos --}}
                                            @foreach($training->training_users as $training_users)
                                                @if($training_users->position_id == $position->id)
                                                    <br>
                                                    <span class="badge @if($training_users->user->id == \Illuminate\Support\Facades\Auth::user()->id) bg-light-green @else bg-green @endif m-t-5">
                                                        {{substr ($training_users->user->first_name, 0, 1)}}. {{$training_users->user->name}}
                                                        {{-- if is user -> possibility to remove him self --}}
                                                        @if(($training_users->user->id == \Illuminate\Support\Facades\Auth::user()->id && !($training_users->training->date)->isToday()) || $isAdmin || $isTrainingEditor)
                                                            {{ Form::open(['url' => '/training/training_user/'. $training_users->id .'/delete/', 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                                            <button type="submit" class="btn btn-xs btn-warning waves-effect btn-delete">
                                                                <i class="material-icons">delete</i>
                                                            </button>
                                                            {{ Form::close() }}
                                                        @endif
                                                    </span>
                                                @endif
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </div>
{{-- End Desktop--}}
                    @else
{{-- Start Mobile--}}
                        <div class="row display-flex container-fluid">

                            @foreach($training->positions as $position)
                                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                    <div class="panel panel-default">

                                        <b @if($position->requiredposition) class="font-underline"@endif>@if(!empty($position->qualification)){{$position->qualification->name}}@endif</b>
                                        <div class="panel-body">

                                            {{-- Has user this qualification? and Has user NOT already a Position at this training --}}
                                            @if((empty($position->qualification) || $user->qualifications->contains('id', $position->qualification->id)) && !$training->positions->contains('user', \Illuminate\Support\Facades\Auth::user()))
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}"><i class="material-icons">touch_app</i>
                                                    @if($training->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" class="btn bg-deep-orange waves-effect btn-subscribe" positionid="{{$position->id}}" disabled><i class="material-icons">touch_app</i>
                                                    @if($training->hastoauthorize) Melden
                                                    @else Eintragen
                                                    @endif
                                                </button>
                                            @endif
                                            @if($isAdmin || $isTrainingEditor)
                                                {{ Form::open(['url' => '/position/'. $position->id .'/position_user', 'method' => 'get', 'style'=>'display:inline-block']) }}
                                                <button type="submit" class="btn btn-xs bg-deep-orange waves-effect btn-delete">
                                                    <i class="material-icons">playlist_add</i>
                                                </button>
                                                {{ Form::close() }}
                                            @endif
                                            @if($position->comment)
                                                <br>
                                                <smal>({{$position->comment}})</smal>
                                            @endif

                                            {{-- List all training_user_pos --}}
                                            @foreach($training->training_users as $training_users)
                                                @if($training_users->position_id == $position->id)
                                                    <br>
                                                    <span class="badge @if($training_users->user->id == \Illuminate\Support\Facades\Auth::user()->id) bg-light-green @else bg-green @endif m-t-5">
                                                        {{substr ($training_users->user->first_name, 0, 1)}}. {{$training_users->user->name}}
                                                    </span>
                                                @endif
                                            @endforeach

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

            //TODO: change to delete
            @if($isAdmin || $isTrainingEditor)
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