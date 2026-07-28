@extends('layouts.frontend')

@section('content')
    <!-- Hero -->
    <div class="bg-body-light border-top border-bottom">
        <div class="content content-full py-1">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-sm text-uppercase font-w700 mt-2 mb-0 mb-sm-2">
                    <i class="fa fa-angle-right fa-fw text-primary"></i> Portal Peserta
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3 font-size-sm" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
                        <li class="breadcrumb-item active" aria-current="page">Portal Peserta</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="content content-full">
        <div class="row justify-content-center mt-4">
            <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
                <div class="block block-rounded block-themed mb-0">
                    <div class="block-header bg-primary">
                        <h3 class="block-title">
                            <i class="fa fa-user-circle mr-1"></i> Masuk Portal Peserta
                        </h3>
                    </div>
                    <div class="block-content block-content-full">

                        {{-- Tampilkan error SSO --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <p class="mb-0">{{ $errors->first('authentik') }}</p>
                            </div>
                        @endif

                        <p class="text-muted mb-4">
                            Gunakan akun SSO BPSDM yang telah terdaftar sebagai peserta.
                        </p>

                        <a href="{{ route('authentik.redirect') }}" class="btn btn-block btn-primary">
                            <i class="fa fa-sign-in-alt mr-2"></i> Login SSO
                        </a>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Belum punya akun SSO? Hubungi panitia atau admin BPSDM Kaltim.
                            </small>
                        </div>

                        <hr>

                        <div class="text-center">
                            <a href="{{ url('/') }}" class="text-muted">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali ke Beranda
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection