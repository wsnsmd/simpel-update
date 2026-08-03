@extends('layouts.backend')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        /* ── Stat cards ── */
        .jdw-stat {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .875rem;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            text-decoration: none;
            color: inherit;
        }

        .jdw-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .1);
            text-decoration: none;
            color: inherit;
        }

        .jdw-stat.active {
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px currentColor;
        }

        .jdw-stat .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .jdw-stat .stat-val {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .jdw-stat .stat-lbl {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .75;
        }

        /* ── Filter pills ── */
        .filter-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-bottom: 1.25rem;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .85rem;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            border: 1.5px solid #dee2e6;
            background: #fff;
            color: #495057;
            cursor: pointer;
            transition: all .15s;
        }

        .filter-pill:hover {
            border-color: #adb5bd;
            background: #f8f9fa;
        }

        .filter-pill.active {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }

        /* ── Jadwal cards ── */
        .jdw-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
        }

        .jdw-card {
            border: 1px solid #e4e9f0;
            border-radius: 12px;
            background: #fff;
            transition: box-shadow .15s, transform .15s;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .jdw-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            transform: translateY(-2px);
        }

        .jdw-card .jdw-accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .jdw-card .jdw-body {
            padding: 1rem 1rem 1rem 1.25rem;
            flex: 1;
        }

        .jdw-card .jdw-nama {
            font-weight: 600;
            font-size: .9rem;
            color: #1e293b;
            line-height: 1.4;
            margin-bottom: .35rem;
        }

        .jdw-card .jdw-meta {
            font-size: .75rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .2rem;
        }

        .jdw-card .jdw-footer {
            padding: .65rem 1rem .65rem 1.25rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafbfc;
        }

        /* Kuota progress */
        .kuota-bar {
            height: 5px;
            border-radius: 3px;
            background: #e5e7eb;
            margin-top: 4px;
        }

        .kuota-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .3s;
        }

        /* Tabel mode */
        .tbl-mode table {
            font-size: .82rem;
        }

        .tbl-mode .badge {
            font-size: .68rem;
        }

        /* ── Paging ── */
        .jdw-paging {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        .jdw-paging .pg-btn {
            min-width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e4e9f0;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all .15s;
            padding: 0 .5rem;
        }

        .jdw-paging .pg-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .jdw-paging .pg-btn.active {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }

        .jdw-paging .pg-btn.disabled {
            opacity: .4;
            pointer-events: none;
        }

        .perpage-select {
            font-size: .8rem;
            padding: .2rem .5rem;
            border-radius: 6px;
            border: 1px solid #e4e9f0;
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        var allCards = [];
        var filtered = [];
        var currentPage = 1;
        var perPage = parseInt(localStorage.getItem('jdw_perpage') || '12');

        $(function () {
            document.querySelectorAll('.jdw-card').forEach(function (card) {
                allCards.push(card);
            });

            // Isi perpage select
            document.getElementById('perpage-select').value = perPage;

            $('#jdw-search').on('input', function () { currentPage = 1; applyFilter(); });
            applyFilter();

            @if (session('success'))
                $.notify({ icon: "fa fa-check mr-1", message: "{{ session('success') }}" }, {
                    allow_dismiss: false, type: 'success', placement: { from: "top", align: "center" }
                });
            @elseif (session('error'))
                $.notify({ icon: "fa fa-times mr-1", message: "{{ session('error') }}" }, {
                    allow_dismiss: false, type: 'danger', placement: { from: "top", align: "center" }
                });
            @endif
                                    });

        var activeFilter = 'semua';

        function setFilter(f) {
            activeFilter = f;
            currentPage = 1;
            document.querySelectorAll('.filter-pill').forEach(function (p) {
                p.classList.toggle('active', p.dataset.filter === f);
            });
            document.querySelectorAll('.jdw-stat').forEach(function (s) {
                s.classList.toggle('active', s.dataset.filter === f);
            });
            applyFilter();
        }

        function setPerPage(val) {
            perPage = parseInt(val);
            currentPage = 1;
            localStorage.setItem('jdw_perpage', perPage);
            renderPage();
        }

        function applyFilter() {
            var q = ($('#jdw-search').val() || '').toLowerCase();

            filtered = allCards.filter(function (card) {
                var status = card.dataset.status;
                var text = card.dataset.search;
                var passQ = !q || text.indexOf(q) !== -1;
                var passF = (activeFilter === 'semua') ||
                    (activeFilter === 'berjalan' && status === 'berjalan') ||
                    (activeFilter === 'akan_datang' && status === 'akan_datang') ||
                    (activeFilter === 'selesai' && status === 'selesai');
                return passQ && passF;
            });

            renderPage();
        }

        function renderPage() {
            var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
            currentPage = Math.min(currentPage, totalPages);

            var start = (currentPage - 1) * perPage;
            var end = start + perPage;

            // Tampilkan/sembunyikan card
            allCards.forEach(function (card) { card.style.display = 'none'; });
            filtered.slice(start, end).forEach(function (card) { card.style.display = ''; });

            // Update info
            var showing = Math.min(end, filtered.length) - start;
            document.getElementById('jdw-count').textContent =
                filtered.length + ' jadwal · halaman ' + currentPage + ' dari ' + totalPages;

            // Render tombol paging
            renderPaging(totalPages);
        }

        function renderPaging(totalPages) {
            var wrap = document.getElementById('jdw-paging');
            wrap.innerHTML = '';

            if (totalPages <= 1) return;

            function btn(label, page, isActive, isDisabled) {
                var el = document.createElement('span');
                el.className = 'pg-btn' +
                    (isActive ? ' active' : '') +
                    (isDisabled ? ' disabled' : '');
                el.innerHTML = label;
                if (!isDisabled && !isActive) {
                    el.onclick = function () { currentPage = page; renderPage(); };
                }
                wrap.appendChild(el);
            }

            // Prev
            btn('<i class="fa fa-chevron-left"></i>', currentPage - 1, false, currentPage <= 1);

            // Nomor halaman dengan ellipsis
            var pages = [];
            for (var i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    pages.push(i);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }

            pages.forEach(function (p) {
                if (p === '...') {
                    var el = document.createElement('span');
                    el.className = 'pg-btn disabled';
                    el.textContent = '…';
                    wrap.appendChild(el);
                } else {
                    btn(p, p, p === currentPage, false);
                }
            });

            // Next
            btn('<i class="fa fa-chevron-right"></i>', currentPage + 1, false, currentPage >= totalPages);
        }

        function toggleView(mode) {
            var grid = document.getElementById('jdw-grid');
            document.querySelectorAll('.view-toggle .btn').forEach(function (b) {
                b.classList.toggle('active', b.dataset.view === mode);
            });
            grid.classList.toggle('jdw-grid', mode === 'grid');
            grid.classList.toggle('tbl-mode', mode === 'list');
            localStorage.setItem('jdw_view', mode);
        }

        function showAlert(form) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Data tidak dapat dikembalikan.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-danger m-1', cancelButton: 'btn btn-secondary m-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) form.submit();
            });
        }

        $(function () {
            var saved = localStorage.getItem('jdw_view') || 'grid';
            toggleView(saved);
        });
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <div>
                        <h1 class="h3 font-w700 mb-1">Jadwal Pelatihan</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">Pelatihan</li>
                                <li class="breadcrumb-item active">Jadwal</li>
                            </ol>
                        </nav>
                    </div>
                    @can('isUser') @cannot('isViewer')
                        <a href="{{ route('backend.diklat.jadwal.create') }}" class="btn btn-primary mt-3 mt-sm-0">
                            <i class="fa fa-plus mr-1"></i> Tambah Jadwal
                        </a>
                    @endcannot @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- ── Stat cards ── --}}
        <div class="row mb-4">
            @foreach ([
                    ['key' => 'semua', 'val' => $stats['total'], 'lbl' => 'Total Jadwal', 'icon' => 'fa-th-list', 'bg' => '#eff6ff', 'fg' => '#1d4ed8'],
                    ['key' => 'berjalan', 'val' => $stats['berjalan'], 'lbl' => 'Sedang Berjalan', 'icon' => 'fa-play-circle', 'bg' => '#f0fdf4', 'fg' => '#15803d'],
                    ['key' => 'akan_datang', 'val' => $stats['akan_datang'], 'lbl' => 'Akan Datang', 'icon' => 'fa-calendar-alt', 'bg' => '#fff7ed', 'fg' => '#c2410c'],
                    ['key' => 'selesai', 'val' => $stats['selesai'], 'lbl' => 'Selesai', 'icon' => 'fa-check-circle', 'bg' => '#f1f5f9', 'fg' => '#475569'],
                ] as $s)
                <div class="col-6 col-xl-3 mb-3">
                    <div class="jdw-stat block block-rounded mb-0" data-filter="{{ $s['key'] }}"
                        onclick="setFilter('{{ $s['key'] }}')" style="box-shadow:0 2px 8px rgba(0,0,0,.06)">
                        <div class="stat-icon" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }}">
                            <i class="fa {{ $s['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="stat-val" style="color:{{ $s['fg'] }}">{{ $s['val'] }}</div>
                            <div class="stat-lbl" style="color:{{ $s['fg'] }}">{{ $s['lbl'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Toolbar ── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap:.75rem">
            <div class="filter-pills">
                @foreach ([
                        ['key' => 'semua', 'lbl' => 'Semua'],
                        ['key' => 'berjalan', 'lbl' => 'Berjalan'],
                        ['key' => 'akan_datang', 'lbl' => 'Akan Datang'],
                        ['key' => 'selesai', 'lbl' => 'Selesai'],
                    ] as $f)
                    <span class="filter-pill {{ $f['key'] === 'semua' ? 'active' : '' }}" data-filter="{{ $f['key'] }}"
                        onclick="setFilter('{{ $f['key'] }}')">
                        {{ $f['lbl'] }}
                    </span>
                @endforeach
            </div>
            <div class="d-flex align-items-center" style="gap:.5rem">
                <input type="text" id="jdw-search" class="form-control form-control-sm" placeholder="Cari nama, jenis..."
                    style="width:200px">
                <select class="form-control form-control-sm perpage-select" id="perpage-select"
                    onchange="setPerPage(this.value)" title="Jumlah per halaman">
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="48">48</option>
                    <option value="99999">Semua</option>
                </select>
                <div class="btn-group view-toggle">
                    <button class="btn btn-sm btn-outline-secondary" data-view="grid" onclick="toggleView('grid')"
                        title="Tampilan grid">
                        <i class="fa fa-th-large"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" data-view="list" onclick="toggleView('list')"
                        title="Tampilan list">
                        <i class="fa fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted" id="jdw-count">{{ count($jadwal) }} jadwal</small>
        </div>

        {{-- ── Card grid ── --}}
        <div id="jdw-grid" class="jdw-grid">
            @forelse ($jadwal as $j)
                @php
                    $today = now()->toDateString();
                    $isPast = $j->tgl_akhir < $today;
                    $isRunning = $j->tgl_awal <= $today && $j->tgl_akhir >= $today && $j->status == 1;
                    $isFuture = $j->tgl_awal > $today && $j->status == 1;

                    $statusKey = $isRunning ? 'berjalan' : ($isFuture ? 'akan_datang' : 'selesai');
                    $accentColor = $isRunning ? '#15803d' : ($isFuture ? '#c2410c' : '#94a3b8');
                    $badgeCls = $isRunning ? 'badge-success' : ($isFuture ? 'badge-warning' : 'badge-secondary');
                    $badgeLbl = $isRunning ? 'Berjalan' : ($isFuture ? 'Akan Datang' : 'Selesai');

                    $jmlPeserta = isset($pesertaCount[$j->id]) ? $pesertaCount[$j->id] : 0;
                    $kuotaPct = $j->kuota > 0 ? min(100, round(($jmlPeserta / $j->kuota) * 100)) : 0;
                    $kuotaColor = $kuotaPct >= 90 ? '#dc2626' : ($kuotaPct >= 70 ? '#d97706' : '#15803d');

                    $searchText = strtolower($j->nama . ' ' . $j->jenis . ' ' . $j->kelas);
                @endphp
                <div class="jdw-card mb-3" data-status="{{ $statusKey }}" data-search="{{ $searchText }}">

                    <div class="jdw-accent" style="background:{{ $accentColor }}"></div>

                    <div class="jdw-body">
                        {{-- Badge status --}}
                        <div class="d-flex align-items-start justify-content-between mb-2" style="gap:.5rem">
                            <span class="badge {{ $badgeCls }}" style="font-size:.68rem">{{ $badgeLbl }}</span>
                            <span class="text-muted" style="font-size:.7rem;white-space:nowrap">
                                {{ \Carbon\Carbon::parse($j->tgl_awal)->format('d M') }}
                                –
                                {{ \Carbon\Carbon::parse($j->tgl_akhir)->format('d M Y') }}
                            </span>
                        </div>

                        {{-- Nama --}}
                        <div class="jdw-nama">{{ $j->nama }}</div>

                        {{-- Meta --}}
                        <div class="jdw-meta">
                            <i class="fa fa-tag" style="font-size:.65rem"></i>
                            {{ $j->jenis ?? '-' }}
                        </div>
                        @if ($j->kelas)
                            <div class="jdw-meta">
                                <i class="fa fa-building" style="font-size:.65rem"></i>
                                {{ $j->kelas }}
                            </div>
                        @endif

                        {{-- Kuota --}}
                        <div class="mt-2">
                            <div class="d-flex justify-content-between align-items-center" style="font-size:.72rem">
                                <span class="text-muted">Peserta terverifikasi</span>
                                <span style="font-weight:600;color:{{ $kuotaColor }}">
                                    {{ $jmlPeserta }} / {{ $j->kuota }}
                                </span>
                            </div>
                            <div class="kuota-bar">
                                <div class="kuota-fill" style="width:{{ $kuotaPct }}%;background:{{ $kuotaColor }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer aksi --}}
                    <div class="jdw-footer">
                        <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-cog mr-1"></i> Kelola
                        </a>
                        <div class="d-flex" style="gap:.3rem">
                            @can('isCreator', $j) @cannot('isViewer')
                                <a href="{{ route('backend.diklat.jadwal.edit', $j->id) }}" class="btn btn-sm btn-outline-secondary"
                                    title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('backend.diklat.jadwal.destroy', $j->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="showAlert($(this).closest('form')[0])" title="Hapus">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endcannot @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="block block-rounded text-center py-5">
                        <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada jadwal pelatihan.</p>
                        @can('isUser') @cannot('isViewer')
                            <a href="{{ route('backend.diklat.jadwal.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus mr-1"></i> Tambah Jadwal
                            </a>
                        @endcannot @endcan
                    </div>
                </div>
            @endforelse
        </div>{{-- end jdw-grid --}}

        {{-- Paging --}}
        <div id="jdw-paging" class="jdw-paging mb-3"></div>

    </div>
@endsection