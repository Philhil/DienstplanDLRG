<!-- Left Sidebar -->
<aside id="leftsidebar" class="sidebar">
    <!-- User Info -->
    <div class="user-info">
        <div class="image">
            <img src="../../images/user.png" width="48" height="48" alt="User" />
        </div>
        <div class="info-container">
            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Auth::user()->first_name}} {{Auth::user()->name}}</div>
            <div class="email">{{Auth::user()->email}}</div>
            <div class="btn-group user-helper-dropdown">
                <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                <ul class="dropdown-menu pull-right">
                    <li><a href="{{action('UserController@show', \Illuminate\Support\Facades\Auth::user()->id)}}"><i class="material-icons">person</i>Profil</a></li>
                    <li role="seperator" class="divider"></li>
                    <li><a href="{{ route('logout') }}"><i class="material-icons">input</i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- #User Info -->
    <!-- Menu -->
    <div class="menu">
        <ul class="list">
            <li class="header {{active('user.show')}}">Navigation</li>
            <li class="{{active('home')}}">
                <a href="{{ action('HomeController@index') }}">
                    <i class="material-icons">home</i>
                    <span>Home</span>
                </a>
            </li>
            <li class="{{active('service.index')}}">
                <a href="{{ action('ServiceController@index') }}">
                    <i class="material-icons">assignment</i>
                    <span>Dienste</span>
                </a>
            </li>
            <li class="{{active('news.index')}} {{active('news.edit')}}">
                <a href="{{ action('NewsController@index') }}">
                    <i class="material-icons">forum</i>
                    <span>Nachrichten</span>
                </a>
            </li>
            @can('administration')
            <li class="header {{active('service.edit')}}">Administration</li>

            <li class="{{active('user.*')}}">
                <a href="{{ action('UserController@index') }}">
                    <i class="material-icons">person</i>
                    <span>Benutzer</span>
                </a>
            </li>
            <li class="{{active('position.list_notAuthorized')}}">
                <a href="{{action('PositionController@index_notAuthorized')}}">
                    <i class="material-icons">check_circle</i>
                    <span>Dienste bestätigen</span>
                </a>
            </li>
            <li class="{{active('service.create')}}">
                <a href="{{ action('ServiceController@create') }}">
                    <i class="material-icons">note_add</i>
                    <span>Dienst anlegen</span>
                </a>
            </li>
            <li class="{{active('news.create')}}">
                <a href="{{ action('NewsController@create') }}">
                    <i class="material-icons">chat</i>
                    <span>Nachricht erstellen</span>
                </a>
            </li>
            <li class="{{active('qualification.*')}}">
                <a href="{{ action('QualificationController@index') }}">
                    <i class="material-icons">local_activity</i>
                    <span>Qualifikationen</span>
                </a>
            </li>
            @endcan
        </ul>
    </div>
    <!-- #Menu -->
    <!-- Footer -->
    <div class="legal">
        <div class="copyright">
            Design: &copy; 2016 - 2017 <a href="https://github.com/gurayyarar/AdminBSBMaterialDesign">AdminBSB - Material Design</a>. <br>
            Programming: &copy; 2017 - <?php echo date('Y') ?> <a href="https://github.com/Philhil/DienstplanDLRG">Philippe Käufer</a>.
        </div>
        <div class="version">
            <b>Version: </b> 1.0.5
        </div>
    </div>
    <!-- #Footer -->
</aside>
<!-- #END# Left Sidebar -->