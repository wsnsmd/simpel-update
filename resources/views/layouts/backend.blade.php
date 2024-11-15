<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>{{ setting()->get('app_nama') }} | BPSDM Prov. Kaltim</title>

        <meta name="description" content="Sistem Informasi Manajemen Pelatihan BPSDM Prov. Kaltim">
        <meta name="keywords" content="Pelatihan, SIMPel, BPSDM, Sistem, Informasi, Kaltim, Kalimantan Timur">
        <meta name="author" content="IT BPSDM Prov. Kaltim">
        <meta name="robots" content="noindex, nofollow">

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
        <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed">
            <!-- Sidebar -->
            @section('sidebar')
                @include('layouts.sidebar')
            @show
            <!-- END Sidebar -->

            <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div>
                        <!-- Toggle Sidebar -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
                        <button type="button" class="btn btn-dual mr-1" data-toggle="layout" data-action="sidebar_toggle">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                        <!-- END Toggle Sidebar -->
                        
                        @can('isUser')
                        <a href="{{ URL('/') }}" class="btn btn-dual mr-1" target="_pratinjau">
                            <i class="fa fa-desktop mr-1"></i>
                            <span class="d-none d-sm-inline-block">Pratinjau Beranda</span>
                        </a>
                        @endcan  
                    </div>
                    <!-- END Left Section -->

                    <!-- Right Section -->
                    <div>
                        <button type="button" class="btn btn-dual mr-1">
                            <i class="far fa-calendar-check mr-1"></i>
                            <span class="d-none d-sm-inline-block">Tahun:</span> {{ session('apps_tahun') }}
                        </button>                        

                        <!-- User Dropdown -->
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-fw fa-user-circle"></i>
                                <i class="fa fa-fw fa-angle-down ml-1 d-none d-sm-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">
                                <div class="bg-primary-darker rounded-top font-w600 text-white text-center p-3">
                                    <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ is_null(Auth::user()->photo) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url(Auth::user()->photo)) }}" alt="">
                                    <div class="pt-2">
                                        <a class="text-white font-w600" href="javascript:void(0)">{{ Auth::user()->name }}</a>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <a class="dropdown-item" href="{{ route('backend.user.profil.show') }}">
                                        <i class="far fa-fw fa-user mr-1"></i> Profil
                                    </a>
                                    <div role="separator" class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                        onclick="event.preventDefault(); 
                                        document.getElementById('logout-form').submit();">
                                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END User Dropdown -->                        

                        <!-- Toggle Side Overlay -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

                <!-- Header Loader -->
                <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
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
            <footer id="page-footer" class="bg-body-light">
                <div class="content py-0">
                    <div class="row font-size-sm">
                        <div class="col-sm-6 order-sm-1 text-center text-sm-left">
                            <a class="font-w600" href="https://bpsdm.kaltimprov.go.id/" target="_blank">BPSDM Prov. Kaltim</a> &copy; <span data-toggle="year-copy">2019</span>.
                        </div>
                    </div>
                </div>
            </footer>
            <!-- END Footer -->
        </div>
        <!-- END Page Container -->

        <!-- Dashmix Core JS -->
        <script src="{{ asset('js/dashmix.app.js') }}"></script>

        <!-- Laravel Scaffolding JS -->
        <script src="{{ asset('js/laravel.app.js') }}"></script>

        @yield('js_after')
    </body>
</html>
