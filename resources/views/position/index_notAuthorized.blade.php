@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Dienste bestätigen
                </h2>
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Qualifikation</th>
                        <th>Name</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($positions as $position)
                        <tr>
                            <td>{{$position->service->date->format('d m Y')}}</td>
                            <td>{{$position->qualification->name}}</td>
                            <td>{{$position->user->first_name}} {{$position->user->name}}</td>
                            <td>
                                <button type="button" positionid="{{$position->id}}" class="btn btn-success btn-authorize waves-effect">
                                    <i class="material-icons">check</i>
                                </button>

                                <button type="submit" positionid="{{$position->id}}" class="btn btn-danger waves-effect btn-deauthorize">
                                    <i class="material-icons">delete</i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('post_body')
    <script>
        $( document ).ready(function() {
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

                            $(".btn-authorize[positionid="+data.id+"]").parent().parent().remove();
                        }
                    }
                });
            });

            $('.btn-deauthorize').on('click', function () {
                $.ajax({
                    type: "POST",
                    url: '/position/'+$(this).attr('positionid')+'/deauthorize',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(data){
                        if (data == "false") {
                            showNotification("alert-warning", "Fehler beim löschen der Freigabe", "top", "center", "", "");
                        } else {
                            showNotification("alert-success", "Zuordnung aufgehoben", "top", "center", "", "");

                            $(".btn-deauthorize[positionid="+data.id+"]").parent().parent().remove();
                        }
                    }
                });
            });
        });
    </script>
@endsection