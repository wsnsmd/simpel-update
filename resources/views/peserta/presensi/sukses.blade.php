@extends('layouts.frontend')

@section('content')
<div class="content content-full">
    <div class="row justify-content-center mt-5">
        <div class="col-sm-10 col-md-6 col-lg-4 text-center">
            <div class="block block-rounded">
                <div class="block-content block-content-full py-5">
                    <div class="mb-3">
                        @if ($status === 'hadir')
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success"
                                 style="width:80px;height:80px">
                                <i class="fa fa-check fa-2x text-white"></i>
                            </div>
                            <h3 class="font-w700 mt-3 text-success">Presensi Berhasil!</h3>
                            <p class="text-muted">Kehadiran Anda telah tercatat.</p>
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-warning"
                                 style="width:80px;height:80px">
                                <i class="fa fa-clock fa-2x text-white"></i>
                            </div>
                            <h3 class="font-w700 mt-3 text-warning">Terlambat</h3>
                            <p class="text-muted">Presensi Anda dicatat sebagai <strong>Terlambat</strong>.</p>
                        @endif
                    </div>

                    <div class="bg-body-light rounded p-3 mb-4 text-left">
                        <div class="d-flex justify-content-between font-size-sm mb-1">
                            <span class="text-muted">Nama</span>
                            <span class="font-w600">{{ $peserta->nama_lengkap }}</span>
                        </div>
                        <div class="d-flex justify-content-between font-size-sm mb-1">
                            <span class="text-muted">Sesi</span>
                            <span class="font-w600">{{ $sesi->nama_materi }}</span>
                        </div>
                        <div class="d-flex justify-content-between font-size-sm mb-1">
                            <span class="text-muted">Waktu scan</span>
                            <span class="font-w600">{{ $now->format('H:i:s') }}</span>
                        </div>
                        <div class="d-flex justify-content-between font-size-sm">
                            <span class="text-muted">Status</span>
                            <span class="badge badge-{{ $status === 'hadir' ? 'success' : 'warning' }}">
                                {{ $status === 'hadir' ? 'Hadir' : 'Terlambat' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary">
                        <i class="fa fa-home mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
