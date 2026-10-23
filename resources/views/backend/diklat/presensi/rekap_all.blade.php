@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
<style>
.rekap-wrap { overflow-x:auto; max-height:75vh; overflow-y:auto; border:1px solid #dee2e6; border-radius:6px; }
.tbl-rekap { border-collapse:separate; border-spacing:0; min-width:100%; font-size:.8rem; }
.tbl-rekap thead th { position:sticky; top:0; background:#f8f9fa; z-index:3; border-bottom:2px solid #dee2e6; white-space:nowrap; padding:.4rem .6rem; vertical-align:bottom; }
.tbl-rekap thead th:nth-child(1),
.tbl-rekap thead th:nth-child(2),
.tbl-rekap tbody td:nth-child(1),
.tbl-rekap tbody td:nth-child(2) { position:sticky; background:#fff; z-index:2; }
.tbl-rekap thead th:nth-child(1), .tbl-rekap tbody td:nth-child(1) { left:0; min-width:40px; }
.tbl-rekap thead th:nth-child(2), .tbl-rekap tbody td:nth-child(2) { left:40px; min-width:200px; border-right:2px solid #dee2e6; }
.tbl-rekap thead th:nth-child(1),
.tbl-rekap thead th:nth-child(2) { z-index:4; }
.tbl-rekap tbody td { padding:.35rem .6rem; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.tbl-rekap tbody tr:hover td { background:#f0f4ff !important; }
.badge-h { background:#d1fae5; color:#065f46; }
.badge-t { background:#fef3c7; color:#92400e; }
.badge-i { background:#dbeafe; color:#1e40af; }
.badge-s { background:#f1f5f9; color:#475569; }
.badge-a { background:#fee2e2; color:#991b1b; }
.status-pill { display:inline-block; font-size:.68rem; font-weight:600; padding:.15em .5em; border-radius:12px; }
</style>
@endsection

@section('content')
<div class="content">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-4">
        <div>
            <h2 class="h3 my-2">Rekap Presensi</h2>
            <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
        </div>
        <div class="d-flex mt-2 mt-sm-0" style="gap:.5rem">
            <a href="{{ route('backend.diklat.presensi.export_excel', $jadwal->id) }}"
               class="btn btn-success btn-sm">
                <i class="fa fa-file-excel mr-1"></i> Export Excel
            </a>
            <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    @if ($sesiList->isEmpty())
        <div class="alert alert-info">Belum ada sesi presensi.</div>
    @elseif ($pesertaList->isEmpty())
        <div class="alert alert-warning">Belum ada peserta terverifikasi.</div>
    @else

    {{-- Ringkasan per sesi --}}
    <div class="row mb-3">
        @foreach ($sesiList as $s)
        @php
            $hadirCount = 0;
            foreach ($pesertaList as $p) {
                $st = isset($presensiMap[$p->id][$s->id]) ? $presensiMap[$p->id][$s->id]->status : 'alpha';
                if (in_array($st, ['hadir','terlambat'])) $hadirCount++;
            }
        @endphp
        <div class="col-6 col-md-3 mb-2">
            <div class="block block-rounded mb-0" style="border-left:3px solid #1d4ed8">
                <div class="block-content py-2 px-3">
                    <div class="font-size-sm font-w600 text-truncate" title="{{ $s->nama_materi }}">
                        {{ $s->nama_materi }}
                    </div>
                    <div class="text-muted" style="font-size:.72rem">
                        {{ \Carbon\Carbon::parse($s->tanggal)->format('d M') }}
                        {{ substr($s->jam_mulai,0,5) }}
                    </div>
                    <div class="mt-1">
                        <span class="text-success font-w600">{{ $hadirCount }}</span>
                        <span class="text-muted">/ {{ $pesertaList->count() }} hadir</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel rekap silang --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default d-flex justify-content-between">
            <h3 class="block-title">Rekap per Peserta</h3>
            <small class="text-muted align-self-center">
                <span class="status-pill badge-h">H</span>=Hadir
                <span class="status-pill badge-t ml-1">T</span>=Terlambat
                <span class="status-pill badge-i ml-1">I</span>=Izin
                <span class="status-pill badge-s ml-1">S</span>=Sakit
                <span class="status-pill badge-a ml-1">A</span>=Alpha
            </small>
        </div>
        <div class="block-content p-0">
            <div class="rekap-wrap">
                <table class="tbl-rekap">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Peserta</th>
                            @foreach ($sesiList as $s)
                            <th class="text-center" style="min-width:80px">
                                <div>{{ \Illuminate\Support\Str::limit($s->nama_materi, 18) }}</div>
                                <div style="font-size:.65rem;font-weight:400;color:#6b7280">
                                    {{ \Carbon\Carbon::parse($s->tanggal)->format('d/m') }}
                                </div>
                            </th>
                            @endforeach
                            <th class="text-center" style="min-width:60px">Hadir</th>
                            <th class="text-center" style="min-width:60px">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesertaList as $i => $p)
                        @php
                            $hadirP     = 0;
                            $totalSesi  = $sesiList->count();
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-w600" style="font-size:.82rem">{{ $p->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size:.68rem">{{ $p->nip }}</div>
                            </td>
                            @foreach ($sesiList as $s)
                            @php
                                $st = isset($presensiMap[$p->id][$s->id])
                                    ? $presensiMap[$p->id][$s->id]->status
                                    : 'alpha';
                                $badgeMap = ['hadir'=>'h','terlambat'=>'t','izin'=>'i','sakit'=>'s','alpha'=>'a'];
                                $labelMap = ['hadir'=>'H','terlambat'=>'T','izin'=>'I','sakit'=>'S','alpha'=>'A'];
                                if (in_array($st, ['hadir','terlambat'])) $hadirP++;
                            @endphp
                            <td class="text-center">
                                <span class="status-pill badge-{{ $badgeMap[$st] ?? 'a' }}">
                                    {{ $labelMap[$st] ?? 'A' }}
                                </span>
                                @if (isset($presensiMap[$p->id][$s->id]) && $presensiMap[$p->id][$s->id]->scan_at)
                                <div style="font-size:.65rem;color:#94a3b8">
                                    {{ \Carbon\Carbon::parse($presensiMap[$p->id][$s->id]->scan_at)->format('H:i') }}
                                </div>
                                @endif
                            </td>
                            @endforeach
                            <td class="text-center font-w600 text-success">{{ $hadirP }}</td>
                            <td class="text-center font-size-sm">
                                @if ($totalSesi > 0)
                                    {{ round(($hadirP / $totalSesi) * 100) }}%
                                @else —
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
@endsection
