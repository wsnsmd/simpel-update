@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        @if (session('notifikasi'))
            $.notify({ icon: 'fa fa-check mr-1', message: "{{ session('notifikasi') }}" }, {
                allow_dismiss: false, type: 'success', placement: { from: 'top', align: 'center' }
            });
        @endif
    </script>
@endsection

@section('content')

    {{-- ── Page Hero ─────────────────────────────────────────────── --}}
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div>
                    <h1 class="flex-sm-fill font-size-h3 font-w700 mt-2 mb-1">
                        {{ $sesi->nama_materi }}
                    </h1>
                    <div class="d-flex flex-wrap text-muted font-size-sm" style="gap:.3rem .75rem">
                        <span><i class="fa fa-graduation-cap mr-1"></i>{{ $jadwal->nama }}</span>
                        <span><i
                                class="fa fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($sesi->tanggal)->format('d M Y') }}</span>
                        <span><i
                                class="fa fa-clock mr-1"></i>{{ substr($sesi->jam_mulai, 0, 5) }}–{{ substr($sesi->jam_selesai, 0, 5) }}</span>
                        <span><span class="badge badge-secondary">{{ $sesi->jp }} JP</span></span>
                    </div>
                </div>
                <div class="d-flex flex-wrap mt-3 mt-sm-0" style="gap:.5rem">
                    <a href="{{ route('backend.diklat.presensi.qr', $sesi->id) }}" class="btn btn-primary btn-sm"
                        target="_blank">
                        <i class="fa fa-qrcode mr-1"></i>QR Code
                    </a>
                    <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- ── Stat Cards ─────────────────────────────────────────── --}}
        @php
            $totalPeserta = $totalHadir + $totalTerlambat + $totalIzin + $totalSakit + $totalAlpha;
            $stats = [
                ['val' => $totalHadir, 'lbl' => 'Hadir', 'color' => 'success', 'icon' => 'fa-check-circle'],
                ['val' => $totalTerlambat, 'lbl' => 'Terlambat', 'color' => 'warning', 'icon' => 'fa-clock'],
                ['val' => $totalIzin, 'lbl' => 'Izin', 'color' => 'info', 'icon' => 'fa-info-circle'],
                ['val' => $totalSakit, 'lbl' => 'Sakit', 'color' => 'secondary', 'icon' => 'fa-heartbeat'],
                ['val' => $totalAlpha, 'lbl' => 'Tidak Hadir', 'color' => 'danger', 'icon' => 'fa-times-circle'],
            ];
        @endphp

        <div class="row mb-4">
            @foreach ($stats as $s)
                <div class="col-6 col-sm mb-3">
                    <div class="block block-rounded mb-0 h-100">
                        <div class="block-content py-3 text-center">
                            <div class="item item-circle bg-{{ $s['color'] }}-lighter mx-auto mb-2"
                                style="width:2.5rem;height:2.5rem;min-width:2.5rem">
                                <i class="fa {{ $s['icon'] }} text-{{ $s['color'] }}" style="font-size:.9rem"></i>
                            </div>
                            <div class="font-w700 font-size-h3 text-{{ $s['color'] }}">{{ $s['val'] }}</div>
                            <div class="text-muted font-size-sm">{{ $s['lbl'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Progress kehadiran --}}
        @if ($totalPeserta > 0)
            @php $pctHadir = round((($totalHadir + $totalTerlambat) / $totalPeserta) * 100); @endphp
            <div class="block block-rounded block-bordered mb-4">
                <div class="block-content py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-w600 font-size-sm">
                            <i class="fa fa-chart-pie text-primary mr-1"></i>Tingkat Kehadiran
                        </span>
                        <span
                            class="font-w700 {{ $pctHadir >= 80 ? 'text-success' : ($pctHadir >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ $pctHadir }}%
                        </span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-success" style="width:{{ round(($totalHadir / $totalPeserta) * 100) }}%"
                            title="Hadir"></div>
                        <div class="progress-bar bg-warning" style="width:{{ round(($totalTerlambat / $totalPeserta) * 100) }}%"
                            title="Terlambat"></div>
                        <div class="progress-bar bg-info" style="width:{{ round(($totalIzin / $totalPeserta) * 100) }}%"
                            title="Izin"></div>
                        <div class="progress-bar bg-secondary" style="width:{{ round(($totalSakit / $totalPeserta) * 100) }}%"
                            title="Sakit"></div>
                        <div class="progress-bar bg-danger" style="width:{{ round(($totalAlpha / $totalPeserta) * 100) }}%"
                            title="Tidak Hadir"></div>
                    </div>
                    <div class="d-flex flex-wrap mt-2 font-size-sm" style="gap:.25rem .875rem">
                        <span class="text-success"><i class="fa fa-circle mr-1"
                                style="font-size:.5rem;vertical-align:middle"></i>Hadir</span>
                        <span class="text-warning"><i class="fa fa-circle mr-1"
                                style="font-size:.5rem;vertical-align:middle"></i>Terlambat</span>
                        <span class="text-info"><i class="fa fa-circle mr-1"
                                style="font-size:.5rem;vertical-align:middle"></i>Izin</span>
                        <span class="text-secondary"><i class="fa fa-circle mr-1"
                                style="font-size:.5rem;vertical-align:middle"></i>Sakit</span>
                        <span class="text-danger"><i class="fa fa-circle mr-1"
                                style="font-size:.5rem;vertical-align:middle"></i>Tidak Hadir</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Tabel Desktop (≥ md) ───────────────────────────────── --}}
        <div class="block block-rounded d-none d-md-block">
            <div class="block-header block-header-default">
                <h3 class="block-title font-size-sm text-uppercase font-w700">
                    <i class="fa fa-list text-primary mr-1"></i>Daftar Presensi
                </h3>
                <div class="block-options">
                    <span class="text-muted font-size-sm">{{ count($presensi) }} peserta</span>
                </div>
            </div>
            <div class="block-content block-content-full p-0">
                <table class="table table-striped table-vcenter mb-0" style="font-size:.85rem">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>Peserta</th>
                            <th class="text-center" style="width:110px">Scan</th>
                            <th class="text-center" style="width:105px">Status</th>
                            <th style="width:260px">Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presensi as $i => $p)
                            <tr>
                                <td class="text-center text-muted">{{ $i + 1 }}</td>
                                <td>
                                    @if ($p->peserta)
                                        <div class="font-w600">{{ $p->peserta->nama_lengkap }}</div>
                                        <div class="text-muted font-size-sm">{{ $p->peserta->nip }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center font-size-sm">
                                    @if ($p->scan_at)
                                        <span class="font-w600">
                                            {{ \Carbon\Carbon::parse($p->scan_at)->format('H:i') }}
                                        </span>
                                        <div class="text-muted" style="font-size:.68rem">
                                            {{ \Carbon\Carbon::parse($p->scan_at)->format(':s') }}
                                        </div>
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
                                    <form action="{{ route('backend.diklat.presensi.update_status', $p->id) }}" method="POST"
                                        class="d-flex align-items-center" style="gap:.3rem">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-control form-control-sm"
                                            style="width:115px;min-width:0">
                                            @foreach (['hadir', 'terlambat', 'izin', 'sakit', 'alpha'] as $st)
                                                <option value="{{ $st }}" {{ $p->status === $st ? 'selected' : '' }}>
                                                    {{ \App\PresensiPeserta::labelStatus($st) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="keterangan" class="form-control form-control-sm"
                                            placeholder="Keterangan" value="{{ $p->keterangan }}" style="min-width:0;flex:1">
                                        <button type="submit" class="btn btn-sm btn-outline-primary flex-shrink-0"
                                            title="Simpan">
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

        {{-- ── Card Mobile (< md) ─────────────────────────────────── --}} <div class="d-md-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-w700 mb-0 font-size-sm text-uppercase text-muted" style="letter-spacing:.05em">
                    <i class="fa fa-list text-primary mr-1"></i>Daftar Presensi
                </h6>
                <span class="badge badge-secondary">{{ count($presensi) }} peserta</span>
            </div>

            @foreach ($presensi as $i => $p)
                @php $badgeColor = \App\PresensiPeserta::badgeStatus($p->status); @endphp
                <div class="block block-rounded block-bordered mb-3">

                    {{-- Header kartu --}}
                    <div class="block-header block-header-default">
                        <div style="min-width:0;flex:1">
                            @if ($p->peserta)
                                <h3 class="block-title font-w700 text-wrap-break-word">
                                    {{ $p->peserta->nama_lengkap }}
                                </h3>
                                <div class="text-muted font-size-sm">{{ $p->peserta->nip }}</div>
                            @else
                                <h3 class="block-title text-muted">Peserta tidak ditemukan</h3>
                            @endif
                        </div>
                        <div class="block-options">
                            <span class="badge badge-{{ $badgeColor }}">
                                {{ \App\PresensiPeserta::labelStatus($p->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Info waktu scan --}}
                    @if ($p->scan_at)
                        <div class="block-content block-content-sm py-2 bg-body-light">
                            <div class="d-flex align-items-center font-size-sm text-muted" style="gap:.5rem">
                                <i class="fa fa-qrcode text-primary"></i>
                                <span>Scan pada
                                    <strong class="text-body-color">
                                        {{ \Carbon\Carbon::parse($p->scan_at)->format('H:i:s') }}
                                    </strong>
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Form ubah status --}}
                    <div class="block-content py-3">
                        <form action="{{ route('backend.diklat.presensi.update_status', $p->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="form-group mb-2">
                                <label class="font-size-sm font-w600 mb-1">Ubah Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    @foreach (['hadir', 'terlambat', 'izin', 'sakit', 'alpha'] as $st)
                                        <option value="{{ $st }}" {{ $p->status === $st ? 'selected' : '' }}>
                                            {{ \App\PresensiPeserta::labelStatus($st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="font-size-sm font-w600 mb-1">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control form-control-sm"
                                    placeholder="Keterangan (opsional)" value="{{ $p->keterangan }}">
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fa fa-save mr-1"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
    </div>

    </div>
@endsection