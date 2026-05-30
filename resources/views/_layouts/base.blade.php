<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Dienstplan</title>

    <meta name="description" content="Dienstplanverwaltung">
    <meta name="author" content="Philippe Käufer">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#E30613">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Dienstplan">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <!-- Favicon-->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Waves Effect Css -->
    <link href="/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/dienstplan.css" rel="stylesheet">

    <!-- AdminBSB Themes -->
    <link href="/css/themes/all-themes.css" rel="stylesheet" />

    @yield('head')

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</head>
@yield('body')
</html>
