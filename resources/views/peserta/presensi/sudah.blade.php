@extends('layouts.frontend')

@section('content')
<div class="content content-full">
    <div class="row justify-content-center mt-5">
        <div class="col-sm-10 col-md-6 col-lg-4 text-center">
            <div class="block block-rounded">
                <div class="block-content block-content-full py-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                         style="width:80px;height:80px;background:#dbeafe">
                        <i class="fa fa-info-circle fa-2x text-primary"></i>
                    </div>
                    <h3 class="font-w700 mt-3">Sudah Presensi</h3>
                    <p class="text-muted">Anda sudah tercatat hadir pada sesi ini.</p>

                    <div class="bg-body-light rounded p-3 mb-4 text-left font-size-sm">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Sesi</span>
                            <span class="font-w600">{{ $sesi->nama_materi }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Waktu scan</span>
                            <span class="font-w600">
                                {{ \Carbon\Carbon::parse($sudahPresensi->scan_at)->format('H:i:s') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="badge badge-{{ \App\PresensiPeserta::badgeStatus($sudahPresensi->status) }}">
                                {{ \App\PresensiPeserta::labelStatus($sudahPresensi->status) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary">
                        <i class="fa fa-home mr-1"></i> Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
