@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
<style>
/* Tabel rekap sticky header & kolom pertama */
.rekap-wrap {
    overflow-x: auto;
    max-height: 75vh;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 6px;
}
.tbl-rekap {
    border-collapse: separate;
    border-spacing: 0;
    min-width: 100%;
    font-size: .82rem;
}
.tbl-rekap thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
    z-index: 3;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
    padding: .5rem .75rem;
    vertical-align: bottom;
}
/* Kolom No + Nama sticky kiri */
.tbl-rekap thead th:nth-child(1),
.tbl-rekap thead th:nth-child(2),
.tbl-rekap tbody td:nth-child(1),
.tbl-rekap tbody td:nth-child(2) {
    position: sticky;
    background: #fff;
    z-index: 2;
}
.tbl-rekap thead th:nth-child(1),
.tbl-rekap tbody td:nth-child(1) { left: 0; min-width: 44px; }
.tbl-rekap thead th:nth-child(2),
.tbl-rekap tbody td:nth-child(2) { left: 44px; min-width: 220px; border-right: 2px solid #dee2e6; }
.tbl-rekap thead th:nth-child(1),
.tbl-rekap thead th:nth-child(2) { z-index: 4; }
.tbl-rekap tbody tr:hover td { background: #f0f4ff !important; }
.tbl-rekap tbody td { padding: .4rem .75rem; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }

/* Status icon di sel */
.dok-status {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 50%;
    font-size: .8rem;
}
.dok-ok      { background: #d1fae5; color: #065f46; }
.dok-tunggu  { background: #fef3c7; color: #92400e; }
.dok-tolak   { background: #fee2e2; color: #991b1b; }
.dok-missing { background: #f1f5f9; color: #94a3b8; }

/* Stat chip di header kolom */
.stat-chip { font-size: .65rem; font-weight: 600; padding: .15em .4em; border-radius: 10px; }
</style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}"></script>
    <script>
    // Cepat verifikasi dari rekap
    function aksiDokumen(dokumenId, aksi, namaPeserta, namaDokumen) {
        var label = aksi === 'terima' ? 'Terima' : 'Tolak';
        var color = aksi === 'terima' ? '#28a745' : '#e74c3c';
        Swal.fire({
            title: label + ' dokumen?',
            html: '<b>' + namaDokumen + '</b><br>' + namaPeserta,
            icon: aksi === 'terima' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonText: 'Batal',
            confirmButtonText: label,
            input: aksi === 'tolak' ? 'text' : null,
            inputPlaceholder: 'Catatan penolakan (opsional)',
        }).then(function(result) {
            if (!result.value && aksi === 'terima' && !result.isConfirmed) return;
            if (!result.isConfirmed) return;
            var form = document.getElementById('form-aksi-' + dokumenId);
            form.querySelector('[name=aksi]').value = aksi;
            if (aksi === 'tolak' && result.value) {
                form.querySelector('[name=catatan_admin]').value = result.value;
            }
            form.submit();
        });
    }
    </script>
@endsection

@section('content')
<div class="content">

    {{-- Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-3">
        <div>
            <h2 class="h3 my-2">Rekapitulasi Dokumen</h2>
            <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
        </div>
        <div class="mt-2 mt-sm-0 d-flex" style="gap:.5rem">
            <a href="{{ route('backend.diklat.dokumen_syarat.download_all', $jadwal->id) }}"
               class="btn btn-success btn-sm">
                <i class="fa fa-file-archive mr-1"></i> Download Semua
            </a>
            <a href="{{ route('backend.diklat.dokumen_syarat.index', $jadwal->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-cog mr-1"></i> Kelola Syarat
            </a>
        </div>
    </div>

    @if (session('notifikasi'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            {{ session('notifikasi') }}
        </div>
    @endif

    @if ($daftarSyarat->isEmpty())
        <div class="alert alert-info">
            <i class="fa fa-info-circle mr-2"></i>
            Belum ada persyaratan dokumen untuk pelatihan ini.
            <a href="{{ route('backend.diklat.dokumen_syarat.index', $jadwal->id) }}">Tambahkan sekarang</a>.
        </div>
    @elseif ($pesertaList->isEmpty())
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            Belum ada peserta aktif yang terdaftar di pelatihan ini.
        </div>
    @else

    {{-- Statistik ringkas per syarat --}}
    <div class="row mb-3">
        @foreach ($daftarSyarat as $s)
        @php $st = $statPerSyarat[$s->id]; @endphp
        <div class="col-6 col-md-3 mb-2">
            <div class="block block-rounded mb-0" style="border-left: 3px solid {{ $s->wajib ? '#dc3545' : '#6c757d' }}">
                <div class="block-content py-2 px-3">
                    <div class="font-w600 font-size-sm mb-1 text-truncate" title="{{ $s->nama }}">{{ $s->nama }}</div>
                    <div class="d-flex flex-wrap" style="gap:.25rem">
                        <span class="stat-chip" style="background:#d1fae5;color:#065f46">
                            <i class="fa fa-check"></i> {{ $st['verified'] }} terima
                        </span>
                        <span class="stat-chip" style="background:#fef3c7;color:#92400e">
                            <i class="fa fa-clock"></i> {{ $st['uploaded'] - $st['verified'] - $st['rejected'] }} tunggu
                        </span>
                        @if ($st['rejected'] > 0)
                        <span class="stat-chip" style="background:#fee2e2;color:#991b1b">
                            <i class="fa fa-times"></i> {{ $st['rejected'] }} tolak
                        </span>
                        @endif
                        @if ($st['missing'] > 0)
                        <span class="stat-chip" style="background:#f1f5f9;color:#64748b">
                            <i class="fa fa-minus"></i> {{ $st['missing'] }} belum
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel rekap --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                Status Dokumen per Peserta
                <span class="badge badge-secondary ml-2">{{ $pesertaList->count() }} peserta</span>
            </h3>
            <div class="block-options">
                <small class="text-muted">
                    <span class="dok-status dok-ok d-inline-flex mr-1" style="width:16px;height:16px;font-size:.6rem"><i class="fa fa-check"></i></span>Diterima
                    <span class="dok-status dok-tunggu d-inline-flex mx-1" style="width:16px;height:16px;font-size:.6rem"><i class="fa fa-clock"></i></span>Menunggu
                    <span class="dok-status dok-tolak d-inline-flex mx-1" style="width:16px;height:16px;font-size:.6rem"><i class="fa fa-times"></i></span>Ditolak
                    <span class="dok-status dok-missing d-inline-flex mx-1" style="width:16px;height:16px;font-size:.6rem"><i class="fa fa-minus"></i></span>Belum upload
                </small>
            </div>
        </div>
        <div class="block-content p-0">
            <div class="rekap-wrap">
                <table class="tbl-rekap">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Peserta</th>
                            @foreach ($daftarSyarat as $s)
                            <th class="text-center" style="min-width:120px">
                                <div>{{ $s->nama }}</div>
                                @if ($s->wajib)
                                    <span class="badge badge-danger" style="font-size:.6rem">Wajib</span>
                                @else
                                    <span class="badge badge-secondary" style="font-size:.6rem">Opsional</span>
                                @endif
                            </th>
                            @endforeach
                            <th class="text-center" style="min-width:80px">Lengkap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesertaList as $i => $p)
                        @php
                            $dokumenPeserta  = isset($dokumenMap[$p->id]) ? $dokumenMap[$p->id] : array();
                            $wajibCount      = $daftarSyarat->where('wajib', true)->count();
                            $wajibTerpenuhi  = 0;
                            $semuaLengkap    = true;

                            foreach ($daftarSyarat as $s) {
                                if ($s->wajib && isset($dokumenPeserta[$s->id]) && $dokumenPeserta[$s->id]->verified_by_admin) {
                                    $wajibTerpenuhi++;
                                } elseif ($s->wajib) {
                                    $semuaLengkap = false;
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-w600">{{ $p->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $p->nip }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $p->instansi }}</div>
                                <div class="d-flex align-items-center mt-1" style="gap:.3rem">
                                    @if ($p->verifikasi)
                                        <span class="badge badge-success" style="font-size:.6rem">Terverifikasi</span>
                                    @else
                                        <span class="badge badge-warning" style="font-size:.6rem">Menunggu</span>
                                    @endif
                                    <a href="{{ route('backend.diklat.dokumen_syarat.download_by_peserta', [$jadwal->id, $p->id]) }}"
                                       class="btn btn-xs btn-outline-success"
                                       style="font-size:.65rem;padding:.1rem .35rem"
                                       title="Download semua dokumen peserta ini">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </td>

                            @foreach ($daftarSyarat as $s)
                            @php
                                $dok      = isset($dokumenPeserta[$s->id]) ? $dokumenPeserta[$s->id] : null;
                                $verified = $dok && $dok->verified_by_admin;
                                $rejected = $dok && !$dok->verified_by_admin && $dok->verified_at;
                                $waiting  = $dok && !$dok->verified_at;
                            @endphp
                            <td class="text-center">
                                @if ($verified)
                                    <span class="dok-status dok-ok" title="Diterima">
                                        <i class="fa fa-check"></i>
                                    </span>
                                @elseif ($rejected)
                                    {{-- Tombol aksi: upload sudah ada tapi ditolak --}}
                                    <span class="dok-status dok-tolak" title="Ditolak{{ $dok->catatan_admin ? ': '.$dok->catatan_admin : '' }}">
                                        <i class="fa fa-times"></i>
                                    </span>
                                    <br>
                                    <button type="button"
                                            class="btn btn-xs btn-outline-success mt-1"
                                            style="font-size:.65rem;padding:.1rem .35rem"
                                            onclick="aksiDokumen({{ $dok->id }}, 'terima', '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($s->nama) }}')">
                                        Terima
                                    </button>
                                @elseif ($waiting)
                                    {{-- File ada, menunggu verifikasi --}}
                                    <span class="dok-status dok-tunggu" title="Menunggu verifikasi">
                                        <i class="fa fa-clock"></i>
                                    </span>
                                    <br>
                                    <div class="d-flex justify-content-center mt-1" style="gap:.2rem">
                                        <button type="button"
                                                class="btn btn-xs btn-outline-success"
                                                style="font-size:.65rem;padding:.1rem .35rem"
                                                onclick="aksiDokumen({{ $dok->id }}, 'terima', '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($s->nama) }}')">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-xs btn-outline-danger"
                                                style="font-size:.65rem;padding:.1rem .35rem"
                                                onclick="aksiDokumen({{ $dok->id }}, 'tolak', '{{ addslashes($p->nama_lengkap) }}', '{{ addslashes($s->nama) }}')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                    {{-- Link lihat file --}}
                                    <button type="button"
                                            class="d-block mx-auto mt-1 btn btn-xs btn-outline-primary"
                                            style="font-size:.65rem;padding:.1rem .4rem"
                                            onclick="previewDokumen({{ $dok->id }})">
                                        <i class="fa fa-eye"></i> Lihat
                                    </button>
                                @else
                                    {{-- Belum upload --}}
                                    <span class="dok-status dok-missing" title="Belum diupload">
                                        <i class="fa fa-minus"></i>
                                    </span>
                                @endif

                                {{-- Form hidden untuk verifikasi cepat --}}
                                @if ($dok && !$verified)
                                <form id="form-aksi-{{ $dok->id }}"
                                      action="{{ route('backend.diklat.dokumen_peserta.verifikasi', $dok->id) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                    <input type="hidden" name="aksi" value="">
                                    <input type="hidden" name="catatan_admin" value="">
                                </form>
                                @endif
                            </td>
                            @endforeach

                            {{-- Kolom kelengkapan --}}
                            <td class="text-center">
                                @if ($wajibCount == 0)
                                    <span class="text-muted font-size-sm">—</span>
                                @elseif ($semuaLengkap)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check-circle"></i> Lengkap
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        {{ $wajibTerpenuhi }}/{{ $wajibCount }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endif
</div>

@include('backend.diklat.dokumen_syarat._modal_preview')

@endsection
