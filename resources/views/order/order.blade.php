@extends('_layouts.base')

@section('head')
    <!-- Login Page Css -->
    <link href="/css/login.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/css/offer.css" rel="stylesheet">
@endsection

@section('body')
<body class="login-background">
    <div class="login-page offer-container">
        <div class="row">
            <div class="col-lg-6 col-md-6 ml-auto mr-auto col-centered text-center card-header">
                <h2>Dein Dienstplan</h2>
                <p>Der Dienstplan für deine Gliederung.</p>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            @switch($package)
                                @case("basic")
                            Basis Paket (3€ / Monat)
                                    @break
                                @case("module")
                            Modulares Paket (5€ / Monat)
                                    @break
                                @case("support")
                            Support Paket (15€ / Monat)
                                    @break
                            @endswitch
                        </h2>
                    </div>
                    <div class="body">
                        <form id="sign_up" method="POST" action="{{ action('OrderController@store', $package) }}">
                            {{ csrf_field() }}
                            <h2 class="card-inside-title">Ansprechpartner und Admin Account</h2>
                            <div class="row clearfix">
                                <div class="col-sm-12">
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
                                    <div class="col-sm-12 input-group form-group{{ $errors->has('email') ? ' has-error' : '' }}">
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
                                            <input type="password" id="password" class="form-control" name="password" minlength="8" placeholder="Passwort" required>
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
                                            <input type="password" id="password-confirm" class="form-control" name="password_confirmation" minlength="8" placeholder="Passwort wiederholen" required>
                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="card-inside-title">Gliederung</h2>
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <div class="input-group form-group{{ $errors->has('client_name') ? ' has-error' : '' }}">
                                    <span class="input-group-addon">
                                        <i class="material-icons">group</i>
                                    </span>
                                        <div class="form-line">
                                            <input type="text" id="client_name" class="form-control" name="client_name" value="{{ old('client_name') }}" minlength="4" placeholder="Gliederungs Name (wird als Gliederungsname in den Ansichten angezeigt)" required autofocus>
                                            @if ($errors->has('client_name'))
                                                <span class="help-block">
                                            <strong>{{ $errors->first('client_name') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-12 input-group form-group{{ $errors->has('client_billing') ? ' has-error' : '' }}">
                                        <span class="input-group-addon">
                                            <i class="material-icons">email</i>
                                        </span>
                                        <div class="form-line">
                                            <input type="email" id="client_billing" class="form-control" name="client_billing" value="{{ old('client_billing') }}" placeholder="Rechnungs-Email Adresse, muss *@*.dlrg.de sein. (Rechnungen werden digital zugestellt)" required>
                                            @if ($errors->has('client_billing'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('client_billing') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-12 input-group form-group{{ $errors->has('client_billing_address') ? ' has-error' : '' }}">
                                        <span class="input-group-addon">
                                            <i class="material-icons">folder_shared</i>
                                        </span>
                                        <div class="form-line">
                                            <textarea rows="4" class="form-control no-resize" name="client_billing_address" id="client_billing_address" placeholder="Rechnungsadresse">{{ old('client_billing_address') }}</textarea>
                                            @if ($errors->has('client_billing_address'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('client_billing_address') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="card-inside-title">Module @if(collect(["module"])->contains($package))(Ein Modul inklusive)@endif</h2>
                            <div class="row clearfix">
                                <div class="col-sm-12">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="module_default" name="module_default" id="module_default" checked disabled>
                                        <label class="form-check-label" for="defaultmodule">
                                            Basismodul - Dienstplanung inc. Grundfunktionen
                                        </label>
                                    </div>

                                    @if(collect(["module", "support"])->contains($package))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="module_training" name="module_training" id="module_training" @if(!empty(old('module_training')) || collect(["support"])->contains($package)) checked @endif  @if(collect(["support"])->contains($package)) disabled @endif>
                                        <label class="form-check-label" for="module_training">
                                            Fortbildungen (+ 1€)
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="module_stats" name="module_stats" id="module_stats" @if(!empty(old('module_stats')) || collect(["support"])->contains($package)) checked @endif @if(collect(["support"])->contains($package)) disabled @endif>
                                        <label class="form-check-label" for="module_training_credit" @if(!collect(["support"])->contains($package))checked disabled @endif>
                                            Erweiterte Analyse und Auswertungen (noch nicht verfügbar)(+ 1€)
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <h2 class="card-inside-title">Captcha</h2>
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <p style=" display: block;">
                                            {!! captcha_img('flat') !!}
                                        </p>
                                    </div>

                                    <div class="input-group form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                                        <span class="input-group-addon">
                                            <i class="material-icons">vpn_key</i>
                                        </span>
                                        <div class="form-line">
                                            <input type="text" id="captcha" class="form-control" name="captcha" minlength="1" required placeholder="Captcha">
                                            @if ($errors->has('captcha'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('captcha') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="text" name="zip" style="display: none;" value="spamprevention">
                            <input type="text" name="street" style="display: none;" value="">
                            <button class="btn btn-block btn-dlrg waves-effect" type="submit">Kostenpflichtig bestellen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center footer-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 mb-4 mb-md-0">
                    <a href="mailto:{{env("impressum.mail")}}?subject=Dienstplan%20Anfrage&amp;body=Hallo%20{{env("impressum.name")}},">
                        <button type="button" class="btn btn-dlrg waves-effect">
                            <i class="material-icons">contact_mail</i>
                            <span>Fragen?</span>
                        </button>
                    </a>
                </div>

                <div class="col-lg-12 col-md-12 mb-4 mb-md-0">
                    <h2></h2>
                    <a href="/impressum">Impressum</a> | <a href="/datenschutz">Datenschutz</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="/plugins/jquery-validation/jquery.validate.js"></script>
</body>
@endsection
