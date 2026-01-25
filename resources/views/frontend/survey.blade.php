<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ setting()->get('app_nama') }} | BPSDM Prov. Kaltim</title>

    <meta name="description" content="Sistem Informasi Manajemen Pelatihan BPSDM Prov. Kaltim">
    <meta name="keywords" content="Pelatihan, SIMPel, BPSDM, Sistem, Informasi, Kaltim, Kalimantan Timur">
    <meta name="author" content="IT BPSDM">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon-32x32.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/android-icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-icon-precomposed.png') }}">
    <!-- END Icons -->

    <!-- Fonts and Styles -->
    <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700">
    <link rel="stylesheet" id="css-theme" href="{{ asset('css/dashmix.min.css') }}">

    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>

    <!-- Optional: Google Analytics (sesuai contoh Anda) -->
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
    <div id="page-container">
        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="bg-image" style="background-image: url('{{ asset('media/photos/photo16@2x.jpg') }}');">
                <div class="hero bg-white-90" style="align-items: baseline;">
                    <div class="content content-full">
                        <div class="px-3 py-5">
                            <div class="row g-0 d-flex justify-content-center">
                                <div class="col-md-7 col-xl-5">
                                    <div class="mb-3 text-center">
                                        <a class="img-link" href="{{ route('index') }}">
                                            <img src="{{ asset('media/images/bpsdm-logo.png') }}" alt="BPSDM Kaltim"
                                                height="80px">
                                        </a>

                                        <h2 class="fs-5 fw-normal my-3 text-uppercase">
                                            {!! $jadwal->nama ?? 'Pelatihan' !!}
                                        </h2>

                                        <div class="fs-sm text-muted">
                                            Sertifikat hanya dapat diunduh setelah <strong>survei wajib</strong> diisi.
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="alert alert-warning d-flex align-items-start" role="alert">
                                        <div class="flex-shrink-0">
                                            <i class="fa fa-exclamation-triangle mt-1"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-semibold">Survei Wajib Belum Lengkap</div>
                                            <div class="fs-sm">
                                                Silakan isi survei berikut. Setelah selesai, kembali ke halaman ini lalu
                                                klik
                                                <strong>Coba Unduh Lagi</strong>.
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $missingCount = is_countable($missing ?? []) ? count($missing) : 0;
                                    @endphp

                                    @if($missingCount > 0)
                                        <div class="block block-rounded block-bordered">
                                            <div class="block-header block-header-default">
                                                <h3 class="block-title">
                                                    Daftar Survei Belum Diisi
                                                    <span class="badge bg-warning text-dark ms-2">{{ $missingCount }}</span>
                                                </h3>
                                            </div>
                                            <div class="block-content">
                                                @foreach($missing as $s)
                                                    <div
                                                        class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                                        <div class="me-3">
                                                            <div class="fw-semibold">{{ $s->survey_name }}</div>
                                                            <div class="fs-sm text-muted">
                                                                Kode: <span
                                                                    class="badge bg-secondary">{{ $s->survey_code }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="text-end">
                                                            <a href="{{ $s->fill_url }}" target="_blank"
                                                                class="btn btn-hero btn-primary rounded-pill">
                                                                Isi Survei <i class="fa fa-external-link-alt ms-1"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-success" role="alert">
                                            Tidak ada survei wajib yang tertunda.
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        @if(!empty($retryUrl))
                                            <div class="mb-3">
                                                <a href="{{ $retryUrl }}"
                                                    class="btn w-100 btn-lg btn-hero btn-success rounded-pill">
                                                    Coba Unduh Lagi <i class="fa fa-redo ms-1"></i>
                                                </a>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <a href="{{ url()->previous() }}"
                                                class="btn w-100 btn-lg btn-hero btn-light rounded-pill">
                                                <i class="fa fa-angle-left me-1"></i> Kembali
                                            </a>
                                        </div>

                                        <div class="text-center fs-sm text-muted">
                                            Jika sudah mengisi survei tapi status belum berubah, coba refresh halaman
                                            atau klik tombol
                                            <strong>Coba Unduh Lagi</strong>.
                                        </div>
                                    </div>

                                </div> <!-- col -->
                            </div> <!-- row -->
                        </div> <!-- px -->
                    </div> <!-- content -->
                </div> <!-- hero -->
            </div> <!-- bg-image -->
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->
</body>

</html>