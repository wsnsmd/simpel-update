<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>{{ setting()->get('app_nama') }} | BPSDM Prov. Kaltim</title>

        <meta name="description" content="Sistem Informasi Manajemen Pelatihan BPSDM Prov. Kaltim">
        <meta name="keywords" content="Pelatihan, SIMPel, BPSDM, Sistem, Informasi, Kaltim, Kalimantan Timur">
        <meta name="author" content="IT BPSDM">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon-32x32.png') }}">
        <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/android-icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-icon-precomposed.png') }}">
        <!-- END Icons -->

        <!-- Fonts and Styles -->
        @yield('css_before')
        <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700">
        <link rel="stylesheet" id="css-theme" href="{{ asset('css/dashmix.css') }}">
        <link rel="stylesheet" href="{{ asset('css/themes/' . setting()->get('app_tema') . '.css') }}">

        @yield('css_after')

        <!-- Scripts -->
        <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-9LW9CVJ1Q9"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-9LW9CVJ1Q9');
        </script>
    </head>
    <body>
        <!-- Page Container -->
        <div id="page-container" class="sidebar side-scroll page-header-dark page-header-fixed main-content-boxed">
            <!-- Sidebar -->
            <nav id="sidebar" aria-label="Main Navigation">
                <!-- Side Header -->
                <div class="content-header bg-primary">
                    <!-- Logo -->
                    <a class="text-dual d-inline-block font-w600" href="{{ route('index') }}">
                        <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-none d-sm-inline-block" style="max-height: 55px" />
                        <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-sm-none d-md-none d-lg-none" style="max-height: 55px; max-width: 250px" />
                    </a>
                    <!-- END Logo -->

                    <!-- Options -->
                    <div>
                        <!-- Close Sidebar, Visible only on mobile screens -->
                        <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                            <i class="fa fa-times-circle"></i>
                        </a>
                        <!-- END Close Sidebar -->
                    </div>
                    <!-- END Options -->
                </div>
                <!-- END Side Header -->

                <!-- Side Navigation -->
                <div class="content-side content-side-full">
                    <ul class="nav-main">
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ url('/') }}">
                                <i class="nav-main-link-icon fa fa-home"></i>
                                <span class="nav-main-link-name">Beranda</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                <i class="nav-main-link-icon fa fa-calendar-alt"></i>
                                <span class="nav-main-link-name">Jadwal</span>
                            </a>
                            <ul class="nav-main-submenu">
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('jadwal.index') }}">
                                        <i class="nav-main-link-icon fa fa-calendar-alt"></i>
                                        <span class="nav-main-link-name">Pelatihan</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('jadwal.wi') }}">
                                        <i class="nav-main-link-icon fa fa-users"></i>
                                        <span class="nav-main-link-name">Widyaiswara</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('alumni.index') }}">
                                <i class="nav-main-link-icon fa fa-compass"></i>
                                <span class="nav-main-link-name">Alumni</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('informasi') }}">
                                <i class="nav-main-link-icon fa fa-info-circle"></i>
                                <span class="nav-main-link-name">Informasi</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('login') }}">
                                <i class="nav-main-link-icon fa fa-sign-in-alt"></i>
                                <span class="nav-main-link-name">Login</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- END Side Navigation -->
            </nav>
            <!-- END Sidebar -->

            <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div class="d-flex align-items-center">
                        <!-- Logo -->
                        <a class="font-size-lg font-w700 text-dark" href="{{ route('index') }}">
                            <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-none d-sm-inline-block" style="max-height: 55px" />
                            <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-sm-none d-md-none d-lg-none" style="max-height: 55px; max-width: 250px" />
                        </a>
                        <!-- END Logo -->
                    </div>
                    <!-- END Left Section -->

                    <!-- Right Section -->
                    <div>
                        <div class="d-none d-lg-flex align-items-center">
                            <!-- Menu -->
                            <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block ml-4">
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ url('/') }}">
                                        <i class="nav-main-link-icon fa fa-home"></i>
                                        <span class="nav-main-link-name">Beranda</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                        <i class="nav-main-link-icon fa fa-calendar-alt"></i>
                                        <span class="nav-main-link-name">Jadwal</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link" href="{{ route('jadwal.index') }}">
                                                <i class="nav-main-link-icon fa fa-calendar-alt"></i>
                                                <span class="nav-main-link-name">Pelatihan</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link" href="{{ route('jadwal.wi') }}">
                                                <i class="nav-main-link-icon fa fa-users"></i>
                                                <span class="nav-main-link-name">Widyaiswara</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('alumni.index') }}">
                                        <i class="nav-main-link-icon fa fa-compass"></i>
                                        <span class="nav-main-link-name">Alumni</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('informasi') }}">
                                        <i class="nav-main-link-icon fa fa-info-circle"></i>
                                        <span class="nav-main-link-name">Informasi</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('login') }}">
                                        <i class="nav-main-link-icon fa fa-sign-in-alt"></i>
                                        <span class="nav-main-link-name">Login</span>
                                    </a>
                                </li>
                            </ul>
                            <!-- END Menu -->
                        </div>
                        <!-- Toggle Sidebar -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_toggle" href="javascript:void(0)">
                            <i class="fa fa-fw fa-bars"></i>
                        </a>
                        <!-- END Toggle Sidebar -->
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

                <!-- Header Loader -->
                <div id="page-header-loader" class="overlay-header bg-primary-darker">
                    <div class="content-header">
                        <div class="w-100 text-center">
                            <i class="fa fa-fw fa-2x fa-sun fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- END Header Loader -->
            </header>
            <!-- END Header -->

            <!-- Main Container -->
            <main id="main-container">
                @yield('content')

                <!-- Footer -->
                <footer id="page-footer" class="bg-body-light">
                    <div class="content py-4">
                        <div class="row items-push font-size-sm border-bottom">
                            {{-- <div class="col-6 col-md-4">
                                <h3 class="h5 font-w700">Tentang  Kami</h3>
                                <div>
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s
                                </div>
                            </div> --}}
                            <div class="col-md-8">
                                <h3 class="h5 font-w700 border-bottom pb-2">Kontak</h3>
                                <div>
                                    Jl. H.A.M.M Riffadin No. 88, Kelurahan Harapan Baru, Kecamatan Loa Janan Ilir <br>
                                    Kota Samarinda, Provinsi Kalimantan Timur <br>
                                    <abbr title="Telepon">Telp:</abbr> 0541 7270201 <br>
                                    <a href="mailto:bpsdm@kaltimprov.go.id" class="link-fx">bpsdm@kaltimprov.go.id</a><br>
                                    <a href="mailto:bpsdm.kaltimprov@gmail.com" class="link-fx">bpsdm.kaltimprov@gmail.com</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h3 class="h5 font-w700 border-bottom pb-2">Tentang Kami</h3>
                                <div>
                                    Mewujudkan Sumber Daya Manusia Aparatur yang Profesional dalam Pelayanan Publik.
                                </div>
                            </div>
                        </div>
                        <div class="row font-size-sm">
                            <div class="col-sm order-sm-1 pt-3 text-center text-sm-left">
                                Hak Cipta &copy; 2019. Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur.
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- END Footer -->

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
