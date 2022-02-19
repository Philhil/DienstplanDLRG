@extends('_layouts.application')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid">
        <!-- input -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-green">
                    <h2>
                        Abfrage bearbeiten
                    </h2>
                    @if($aUserHasAlreadyVoted)
                        <br>
                        <div class="alert alert-danger">
                            <strong>VORSICHT!</strong> Nutzer haben sich zu dieser Abfrage bereits gemeldet. Das verändern der Abfrage kann die Integrität der Rückmeldungen verletzen.
                        </div>
                    @endif
                </div>
                <div class="body">
                    @include('survey._form')
                </div>
            </div>
        </div>
        <!-- End Input -->
    </div>
@endsection