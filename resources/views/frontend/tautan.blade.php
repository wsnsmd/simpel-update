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
        <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700">
        <link rel="stylesheet" id="css-theme" href="{{ asset('css/dashmix.min.css') }}">

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
        <div id="page-container">
            <!-- Main Container -->
            <main id="main-container">
                <!-- Page Content -->
                <div class="bg-image" style="background-image: url('{{ asset('media/photos/photo16@2x.jpg') }}');">
                    <div class="hero bg-white-90" style="align-items: baseline;">
                        <div class="content content-full">
                            <div class="px-3 py-5">
                                <div class="row g-0 d-flex justify-content-center">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="mb-3 text-center">
                                            <a class="img-link" href="{{ route('index') }}">
                                                <img class="" src="{{ asset('media/images/bpsdm-logo.png') }}" alt="BPSDM Kaltim" height="80px">
                                            </a>
                                            <h2 class="fs-5 fw-normal my-3 text-uppercase">{!! $jadwal->nama !!}</h2>
                                        </div>
                                        <hr>
                                        @if($tautan->isNotEmpty())
                                        @foreach ($tautan as $t)
                                            @if($t->url === '{daftar}')
                                                <div class="mb-3">
                                                    <form action="{{ route('jadwal.daftar') }}" method="POST" target="_blank">
                                                        @csrf
                                                        <input type="hidden" id="jadwal_id" name="jadwal_id" value="{{$jadwal->id}}">
                                                        <button type="submit" class="btn w-100 btn-lg btn-hero btn-primary rounded-pill">{{ $t->title }}</button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="mb-3">
                                                    <a href="{!! $t->url !!}" class="btn w-100 btn-lg btn-hero btn-primary rounded-pill" target="_blank">{{ $t->title }}</a>
                                                </div>
                                            @endif
                                        @endforeach
                                        @else
                                            <div class="mb-3">
                                                <form action="{{ route('jadwal.daftar') }}" method="POST" target="_blank">
                                                    @csrf
                                                    <input type="hidden" id="jadwal_id" name="jadwal_id" value="{{$jadwal->id}}">
                                                    <button type="submit" class="btn w-100 btn-lg btn-hero btn-primary rounded-pill">Daftar</button>
                                                </form>
                                            </div>
                                            <div class="mb-3">
                                                <a href="{{ route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}" class="btn w-100 btn-lg btn-hero btn-primary rounded-pill" target="_blank">Detail</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->
        <script>
            function openNewPage(url) {
                window.open(url, '_blank');
            }
    </script>
    </body>
</html>
