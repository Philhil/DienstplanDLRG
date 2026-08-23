@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Clients
                </h2>
                <form action="{{ action('ClientController@create') }}">
                    <button type="submit" class="pull-right btn btn-danger waves-effect btn-success" id="add_client"><i class="material-icons">add</i><span>Client erstellen</span> </button>
                </form>
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Users</th>
                        <th>Saison Start</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($clients as $client)
                        <tr>
                            <td>{{$client->name}}</td>
                            <td>{{$client->user()->count()}}</td>
                            <td>{{$client->seasonStart->format('jS \\of F ')}}</td>
                            <td>
                                <a href="/client/{{$client->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>

                                {{ html()->form('DELETE', '/client/'.$client->id)->style('display:inline-block')->open() }}
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