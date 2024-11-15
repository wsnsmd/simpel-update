@extends('layouts.frontend')

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/photos/bg_bpsdm.jpg')}}');">
        <div class="hero bg-primary-dark-op overflow-hidden">
            <div class="hero-inner">
                <div class="content content-full">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h1 class="h3 font-w700 mb-3 invisible text-white" data-toggle="appear">
                                Pemerintah Provinsi Kalimantan Timur
                            </h1>
                            <h1 class="h1 font-w700 mb-3 invisible text-white" data-toggle="appear">
                                Sistem Informasi Manajemen Pelatihan
                            </h1>
                            <p class="font-size-h4 font-w300 text-muted mb-5 invisible text-white-75" data-toggle="appear">
                                Aplikasi Penunjang Kegiatan Pelatihan Berbasis Online
                            </p>
                            <span class="ml-2 mr-2 mb-2 ml-lg-0 d-inline-block invisible" data-toggle="appear" data-timeout="150">
                                <a class="btn btn-sm btn-hero-primary" href="{{ route('jadwal.index') }}">
                                    <i class="fa fa-fw fa-calendar-alt mr-1"></i> Jadwal
                                </a>
                            </span>
                            <span class="d-inline-block invisible" data-toggle="appear" data-timeout="150">
                                <a class="btn btn-sm btn-hero-warning" href="{{ route('informasi') }}">
                                    <i class="fa fa-fw fa-info mr-1"></i> Informasi
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-meta">
                <div class="invisible" data-toggle="appear" data-timeout="450">
                    <span class="d-inline-block animated bounce infinite">
                        <i class="si si-arrow-down text-muted fa-2x"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Inspiring Dashboards -->
    <div id="dm-dashboards" class="bg-white">
        <div class="content content-full">
            <div class="pt-0 push">
                <h1 class="mb-2 text-center">
                    Jadwal Pelatihan {{$tahun}}<br />
                    Pemerintah Provinsi Kalimantan Timur
                </h1>
                <h5 class="font-w300 text-muted mb-0 text-center">
                    Berikut Jadwal Pelatihan Berdasarkan Tanggal Terdekat
                </h5>
            </div>
            <div class="row py-3 invisible" data-toggle="appear">
                <div class="block block-rounded" style="width: 100%">
                    <div class="block-content block-content-full">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th class="font-w700">Nama Pelatihan</th>
                                        <th class="font-w700 text-center">Jenis Pelatihan</th>
                                        <th class="font-w700 text-center">Tanggal Pelatihan</th>
                                        <th class="font-w700 text-center">Kelas</th>
                                        <th class="font-w700 text-center">Kuota</th>
                                        <th class="font-w700 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwal as $j)
                                    <tr>
                                        <td class="font-w600"><a href="{{ route('jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}">{{ $j->nama}}</td>
                                        <td class="font-w400 text-center">{{$j->jenis}}</td>
                                        <td class="font-w400 text-center">{{formatTgl2($j->tgl_awal)}} - {{formatTgl2($j->tgl_akhir)}}</td>
                                        <td class="font-w400 text-center">{{$j->kelas}}</td>
                                        <td class="font-w400 text-center">{{$j->kuota}}</td>
                                        <td class="text-center">
                                            @if ($j->status == '1')
                                                <span class="badge badge-success">Berjalan</span>
                                            @else
                                                <span class="badge badge-primary">Akan Datang</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-5 text-center">
                <div class="invisible" data-toggle="appear" data-class="animated fadeInUp">
                    <a class="btn btn-hero-primary" href="{{ route('jadwal.index') }}">
                        <i class="fa fa-calendar-alt mr-1"></i> Lihat Seluruh Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- END Inspiring Dashboards -->
@endsection
