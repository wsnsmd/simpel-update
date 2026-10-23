<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ env("APP_NAME", "-") }}</title>

    <meta name="description" content="Sistem Informasi Manajemen Pelatihan BPSDM Prov. Kaltim">
    <meta name="robots" content="noindex, nofollow">
    <meta name="keywords" content="Pelatihan, SIMPel, BPSDM, Sistem, Informasi, Kaltim, Kalimantan Timur">
    <meta name="author" content="IT BPSDM">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon-32x32.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/android-icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-icon-precomposed.png') }}">

    <!-- Fonts and Styles -->
    @yield('css_before')
    <link rel="stylesheet" id="css-main"
        href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700">
    <link rel="stylesheet" id="css-theme" href="{{ asset('/css/dashmix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themes/' . setting()->get('app_tema') . '.css') }}">

    @yield('css_after')

    <!-- Scripts -->
    <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
</head>

<body>
    <!-- Page Container -->
    <div id="page-container">
        <!-- Main Container -->
        <main id="main-container">
            @yield('content')
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <!-- Dashmix Core JS -->
    <script src="{{ asset('js/dashmix.app.js') }}"></script>

    <!-- Laravel Original JS -->
    <script src="{{ asset('js/laravel.app.js') }}"></script>

    @yield('js_after')
</body>

</html>