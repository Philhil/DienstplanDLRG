<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="">DLRG Stuttgart</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <!-- Notifications -->
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">notifications</i>
                        <span class="label-count">{{\Illuminate\Support\Facades\Auth::user()->authorizedpositions()->count()}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Zukünftige Dienste von <br>{{\Illuminate\Support\Facades\Auth::user()->first_name}} {{\Illuminate\Support\Facades\Auth::user()->name}}</li>
                        <li class="body">
                            <ul class="menu">
                                @foreach(\Illuminate\Support\Facades\Auth::user()->authorizedpositions()->get() as $position)
                                <li>
                                    <a href="javascript:void(0);">
                                        <div class="icon-circle bg-green">
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
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 254px;"><ul class="menu tasks" style="overflow: hidden; width: auto; height: 254px;">
                                @foreach(\Illuminate\Support\Facades\Auth::user()->qualifications as $qualification)
                                    <li>
                                        <a href="javascript:void(0);" class=" waves-effect waves-block">
                                            <h4>
                                                {{$qualification->name}}
                                            </h4>
                                        </a>
                                    </li>
                                @endforeach
                                </ul><div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.498039); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 0px; z-index: 99; right: 1px;"></div><div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
                        </li>
                        <li class="footer">
                            <a href="javascript:void(0);" class=" waves-effect waves-block"></a>
                        </li>
                    </ul>
                </li>
                <!-- #END# MyQualifications-->
                <li><a href="{{action('UserController@show', \Illuminate\Support\Facades\Auth::user()->id)}}"><i class="material-icons">person</i></a></li>

                <li class="pull-right"><a href="{{ route('logout') }}" class="js-right-sidebar" data-close="true"><i class="material-icons">input</i></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->