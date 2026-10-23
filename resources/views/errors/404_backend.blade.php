@extends('layouts.backend')

@section('content')
    <!-- Page Content -->
    <div class="" style="">
        <div class="hero" style="max-height: 85vh">
            <div class="hero-inner">
                <div class="content content-full">
                    <div class="px-3 py-5 text-center">
                        <div class="row invisible" data-toggle="appear">
                            <div class="col-sm-12 text-center align-items-sm-center">
                                <span class="display-4 text-danger font-w700">404</span><span class="display-4 text-muted font-w300"> | Error</span>
                            </div>
                        </div>
                        <h1 class="h3 font-w700 mt-5 mb-3 invisible" data-toggle="appear" data-class="animated fadeInUp" data-timeout="300">Halaman Tidak Ditemukan</h1>
                        <h2 class="h5 font-w400 text-muted mb-5 invisible" data-toggle="appear" data-class="animated fadeInUp" data-timeout="450">Kemungkinan halaman telah dihapus atau Anda salah menulis URL.</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection