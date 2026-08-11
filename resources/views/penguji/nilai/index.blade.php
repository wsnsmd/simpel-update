{{-- penguji/nilai/index.blade.php --}}
@extends('layouts.frontend')

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

    {{-- ═══════════════════════════════════════════════════════════════
    HERO — gradient Dashmix bg-gd-sea
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-gd-primary py-4 border-bottom border-black-op">
        <div class="container">

            {{-- Identitas Penguji --}}
            <div class="d-flex align-items-center" style="gap:.875rem">

                {{-- Avatar --}}
                <div class="item item-circle bg-white-25 border border-white-op flex-shrink-0"
                    style="width:3rem;height:3rem;min-width:3rem;color:#fff;font-size:1.1rem">
                    <i class="fa fa-user-tie"></i>
                </div>

                {{-- Info --}}
                <div style="min-width:0">
                    <div class="font-w700 text-white" style="font-size:1rem;
                             white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $penguji->nama }}
                    </div>
                    <div class="text-white-75 font-size-sm">
                        <i class="fa fa-users mr-1"></i>{{ $seminar->kelompok }}
                        <span class="mx-1">&bull;</span>
                        <span>Penguji Seminar</span>
                    </div>
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="d-flex align-items-start mt-3 rounded px-3 py-2 bg-black-10 border border-white-op"
                style="gap:.5rem">
                <i class="fa fa-graduation-cap text-white-75 mt-1 flex-shrink-0"></i>
                <span class="text-white-75 font-size-sm">{{ $jadwal->nama }}</span>
            </div>

            {{-- Progress Bar --}}
            @if ($setup && $totalPeserta > 0 && ($adaRancangan || $adaAkhir))
                <div class="mt-3 rounded px-3 py-2 bg-black-10">
                    <div class="row" style="row-gap:.5rem">
                        @if ($adaRancangan)
                            <div class="col-12 col-sm-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-white-75" style="font-size:.7rem">Seminar Rancangan</span>
                                    <span class="text-white font-w700" style="font-size:.72rem">
                                        {{ $selesaiR }}/{{ $totalPeserta }}
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;background:rgba(255,255,255,.15);border-radius:3px">
                                    <div class="progress-bar bg-success" style="width:{{ $pctR }}%;border-radius:3px"></div>
                                </div>
                            </div>
                        @endif
                        @if ($adaAkhir)
                            <div class="col-12 col-sm-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-white-75" style="font-size:.7rem">Seminar Akhir</span>
                                    <span class="text-white font-w700" style="font-size:.72rem">
                                        {{ $selesaiA }}/{{ $totalPeserta }}
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;background:rgba(255,255,255,.15);border-radius:3px">
                                    <div class="progress-bar bg-info" style="width:{{ $pctA }}%;border-radius:3px"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
    KONTEN
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="content py-4">

        {{-- Notifikasi --}}
        @if (session('notifikasi'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="fa fa-check-circle mr-2"></i>
                <span>{{ session('notifikasi') }}</span>
            </div>
        @endif

        {{-- Setup belum dikonfigurasi --}}
        @if (!$setup)
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                <span>Setup penilaian belum dikonfigurasi. Silakan hubungi panitia.</span>
            </div>

        @else

            {{-- Sub-header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:.5rem">
                <h2 class="font-size-base font-w700 mb-0 text-muted">
                    <i class="fa fa-users mr-1 text-primary"></i>
                    Daftar Peserta — {{ $seminar->kelompok }}
                </h2>
                <span class="badge badge-secondary font-size-sm">
                    {{ $totalPeserta }} peserta
                </span>
            </div>

            {{-- Grid Peserta --}}
            <div class="row" style="row-gap:.75rem">

                @forelse ($pesertaList as $i => $p)
                    @php
                        $st = $statusNilai[$p->id] ?? [];
                        $stR = !empty($st['rancangan_selesai']);
                        $stA = !empty($st['akhir_selesai']);
                    @endphp

                    <div class="col-12 col-lg-6">
                        <div class="block block-rounded block-bordered mb-0 h-100">

                            {{-- Peserta Info --}}
                            <div class="block-content py-3">
                                <div class="d-flex align-items-start" style="gap:.625rem">

                                    {{-- Nomor --}}
                                    <div class="item item-circle bg-body-dark flex-shrink-0 font-w700 text-muted"
                                        style="width:1.75rem;height:1.75rem;min-width:1.75rem;font-size:.7rem">
                                        {{ $i + 1 }}
                                    </div>

                                    {{-- Data --}}
                                    <div style="min-width:0;flex:1">
                                        <div class="font-w700 text-body-color-dark" style="font-size:.875rem;word-break:break-word">
                                            {{ $p->nama_lengkap }}
                                        </div>
                                        <div class="text-muted font-size-sm">
                                            {{ $p->nip ?: '-' }}
                                        </div>
                                        @if ($p->instansi)
                                            <div class="text-muted" style="font-size:.72rem;margin-top:2px;word-break:break-word">
                                                {{ \Illuminate\Support\Str::limit($p->instansi, 40) }}
                                            </div>
                                        @endif

                                        {{-- Status Badge --}}
                                        <div class="d-flex flex-wrap mt-2" style="gap:.3rem">
                                            @if ($adaRancangan)
                                                <span class="badge {{ $stR ? 'badge-success' : 'badge-secondary' }}"
                                                    style="font-size:.6rem">
                                                    <i class="fa fa-{{ $stR ? 'check-circle' : 'clock' }} mr-1"></i>
                                                    Rancangan {{ $stR ? '✓' : '⏳' }}
                                                </span>
                                            @endif
                                            @if ($adaAkhir)
                                                <span class="badge {{ $stA ? 'badge-success' : 'badge-secondary' }}"
                                                    style="font-size:.6rem">
                                                    <i class="fa fa-{{ $stA ? 'check-circle' : 'clock' }} mr-1"></i>
                                                    Akhir {{ $stA ? '✓' : '⏳' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="block-content block-content-sm bg-body-light py-2" style="border-top:1px solid #e4e9f0">
                                <div class="d-flex flex-wrap" style="gap:.35rem">

                                    @if ($adaRancangan)
                                        <a href="{{ route('penguji.nilai.form', [$tokenRow->token, $p->id, 'rancangan']) }}"
                                            class="btn btn-sm flex-fill {{ $stR ? 'btn-outline-success' : 'btn-warning' }}">
                                            <i class="fa fa-{{ $stR ? 'edit' : 'pen' }} mr-1"></i>
                                            {{ $stR ? 'Edit' : 'Input' }} Rancangan
                                        </a>
                                    @endif

                                    @if ($adaAkhir)
                                        @if ($stA)
                                            <a href="{{ route('penguji.nilai.form', [$tokenRow->token, $p->id, 'akhir']) }}"
                                                class="btn btn-sm flex-fill btn-outline-success">
                                                <i class="fa fa-edit mr-1"></i> Edit Akhir
                                            </a>
                                        @elseif ($stR || !$adaRancangan)
                                            <a href="{{ route('penguji.nilai.form', [$tokenRow->token, $p->id, 'akhir']) }}"
                                                class="btn btn-sm flex-fill btn-primary">
                                                <i class="fa fa-star mr-1"></i> Input Akhir
                                            </a>
                                        @else
                                            <span class="btn btn-sm flex-fill btn-outline-secondary disabled"
                                                title="Selesaikan rancangan dulu">
                                                <i class="fa fa-lock mr-1"></i> Akhir
                                            </span>
                                        @endif
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>

                @empty
                    <div class="col-12">
                        <div class="block block-rounded text-center py-5">
                            <div class="item item-3x item-circle bg-body-light mx-auto mb-3">
                                <i class="fa fa-users fa-2x text-muted"></i>
                            </div>
                            <p class="text-muted mb-0">Belum ada peserta dalam kelompok ini.</p>
                        </div>
                    </div>
                @endforelse

            </div>
        @endif

        {{-- Footer info --}}
        <div class="text-center mt-4 pt-3 border-top text-muted font-size-sm">
            <i class="fa fa-clock mr-1"></i>
            Link berlaku sampai
            <strong>{{ \Carbon\Carbon::parse($tokenRow->token_expired_at)->format('d M Y') }}</strong>
            <span class="mx-2 d-none d-sm-inline">&bull;</span>
            <span class="d-block d-sm-inline mt-1 mt-sm-0">BPSDM Prov. Kalimantan Timur</span>
        </div>

    </div>

@endsection