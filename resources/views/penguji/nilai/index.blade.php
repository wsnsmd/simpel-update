{{-- penguji/nilai/index.blade.php --}}
@extends('layouts.frontend')

@section('css_before')
    <style>
        /* ── Custom styles yang tidak tersedia di Bootstrap 4 ── */

        /* Hero Section */
        .penguji-hero {
            background: linear-gradient(135deg, #1e293b 0%, #1d4ed8 100%);
            padding: 1.5rem 0;
            border-bottom: 3px solid #1d4ed8;
        }

        .penguji-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .15);
            border: 2px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .penguji-info {
            min-width: 0;
        }

        .penguji-nama {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .penguji-sub {
            font-size: .75rem;
            color: rgba(255, 255, 255, .65);
        }

        .penguji-jadwal {
            padding: .4rem .75rem;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 6px;
            font-size: .78rem;
            color: rgba(255, 255, 255, .8);
            display: flex;
            align-items: flex-start;
            gap: .4rem;
            margin-top: .5rem;
        }

        /* Progress */
        .progress-wrap {
            background: rgba(255, 255, 255, .08);
            border-radius: 6px;
            padding: .5rem .75rem;
            margin-top: .5rem;
        }

        .progress-item {
            flex: 1;
            min-width: 120px;
        }

        .progress-item+.progress-item {
            margin-top: .5rem;
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }

        .progress-lbl {
            font-size: .7rem;
            color: rgba(255, 255, 255, .65);
        }

        .progress-cnt {
            font-size: .72rem;
            font-weight: 700;
            color: #fff;
        }

        .progress-bar-custom {
            height: 5px;
            background: rgba(255, 255, 255, .15);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .4s;
        }

        .fill-green {
            background: #22c55e;
        }

        .fill-blue {
            background: #60a5fa;
        }

        /* Content */
        .penguji-content {
            padding: 1.25rem 0 1.5rem;
        }

        /* Peserta Card */
        .peserta-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e4e9f0;
            margin-bottom: .75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
            transition: box-shadow .15s;
            height: 100%;
        }

        .peserta-card:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
        }

        .peserta-top {
            padding: .65rem .75rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }

        .peserta-no {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .peserta-body {
            flex: 1;
            min-width: 0;
        }

        .peserta-nama {
            font-size: .85rem;
            font-weight: 700;
            color: #1e293b;
            word-break: break-word;
        }

        .peserta-nip {
            font-size: .68rem;
            color: #94a3b8;
        }

        .peserta-instansi {
            font-size: .68rem;
            color: #64748b;
            margin-top: 2px;
            word-break: break-word;
        }

        .status-row {
            display: flex;
            gap: .3rem;
            flex-wrap: wrap;
            margin-top: .3rem;
        }

        .sbadge {
            font-size: .58rem;
            font-weight: 700;
            padding: .15em .55em;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: .15rem;
        }

        .sbadge.done {
            background: #d1fae5;
            color: #065f46;
        }

        .sbadge.pending {
            background: #f1f5f9;
            color: #94a3b8;
        }

        /* Actions */
        .peserta-actions {
            padding: .4rem .75rem .65rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: .3rem;
            flex-wrap: wrap;
        }

        .btn-nilai {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            padding: .35rem .7rem;
            border-radius: 6px;
            font-size: .72rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
            flex: 1;
            min-width: 0;
        }

        .btn-nilai:hover {
            text-decoration: none;
        }

        .btn-nilai i {
            font-size: .7rem;
        }

        /* Button Variants */
        .bv-rancangan-todo {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .bv-rancangan-todo:hover {
            background: #fde68a;
            color: #78350f;
        }

        .bv-rancangan-done {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .bv-rancangan-done:hover {
            background: #dcfce7;
            color: #14532d;
        }

        .bv-akhir-ready {
            background: #1d4ed8;
            color: #fff;
            border: 1px solid #1d4ed8;
        }

        .bv-akhir-ready:hover {
            background: #1e40af;
            color: #fff;
        }

        .bv-akhir-todo {
            background: #f8faff;
            color: #94a3b8;
            border: 1px solid #e4e9f0;
        }

        .bv-akhir-todo:hover {
            background: #eef2f6;
            color: #64748b;
        }

        .bv-akhir-done {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .bv-akhir-done:hover {
            background: #dcfce7;
            color: #14532d;
        }

        /* Alert */
        .alert-custom {
            margin-bottom: 1rem;
        }

        /* Footer */
        .penguji-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e4e9f0;
            font-size: .7rem;
            color: #94a3b8;
            line-height: 1.8;
        }

        /* ── Responsive breakpoints ── */

        /* Small devices (landscape phones, 576px and up) */
        @media (min-width: 576px) {
            .penguji-avatar {
                width: 52px;
                height: 52px;
                font-size: 1.25rem;
            }

            .penguji-nama {
                font-size: 1.05rem;
            }

            .peserta-top {
                padding: .75rem 1rem;
            }

            .peserta-actions {
                padding: .5rem 1rem .75rem;
            }

            .btn-nilai {
                font-size: .78rem;
                padding: .4rem .85rem;
            }

            .progress-item+.progress-item {
                margin-top: 0;
            }
        }

        /* Medium devices (tablets, 768px and up) */
        @media (min-width: 768px) {
            .penguji-hero {
                padding: 1.75rem 0 1.5rem;
            }

            .penguji-content {
                padding: 1.5rem 0 2rem;
            }

            .peserta-no {
                width: 26px;
                height: 26px;
                font-size: .72rem;
            }

            .peserta-nama {
                font-size: .875rem;
            }

            .sbadge {
                font-size: .6rem;
            }
        }

        /* Large devices (desktops, 992px and up) */
        @media (min-width: 992px) {
            .peserta-card {
                margin-bottom: 1rem;
            }
        }

        /* Extra small devices (portrait phones, less than 576px) */
        @media (max-width: 575.98px) {
            .penguji-hero {
                padding: 1rem 0;
            }

            .penguji-avatar {
                width: 40px;
                height: 40px;
                font-size: .9rem;
            }

            .penguji-nama {
                font-size: .9rem;
            }

            .penguji-sub {
                font-size: .68rem;
            }

            .penguji-jadwal {
                font-size: .7rem;
                padding: .3rem .6rem;
            }

            .peserta-top {
                padding: .5rem .6rem;
                gap: .4rem;
            }

            .peserta-actions {
                padding: .3rem .6rem .5rem;
                gap: .25rem;
            }

            .btn-nilai {
                font-size: .65rem;
                padding: .3rem .5rem;
            }

            .btn-nilai i {
                font-size: .6rem;
            }

            .status-row {
                gap: .2rem;
            }

            .sbadge {
                font-size: .5rem;
                padding: .1em .45em;
            }

            .progress-wrap {
                padding: .4rem .6rem;
            }

            .progress-item {
                min-width: 100px;
            }

            .progress-lbl {
                font-size: .65rem;
            }

            .progress-cnt {
                font-size: .65rem;
            }
        }

        /* Very small devices */
        @media (max-width: 380px) {
            .peserta-actions {
                flex-direction: column;
            }

            .btn-nilai {
                width: 100%;
                justify-content: center;
            }

            .penguji-jadwal {
                flex-direction: column;
                align-items: flex-start;
                gap: .2rem;
            }

            .status-row {
                flex-direction: column;
                align-items: flex-start;
                gap: .15rem;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $totalPeserta = $pesertaList->count();
        $selesaiR = isset($statusNilai)
            ? collect($statusNilai)->filter(function ($s) {
                return !empty($s['rancangan_selesai']);
            })->count()
            : 0;
        $selesaiA = isset($statusNilai)
            ? collect($statusNilai)->filter(function ($s) {
                return !empty($s['akhir_selesai']);
            })->count()
            : 0;
        $adaRancangan = $setup ? DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->where('fase', 'rancangan')->where('penilai', 'penguji')
            ->whereNotNull('parent_id')->exists() : false;
        $adaAkhir = $setup ? DB::table('nilai_komponen')
            ->where('template_id', $setup->template_id)
            ->where('fase', 'akhir')->where('penilai', 'penguji')
            ->whereNotNull('parent_id')->exists() : false;
        $pctR = $totalPeserta > 0 ? round(($selesaiR / $totalPeserta) * 100) : 0;
        $pctA = $totalPeserta > 0 ? round(($selesaiA / $totalPeserta) * 100) : 0;
    @endphp

    {{-- ── HERO ── --}}
    <div class="penguji-hero">
        <div class="container">
            <div class="d-flex align-items-center" style="gap:.75rem">
                <div class="penguji-avatar flex-shrink-0">
                    <i class="fa fa-user-tie"></i>
                </div>
                <div class="penguji-info flex-grow-1">
                    <div class="penguji-nama">{{ $penguji->nama }}</div>
                    <div class="penguji-sub">
                        <i class="fa fa-users mr-1"></i>{{ $seminar->kelompok }}
                        <span class="d-none d-sm-inline">&bull;</span>
                        <span class="d-block d-sm-inline mt-1 mt-sm-0">Penguji Seminar</span>
                    </div>
                </div>
            </div>

            <div class="penguji-jadwal">
                <i class="fa fa-graduation-cap fa-fw mt-1 flex-shrink-0"></i>
                <span class="flex-grow-1">{{ $jadwal->nama }}</span>
            </div>

            @if ($setup && $totalPeserta > 0 && ($adaRancangan || $adaAkhir))
                <div class="progress-wrap">
                    <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3">
                        @if ($adaRancangan)
                            <div class="progress-item px-2">
                                <div class="progress-top">
                                    <span class="progress-lbl">Seminar Rancangan</span>
                                    <span class="progress-cnt">{{ $selesaiR }}/{{ $totalPeserta }}</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill fill-green" style="width:{{ $pctR }}%"></div>
                                </div>
                            </div>
                        @endif
                        @if ($adaAkhir)
                            <div class="progress-item px-2">
                                <div class="progress-top">
                                    <span class="progress-lbl">Seminar Akhir</span>
                                    <span class="progress-cnt">{{ $selesaiA }}/{{ $totalPeserta }}</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill fill-blue" style="width:{{ $pctA }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── KONTEN ── --}}
    <div class="container penguji-content">

        @if (session('notifikasi'))
            <div class="alert alert-success alert-custom d-flex align-items-center" role="alert">
                <i class="fa fa-check-circle mr-2"></i>
                <span>{{ session('notifikasi') }}</span>
            </div>
        @endif

        @if (!$setup)
            <div class="alert alert-warning alert-custom d-flex align-items-center" role="alert">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                <span>Setup penilaian belum dikonfigurasi. Silakan hubungi panitia.</span>
            </div>
        @else
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h6 class="mb-0 font-weight-bold text-secondary">
                    <i class="fa fa-users mr-2 text-primary"></i>
                    Daftar Peserta — {{ $seminar->kelompok }}
                </h6>
                <span class="text-muted small mt-1 mt-sm-0">
                    <span class="badge badge-secondary">{{ $totalPeserta }}</span> peserta
                </span>
            </div>

            <div class="row">
                @forelse ($pesertaList as $i => $p)
                    @php
                        $st = isset($statusNilai[$p->id]) ? $statusNilai[$p->id] : [];
                        $stR = !empty($st['rancangan_selesai']);
                        $stA = !empty($st['akhir_selesai']);
                    @endphp
                    <div class="col-sm-12 col-lg-6 mb-3">
                        <div class="peserta-card">
                            <div class="peserta-top">
                                <div class="peserta-no flex-shrink-0">{{ $i + 1 }}</div>
                                <div class="peserta-body">
                                    <div class="peserta-nama">{{ $p->nama_lengkap }}</div>
                                    <div class="peserta-nip">{{ $p->nip ?: '-' }}</div>
                                    @if ($p->instansi)
                                        <div class="peserta-instansi">
                                            {{ \Illuminate\Support\Str::limit($p->instansi, 35) }}
                                        </div>
                                    @endif
                                    <div class="status-row">
                                        @if ($adaRancangan)
                                            <span class="sbadge {{ $stR ? 'done' : 'pending' }}">
                                                <i class="fa fa-{{ $stR ? 'check-circle' : 'clock' }}"></i>
                                                <span class="d-none d-sm-inline">Rancangan </span>
                                                {{ $stR ? '✓' : '⏳' }}
                                            </span>
                                        @endif
                                        @if ($adaAkhir)
                                            <span class="sbadge {{ $stA ? 'done' : 'pending' }}">
                                                <i class="fa fa-{{ $stA ? 'check-circle' : 'clock' }}"></i>
                                                <span class="d-none d-sm-inline">Akhir </span>
                                                {{ $stA ? '✓' : '⏳' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="peserta-actions">
                                @if ($adaRancangan)
                                    <a href="{{ route('penguji.nilai.form', [$tokenRow->token, $p->id, 'rancangan']) }}"
                                        class="btn-nilai {{ $stR ? 'bv-rancangan-done' : 'bv-rancangan-todo' }}">
                                        <i class="fa fa-{{ $stR ? 'edit' : 'pen' }}"></i>
                                        <span class="d-none d-sm-inline">{{ $stR ? 'Edit' : 'Input' }}</span>
                                        <span class="d-inline d-sm-none">{{ $stR ? 'Edit' : 'Input' }}</span>
                                        <span class="d-inline">Rancangan</span>
                                    </a>
                                @endif
                                @if ($adaAkhir)
                                    <a href="{{ route('penguji.nilai.form', [$tokenRow->token, $p->id, 'akhir']) }}"
                                        class="btn-nilai @if($stA) bv-akhir-done @elseif($stR) bv-akhir-ready @else bv-akhir-todo @endif">
                                        <i class="fa fa-{{ $stA ? 'edit' : 'star' }}"></i>
                                        <span class="d-none d-sm-inline">{{ $stA ? 'Edit' : 'Input' }}</span>
                                        <span class="d-inline d-sm-none">{{ $stA ? 'Edit' : 'Input' }}</span>
                                        <span class="d-inline">Akhir</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-users fa-3x mb-3 d-block" style="color:#e2e8f0"></i>
                            <p class="mb-0">Belum ada peserta dalam kelompok ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

        <div class="penguji-footer">
            <i class="fa fa-clock mr-1"></i>
            Link berlaku sampai
            <strong>{{ \Carbon\Carbon::parse($tokenRow->token_expired_at)->format('d M Y') }}</strong>
            <br class="d-block d-sm-none">
            <span class="d-none d-sm-inline">&bull;</span>
            <span class="d-block d-sm-inline">BPSDM Prov. Kalimantan Timur</span>
        </div>
    </div>
@endsection