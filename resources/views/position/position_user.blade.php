@extends('_layouts.application')

@section('head')
<!-- Materialize design -->
    <link href="/css/materialize.css" rel="stylesheet" />

@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Helfer Zuteilung
                        <small>{{$comment}}</small>
                    </h2>
                    <ul class="pull-right header-dropdown m-r--5">
                        {{ Form::open(['url' => action('TrainingController@index'), 'method' => 'get', 'style'=>'display:inline-block']) }}
                        <button type="submit" class="btn btn-subscribe bg-green waves-effect">
                            <i class="material-icons">save</i>
                        </button>
                        {{ Form::close() }}
                    </ul>
                </div>
                <div class="body table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>NAME</th>
                            <th>VORNAME</th>
                            <th>Qualifikationen</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr @if($selected_users->contains($user->id)) class="bg-cyan"@endif>
                                <th scope="row">
                                    <input type="checkbox" user_id="{{$user->id}}" class="filled-in chk-col-cyan" @if($selected_users->contains($user->id)) checked=""@endif>
                                    <label for="md_checkbox_{{$user->id}}">
                                        {{-- inefficent n+1 querry - but site is not called a lot of times --}}
                                        @if(in_array($parent->id, $user->position_inHolidayList($position)))<i class="material-icons">beach_access</i> Keine Zeit @endif
                                    </label>
                                </th>
                                <td>{{$user->name}}</td>
                                <td>{{$user->first_name}}</td>
                                <td @if($user->qualifications->pluck('id')->contains($position->qualification_id))class="bg-green"@endif>{{$user->qualifications->implode('name', ', ')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {

            $('.table tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('input')) {
                    return;
                }
                $(this).find(':checkbox').trigger('click')
            });

            $("input[type='checkbox']").change(function (e) {
                if ($(this).is(":checked")) {
                    $.ajax({
                        type: "POST",
                        url: '/position/{{$position->id}}/subscribe_user/'+$(this).attr('user_id'),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success : function(user_id){
                            if (user_id == "false") {
                                showNotification("alert-warning", "Fehler beim speichern der Zuordnung", "top", "center", "", "");
                            } else {
                                showNotification("alert-success", "Zuordnung gespeichert", "top", "center", "", "");
                                $("input[user_id="+user_id+"]").closest('tr').addClass("bg-cyan");
                            }
                        }
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: '/position/{{$position->id}}/unsubscribe_user/'+$(this).attr('user_id'),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success : function(user_id){
                            if (user_id == "false") {
                                showNotification("alert-warning", "Fehler beim speichern der Zuordnung", "top", "center", "", "");
                            } else {
                                showNotification("alert-success", "Meldung zurückgenommen", "top", "center", "", "");
                                $("input[user_id="+user_id+"]").closest('tr').removeClass("bg-cyan");
                            }
                        }
                    });
                }
            });

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

        });
    </script>
@endsection