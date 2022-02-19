@extends('_layouts.base')

@section('body')
<body class="theme-red">

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Bitte warten...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->

    @include('_layouts/header')

    <section>
        @include('_layouts/menu')
    </section>


    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>

    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Custom Js -->
    <script src="/js/admin.js"></script>

    <!-- Bootstrap Notify Plugin Js -->
    <script src="/plugins/bootstrap-notify/bootstrap-notify.js"></script>

    <!-- Input Mask Plugin Js -->
    <script src="/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>

    <script>
        function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
            if (colorName === null || colorName === '') { colorName = 'alert-success'; }
            if (text === null || text === '') { text = ''; }
            if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
            if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
            if (placementFrom === null || placementFrom === '') { placementFrom = 'top'; }
            if (placementAlign === null || placementAlign === '') { placementAlign = 'center'; }

            var allowDismiss = true;

            $.notify({
                        message: text
                    },
                    {
                        type: colorName,
                        allow_dismiss: allowDismiss,
                        newest_on_top: true,
                        timer: 1000,
                        placement: {
                            from: placementFrom,
                            align: placementAlign
                        },
                        animate: {
                            enter: animateEnter,
                            exit: animateExit
                        },
                        template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<span data-notify="icon"></span> ' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span data-notify="message">{2}</span>' +
                        '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                        '</div>' +
                        '<a href="{3}" target="{4}" data-notify="url"></a>' +
                        '</div>'
                    });
        }

        $( document ).ready(function() {
            @if(Illuminate\Support\Facades\Auth::user()->clients()->count() > 0)
            $('#clientchange').on('change', function(){
                var newclient = $('#clientchange option:selected').val();
                window.location.href='/changeclient/'+newclient;
            });
            @endif

            @if(Session::get('errormessage') != null)
                showNotification('alert-info', '{{Session::get('errormessage')}}', "top", "center", "", "");
            @php
                Session::forget('errormessage');
            @endphp
            @endif

            @if(Session::get('successmessage') != null)
            showNotification('alert-success', '{{Session::get('successmessage')}}', "top", "center", "", "");
            @php
            Session::forget('successmessage');
            @endphp
            @endif
        });

    </script>

    @yield('post_body')

    @include('_layouts/modal')

</body>

@endsection