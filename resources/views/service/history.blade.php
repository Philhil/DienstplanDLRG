@extends('_layouts.application')

@section('head')
@endsection

@section('content')
<h1>Dienst Historie</h1>
{{-- Pagination --}}
<div class="d-felx justify-content-center">
  {{ $services->links() }}
</div>

    @foreach($services as $service)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <span class="anchor" id="service{{$service->id}}"></span>
            <div class="card">
                <div class="header @if(!empty($service->finalized_at)) bg-green @else bg-red @endif">
                    <h2 data-toggle="collapse" data-target="#card_{{$service->id}}">
                        <span class="glyphicon glyphicon-collapse-down float-left"></span>

                        {{$service->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr @if(!empty($service->dateEnd)) - {{$service->dateEnd->isoFormat('DD.MM.YY H:mm')}} Uhr @endif
                        <small>{{$service->comment}}</small>
                        @if(!empty($service->location)) <small>{{$service->location}}</small> @endif
                    </h2>

                    <ul class="header-dropdown m-r--5">

                        @if($isAdmin)
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="{{action('ServiceController@edit', $service->id) }}" class="waves-effect waves-block bg-orange"><i class="material-icons">mode_edit</i>Bearbeiten</a></li>
                                <li><a href="{{action('ServiceController@finalize', $service->id) }}" class="waves-effect waves-block bg-green"><i class="material-icons">done</i>Abschließen</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>

                </div>
                <div class="body collapse @if(Browser::isDesktop() && !in_array($service->id, $servicesHoliday)) in @endif" id="card_{{$service->id}}">

                    @if(Browser::isDesktop())
{{-- Start Desktop --}}
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <tr>
                                    @foreach($service->positions as $position)
                                        <th @if($position->requiredposition) class="font-underline"@endif>{{$position->qualification->name}}</th>
                                    @endforeach
                                </tr>
                                <tr>

                                    @foreach($service->positions as $position)
                                        <td>
                                            {{-- show candidates if nobody is approved --}}
                                            @if(!isset($position->user))
                                                @if($isAdmin)
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
                                                @endif
                                            @endif
                                            @if(isset($position->user))
                                                <span class="badge @if($position->user->id == $user->id) bg-light-green @else bg-green @endif">
                                                    {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                                </span>
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

                                        <b @if($position->requiredposition) class="font-underline"@endif>{{$position->qualification->name}}:</b>
                                        <div class="panel-body">

                                            {{-- show candidates if nobody is approved --}}
                                            @if(!isset($position->user))
                                                @if($isAdmin)
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
                                                @endif
                                            @endif
                                            @if(isset($position->user))
                                                <span class="badge @if($position->user->id == $user->id) bg-light-green @else bg-green @endif">
                                                    {{substr ($position->user->first_name, 0, 1)}}. {{$position->user->name}}
                                                </span>
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

    {{-- Pagination --}}
    <div class="d-felx justify-content-center">
        {{ $services->links() }}
    </div>

@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {
            @if($isAdmin)
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
