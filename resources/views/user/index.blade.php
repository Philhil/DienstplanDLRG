@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Benutzer
                </h2>
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Vorname</th>
                        <th>E-Mail</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->name}}</td>
                            <td>{{$user->first_name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <a href="/user/{{$user->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>

                                @if(Auth::user()->isSuperAdmin())
                                    {{ Form::open(['url' => '/user/'.$user->id, 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                    <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                        <i class="material-icons">delete</i>
                                    </button>
                                    {{ Form::close() }}
                                @endif

                                @if(!empty(\App\Client_user::where(['user_id' => $user->id, 'client_id' => Auth::user()->currentclient_id])->first()) && !\App\Client_user::where(['user_id' => $user->id, 'client_id' => Auth::user()->currentclient_id])->first()->approved)
                                    <a href="{{action('UserController@approve_user', $user->id)}}">
                                        <button type="button" class="btn btn-success waves-effect">
                                            <i class="material-icons">check</i>
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