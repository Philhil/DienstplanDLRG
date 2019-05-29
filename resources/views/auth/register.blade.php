@extends('_layouts.base')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection

@section('body')
    <body class="signup-page">
    <div class="signup-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>DLRG</b></a>
            <small>Online Dienstplan</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_up" method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}
                    <div class="msg">Registrieren</div>

                    <div class="input-group form-group{{ $errors->has('client') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">group</i>
                        </span>
                        <div class="form-line">

                            <select class="bootstrap-select show-tick" data-live-search="true" name="client[]">
                                @foreach(\App\Client::all() as $client)
                                    <option value="{{$client->id}}" @if(old('client') != null &&  old('client')[0] == $client->id) selected @endif >{{$client->name}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('client'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('client') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="input-group form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" id="first_name" class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="Vorname" required autofocus>
                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="input-group form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" placeholder="Name" required>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="input-group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Adresse" required>
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
                            <input type="password" id="password" class="form-control" name="password" minlength="6" placeholder="Passwort" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" id="password-confirm" class="form-control" name="password_confirmation" minlength="6" placeholder="Passwort wiederholen" required>
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="input-group">
                        <p style="text-align: center;  display: block;">
                            {{captcha_img('flat')}}
                        </p>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">vpn_key</i>
                        </span>

                        <div class="form-line">
                            <input type="text" id="captcha" class="form-control" name="captcha" minlength="1" required>
                            @if ($errors->has('captcha'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('captcha') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">Registrieren</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="{{ route('login') }}">Ich habe bereits einen Benutzeraccount!</a>
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

    <!-- Select Plugin Js -->
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/sign-up.js"></script>

    <div class="pull-right top-buffer">
        <a href="/impressum">Impressum</a> <a>|</a>
        <a href="/datenschutz">Datenschutz</a>
    </div>
    </body>
@endsection