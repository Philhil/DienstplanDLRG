@extends('_layouts.base')

@section('body')
    <style>
        /* Shared */
        .loginBtn {
            box-sizing: border-box;
            position: relative;
            /* width: 13em;  - apply for fixed size */
            margin: 0.2em;
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


        /* Facebook */
        .loginBtn--facebook {
            background-color: #4C69BA;
            background-image: linear-gradient(#4C69BA, #3B55A0);
            /*font-family: "Helvetica neue", Helvetica Neue, Helvetica, Arial, sans-serif;*/
            text-shadow: 0 -1px 0 #354C8C;
        }
        .loginBtn--facebook:before {
            border-right: #364e92 1px solid;
            background: url('{{URL::asset('/images/icon_facebook.png')}}') 6px 6px no-repeat;
        }
        .loginBtn--facebook:hover,
        .loginBtn--facebook:focus {
            background-color: #5B7BD5;
            background-image: linear-gradient(#5B7BD5, #4864B1);
        }

        /* Google */
        .loginBtn--google {
            /*font-family: "Roboto", Roboto, arial, sans-serif;*/
            background: #DD4B39;
        }
        .loginBtn--google:before {
            border-right: #BB3F30 1px solid;
            background: url('{{URL::asset('/images/icon_google.png')}}') 6px 6px no-repeat;
        }
        .loginBtn--google:hover,
        .loginBtn--google:focus {
            background: #E74B37;
        }
    </style>
    <body class="login-page">

    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>DLRG</b> Stuttgart</a>
            <small>Online Dienstplan</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_in" method="POST" class="form-horizontal" action="{{ route('login') }}">
                    <div class="msg">Anmelden um loszulegen</div>
                    {{ csrf_field() }}

                    <div class="input-group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="email" id="email"  class="form-control" name="email" placeholder="Benutzername" value="{{ old('email') }}" required autofocus>
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
                            <input type="checkbox" name="rememberme" {{ old('remember') ? 'checked' : '' }} id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Angemeldet bleiben</label>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">Anmelden</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <button type="button" class="loginBtn loginBtn--facebook" onclick="location.href='redirect';">
                                Facebook Login
                            </button>
                        </div>
                    </div>

                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="{{ route('register') }}">Registrieren!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="{{ route('password.request') }}">Passwort vergessen?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <script src="js/pages/examples/sign-in.js"></script>
    </body>
@endsection