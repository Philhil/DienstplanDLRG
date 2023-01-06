@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Kategorien
                    <small>Kategorien können u.a. Nutzern, Diensten oder Trainings zugeordnet werden und helfen beim filtern oder Abbonieren von News.
                    </small>
                </h2>
                <ul class="header-dropdown m-r--5">
                    <a href="{{action('TagController@create')}}">
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
                        <th>Farbe</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($tags as $tag)
                        <tr class="strikeout">
                            <td>
                                <p>{{$tag->name}}</p>
                            </td>
                            <td>
                                <input type="color" value="{{$tag->color}}" disabled="disabled">
                            </td>
                            <td>
                                <a href="/tag/{{$tag->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>

                                {{ Form::open(['url' => '/tag/'.$tag->id, 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                    <i class="material-icons">delete</i>
                                </button>
                                {{ Form::close() }}
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