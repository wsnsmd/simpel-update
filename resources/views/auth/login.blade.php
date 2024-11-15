<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>BPSDM Prov. Kaltim | {{ setting()->get('app_nama') }}</title>

        <meta name="description" content="Sistem Informasi Pelatihan BPSDM Prov. Kaltim">
        <meta name="keywords" content="Pelatihan,SIMPel,BPSDM,Sistem Informasi Pelatihan">
        <meta name="author" content="IT BPSDM Prov. Kaltim">
        <meta name="robots" content="noindex, nofollow">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon-32x32.png') }}">
        <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/android-icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-icon-precomposed.png') }}">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700">
        <link rel="stylesheet" id="css-theme" href="{{ asset('css/dashmix.css') }}">
        <link rel="stylesheet" href="{{ asset('css/themes/' . setting()->get('app_tema') . '.css') }}">

        <!-- Scripts -->
        <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
    </head>
    <body>
        <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
                <div class="bg-image" style="background-image: url('{{ asset('media/photos/bg_bpsdm.jpg') }}');">
                    <div class="row no-gutters justify-content-center bg-primary-dark-op">
                        <div class="hero-static col-sm-6 col-md-6 col-lg-4 d-flex pt-6 p-2 px-sm-0">
                            <!-- Sign In Block -->
                            <div class="block block-rounded block-transparent w-100 mb-0 overflow-hidden">
                                <div class="block-content block-content-full px-lg-4 px-xl-5 py-3 py-md-4 py-lg-5 bg-white" style="max-width: 450px">
                                    <!-- Header -->
                                    <div class="mb-2 text-center">
                                        <a class="link-fx font-w700 font-size-h1" href="javascript:;">
                                            <span class="text-dark">SIM</span><span class="text-primary">Pel</span>
                                        </a>
                                        <p class="text-uppercase font-w700 font-size-sm text-muted">BPSDM Kaltim</p>
                                    </div>
                                    <!-- END Header -->

                                    <!-- Sign In Form -->
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="email" name="email" placeholder="Username" value="{{ old('email') }}" autocomplete="off" autofocus required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-user-circle"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" placeholder="Password" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-asterisk"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control" id="tahun" name="tahun">
                                                    @foreach ($tahun as $t)
                                                    <option value="{{ $t->tahun }}" {{ setting()->get('app_tahun') == $t->tahun ? 'selected' : '' }}>{{ $t->tahun }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="captcha" id="captcha" class="form-control py-2" placeholder="Captcha">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <a href="javascript:;" id="reload"><i class="fa fa-sync"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="text-center captcha">
                                                <span>{!! captcha_img('flat') !!}</span>
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-hero-primary">
                                                <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Login
                                            </button>
                                        </div>
                                    </form>
                                    <!-- END Sign In Form -->
                                </div>
                            </div>
                            <!-- END Sign In Block -->
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>

        <!-- Dashmix Core JS -->
        <script src="{{ asset('js/dashmix.app.js') }}"></script>

        <!-- Laravel Scaffolding JS -->
        <script src="{{ asset('js/laravel.app.js') }}"></script>

        <!-- Page JS Plugins -->
        <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

        @if ($errors->has('email'))
        <script>
            $.notify({
                icon: "fa fa-times mr-1",
                message: "Username / Password tidak sesuai!"
            }, {
                allow_dismiss: false,
                type: 'danger',
                placement: {
                    from: "top",
                    align: "center"
                }
            });
        </script>
        @endif

        <script>
        $("#reload").click(function () {
            $.ajax({
                type: "GET",
                url: "{{ route('reload.captcha') }}",
                success: function (data) {
                    $(".captcha span").html(data.captcha);
                }
            });
        });
        </script>

    </body>
</html>
