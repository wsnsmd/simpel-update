@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->  
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script>
        jQuery(function(){ 
            Dashmix.helpers(['select2', 'table-tools-checkable']); 
        });   
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Jadwal</li>
                        <li class="breadcrumb-item">Detail</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Buat Sertifikat Peserta</h3>
            </div>
            <div class="block-content block-content-full">
                <form action="{{ route('backend.diklat.sertifikat.buat.peserta.simpan', ['jadwal' => $jadwal->id]) }}" method="POST" autocomplete="off">
                    @csrf
                    <table class="table table-vcenter table-hover table-bordered table-striped js-table-checkable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">
                                    <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                                        <input type="checkbox" class="custom-control-input" id="check-all" name="check-all">
                                        <label class="custom-control-label" for="check-all"></label>
                                    </div>
                                </th>
                                <th class="font-w700" style="width: 60px;">Foto</th>
                                <th class="font-w700" style="width: 12%;">NIP</th>
                                <th class="font-w700" style="width: 20%;">Nama</th>
                                <th class="font-w700">Instansi</th>
                                <th class="font-w700">Satuan Kerja</th>
                                @if(($sertifikat->is_upload || is_null($sertifikat->format_nomor)))
                                <th class="font-w700" style="width: 20%;">Nomor Sertifikat</th>
                                @endif
                                @if($sertifikat->kualifikasi)
                                <th class="font-w700" style="width: 15%;">Kualifikasi</th>
                                @endif
                                <th class="font-w700" style="width: 13%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peserta as $p)
                            <tr>
                                <td class="text-center">
                                    <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                                        <input type="checkbox" class="custom-control-input" id="row_{{$loop->iteration}}" name="pid[]" value="{{$p->id}}">
                                        <label class="custom-control-label" for="row_{{$loop->iteration}}"></label>
                                    </div>
                                </td>
                                <td>
                                    <img src="{{ is_null($p->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($p->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    {{ $p->nip }}
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    {{ $p->nama_lengkap }}
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    {{ $p->instansi }}
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    {{ $p->satker_nama }}
                                </td>
                                @if(($sertifikat->is_upload || is_null($sertifikat->format_nomor)))
                                <td class="d-none d-sm-table-cell">
                                    <input type="text" class="form-control" id="row_nomor_{{$loop->iteration}}" name="pid_nomor_{{$p->id}}" value="">
                                </td>
                                @endif
                                @if($sertifikat->kualifikasi)
                                <td class="d-none d-sm-table-cell">
                                    <select class="form-control" id="row_kualifikasi_{{$loop->iteration}}" name="pid_kualifikasi_{{$p->id}}">
                                        <option value="-">-</option>
                                        <option value="Cukup Memuaskan">Cukup Memuaskan</option>
                                        <option value="Memuaskan">Memuaskan</option>
                                        <option value="Sangat Memuaskan">Sangat Memuaskan</option>                                        
                                    </select>
                                </td>
                                @endif
                                <td class="d-none d-sm-table-cell">
                                    <select class="form-control" id="row_kualifikasi_{{$loop->iteration}}" name="pid_status_{{$p->id}}" required>
                                        <option value="Telah Mengikuti">Telah Mengikuti</option>
                                        <option value="Lulus">Lulus</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                    <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-block btn-primary"{{ count($peserta) == 0 ? 'disabled' : ''}}>
                                    Buat Sertifikat
                                </button>                                        
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->
        
        
    </div>
    <!-- END Page Content -->
@endsection
