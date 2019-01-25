@extends('_layouts.base')

@section('body')
<body class="fp-page">
    <div class="fp-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>DLRG</b> Stuttgart</a>
            <small>Online Dienstplan</small>
        </div>
        <div class="card">
            <div class="body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <form id="forgot_password" method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}
                    <div class="msg">
                        Bitte bei der Registrierung angegebene E-Mail Adresse eingeben.
                        Es wird eine E-Mail mit Benutzername und einem Link um das Passwort zurückzusetzen zugesendet.
                    </div>
                    <div class="input-group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">Passwort zurücksetzen</button>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="{{ route('login') }}">Login!</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="/plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="/js/admin.js"></script>
    <script src="/js/pages/examples/forgot-password.js"></script>

    <div class="pull-right top-buffer">
        <a href="/impressum">Impressum</a>
        <a href="/datenschutz">Datenschutz</a>
    </div>
</body>
@endsection
