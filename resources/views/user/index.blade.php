@extends('_layouts.application')

@section('head')
    <link rel="stylesheet" href="/plugins/bootstrap-table/bootstrap-table.min.css">
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
                <table data-toggle="table" class="table table-striped table-hover" data-show-toggle="true"
                       data-show-columns="true"
                       data-search="true" data-search-highlight="true" data-show-search-clear-button="true"
                       data-cookie="true" data-cookie-id-table="userView"
                       data-filter-control="true"
                       data-show-export="true"
                       @if(!Browser::isDesktop())
                       data-card-view="true"
                       @endif
                >
                    <thead>
                    <tr>
                        <th data-sortable="true" data-field="Name" data-filter-control="input">Name</th>
                        <th data-sortable="true" data-field="Vorname" data-filter-control="input">Vorname</th>
                        <th data-field="mail">E-Mail</th>
                        @foreach($qualifications as $qualification)
                            <th data-field="quali_{{$qualification->short}}" data-filter-control="select" data-visible="false">{{$qualification->short}}</th>
                        @endforeach
                        <th data-field="action" data-force-hide="true">Aktion</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->name}}</td>
                            <td>{{$user->first_name}}</td>
                            <td>{{$user->email}}</td>
                            @foreach($qualifications as $qualification)
                                <td>
                                    @if($user->qualifications->contains('id', $qualification->id))
                                        ✓
                                    @else
                                        ✗
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <a href="/user/{{$user->id}}/edit">
                                    <button type="button" class="btn btn-warning waves-effect">
                                        <i class="material-icons">mode_edit</i>
                                    </button>
                                </a>

                                @if(Auth::user()->isSuperAdmin() && \Illuminate\Support\Facades\Route::current()->getPrefix() == '/superadmin')
                                    {{ Form::open(['url' => '/user/'.$user->id, 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                    <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                        <i class="material-icons">delete</i>
                                    </button>
                                    {{ Form::close() }}
                                @endif

                                @if($user->clients->contains('id', Auth::user()->currentclient_id) && $user->client_user->contains(
                                function($value, $key) use ($authuser) {
                                        return (
                                                   $value->client_id == $authuser->currentclient_id &&
                                                   $value->approved == false
                                               );
                                }))
                                     <a href="{{action('UserController@approve_user', $user->id)}}">
                                        <button type="button" class="btn btn-success waves-effect">
                                            <i class="material-icons">check</i>
                                        </button>
                                    </a>
                                @elseif($user->id != Auth::user()->id)
                                    {{ Form::open(['url' => '/client/'. Auth::user()->currentclient_id .'/removeuser/'.$user->id, 'method' => 'delete', 'style'=>'display:inline-block']) }}
                                    <button type="submit" class="btn btn-danger waves-effect btn-delete">
                                        <i class="material-icons">highlight_off</i>
                                    </button>
                                    {{ Form::close() }}
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
    <script src="/plugins/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/cookie/bootstrap-table-cookie.min.js"></script>

    <script src="/plugins/jquery-tableexport/tableExport.min.js"></script>
    <script src="/plugins/bootstrap-table/extensions/export/bootstrap-table-export.js"></script>

    <script>
        $( document ).ready(function() {
            $('.table').bootstrapTable({});
        });
    </script>
@endsection