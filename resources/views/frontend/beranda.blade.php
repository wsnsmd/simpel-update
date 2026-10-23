@extends('layouts.frontend')

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/photos/bg_bpsdm.jpg') }}');">
        <div class="hero bg-primary-dark-op overflow-hidden">
            <div class="hero-inner">
                <div class="content content-full">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 text-center">
                            <h2 class="font-w600 mb-2 invisible text-white-75" data-toggle="appear" style="letter-spacing: 1px; font-size: 0.85rem; text-transform: uppercase;">
                                Pemerintah Provinsi Kalimantan Timur
                            </h2>
                            <h1 class="font-w700 mb-2 invisible text-white" data-toggle="appear" data-timeout="100">
                                Sistem Informasi Manajemen Pelatihan
                            </h1>
                            <p class="font-size-lg font-w300 mb-4 invisible text-white-75" data-toggle="appear" data-timeout="200">
                                Platform digital penunjang kegiatan pelatihan berbasis online
                                BPSDM Provinsi Kalimantan Timur
                            </p>
                            <div class="invisible" data-toggle="appear" data-timeout="300">
                                <a class="btn btn-hero-primary mr-1 mb-2" href="{{ route('jadwal.index') }}">
                                    <i class="fa fa-calendar-alt mr-1"></i> Lihat Jadwal
                                </a>
                                <a class="btn btn-hero-warning mb-2" href="{{ route('informasi') }}">
                                    <i class="fa fa-info-circle mr-1"></i> Informasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-meta">
                <div class="invisible" data-toggle="appear" data-timeout="450">
                    <span class="d-inline-block animated bounce infinite">
                        <i class="si si-arrow-down text-white-50 fa-2x"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Layanan Cepat -->
    <div class="bg-white border-bottom">
        <div class="content content-full py-4">
            <div class="row text-center invisible" data-toggle="appear">
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <a class="block block-rounded block-bordered block-link-shadow" href="{{ route('jadwal.index') }}">
                        <div class="block-content block-content-full">
                            <div class="py-2">
                                <i class="fa fa-2x fa-calendar-alt text-primary"></i>
                                <div class="font-w600 font-size-sm mt-2">Jadwal Pelatihan</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-3 mb-md-0">
                    <a class="block block-rounded block-bordered block-link-shadow" href="{{ route('alumni.index') }}">
                        <div class="block-content block-content-full">
                            <div class="py-2">
                                <i class="fa fa-2x fa-compass text-success"></i>
                                <div class="font-w600 font-size-sm mt-2">Data Alumni</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="{{ route('sertifikat.cek') }}">
                        <div class="block-content block-content-full">
                            <div class="py-2">
                                <i class="fa fa-2x fa-address-card text-warning"></i>
                                <div class="font-w600 font-size-sm mt-2">Cek Sertifikat</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="{{ route('informasi') }}">
                        <div class="block-content block-content-full">
                            <div class="py-2">
                                <i class="fa fa-2x fa-info-circle text-info"></i>
                                <div class="font-w600 font-size-sm mt-2">Informasi</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- END Layanan Cepat -->

    <!-- Jadwal Pelatihan -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="pt-4 pb-2">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 invisible" data-toggle="appear">
                    <div class="mb-2 mb-md-0">
                        <h2 class="h3 font-w700 mb-1">Jadwal Pelatihan {{ $tahun }}</h2>
                        <p class="font-size-sm text-muted mb-0">Pelatihan terdekat yang akan datang</p>
                    </div>
                    <a class="btn btn-sm btn-alt-primary" href="{{ route('jadwal.index') }}">
                        Lihat Semua <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            @if($jadwal->count() > 0)
                <div class="row invisible" data-toggle="appear">
                    @foreach ($jadwal as $j)
                        <div class="col-md-6 col-xl-4">
                            <a class="block block-rounded block-bordered block-link-shadow" href="{{ route('jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}">
                                <div class="block-content block-content-full">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge badge-primary font-size-xs">{{ $j->jenis }}</span>
                                        @if ($j->status == '1')
                                            <span class="badge badge-success">Berjalan</span>
                                        @else
                                            <span class="badge badge-info">Akan Datang</span>
                                        @endif
                                    </div>
                                    <p class="font-w600 text-dark mb-2" style="min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $j->nama }}
                                    </p>
                                    <div class="font-size-sm text-muted border-top pt-2">
                                        <div class="mb-1">
                                            <i class="fa fa-fw fa-calendar-alt text-muted mr-1"></i>
                                            {{ formatTgl2($j->tgl_awal) }} - {{ formatTgl2($j->tgl_akhir) }}
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span><i class="fa fa-fw fa-layer-group text-muted mr-1"></i> {{ $j->kelas }} Kelas</span>
                                            <span><i class="fa fa-fw fa-users text-muted mr-1"></i> Kuota {{ $j->kuota }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="block block-rounded block-bordered invisible" data-toggle="appear">
                    <div class="block-content block-content-full text-center py-5">
                        <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="font-w600 text-muted mb-0">Belum ada jadwal pelatihan yang tersedia</p>
                    </div>
                </div>
            @endif

            <div class="text-center py-4 invisible" data-toggle="appear" data-class="animated fadeInUp">
                <a class="btn btn-hero-primary" href="{{ route('jadwal.index') }}">
                    <i class="fa fa-calendar-alt mr-1"></i> Lihat Seluruh Jadwal Pelatihan
                </a>
            </div>
        </div>
    </div>
    <!-- END Jadwal Pelatihan -->
@endsection
