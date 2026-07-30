@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('js_after')
<script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script>
    @if (session('notifikasi'))
    $.notify({ icon: "fa fa-check mr-1", message: "{{ session('notifikasi') }}" }, {
        allow_dismiss: false, type: 'success', placement: { from: "top", align: "center" }
    });
    @endif
</script>
@endsection

@section('content')
<div class="content">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-4">
        <div>
            <h2 class="h3 my-2">{{ $sesi->nama_materi }}</h2>
            <p class="text-muted mb-0 font-size-sm">
                {{ $jadwal->nama }} &bull;
                {{ \Carbon\Carbon::parse($sesi->tanggal)->format('d M Y') }}
                {{ substr($sesi->jam_mulai, 0, 5) }}–{{ substr($sesi->jam_selesai, 0, 5) }}
                &bull; {{ $sesi->jp }} JP
            </p>
        </div>
        <div class="d-flex mt-2 mt-sm-0" style="gap:.5rem">
            <a href="{{ route('backend.diklat.presensi.qr', $sesi->id) }}"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-qrcode mr-1"></i> QR Code
            </a>
            <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row mb-4">
        @foreach ([
            ['val' => $totalHadir,     'lbl' => 'Hadir',       'color' => 'success'],
            ['val' => $totalTerlambat, 'lbl' => 'Terlambat',   'color' => 'warning'],
            ['val' => $totalIzin,      'lbl' => 'Izin',        'color' => 'info'],
            ['val' => $totalSakit,     'lbl' => 'Sakit',       'color' => 'secondary'],
            ['val' => $totalAlpha,     'lbl' => 'Tidak Hadir', 'color' => 'danger'],
        ] as $s)
        <div class="col mb-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="font-size-h2 font-w700 text-{{ $s['color'] }}">{{ $s['val'] }}</div>
                    <div class="text-muted font-size-sm">{{ $s['lbl'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Daftar Presensi</h3>
        </div>
        <div class="block-content p-0">
            <div class="table-responsive">
                <table class="table table-striped table-vcenter mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>Peserta</th>
                            <th class="text-center" style="width:120px">Waktu Scan</th>
                            <th class="text-center" style="width:100px">Status</th>
                            <th style="width:220px">Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presensi as $i => $p)
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>
                                @if ($p->peserta)
                                    <div class="font-w600">{{ $p->peserta->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $p->peserta->nip }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center font-size-sm">
                                @if ($p->scan_at)
                                    {{ \Carbon\Carbon::parse($p->scan_at)->format('H:i:s') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ \App\PresensiPeserta::badgeStatus($p->status) }}">
                                    {{ \App\PresensiPeserta::labelStatus($p->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('backend.diklat.presensi.update_status', $p->id) }}"
                                      method="POST" class="d-flex" style="gap:.3rem">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-control form-control-sm" style="width:110px">
                                        @foreach (['hadir','terlambat','izin','sakit','alpha'] as $st)
                                        <option value="{{ $st }}" {{ $p->status === $st ? 'selected' : '' }}>
                                            {{ \App\PresensiPeserta::labelStatus($st) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="keterangan"
                                           class="form-control form-control-sm"
                                           placeholder="Keterangan"
                                           value="{{ $p->keterangan }}"
                                           style="width:80px">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
