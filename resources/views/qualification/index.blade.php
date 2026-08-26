@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Qualifikationen
                    <small>Positionen können beim Anlegen von Diensten mit den hier definierten Qualifikationen belegt werden.</small>
                </h2>
                <ul class="header-dropdown m-r--5">
                    <a href="{{action('QualificationController@create')}}">
                        <button type="button" class="btn btn-success waves-effect">
                            <i class="material-icons">playlist_add</i>
                            <span>Hinzufügen</span>
                        </button>
                    </a>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Abkürzung</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($qualifications as $qualification)
                        <tr>
                            <td>{{$qualification->name}}</td>
                            <td>{{$qualification->short}}</td>
                            <td>
                                <a href="/qualification/{{$qualification->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>

                                {{ html()->form('DELETE', '/qualification/'.$qualification->id)->attribute('style = "display:inline-block"')->open() }}
                                <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                    <i class="material-icons">delete</i>
                                </button>
                                {{ html()->form()->close() }}
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
        });
    </script>
@endsection