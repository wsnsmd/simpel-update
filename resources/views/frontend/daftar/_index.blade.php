@extends('layouts.frontend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/jasny-bootstrap/css/jasny-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/magnific-popup/magnific-popup.css') }}">
@endsection


@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers(['datepicker', 'maxlength', 'magnific-popup']);
        });

        @if (session('success'))
        $.notify({
            icon: "fa fa-check mr-1",
            message: "{{ session('success') }}"
        }, {
            allow_dismiss: false,
            type: 'success',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @elseif (session('error'))
        $.notify({
            icon: "fa fa-times mr-1",
            message: "{{ session('error') }}"
        }, {
            allow_dismiss: false,
            type: 'danger',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @endif

    </script>

    @yield('js_sub')
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light border-top border-bottom">
        <div class="content content-full py-1">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-sm text-uppercase font-w700 mt-2 mb-0 mb-sm-2">
                    <i class="fa fa-angle-right fa-fw text-primary"></i> Jadwal
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3 font-size-sm" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
                        <li class="breadcrumb-item">Jadwal</i></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content invisible" data-toggle="appear">
                <div class="text-center py-3">
                    <h1 class="h3 font-w700 mb-2">{{ $jadwal->nama }}</h1>
                    <h2 class="h5 font-w400 text-muted">{{ $jadwal->kelas }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white">
        <div class="content">
            <div class="row invisible" data-toggle="appear">
                <div class="col-md-9">
                    <div class="block block-rounded block-bordered mb-3">
                        <!-- Content Block -->
                        @yield('content-block')
                        <!-- END Content Block -->
                    </div>
                </div>
                <div class="col-md-3 px-0">
                    <div class="block block-rounded block-bordered mb-3">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Informasi</h3>
                        </div>
                        <div class="block-content">
                            <table class="table table-borderless table-sm font-size-sm">
                                <tbody>
                                    <tr>
                                        <td class="font-w700">Tanggal Mulai</td>
                                        <td>{{$jadwal->tgl_awal}}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Tanggal Akhir</td>
                                        <td>{{$jadwal->tgl_akhir}}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Durasi</td>
                                        <td>{{$jadwal->jum_hari }} Hari</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Total JP</td>
                                        <td>{{$jadwal->total_jp}} JP</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Kuota</td>
                                        <td>{{$jadwal->kuota}} Peserta</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Registrasi</td>
                                        <td>
                                            @switch($jadwal->registrasi)
                                            @case(0)
                                                <span class="badge badge-warning">Internal</span>
                                                @break
                                            @case(1)
                                                <span class="badge badge-primary">Online</span>
                                                @break
                                            @default
                                                <span class="badge badge-warning">Internal</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Status</td>
                                        <td>
                                            @switch($jadwal->status_jadwal)
                                            @case(1)
                                                <span class="badge badge-success">Berjalan</span>
                                                @break
                                            @case(2)
                                                <span class="badge badge-primary">Akan Datang</span>
                                                @break
                                            @default
                                                <span class="badge badge-danger">Selesai</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="block block-rounded block-bordered">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Kontak</h3>
                        </div>
                        <div class="block-content">
                            <p class="font-size-sm">
                                <i class="fa fa-fw fa-user mr-1"></i> {{$jadwal->panitia_nama}} <br>
                                <i class="fa fa-fw fa-phone mr-1"></i> {{$jadwal->panitia_telp}} <br>
                                <i class="fa fa-fw fa-envelope mr-1"></i> {{$jadwal->panitia_email}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
