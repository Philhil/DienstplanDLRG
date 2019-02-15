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
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    //Background color if approved
                    //Background color if not approved
                    //revert/delete application
                    //apply

                    @foreach($clients as $client)
                        <tr>
                            <td>
                                @if(count($client->client_authuser) > 0 && $client->client_authuser[0]->approved == 1)<i class="material-icons bg-success">check</i>@endif
                                @if(count($client->client_authuser) > 0 && $client->client_authuser[0]->approved == 0)<i class="material-icons">question_answer</i>@endif
                            </td>
                            <td>{{$client->name}}</td>
                            <td>
                                @if(count($client->client_authuser) <= 0)
                                <a href="/client/{{$client->id}}/apply">
                                    <button type="button" class="btn btn-success waves-effect">
                                        <i class="material-icons">check</i>
                                    </button>
                                </a>
                                @elseif($client->client_authuser[0]->approved == 0)
                                    <a href="/client/{{$client->id}}/apply/revert">
                                        <button type="button" class="btn btn-danger waves-effect">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </a>
                                @endif
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