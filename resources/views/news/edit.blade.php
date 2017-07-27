@extends('_layouts.application')

@section('head')
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Input -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Nachricht bearbeiten
                        </h2>
                    </div>
                    <div class="body">
                        @include('news._form')
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Input -->
    </div>
@endsection
