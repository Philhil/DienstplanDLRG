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
                <p> Der Dienstplan für deine Gliederung.</p>
            </div>
        </div>
        <div class="row" style="margin-top: 100px">

            <div class="col-lg-4 col-md-6">
                <div class="card card-offer-focus">
                    <h3 class="card-offer-header text-center">Basis Paket</h3>
                    <p class="text-center">Perfekt für den Einstieg</p>
                    <div class="card-offer-body">
                        <div class="offer-icon">
                            <i class="material-icons"  style="font-size: 75px;">home</i>
                        </div>
                        <h3>3€ / Monat</h3>
                        <p>Das Basispaket enthält alle grundlegenden Dienstplan Funktionen. Wie in jedem Paket sind Updates, automatische Backups sowie Basis Support enthalten.</p>
                    </div>
                    <div class="card-offer-footer">
                        <a href="{{ action('OrderController@create', "basic") }}">
                            <button class="btn btn-block btn-dlrg waves-effect" type="submit">Auswählen</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card card-offer">
                    <h3 class="card-offer-header text-center">Modulares Paket</h3>
                    <p class="text-center">Für den ambitionierten Gebrauch</p>
                    <div class="card-offer-body">
                        <div class="offer-icon">
                            <i class="material-icons"  style="font-size: 75px;">dashboard</i>
                        </div>
                        <h3>ab 5€ / Monat</h3>
                        <p>Das Basispaket inc. ein frei wählbares Modul wie z.B. Übungs-Verwaltung. Wie in jedem Paket sind Updates, automatische Backups sowie Basis Support enthalten.</p>
                    </div>
                    <div class="card-offer-footer">
                        <a href="{{ action('OrderController@create', "module") }}">
                            <button class="btn btn-block btn-dlrg waves-effect" type="submit">Auswählen</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card card-offer">
                    <h3 class="card-offer-header text-center">Support Paket</h3>
                    <p class="text-center">Rundum umsorgt</p>
                    <div class="card-offer-body">
                        <div class="offer-icon">
                            <i class="material-icons"  style="font-size: 75px;">headset</i>
                        </div>
                        <h3>15€ / Monat</h3>
                        <p>Alle Funktionen sowie voller Support. Es sind Dienstleistungen wie Datenimporte, anlegen von Diensten etc inbegriffen. Wie in jedem Paket sind Updates und automatische Backups enthalten.</p>
                    </div>
                    <div class="card-offer-footer">
                        <a href="{{ action('OrderController@create', "support") }}">
                            <button class="btn btn-block btn-dlrg waves-effect" type="submit">Auswählen</button>
                        </a>
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

                    <a href="https://demo.dlrgdienstplan.de/">
                        <button type="button" class="btn btn-dlrg waves-effect">
                            <i class="material-icons">new_releases</i>
                            <span>Demo</span>
                        </button>
                    </a>
                </div>

                <div class="col-lg-12 col-md-12 mb-4 mb-md-0">
                    <h2></h2>
                    <a href="/impressum">Impressum</a> | <a href="/datenschutz">Datenschutz</a>
                </div>
            </div>
    </footer>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>
</body>
@endsection
