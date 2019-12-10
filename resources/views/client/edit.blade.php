@extends('_layouts.application')

@section('head')
    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Multi Select Css -->
    <link href="/plugins/multi-select/css/multi-select.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Input -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Client: {{$client->name}}</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @include('client._form')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Admin Zuteilung</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <div class="row clearfix" style="margin-left: 50px;">
                                <select id='client_user_adminselect' class="ms" multiple='multiple' style="position: absolute; left: -9999px;">
                                    @foreach($usersOfClient as $userOfClient)
                                        <option value='{{$userOfClient->id}}' >{{$userOfClient->name}} {{$userOfClient->first_name}}</option>
                                    @endforeach

                                    @foreach($adminsOfClient as $adminOfClient)
                                        <option value='{{$adminOfClient->id}}' selected>{{$adminOfClient->name}} {{$adminOfClient->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Bonus-Punkte Typen</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <div class="row clearfix" style="margin-left: 50px;">

                                <!--
                                Add
                                send ajax and show in list
                                when ajax error -> remove from list
                                -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Input -->
    </div>
@endsection

@section('post_body')
    <!-- Multi Select Plugin Js -->
    <script src="/plugins/multi-select/js/jquery.multi-select.js"></script>

    <script>
        $( document ).ready(function() {

            @if(session('message'))
            showNotification("alert-success", "{{session('message')}}", "top", "center", "", "");
            @endif

            $('.time24').inputmask('hh:mm', { placeholder: '__:__ _m', alias: 'time24', hourFormat: '24' });
            $('.saison').inputmask('d.m', { placeholder: '__.__', alias: 'saison'});
            $('.email').inputmask({ alias: "email" });
        });

        $('#client_user_adminselect').multiSelect({
            selectableHeader: "<h6>Benutzer von {{$client->name}}</h6>",
            selectionHeader: "<h6>Admins von {{$client->name}}</div>",
            selectableFooter: "<div class='custom-header'></div>",
            selectionFooter: "<div class='custom-header'></div>",

            afterSelect: function(value){
                $.ajax({
                    type: "POST",
                    url: '/client_user/admin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    'data' : {
                        'user_id': value[0],
                        'client_id': "{{$client->id}}",
                        'isAdmin' : 1
                    },
                    success : function(data){
                        if (data == "true") {
                            showNotification("alert-success", "Zuordnung gespeichert", "top", "center", "", "");
                        } else {
                            showNotification("alert-warning", "Fehler beim speichern der Zuordnung", "top", "center", "", "");
                        }
                    }
                });
            },
            afterDeselect: function(value){
                $.ajax({
                    type: "POST",
                    url: '/client_user/admin/',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },'data' : {
                        'user_id': value[0],
                        'client_id': "{{$client->id}}",
                        'isAdmin' : 0
                    },
                    success : function(data){
                        if (data == "true") {
                            showNotification("alert-success", "Zuordnung gelöscht", "top", "center", "", "");
                        } else {
                            showNotification("alert-warning", "Fehler beim löschen der zuordnung", "top", "center", "", "");
                        }
                    }
                });
            }
        });
    </script>
@endsection