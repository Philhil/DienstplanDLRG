@extends('_layouts.base')

@section('body')
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