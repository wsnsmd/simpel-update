{{-- penguji/nilai/form.blade.php --}}
@extends('layouts.frontend')
@section('content')
    @php
        $isRancangan = $fase === 'rancangan';
        $faseColor = $isRancangan ? '#d97706' : '#1d4ed8';
        $faseBorder = $isRancangan ? '#fcd34d' : '#93c5fd';
    @endphp
    <style>
        .fase-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .18rem .65rem;
            border-radius: 99px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            background:
                {{ $isRancangan ? '#fef3c7' : '#dbeafe' }}
            ;
            color:
                {{ $isRancangan ? '#92400e' : '#1e40af' }}
            ;
            border: 1px solid
                {{ $faseBorder }}
            ;
        }

        .peserta-accent {
            height: 4px;
            background:
                {{ $faseColor }}
            ;
            border-radius: .25rem .25rem 0 0;
        }

        .nilai-wrap {
            position: relative;
        }

        .nilai-wrap .form-control {
            padding-right: 3rem;
        }

        .nilai-suffix {
            position: absolute;
            right: .65rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: .72rem;
            color: #adb5bd;
            pointer-events: none;
        }

        .rekod-sub-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            padding: .4rem 0;
            border-bottom: 1px solid #fef9c3;
        }

        .rekod-sub-row:last-child {
            border-bottom: none;
        }
    </style>

    <div class="content content-full">
        <div class="row justify-content-center mt-3">
            <div class="col-md-10 col-lg-8">

                {{-- Back link --}}
                <p class="font-size-sm text-muted mb-3">
                    <a href="{{ route('penguji.nilai.index', $tokenRow->token) }}">← Kembali ke Daftar Peserta</a>
                </p>

                {{-- ── Kartu info peserta ──────────────────────────── --}}
                <div class="block block-rounded mb-3" style="border: 1.5px solid {{ $faseBorder }}; box-shadow: none;">
                    <div class="peserta-accent"></div>
                    <div class="block-content py-2 px-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:.5rem">
                            <div>
                                <div class="fase-pill mb-2">
                                    @if ($isRancangan)
                                        <i class="fa fa-clock"></i>
                                    @else
                                        <i class="fa fa-check"></i>
                                    @endif
                                    Seminar {{ ucfirst($fase) }}
                                </div>
                                <div class="font-w700" style="font-size:1rem; color:#111827; line-height:1.3">
                                    {{ $peserta->nama_lengkap }}
                                </div>
                                <div class="text-muted font-size-sm">
                                    {{ $peserta->nip ?: '—' }}
                                    @if ($peserta->instansi) &middot; {{ $peserta->instansi }} @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-size-sm text-muted">{{ $seminar->kelompok }}</div>
                                <div class="font-size-sm font-w600">{{ $penguji->nama }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Rekod seminar rancangan (fase akhir saja) ───── --}}
                @if (!$isRancangan && $rekorRancangan->count() > 0)
                    <div class="block block-rounded mb-3"
                        style="background:#fffdf0; border: 1px solid #fde68a; box-shadow:none;">
                        <div class="block-header py-2 px-3"
                            style="background:#fef3c7; border-bottom:1px solid #fde68a; min-height:auto;">
                            <h3 class="block-title font-size-sm font-w700" style="color:#92400e; font-size:.8rem !important;">
                                <i class="fa fa-history text-warning mr-1"></i>
                                Rekod Seminar Rancangan — Hanya Baca
                            </h3>
                        </div>
                        <div class="block-content py-2 px-3">
                            @foreach ($rekorRancangan as $aspek)
                                <p class="text-uppercase font-w700 text-muted mb-1 mt-2"
                                    style="font-size:.65rem; letter-spacing:.06em; color:#b45309 !important;">
                                    {{ $aspek->nama }}
                                </p>
                                @foreach ($aspek->sub as $s)
                                    <div class="rekod-sub-row">
                                        <div class="font-size-sm" style="flex:1; color:#374151;">
                                            {{ $s->nama }}
                                            @if ($s->catatan)
                                                <div class="text-muted mt-1" style="font-size:.7rem; color:#b45309 !important;">
                                                    <i class="fa fa-comment-alt mr-1"></i>{{ $s->catatan }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if ($s->nilai !== null)
                                                <span class="badge badge-warning">{{ number_format($s->nilai, 2) }}</span>
                                            @else
                                                <span class="text-muted font-size-sm">—</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            @if (!empty($catatanRancanganUmum))
                                <div class="mt-3 p-2 rounded" style="background:#fef9c3; border:1px solid #fde68a;">
                                    <p class="font-w700 mb-1" style="font-size:.7rem; color:#92400e;">
                                        <i class="fa fa-comment-dots mr-1"></i>Catatan Penguji — Seminar Rancangan
                                    </p>
                                    <p class="mb-0" style="font-size:.82rem; color:#78350f; white-space:pre-wrap;">
                                        {{ $catatanRancanganUmum }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── Form input nilai ────────────────────────────── --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm">
                            <i class="fa fa-star mr-1" style="color:{{ $faseColor }}"></i>
                            Input Nilai — Seminar {{ ucfirst($fase) }}
                        </h3>
                    </div>
                    <div class="block-content">

                        @if ($aspekList->isEmpty())
                            <p class="text-muted font-size-sm">Tidak ada komponen untuk fase ini. Hubungi panitia.</p>
                        @else
                            <form action="{{ route('penguji.nilai.save', [$tokenRow->token, $peserta->id, $fase]) }}"
                                method="POST" novalidate>
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger font-size-sm py-2">
                                        <i class="fa fa-exclamation-circle mr-1"></i>{{ $errors->first() }}
                                    </div>
                                @endif

                                @foreach ($aspekList as $aspek)
                                    <div class="mb-4">
                                        {{-- Header aspek --}}
                                        <div class="d-flex align-items-center justify-content-between px-2 py-1 rounded mb-3"
                                            style="background:#eff6ff;">
                                            <span class="font-w700 text-uppercase"
                                                style="font-size:.72rem; color:#1e40af; letter-spacing:.03em;">
                                                {{ $aspek->nama }}
                                            </span>
                                            <span class="badge badge-primary ml-2" style="font-size:.6rem;">
                                                {{ round($aspek->bobot * 100) }}%
                                            </span>
                                        </div>

                                        @foreach ($aspek->sub as $k)
                                            <div class="pb-3 mb-3 border-bottom pl-2">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <label class="font-w600 mb-0 font-size-sm" for="nilai_{{ $k->id }}">
                                                        {{ $k->nama }}
                                                    </label>
                                                    <span class="badge badge-secondary ml-2" style="font-size:.6rem;">
                                                        {{ round($k->bobot * 100, 1) }}%
                                                    </span>
                                                </div>

                                                @if ($k->keterangan)
                                                    <p class="text-muted mb-2" style="font-size:.72rem;">
                                                        <i class="fa fa-info-circle mr-1"></i>{{ $k->keterangan }}
                                                    </p>
                                                @endif

                                                <div class="row no-gutters" style="gap:0; margin:0 -4px;">
                                                    <div class="col-5 col-sm-4 px-1">
                                                        <div class="nilai-wrap">
                                                            <input type="number" id="nilai_{{ $k->id }}" name="nilai[{{ $k->id }}]"
                                                                class="form-control form-control-sm text-center" min="0" max="100"
                                                                step="0.01" placeholder="0–100" value="{{ $nilaiMap[$k->id] ?? '' }}"
                                                                required>
                                                            <span class="nilai-suffix">/ 100</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-sm-8 px-1">
                                                        <input type="text" name="catatan[{{ $k->id }}]"
                                                            class="form-control form-control-sm" placeholder="Catatan (opsional)"
                                                            value="{{ $catatanMap[$k->id] ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                                {{-- Catatan umum --}}
                                <div class="border-top pt-3 mt-1">
                                    <div class="form-group mb-3">
                                        <label class="font-w600 font-size-sm">
                                            <i class="fa fa-comment-dots mr-1" style="color:{{ $faseColor }}"></i>
                                            Catatan / Masukan Umum
                                            <small class="text-muted font-w400">(opsional)</small>
                                        </label>
                                        <textarea name="catatan_umum" class="form-control" rows="4"
                                            placeholder="Tuliskan catatan atau masukan umum untuk peserta terkait seminar {{ $isRancangan ? 'rancangan' : 'akhir' }} ini...">{{ $catatanUmum }}</textarea>
                                        @if($isRancangan)
                                            <small class="text-muted">
                                                Catatan ini akan menjadi referensi bagi penguji pada seminar akhir.
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action --}}
                                <div class="d-flex flex-wrap pb-3" style="gap:.5rem">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa fa-save mr-1"></i> Simpan Nilai
                                    </button>
                                    <a href="{{ route('penguji.nilai.index', $tokenRow->token) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        Batal
                                    </a>
                                </div>

                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection