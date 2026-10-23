{{-- backend/diklat/nilai/laporan_seminar.blade.php --}}
@extends('layouts.backend')
@section('sidebar') @include('layouts.sidebar_jadwal') @endsection

@section('content')

    {{-- ══════════════════════════════════════════════════════════════
    HEADER
    ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-body-light border-bottom">
        <div class="content content-full py-4">

            {{-- Breadcrumb --}}
            <nav class="mb-2" style="font-size:.8rem">
                <a href="{{ route('backend.diklat.nilai.rekap', $jadwal->id) }}" class="text-muted">
                    <i class="fa fa-chart-bar mr-1"></i>Rekap Nilai
                </a>
                <span class="mx-1 text-muted">/</span>
                <span class="text-body-color">Laporan Seminar</span>
            </nav>

            <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:.75rem">
                <div>
                    <h1 class="font-w700 mb-1" style="font-size:1.2rem">
                        <i class="fa fa-folder-open text-primary mr-2"></i>Laporan Seminar Peserta
                    </h1>
                    <p class="text-muted font-size-sm mb-0">
                        {{ $jadwal->nama }}
                    </p>
                </div>

                {{-- Tombol Export CSV --}}
                <a href="{{ route('backend.diklat.nilai.laporan_seminar.export', $jadwal->id) }}"
                    class="btn btn-success btn-sm d-inline-flex align-items-center flex-shrink-0" style="gap:.4rem">
                    <i class="fa fa-file-csv"></i>
                    <span>Export CSV</span>
                </a>
            </div>

        </div>
    </div>

    <div class="content content-full">

        {{-- ══════════════════════════════════════════════════════════
        STAT CARDS
        ══════════════════════════════════════════════════════════ --}}
        @php $totalPeserta = $pesertaList->count(); @endphp
        <div class="row mb-4" style="row-gap:.75rem">
            @php
                $cards = [
                    ['val' => $totalPeserta, 'lbl' => 'Total Peserta', 'icon' => 'fa-users', 'bg' => 'bg-primary-lighter', 'fg' => 'text-primary'],
                    ['val' => $totalUploadR, 'lbl' => 'Upload Rancangan', 'icon' => 'fa-clock', 'bg' => 'bg-warning-lighter', 'fg' => 'text-warning'],
                    ['val' => $totalUploadA, 'lbl' => 'Upload Akhir', 'icon' => 'fa-star', 'bg' => 'bg-info-lighter', 'fg' => 'text-info'],
                    ['val' => $totalPeserta - max($totalUploadR, $totalUploadA), 'lbl' => 'Belum Upload', 'icon' => 'fa-exclamation-circle', 'bg' => 'bg-danger-lighter', 'fg' => 'text-danger'],
                ];
            @endphp
            @foreach ($cards as $c)
                <div class="col-6 col-md-3">
                    <div class="block block-rounded mb-0 h-100" style="box-shadow:none;border:1px solid #e4e9f0">
                        <div class="block-content py-3 d-flex align-items-center" style="gap:.75rem">
                            <div class="item item-rounded {{ $c['bg'] }} flex-shrink-0">
                                <i class="fa {{ $c['icon'] }} {{ $c['fg'] }}"></i>
                            </div>
                            <div>
                                <div class="font-w700 {{ $c['fg'] }}" style="font-size:1.6rem;line-height:1">
                                    {{ max(0, $c['val']) }}
                                </div>
                                <div class="text-muted text-uppercase" style="font-size:.65rem;letter-spacing:.04em">
                                    {{ $c['lbl'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════════
        TABEL LAPORAN
        ══════════════════════════════════════════════════════════ --}}
        <div class="block block-rounded" style="box-shadow:none;border:1px solid #e4e9f0">

            {{-- Toolbar: filter kelompok + search --}}
            <div class="block-header py-3 bg-body-light border-bottom d-flex flex-wrap align-items-center"
                style="gap:.5rem">
                <div class="flex-grow-1">
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                        placeholder="Cari nama / NIP / judul laporan..." style="max-width:320px">
                </div>
                <div class="d-flex align-items-center" style="gap:.4rem">
                    <label class="text-muted font-size-sm mb-0">Filter:</label>
                    <select id="filterFase" class="form-control form-control-sm" style="width:auto">
                        <option value="">Semua Peserta</option>
                        <option value="uploaded_r">Ada Rancangan</option>
                        <option value="uploaded_a">Ada Akhir</option>
                        <option value="missing_r">Belum Upload Rancangan</option>
                        <option value="missing_a">Belum Upload Akhir</option>
                        <option value="missing_any">Belum Upload Sama Sekali</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-vcenter mb-0" id="tblLaporan" style="font-size:.82rem">
                    <thead class="bg-body-light">
                        <tr>
                            <th class="text-muted" style="width:2.5rem">#</th>
                            <th>Peserta</th>
                            <th class="d-none d-md-table-cell" style="width:130px">Kelompok</th>
                            <th style="width:260px">
                                <span class="badge badge-warning mr-1" style="font-size:.65rem">
                                    <i class="fa fa-clock mr-1"></i>Rancangan
                                </span>
                            </th>
                            <th style="width:260px">
                                <span class="badge badge-primary mr-1" style="font-size:.65rem">
                                    <i class="fa fa-star mr-1"></i>Akhir
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesertaList as $i => $p)
                            @php
                                $lapR = $laporanMap[$p->id]['rancangan'] ?? null;
                                $lapA = $laporanMap[$p->id]['akhir'] ?? null;
                                $kelompok = $kelompokMap[$p->id] ?? '-';
                            @endphp
                            <tr class="peserta-row" data-nama="{{ strtolower($p->nama_lengkap) }}" data-nip="{{ $p->nip }}"
                                data-judul-r="{{ strtolower($lapR->judul ?? '') }}"
                                data-judul-a="{{ strtolower($lapA->judul ?? '') }}" data-has-r="{{ $lapR ? '1' : '0' }}"
                                data-has-a="{{ $lapA ? '1' : '0' }}">

                                <td class="text-muted font-size-sm">{{ $i + 1 }}</td>

                                {{-- Info Peserta --}}
                                <td>
                                    <div class="font-w600" style="font-size:.83rem">{{ $p->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size:.72rem">
                                        {{ $p->nip ?: '-' }}
                                        @if ($p->instansi)
                                            &bull; {{ \Illuminate\Support\Str::limit($p->instansi, 35) }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Kelompok --}}
                                <td class="d-none d-md-table-cell">
                                    <span class="badge badge-secondary font-size-xs">{{ $kelompok }}</span>
                                </td>

                                {{-- Laporan Rancangan --}}
                                <td>
                                    @if ($lapR)
                                        <a href="{{ $lapR->url }}" target="_blank" rel="noopener noreferrer"
                                            class="btn btn-sm btn-warning d-flex align-items-center"
                                            style="gap:.4rem;text-align:left;padding:.3rem .6rem;max-width:240px"
                                            title="{{ $lapR->judul }}">
                                            <i class="fa fa-clock flex-shrink-0" style="font-size:.75rem"></i>
                                            <span style="min-width:0;flex:1">
                                                <span class="d-block"
                                                    style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.75rem;font-weight:600">
                                                    {{ \Illuminate\Support\Str::limit($lapR->judul, 30) }}
                                                </span>
                                                <span class="d-block text-dark" style="font-size:.65rem;opacity:.65;margin-top:1px">
                                                    {{ \Carbon\Carbon::parse($lapR->updated_at)->format('d M Y H:i') }}
                                                </span>
                                            </span>
                                            <i class="fa fa-external-link-alt flex-shrink-0" style="font-size:.6rem;opacity:.7"></i>
                                        </a>
                                    @else
                                        <span class="text-muted font-size-sm">
                                            <i class="fa fa-minus-circle mr-1 text-danger" style="font-size:.7rem"></i>Belum upload
                                        </span>
                                    @endif
                                </td>

                                {{-- Laporan Akhir --}}
                                <td>
                                    @if ($lapA)
                                        <a href="{{ $lapA->url }}" target="_blank" rel="noopener noreferrer"
                                            class="btn btn-sm btn-primary d-flex align-items-center"
                                            style="gap:.4rem;text-align:left;padding:.3rem .6rem;max-width:240px"
                                            title="{{ $lapA->judul }}">
                                            <i class="fa fa-star flex-shrink-0" style="font-size:.75rem"></i>
                                            <span style="min-width:0;flex:1">
                                                <span class="d-block"
                                                    style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.75rem;font-weight:600">
                                                    {{ \Illuminate\Support\Str::limit($lapA->judul, 30) }}
                                                </span>
                                                <span class="d-block text-white"
                                                    style="font-size:.65rem;opacity:.75;margin-top:1px">
                                                    {{ \Carbon\Carbon::parse($lapA->updated_at)->format('d M Y H:i') }}
                                                </span>
                                            </span>
                                            <i class="fa fa-external-link-alt flex-shrink-0" style="font-size:.6rem;opacity:.7"></i>
                                        </a>
                                    @else
                                        <span class="text-muted font-size-sm">
                                            <i class="fa fa-minus-circle mr-1 text-danger" style="font-size:.7rem"></i>Belum upload
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa fa-users fa-2x mb-2 d-block"></i>
                                    Belum ada peserta terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tidak ada hasil filter --}}
            <div id="noResult" class="text-center py-4 text-muted d-none">
                <i class="fa fa-search fa-2x mb-2 d-block"></i>
                Tidak ada peserta yang sesuai filter.
            </div>

        </div>

    </div>

@endsection

@section('js_after')
    <script>
        (function () {
            var searchInput = document.getElementById('searchInput');
            var filterSelect = document.getElementById('filterFase');
            var rows = document.querySelectorAll('.peserta-row');
            var noResult = document.getElementById('noResult');

            function applyFilter() {
                var q = searchInput.value.toLowerCase().trim();
                var filter = filterSelect.value;
                var visible = 0;

                rows.forEach(function (row) {
                    var nama = row.dataset.nama || '';
                    var nip = row.dataset.nip || '';
                    var judulR = row.dataset.judulR || '';
                    var judulA = row.dataset.judulA || '';
                    var hasR = row.dataset.hasR === '1';
                    var hasA = row.dataset.hasA === '1';

                    var matchSearch = !q || nama.indexOf(q) > -1 || nip.indexOf(q) > -1
                        || judulR.indexOf(q) > -1 || judulA.indexOf(q) > -1;

                    var matchFilter = true;
                    if (filter === 'uploaded_r') matchFilter = hasR;
                    if (filter === 'uploaded_a') matchFilter = hasA;
                    if (filter === 'missing_r') matchFilter = !hasR;
                    if (filter === 'missing_a') matchFilter = !hasA;
                    if (filter === 'missing_any') matchFilter = !hasR && !hasA;

                    var show = matchSearch && matchFilter;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                noResult.classList.toggle('d-none', visible > 0);
            }

            searchInput.addEventListener('input', applyFilter);
            filterSelect.addEventListener('change', applyFilter);
        })();
    </script>
@endsection