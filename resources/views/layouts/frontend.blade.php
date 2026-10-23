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
    <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700">
    <link rel="stylesheet" id="css-theme" href="{{ asset('css/simpel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themes/' . setting()->get('app_tema') . '.css') }}">

    <style>
        /* Sticky footer setup */
        html, body {
            height: 100%;
        }
        #page-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #main-container {
            flex: 1 0 auto;
        }
        #page-footer {
            flex-shrink: 0;
            margin-top: auto;
            border-top: 1px solid #e4e7ed;
        }

        /* Header: logo lebih compact */
        #page-header .content-header { min-height: 60px; }
        #page-header .content-header img { max-height: 45px; }
        #page-footer h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .footer-link {
            color: #575757;
            text-decoration: none;
        }
        .footer-link:hover {
            color: #3b82f6;
            text-decoration: none;
        }
        .footer-bottom {
            border-top: 1px solid #e4e7ed;
            padding-top: 1rem;
            margin-top: 0.5rem;
        }
        .footer-contact-item {
            display: flex;
            align-items: start;
            gap: 8px;
            margin-bottom: 6px;
        }
        .footer-contact-item i {
            margin-top: 3px;
            color: #6c757d;
            width: 14px;
            text-align: center;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Block hover subtle */
        .block-link-shadow { transition: box-shadow 0.2s ease, transform 0.2s ease; }
        .block-link-shadow:hover { transform: translateY(-2px); }
    </style>

    @yield('css_after')

    <!-- Scripts -->
    <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9LW9CVJ1Q9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-9LW9CVJ1Q9');
    </script>
</head>

<body>
    <!-- Page Container -->
    <div id="page-container" class="sidebar side-scroll page-header-dark page-header-fixed main-content-boxed">
        <!-- Sidebar (Mobile) -->
        <nav id="sidebar" aria-label="Main Navigation">
            <!-- Side Header -->
            <div class="content-header bg-primary">
                <a class="text-dual d-inline-block font-w600" href="{{ route('index') }}">
                    <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-none d-sm-inline-block"
                        style="max-height: 45px" />
                    <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-sm-none d-md-none d-lg-none"
                        style="max-height: 45px; max-width: 220px" />
                </a>
                <div>
                    <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close"
                        href="javascript:void(0)">
                        <i class="fa fa-times-circle"></i>
                    </a>
                </div>
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
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                            aria-expanded="false" href="#">
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
                        <a class="nav-main-link" href="{{ route('sertifikat.cek') }}">
                            <i class="nav-main-link-icon fa fa-address-card"></i>
                            <span class="nav-main-link-name">Cek Sertifikat</span>
                        </a>
                    </li>
                    @auth('peserta')
                        <li class="nav-main-heading">Akun</li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('peserta.dashboard') }}">
                                <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                                <span class="nav-main-link-name">Dashboard Saya</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <form action="{{ route('peserta.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-main-link"
                                    style="width:100%;background:none;border:none;cursor:pointer;text-align:left">
                                    <i class="nav-main-link-icon fa fa-sign-out-alt"></i>
                                    <span class="nav-main-link-name">Keluar</span>
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-main-heading">Akun</li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('peserta.login') }}">
                                <i class="nav-main-link-icon fa fa-user-circle"></i>
                                <span class="nav-main-link-name">Portal Peserta</span>
                            </a>
                        </li>
                    @endauth
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
                    <a class="font-size-lg font-w700 text-dark" href="{{ route('index') }}">
                        <img src="{{ asset('media/images/logo-bpsdm-white.png') }}" class="d-none d-sm-inline-block"
                            style="max-height: 45px" />
                        <img src="{{ asset('media/images/logo-bpsdm-white.png') }}"
                            class="d-sm-none d-md-none d-lg-none" style="max-height: 45px; max-width: 220px" />
                    </a>
                </div>
                <!-- END Left Section -->

                <!-- Right Section -->
                <div>
                    <div class="d-none d-lg-flex align-items-center">
                        <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block ml-4">
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ url('/') }}">
                                    <i class="nav-main-link-icon fa fa-home"></i>
                                    <span class="nav-main-link-name">Beranda</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                    aria-haspopup="true" aria-expanded="false" href="#">
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
                                <a class="nav-main-link" href="{{ route('sertifikat.cek') }}">
                                    <i class="nav-main-link-icon fa fa-address-card"></i>
                                    <span class="nav-main-link-name">Cek Sertifikat</span>
                                </a>
                            </li>
                            @auth('peserta')
                                <li class="nav-main-item">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                        aria-haspopup="true" aria-expanded="false" href="#">
                                        <i class="nav-main-link-icon fa fa-user-circle"></i>
                                        <span class="nav-main-link-name">{{ session('peserta_name', Auth::guard('peserta')->user()->nama_lengkap) }}</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link" href="{{ route('peserta.dashboard') }}">
                                                <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                                                <span class="nav-main-link-name">Dashboard Saya</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <form action="{{ route('peserta.logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="nav-main-link"
                                                    style="width:100%;background:none;border:none;cursor:pointer;text-align:left">
                                                    <i class="nav-main-link-icon fa fa-sign-out-alt"></i>
                                                    <span class="nav-main-link-name">Keluar</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('peserta.login') }}">
                                        <i class="nav-main-link-icon fa fa-sign-in-alt"></i>
                                        <span class="nav-main-link-name">Portal Peserta</span>
                                    </a>
                                </li>
                            @endauth
                        </ul>
                    </div>
                    <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_toggle"
                        href="javascript:void(0)">
                        <i class="fa fa-fw fa-bars"></i>
                    </a>
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
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer" class="bg-white">
            <div class="content py-4">
                <!-- Footer Top -->
                <div class="row items-push pb-3">
                    <!-- Tentang -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <img src="{{ asset('media/images/logo-bpsdm.png') }}" alt="BPSDM" style="max-height: 45px;" class="mb-3">
                        <p class="font-size-sm text-muted mb-0">
                            Mewujudkan Sumber Daya Manusia Aparatur yang Profesional dalam Pelayanan Publik.
                        </p>
                    </div>

                    <!-- Navigasi -->
                    <div class="col-6 col-md-2">
                        <h3 class="font-w700 border-bottom pb-2 mb-3">Navigasi</h3>
                        <ul class="list-unstyled font-size-sm mb-0">
                            <li class="mb-1"><a class="footer-link" href="{{ url('/') }}"><i class="fa fa-angle-right fa-fw mr-1 text-muted"></i>Beranda</a></li>
                            <li class="mb-1"><a class="footer-link" href="{{ route('jadwal.index') }}"><i class="fa fa-angle-right fa-fw mr-1 text-muted"></i>Jadwal</a></li>
                            <li class="mb-1"><a class="footer-link" href="{{ route('alumni.index') }}"><i class="fa fa-angle-right fa-fw mr-1 text-muted"></i>Alumni</a></li>
                            <li class="mb-1"><a class="footer-link" href="{{ route('sertifikat.cek') }}"><i class="fa fa-angle-right fa-fw mr-1 text-muted"></i>Cek Sertifikat</a></li>
                            <li class="mb-1"><a class="footer-link" href="{{ route('informasi') }}"><i class="fa fa-angle-right fa-fw mr-1 text-muted"></i>Informasi</a></li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div class="col-md-4">
                        <h3 class="font-w700 border-bottom pb-2 mb-3">Kontak</h3>
                        <div class="font-size-sm">
                            <div class="footer-contact-item">
                                <i class="fa fa-map-marker-alt"></i>
                                <span>Jl. H.A.M.M Riffadin No. 88, Kel. Harapan Baru, Kec. Loa Janan Ilir, Kota Samarinda, Kalimantan Timur</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fa fa-phone"></i>
                                <span>0541 7270201</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fa fa-envelope"></i>
                                <div>
                                    <a href="mailto:bpsdm@kaltimprov.go.id" class="footer-link">bpsdm@kaltimprov.go.id</a><br>
                                    <a href="mailto:bpsdm.kaltimprov@gmail.com" class="footer-link">bpsdm.kaltimprov.go.id</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Kerja -->
                    <div class="col-6 col-md-2">
                        <h3 class="font-w700 border-bottom pb-2 mb-3">Jam Kerja</h3>
                        <div class="font-size-sm text-muted">
                            <p class="mb-1"><span class="font-w600 text-dark">Sen - Kam</span><br>07.30 - 16.30</p>
                            <p class="mb-1"><span class="font-w600 text-dark">Jumat</span><br>07.30 - 12.00</p>
                            <p class="mb-0"><span class="font-w600 text-dark">Libur</span><br>Sab, Min, Nasional</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="footer-bottom text-center font-size-sm text-muted">
                    <span>Hak Cipta &copy; {{ date('Y') }}. Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur.</span>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->

    <!-- SIMPel Core & App JS -->
    <script src="{{ asset('js/simpel-vendor.min.js') }}"></script>
    <script src="{{ asset('js/simpel.min.js') }}"></script>

    @yield('js_after')
</body>

</html>
