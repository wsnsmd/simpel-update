@extends('layouts.frontend')

@section('content')
    <div class="content content-full">
        <div class="row justify-content-center mt-5">
            <div class="col-sm-10 col-md-6 col-lg-4">
                <div class="block block-rounded block-themed mb-0">
                    <div class="block-header bg-primary">
                        <h3 class="block-title">
                            <i class="fa fa-clipboard-check mr-1"></i> Konfirmasi Presensi
                        </h3>
                    </div>
                    <div class="block-content block-content-full">

                        {{-- Info sesi --}}
                        <div class="bg-body-light rounded p-3 mb-4">
                            <div class="font-w700 font-size-sm text-uppercase text-muted mb-1">Sesi Pelatihan</div>
                            <div class="font-w600" style="font-size:1rem">{{ $sesi->nama_materi }}</div>
                            @if ($sesi->widyaiswara)
                                <div class="text-muted font-size-sm">
                                    <i class="fa fa-chalkboard-teacher mr-1"></i>{{ $sesi->widyaiswara }}
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between font-size-sm">
                                <span class="text-muted">Tanggal</span>
                                <span class="font-w600">
                                    {{ \Carbon\Carbon::parse($sesi->tanggal)->format('d M Y') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between font-size-sm mt-1">
                                <span class="text-muted">Waktu</span>
                                <span class="font-w600">
                                    {{ substr($sesi->jam_mulai, 0, 5) }} – {{ substr($sesi->jam_selesai, 0, 5) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between font-size-sm mt-1">
                                <span class="text-muted">JP</span>
                                <span class="font-w600">{{ $sesi->jp }} JP</span>
                            </div>
                        </div>

                        {{-- Info peserta --}}
                        <div class="d-flex align-items-center mb-4" style="gap:.75rem">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white font-w700"
                                style="width:48px;height:48px;flex-shrink:0;font-size:1.1rem">
                                @php
                                    $words = explode(' ', $peserta->nama_lengkap);
                                    echo strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                @endphp
                            </div>
                            <div>
                                <div class="font-w600">{{ $peserta->nama_lengkap }}</div>
                                <div class="text-muted font-size-sm">{{ $peserta->nip }}</div>
                            </div>
                        </div>

                        {{-- Peringatan terlambat --}}
                        @php
                            $now = \Carbon\Carbon::now();
                            $mulai = \Carbon\Carbon::parse($sesi->tanggal->format('Y-m-d') . ' ' . $sesi->jam_mulai);
                            $batas = $mulai->copy()->addMinutes($sesi->batas_terlambat);
                            $akánTerlambat = $now->gt($batas);
                        @endphp

                        @if ($akánTerlambat)
                            <div class="alert alert-warning font-size-sm">
                                <i class="fa fa-exclamation-triangle mr-1"></i>
                                Anda melewati batas waktu kehadiran ({{ $sesi->batas_terlambat }} menit).
                                Presensi akan dicatat sebagai <strong>Terlambat</strong>.
                            </div>
                        @endif

                        <form action="{{ route('peserta.presensi.konfirmasi', $token) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-block btn-primary">
                                <i class="fa fa-check-circle mr-2"></i>
                                Konfirmasi Kehadiran
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('peserta.dashboard') }}" class="text-muted font-size-sm">
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection