@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Detail Jadwal</h1>
                    <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Jadwal</li>
                            <li class="breadcrumb-item">Detail</li>
                            <li class="breadcrumb-item active" aria-current="page">{{$jadwal->nama}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Detail</h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table font-size-sm" style="width:100%">
                        <tbody>
                            <tr>
                                <td class="font-w700 text-right" style="width:25%">Tahun Pelaksanaan:</td>
                                <td>{{$jadwal->tahun}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Jenis Pelatihan:</td>
                                <td>{{$jadwal->jenis}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Nama Pelatihan:</td>
                                <td>{{$jadwal->nama}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Kelas:</td>
                                <td>{{$jadwal->kelas}}</td>
                            </tr>                                    
                            <tr>
                                <td class="font-w700 text-right">Tanggal Pelatihan:</td>
                                <td>{{$jadwal->tgl_awal}} s/d {{$jadwal->tgl_akhir}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Lokasi:</td>
                                <td>{{$jadwal->lokasi}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Kuota:</td>
                                <td>{{$jadwal->kuota}} Peserta</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Panitia:</td>
                                <td>{{$jadwal->panitia_nama}} | {{$jadwal->panitia_telp}} | {{$jadwal->panitia_email}}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Lampiran:</td>
                                <td>
                                    @if(!is_null($jadwal->lampiran))
                                    <a href="#" class="font-w700 link-fx">Unduh-File</a>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Deskripsi:</td>
                                <td>{!!$jadwal->deskripsi!!}</td>
                            </tr>
                            <tr>
                                <td class="font-w700 text-right">Syarat:</td>
                                <td>{!!$jadwal->syarat!!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>       
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->     
@endsection
