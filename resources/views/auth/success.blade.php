@extends('_layouts.base')

@section('head')
    <!-- Login Page Css -->
    <link href="/css/login.css" rel="stylesheet">
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
                        <div class="msg"><h4>Erfolgreich registriert! <br><br>Dein Benutzer muss erst freigeschaltet werden. Du wirst per E-Mail benachrichtigt.</h4></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pull-right top-buffer footer-register">
            <a href="/impressum">Impressum</a> <a>|</a>
            <a href="/datenschutz">Datenschutz</a>
        </div>
    </body>
@endsection