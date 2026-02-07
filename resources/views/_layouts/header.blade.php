<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
                @if(empty(Auth::user()->currentclient())) <a class="navbar-brand" href="/home">Dienstplan</a>
                @else
                    @if(Illuminate\Support\Facades\Auth::user()->clients()->count() > 1)
                    <ul class="header-dropdown" style="list-style: none;">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle navbar-brand" aria-expanded="false" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                {{Illuminate\Support\Facades\Auth::user()->currentclient()->name}} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" style="margin-left: 15px; margin-top: 40px !important;">
                                @foreach(Illuminate\Support\Facades\Auth::user()->clients()->get() as $client)
                                    <li><a href="/changeclient/{{$client->id}}" class=" waves-effect waves-block">{{$client->name}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                    @else
                    <a class="navbar-brand" href="/home">{{Auth::user()->currentclient()->name}}</a>
                    @endif
                @endif
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">

                <li><a href="{{ action('ClientController@apply') }}" class="" data-close="true" role="button"><i class="material-icons">group_add</i></a></li>

                <!-- Notifications -->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">notifications</i>
                        <span class="label-count">{{\Illuminate\Support\Facades\Auth::user()->authorizedpositions_future()->count()}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Zukünftige Dienste von <br>{{\Illuminate\Support\Facades\Auth::user()->first_name}} {{\Illuminate\Support\Facades\Auth::user()->name}}</li>
                        <li class="body">
                               <ul class="menu">
                                    @foreach(\Illuminate\Support\Facades\Auth::user()->authorizedpositions_future()->get() as $position)
                                    <li>
                                        <a href="javascript:void(0);">
                                            <div class="icon-circle bg-green fa-sm">
                                                <i class="material-icons">forward</i>
                                            </div>
                                            <div class="menu-info">
                                                <h4> {{$position->service->date->format('d.m.Y')}} <small>{{$position->comment}}</small></h4>
                                                <p>
                                                    <i class="material-icons">recent_actors</i> {{$position->qualification->name}}
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach
                               </ul>

                        </li>
                        <li class="footer">
                            <a href="{{ action('ServiceController@index') }}">Alle Dienste</a>
                        </li>
                    </ul>
                </li>
                <!-- #END# MyServices -->
                <!-- MyQualifications-->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">local_activity</i>
                        <span class="label-count">{{\Illuminate\Support\Facades\Auth::user()->qualifications->count()}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Qualifikationen</li>
                        <li class="body">
                            <ul class="menu tasks">
                            @foreach(\Illuminate\Support\Facades\Auth::user()->qualifications as $qualification)
                                <li>
                                    <a href="javascript:void(0);" class=" waves-effect waves-block">
                                        <h4>
                                            {{$qualification->name}}
                                        </h4>
                                    </a>
                                </li>
                            @endforeach
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="javascript:void(0);" class=" waves-effect waves-block"></a>
                        </li>
                    </ul>
                </li>
                <!-- #END# MyQualifications-->
                <li><a href="{{action('UserController@show', \Illuminate\Support\Facades\Auth::user()->id) }}"><i class="material-icons">person</i></a></li>
                @can('administration')
                    <li><a href="{{action('ClientController@show', \Illuminate\Support\Facades\Auth::user()->currentclient_id) }}"><i class="material-icons">group</i></a></li>
                @endcan
                <li class="pull-right"><a href="{{ route('logout') }}" class="js-right-sidebar" data-close="true"><i class="material-icons">input</i></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->