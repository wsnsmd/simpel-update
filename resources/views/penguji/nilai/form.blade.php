{{-- penguji/nilai/form.blade.php --}}
@extends('layouts.frontend')

@section('content')
    @php
        $isRancangan = $fase === 'rancangan';
    @endphp

    <div class="content content-full">
        <div class="row justify-content-center mt-3">
            <div class="col-md-10 col-lg-8">

                {{-- Kembali --}}
                <p class="font-size-sm mb-3">
                    <a href="{{ route('penguji.nilai.index', $tokenRow->token) }}" class="text-muted">
                        <i class="fa fa-arrow-left mr-1"></i>Kembali ke Daftar Peserta
                    </a>
                </p>

                {{-- ── Kartu Identitas Peserta ──────────────────────── --}}
                <div class="block block-rounded {{ $isRancangan ? 'border-warning' : 'border-primary' }} border-2x mb-3"
                    style="box-shadow:none">
                    <div class="block-content py-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:.5rem">

                            <div>
                                {{-- Badge fase --}}
                                <span class="badge {{ $isRancangan ? 'badge-warning' : 'badge-primary' }} mb-2">
                                    <i class="fa fa-{{ $isRancangan ? 'clock' : 'star' }} mr-1"></i>
                                    Seminar {{ ucfirst($fase) }}
                                </span>

                                <div class="font-w700 font-size-h5 mb-1">{{ $peserta->nama_lengkap }}</div>
                                <div class="text-muted font-size-sm">
                                    {{ $peserta->nip ?: '—' }}
                                    @if ($peserta->instansi)
                                        <span class="mx-1">&middot;</span>{{ $peserta->instansi }}
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-size-sm text-muted">{{ $seminar->kelompok }}</div>
                                <div class="font-size-sm font-w600">{{ $penguji->nama }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Rekod Seminar Rancangan (fase akhir saja) ──────── --}}
                @if (!$isRancangan && $rekorRancangan->count() > 0)
                    <div class="block block-rounded border-warning border-2x mb-3" style="box-shadow:none">
                        <div class="block-header py-2 bg-warning-lighter" style="min-height:auto">
                            <h3 class="block-title font-size-sm font-w700 text-warning">
                                <i class="fa fa-history mr-1"></i>
                                Rekod Seminar Rancangan — Hanya Baca
                            </h3>
                        </div>
                        <div class="block-content py-2">

                            @foreach ($rekorRancangan as $aspek)
                                <p class="text-uppercase font-w700 text-warning font-size-sm mb-1 mt-3"
                                    style="font-size:.68rem;letter-spacing:.06em">
                                    {{ $aspek->nama }}
                                </p>

                                @foreach ($aspek->sub as $s)
                                    <div class="d-flex justify-content-between align-items-start py-2
                                                                                            border-bottom" style="gap:.75rem">
                                        <div class="font-size-sm flex-fill">
                                            {{ $s->nama }}
                                            @if ($s->catatan)
                                                <div class="text-warning font-size-sm mt-1">
                                                    <i class="fa fa-comment-alt mr-1"></i>{{ $s->catatan }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if ($s->nilai !== null)
                                                <span class="badge badge-warning">{{ number_format($s->nilai, 2) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            @if (!empty($catatanRancanganUmum))
                                <div class="mt-3 p-2 rounded bg-warning-lighter border border-warning">
                                    <p class="font-w700 text-warning font-size-sm mb-1">
                                        <i class="fa fa-comment-dots mr-1"></i>Catatan Penguji — Seminar Rancangan
                                    </p>
                                    <p class="mb-0 font-size-sm text-warning">
                                        {{ $catatanRancanganUmum }}
                                    </p>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

                {{-- ── Form Input Nilai ─────────────────────────────── --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm">
                            <i class="fa fa-star mr-1 {{ $isRancangan ? 'text-warning' : 'text-primary' }}"></i>
                            Input Nilai — Seminar {{ ucfirst($fase) }}
                        </h3>
                    </div>
                    <div class="block-content">

                        @if ($aspekList->isEmpty())
                            <p class="text-muted font-size-sm">
                                Tidak ada komponen untuk fase ini. Hubungi panitia.
                            </p>
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
                                        <div class="d-flex align-items-center justify-content-between
                                                                                    px-2 py-2 rounded mb-3 bg-primary-lighter">
                                            <span class="font-w700 text-primary text-uppercase"
                                                style="font-size:.72rem;letter-spacing:.03em">
                                                {{ $aspek->nama }}
                                            </span>
                                            <span class="badge badge-primary ml-2">
                                                {{ round($aspek->bobot * 100) }}%
                                            </span>
                                        </div>

                                        @foreach ($aspek->sub as $k)
                                            <div class="pb-3 mb-3 border-bottom pl-2">

                                                {{-- Label sub + bobot --}}
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <label class="font-w600 mb-0 font-size-sm" for="nilai_{{ $k->id }}">
                                                        {{ $k->nama }}
                                                    </label>
                                                    <span class="badge badge-secondary ml-2">
                                                        {{ round($k->bobot * 100, 1) }}%
                                                    </span>
                                                </div>

                                                {{-- Keterangan rubrik --}}
                                                @if ($k->keterangan)
                                                    <p class="text-muted mb-2 font-size-sm">
                                                        <i class="fa fa-info-circle mr-1"></i>{{ $k->keterangan }}
                                                    </p>
                                                @endif

                                                {{-- Input nilai + catatan --}}
                                                <div class="row no-gutters">
                                                    <div class="col-5 col-sm-4 pr-1">
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" id="nilai_{{ $k->id }}" name="nilai[{{ $k->id }}]"
                                                                class="form-control text-center" min="0" max="100" step="0.01"
                                                                placeholder="0–100" value="{{ $nilaiMap[$k->id] ?? '' }}" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text font-size-sm text-muted">
                                                                    /100
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-sm-8 pl-1">
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
                                            <i
                                                class="fa fa-comment-dots mr-1
                                                                          {{ $isRancangan ? 'text-warning' : 'text-primary' }}"></i>
                                            Catatan / Masukan Umum
                                            <small class="text-muted font-w400">(opsional)</small>
                                        </label>
                                        <textarea name="catatan_umum" class="form-control" rows="4"
                                            placeholder="Tuliskan catatan atau masukan umum untuk peserta terkait seminar {{ $isRancangan ? 'rancangan' : 'akhir' }} ini...">{{ $catatanUmum }}</textarea>
                                        @if ($isRancangan)
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle mr-1"></i>
                                                Catatan ini akan menjadi referensi bagi penguji pada seminar akhir.
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tombol aksi --}}
                                <div class="d-flex flex-wrap pb-3" style="gap:.5rem">
                                    <button type="submit" class="btn btn-sm {{ $isRancangan ? 'btn-warning' : 'btn-primary' }}">
                                        <i class="fa fa-save mr-1"></i> Simpan Nilai
                                    </button>
                                    <a href="{{ route('penguji.nilai.index', $tokenRow->token) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times mr-1"></i> Batal
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