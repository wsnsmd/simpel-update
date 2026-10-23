{{-- peserta/laporan_seminar/index.blade.php --}}
@extends('layouts.frontend')

@section('content')

    {{-- ═══════════════════════════════════════════════════════
    HERO
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-body border-bottom py-4">
        <div class="container">
            <div class="d-flex align-items-center" style="gap:.875rem">
                <div class="item item-rounded bg-primary-lighter flex-shrink-0">
                    <i class="fa fa-link text-primary"></i>
                </div>
                <div>
                    <h1 class="font-w700 mb-0" style="font-size:1.2rem">Upload Tautan Laporan Seminar</h1>
                    <p class="text-muted font-size-sm mb-0">
                        Bagikan tautan laporan Anda agar dapat dibaca oleh penguji kelompok.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4 mb-5">

        {{-- Notifikasi --}}
        @if (session('notifikasi'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle mr-2"></i>{{ session('notifikasi') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Panduan --}}
        <div class="block block-rounded border-left border-primary border-4x mb-4" style="box-shadow:none">
            <div class="block-content py-3">
                <p class="font-w600 mb-2"><i class="fa fa-info-circle text-primary mr-1"></i>Panduan Pengisian</p>
                <ul class="mb-0 pl-4 text-muted font-size-sm" style="line-height:1.8">
                    <li>Unggah tautan laporan dari <strong>Google Drive, OneDrive, Dropbox, Notion,</strong> atau <strong>Canva</strong>.</li>
                    <li>Pastikan tautan sudah diatur <strong>bisa diakses siapa saja</strong> yang memiliki link (publik/anyone with link).</li>
                    <li>Anda dapat mengubah tautan selama data penilaian belum dikunci oleh panitia.</li>
                    <li>Setiap pelatihan memiliki slot laporan <strong>Seminar Rancangan</strong> dan <strong>Seminar Akhir</strong> secara terpisah.</li>
                </ul>
            </div>
        </div>

        {{-- Tidak ada pelatihan --}}
        @if ($jadwalList->isEmpty())
            <div class="block block-rounded text-center py-5" style="box-shadow:none">
                <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Anda belum terdaftar di pelatihan yang memiliki sesi seminar.</p>
                <a href="{{ route('peserta.dashboard') }}" class="btn btn-sm btn-outline-primary mt-3">
                    <i class="fa fa-arrow-left mr-1"></i>Kembali ke Dashboard
                </a>
            </div>
        @endif

        {{-- Daftar pelatihan --}}
        @foreach ($jadwalList as $jdw)
            <div class="block block-rounded mb-4" style="box-shadow:0 1px 6px rgba(0,0,0,.07)">

                {{-- Header jadwal --}}
                <div class="block-header py-3 bg-body-light border-bottom">
                    <h3 class="block-title font-size-base font-w700 mb-0">
                        <i class="fa fa-graduation-cap text-primary mr-2"></i>{{ $jdw->jadwal_nama }}
                    </h3>
                    <div class="block-options">
                        <span class="badge badge-{{ $jdw->is_locked ? 'danger' : 'success' }}">
                            <i class="fa fa-{{ $jdw->is_locked ? 'lock' : 'lock-open' }} mr-1"></i>
                            {{ $jdw->is_locked ? 'Dikunci' : 'Terbuka' }}
                        </span>
                    </div>
                </div>

                <div class="block-content py-3">
                    <div class="row" style="gap:0">

                        {{-- ── Seminar Rancangan ──────────────────────── --}}
                        @if ($jdw->ada_rancangan)
                            <div class="col-12 @if($jdw->ada_akhir) col-md-6 border-right @endif mb-3 mb-md-0">
                                @include('peserta.laporan_seminar._slot', [
                                    'fase'       => 'rancangan',
                                    'label'      => 'Seminar Rancangan',
                                    'icon'       => 'fa-clock',
                                    'colorClass' => 'warning',
                                    'laporan'    => $jdw->laporan_rancangan,
                                    'jadwalId'   => $jdw->jadwal_id,
                                    'isLocked'   => $jdw->is_locked,
                                ])
                            </div>
                        @endif

                        {{-- ── Seminar Akhir ───────────────────────────── --}}
                        @if ($jdw->ada_akhir)
                            <div class="col-12 @if($jdw->ada_rancangan) col-md-6 pl-md-4 @endif">
                                @include('peserta.laporan_seminar._slot', [
                                    'fase'       => 'akhir',
                                    'label'      => 'Seminar Akhir',
                                    'icon'       => 'fa-star',
                                    'colorClass' => 'primary',
                                    'laporan'    => $jdw->laporan_akhir,
                                    'jadwalId'   => $jdw->jadwal_id,
                                    'isLocked'   => $jdw->is_locked,
                                ])
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach

        {{-- Kembali --}}
        <div class="text-center mt-2">
            <a href="{{ route('peserta.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i>Kembali ke Dashboard
            </a>
        </div>

    </div>

@endsection
