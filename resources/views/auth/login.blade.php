@extends('_layouts.base')

@section('head')
    <!-- Login Page Css -->
    <link href="/css/login.css" rel="stylesheet">

    <style>
        .loginBtn {
            box-sizing: border-box;
            position: relative;
            /* width: 13em;  - apply for fixed size */
            padding: 0 15px 0 46px;
            border: none;
            text-align: left;
            line-height: 34px;
            white-space: nowrap;
            border-radius: 0.2em;
            font-size: 16px;
            color: #FFF;
        }
        .loginBtn:before {
            content: "";
            box-sizing: border-box;
            position: absolute;
            top: 0;
            left: 0;
            width: 34px;
            height: 100%;
        }
        .loginBtn:focus {
            outline: none;
        }
        .loginBtn:active {
            box-shadow: inset 0 0 0 32px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('body')
    <body class="login-background">

    <div class="login-page">
        <div class="login-box">
            <div class="logo card-header">
                <a href="javascript:void(0);"><b>Dienstplan</b></a>
                <small>Online</small>
            </div>
            <div class="card">
                <div class="body">
                    <form id="sign_in" method="POST" class="form-horizontal" action="{{ route('login') }}">
                        <div class="msg">Anmelden um loszulegen</div>

                        @if(env("IS_DEMO", false))
                        <h4 class="text-center btn-nicered">!! Demo Portal !!</h4>
                        <div class="msg">Admin: admin.demodienstplan@philhil.de <br> Passwort: admin</div>
                        <div class="msg">User: user.demodienstplan@philhil.de <br> Passwort: user</div>
                        @endif

                        {{ csrf_field() }}

                        <noscript>
                            <div class="msg">
                                <span class="badge bg-red">JavaScript muss für diese Seite aktiviert sein!</span>
                            </div>
                        </noscript>

                        <div class="input-group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <span class="input-group-addon">
                                <i class="material-icons">person</i>
                            </span>
                            <div class="form-line">
                                <input type="email" id="email"  class="form-control" name="email" placeholder="E-Mail" value="{{ old('email') }}" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="input-group form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <span class="input-group-addon">
                                <i class="material-icons">lock</i>
                            </span>
                            <div class="form-line">
                                <input type="password" id="password"  class="form-control" name="password" placeholder="Passwort" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-8 p-t-5">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} id="remember" class="filled-in chk-col-pink">
                                <label for="remember">Angemeldet bleiben</label>
                            </div>
                            <div class="col-xs-4">
                                <button class="btn btn-block btn-nicered waves-effect" type="submit">Anmelden</button>
                            </div>
                        </div>

                        <div class="row m-t-15 m-b--20">
                            <div class="col-xs-6">
                                <a href="{{ route('register') }}"><button type="button" class="btn btn-default waves-effect">Registrieren!</button></a>
                            </div>
                            <div class="col-xs-6 align-right">
                                <a href="{{ route('password.request') }}"><button type="button" class="btn btn-default waves-effect">Passwort vergessen?</button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if(!env("OFFER.DIENSTPLAN", true))
            <div class="pull-right top-buffer footer-register">
                <a href="/impressum">Impressum</a> <a>|</a>
                <a href="/datenschutz">Datenschutz</a>
            </div>
        @endif
    </div>

    @if(env("OFFER.DIENSTPLAN", true))
    <footer class="text-center footer-login footer-login-buttom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <a href="{{ action('OrderController@index') }}">
                        <button type="button" class="btn btn-nicered waves-effect">
                            <i class="material-icons">flight_takeoff</i>
                            <span>Hol dir den Dienstplan für deine Gliederung!</span>
                        </button>
                    </a>
                </div>

                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h2></h2>
                    <a href="/impressum">Impressum</a> | <a href="/datenschutz">Datenschutz</a>
                </div>
            </div>
        </div>
    </footer>
    @endif

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>

    </body>
@endsection
