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
                <div class="msg"><h4>Erfolgreich registriert! <br><br>Dein Benutzer muss erst freigeschaltet werden. Du wirst per E-Mail benachrichtigt.</h4></div>
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
    <div class="pull-right top-buffer">
        <a href="/impressum">Impressum</a>
        <a href="/datenschutz">Datenschutz</a>
    </div>
    </body>
@endsection