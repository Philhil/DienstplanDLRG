@extends('_layouts.application')

link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

@section('head')

@endsection

@section('content')

    <div class="container">

        <h2>Ausbildungskalender</h2>
        <p class="lead">
            Hier findet ihr alles Aus-/Fortbildungen der DLRG Stuttgart
        </p>

        <hr />

        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>Datum und Uhrzeit</th>
                        <th>Titel</th>
                        <th>Verantwortlicher</th>
                        @if($isAdmin || $isTrainingEditor)
                            <th>Aktion</th>
                        @endif
                    </tr>
                </thead>
                @foreach($calendars as $calendar)
                    <tbody>
                        <tr>
                            <td class="agenda-date" class="active" rowspan="1">
                                <div class="date">{{$calendar->date->isoFormat('ddd  DD.MM.YY H:mm')}} Uhr</div>
                            </td>
                            <td class="agenda-events">
                                <div class="agenda-event">
                                    {{$calendar->title}}
                                </div>
                            </td>
                            <td>
                                {{$calendar->verantwortlicher}}
                            </td>
                            @if($isAdmin || $isTrainingEditor)
                                <td>
                                    <a href="{{action('CalendarController@edit', $calendar->id)}}" class="btn-warning waves-effect"><i class="material-icons">mode_edit</i>Bearbeiten</a>
                                    <a href="{{action('CalendarController@destroy', $calendar->id)}}" class="btn-danger waves-effect"><i class="material-icons">delete</i>Löschen</a>
                                </td>
                            @endif
                        </tr>
                    </tbody>
                @endforeach
            </table>
        </div>
    </div>
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