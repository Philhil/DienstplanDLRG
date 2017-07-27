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
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Benutzer
                        </h2>
                    </div>
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                @include('user._form')
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

                                <div class="row clearfix">
                                    <select id='user_qualification_select' class="ms" multiple='multiple' style="position: absolute; left: -9999px;">
                                        @foreach($qualifications_notassigned as $qualification_notassigned)
                                            <option value='{{$qualification_notassigned->id}}' >{{$qualification_notassigned->name}}</option>
                                        @endforeach

                                        @foreach($qualifications_assigned as $qualification_assigned)
                                            <option value='{{$qualification_assigned->id}}' selected>{{$qualification_assigned->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
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

            $('#user_qualification_select').multiSelect({
                selectableHeader: "<h6>Alle Qualifikationen</h6>",
                selectionHeader: "<h6>zugeordnete Qualifikationen</div>",
                selectableFooter: "<div class='custom-header'></div>",
                selectionFooter: "<div class='custom-header'></div>",

                afterSelect: function(value){
                    $.ajax({
                        type: "POST",
                        url: '/qualification_user/create',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        'data' : {
                           'qualification_id': value[0],
                            'user_id': "{{$user->id}}"
                        },
                        success : function(data){
                            if (data == "true") {
                                showNotification("alert-success", "Qualifikation gespeichert", "top", "center", "", "");
                            } else {
                                showNotification("alert-warning", "Fehler beim speichern der Qualifikation", "top", "center", "", "");
                            }
                        }
                    });
                },
                afterDeselect: function(value){
                    $.ajax({
                        type: "POST",
                        url: '/qualification_user/delete/{{$user->id}}/'+value,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success : function(data){
                            if (data == "true") {
                                showNotification("alert-success", "Qualifikation gelöscht", "top", "center", "", "");
                            } else {
                                showNotification("alert-warning", "Fehler beim löschen der Qualifikation", "top", "center", "", "");
                            }
                        }
                    });
                }
            });

        });
    </script>
@endsection