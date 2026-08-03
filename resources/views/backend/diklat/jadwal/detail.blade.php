@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
<style>
/* ── Hero compact ── */
.detail-hero {
    background: linear-gradient(135deg, #1e293b 0%, #1d4ed8 100%);
    padding: 1.5rem 0 1.5rem;
    border-bottom: 3px solid #1d4ed8;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .7rem; font-weight: 700; padding: .25em .75em;
    border-radius: 20px; margin-bottom: .6rem; letter-spacing: .03em;
}
.hero-nama {
    font-size: 1.2rem; font-weight: 700; color: #fff;
    line-height: 1.4; margin-bottom: .6rem;
}
.hero-meta-row {
    display: flex; flex-wrap: wrap; gap: .5rem .75rem;
    font-size: .78rem; color: rgba(255,255,255,.65);
}
.hero-meta-row span { display: inline-flex; align-items: center; gap: .3rem; }

/* ── Stat chips (baris bawah hero) ── */
.hero-chips {
    display: flex; flex-wrap: wrap; gap: .5rem;
    margin-top: 1rem;
}
.hero-chip {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
    border-radius: 8px; padding: .45rem .85rem;
    text-decoration: none; transition: background .15s;
    color: #fff;
}
.hero-chip:hover { background: rgba(255,255,255,.2); color: #fff; text-decoration: none; }
.hero-chip .chip-val { font-size: 1.1rem; font-weight: 700; line-height: 1; }
.hero-chip .chip-lbl { font-size: .65rem; font-weight: 600; text-transform: uppercase;
                       letter-spacing: .04em; color: rgba(255,255,255,.65); line-height: 1.2; }

/* ── Quick actions ── */
.qa-bar {
    display: flex; flex-wrap: wrap; gap: .4rem;
    padding: .85rem 0; border-bottom: 1px solid #e4e9f0;
    margin-bottom: 1.25rem;
}
.qa-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .8rem; border-radius: 7px; font-size: .78rem; font-weight: 500;
    border: 1px solid #e4e9f0; background: #fff; color: #374151;
    text-decoration: none; transition: all .15s; white-space: nowrap;
}
.qa-btn:hover { border-color: #1d4ed8; color: #1d4ed8; text-decoration: none; background: #eff6ff; }
.qa-btn.qa-primary { background: #1d4ed8; border-color: #1d4ed8; color: #fff; }
.qa-btn.qa-primary:hover { background: #1e40af; color: #fff; }

/* ── Info card ── */
.ic { border-radius: 10px; border: 1px solid #e4e9f0; background: #fff; overflow: hidden; margin-bottom: 1rem; }
.ic-head {
    background: #f8faff; border-bottom: 1px solid #e4e9f0;
    padding: .55rem .875rem;
    display: flex; align-items: center; justify-content: space-between;
}
.ic-title { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #475569; }
.ic-link  { font-size: .72rem; color: #1d4ed8; }
.ic-body  { padding: .875rem; }

/* ── Info rows ── */
.ir { display: flex; gap: .75rem; padding: .4rem 0; border-bottom: 1px solid #f1f5f9; font-size: .8rem; }
.ir:last-child { border-bottom: none; }
.ir-k { color: #94a3b8; font-weight: 500; width: 120px; flex-shrink: 0; padding-top: 1px; font-size: .75rem; }
.ir-v { color: #1e293b; flex: 1; }

/* ── Kuota ── */
.kuota-bar { height: 5px; border-radius: 3px; background: #e5e7eb; margin: .4rem 0; }
.kuota-fill { height: 100%; border-radius: 3px; }
.kuota-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem; text-align: center; margin-top: .75rem; }
.ks-val { font-size: 1.15rem; font-weight: 700; line-height: 1; }
.ks-lbl { font-size: .65rem; text-transform: uppercase; letter-spacing: .03em; color: #94a3b8; margin-top: 2px; }

/* ── Aktivitas ── */
.act { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
.act:last-child { border-bottom: none; }
.act-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
.act-nama { font-size: .8rem; font-weight: 500; color: #1e293b; }
.act-sub  { font-size: .7rem; color: #94a3b8; }

/* ── Sesi ── */
.sesi { display: flex; justify-content: space-between; align-items: center;
        gap: .5rem; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
.sesi:last-child { border-bottom: none; }
.sesi-nama { font-size: .8rem; font-weight: 500; color: #1e293b; }
.sesi-time { font-size: .7rem; color: #94a3b8; }

/* ── Menu grid ── */
.menu-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: .35rem;
}
.menu-item {
    display: flex; align-items: center; gap: .4rem;
    padding: .4rem .55rem; border-radius: 6px;
    border: 1px solid #e4e9f0; font-size: .75rem; color: #374151;
    text-decoration: none; transition: all .15s;
}
.menu-item:hover {
    background: #eff6ff; border-color: #1d4ed8;
    color: #1d4ed8; text-decoration: none;
}
.menu-item i { font-size: .75rem; opacity: .55; width: 13px; text-align: center; }
</style>
@endsection

@section('content')
@php
    $today    = now()->toDateString();
    $isPast   = $jadwal->tgl_akhir < $today;
    $isRun    = $jadwal->tgl_awal <= $today && $jadwal->tgl_akhir >= $today;
    $isFuture = $jadwal->tgl_awal > $today;

    $statusLabel = $isRun ? 'Sedang Berjalan' : ($isFuture ? 'Akan Datang' : 'Selesai');
    $statusBg    = $isRun ? 'rgba(187,247,208,.2)' : ($isFuture ? 'rgba(254,215,170,.2)' : 'rgba(226,232,240,.15)');
    $statusColor = $isRun ? '#86efac' : ($isFuture ? '#fdba74' : '#94a3b8');

    $verif      = $statPeserta->verif  ?? 0;
    $noverif    = $statPeserta->noverif ?? 0;
    $batalCount = $statPeserta->batal  ?? 0;
    $kuota      = $jadwal->kuota > 0 ? $jadwal->kuota : 0;
    $kuotaPct   = $kuota > 0 ? min(100, round(($verif / $kuota) * 100)) : 0;
    $kuotaColor = $kuotaPct >= 90 ? '#dc2626' : ($kuotaPct >= 70 ? '#d97706' : '#22c55e');
@endphp

{{-- ── HERO ── --}}
<div class="detail-hero">
    <div class="content content-full py-0">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="background:transparent;padding:0;font-size:.75rem">
                <li class="breadcrumb-item">
                    <a href="{{ route('backend.diklat.jadwal.index') }}" style="color:rgba(255,255,255,.5)">Jadwal</a>
                </li>
                <li class="breadcrumb-item" style="color:rgba(255,255,255,.35)">Detail</li>
            </ol>
        </nav>

        {{-- Badge --}}
        <span class="hero-badge"
              style="background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusColor }}">
            <i class="fa fa-circle" style="font-size:.45rem"></i>
            {{ $statusLabel }}
        </span>

        {{-- Nama --}}
        <div class="hero-nama">{{ $jadwal->nama }}</div>

        {{-- Meta --}}
        <div class="hero-meta-row">
            @if ($jadwal->jenis)
            <span><i class="fa fa-tag"></i>{{ $jadwal->jenis }}</span>
            @endif
            <span>
                <i class="fa fa-calendar"></i>
                {{ \Carbon\Carbon::parse($jadwal->tgl_awal)->format('d M Y') }}
                –
                {{ \Carbon\Carbon::parse($jadwal->tgl_akhir)->format('d M Y') }}
            </span>
            @if ($jadwal->lokasi)
            <span><i class="fa fa-map-marker-alt"></i>{{ $jadwal->lokasi }}</span>
            @endif
            @if ($jadwal->kelas)
            <span><i class="fa fa-building"></i>{{ $jadwal->kelas }}</span>
            @endif
        </div>

        {{-- Stat chips ── --}}
        <div class="hero-chips">
            <a class="hero-chip"
               href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta']) }}">
                <div>
                    <div class="chip-val">{{ $verif }}</div>
                    <div class="chip-lbl">Terverifikasi</div>
                </div>
            </a>
            <a class="hero-chip"
               href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta']) }}">
                <div>
                    <div class="chip-val">{{ $noverif }}</div>
                    <div class="chip-lbl">Menunggu Verif</div>
                </div>
            </a>
            <a class="hero-chip"
               href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}">
                <div>
                    <div class="chip-val">{{ $totalSesi }}</div>
                    <div class="chip-lbl">Sesi Presensi</div>
                </div>
            </a>
            <a class="hero-chip"
               href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat']) }}">
                <div>
                    <div class="chip-val">{{ $statSertifikat }}</div>
                    <div class="chip-lbl">Sertifikat</div>
                </div>
            </a>
        </div>

    </div>
</div>

{{-- ── KONTEN ── --}}
<div class="content">

    {{-- Quick actions --}}
    <div class="qa-bar">
        @can('isCreator', \App\Jadwal::find($jadwal->id)) @cannot('isViewer')
        <a href="{{ route('backend.diklat.jadwal.edit', $jadwal->id) }}" class="qa-btn">
            <i class="fa fa-pencil-alt"></i> Edit
        </a>
        @endcannot @endcan
        <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta']) }}"
           class="qa-btn qa-primary">
            <i class="fa fa-users"></i> Kelola Peserta
        </a>
        <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}" class="qa-btn">
            <i class="fa fa-clipboard-check"></i> Presensi
        </a>
        <a href="{{ route('backend.diklat.dokumen_syarat.rekap', $jadwal->id) }}" class="qa-btn">
            <i class="fa fa-folder-open"></i> Rekap Dokumen
        </a>
        <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat']) }}"
           class="qa-btn">
            <i class="fa fa-certificate"></i> Sertifikat
        </a>
        <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'cetak']) }}"
           class="qa-btn">
            <i class="fa fa-print"></i> Cetak
        </a>
    </div>

    <div class="row">
        {{-- ── KOLOM KIRI ── --}}
        <div class="col-lg-7">

            {{-- Informasi Pelatihan --}}
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-info-circle mr-1 text-primary"></i>Informasi Pelatihan</span>
                </div>
                <div class="ic-body">
                    <div class="ir"><span class="ir-k">Tahun</span><span class="ir-v">{{ $jadwal->tahun }}</span></div>
                    <div class="ir"><span class="ir-k">Kompetensi</span><span class="ir-v">{{ $jadwal->jenis ?? '-' }}</span></div>
                    <div class="ir"><span class="ir-k">Kelas / Unit</span><span class="ir-v">{{ $jadwal->kelas ?: '-' }}</span></div>
                    <div class="ir"><span class="ir-k">Lokasi</span><span class="ir-v">{{ $jadwal->lokasi ?: '-' }}</span></div>
                    <div class="ir">
                        <span class="ir-k">Tanggal</span>
                        <span class="ir-v">
                            {{ \Carbon\Carbon::parse($jadwal->tgl_awal)->format('d M Y') }}
                            –
                            {{ \Carbon\Carbon::parse($jadwal->tgl_akhir)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="ir">
                        <span class="ir-k">Registrasi</span>
                        <span class="ir-v">{{ $jadwal->registrasi ? 'Online (Publik)' : 'Internal' }}</span>
                    </div>
                    <div class="ir">
                        <span class="ir-k">Panitia</span>
                        <span class="ir-v">
                            <div class="font-w600">{{ $jadwal->panitia_nama }}</div>
                            <div class="text-muted" style="font-size:.72rem;margin-top:2px">
                                <i class="fa fa-phone mr-1"></i>{{ $jadwal->panitia_telp }}
                                &nbsp;&bull;&nbsp;
                                <i class="fa fa-envelope mr-1"></i>{{ $jadwal->panitia_email }}
                            </div>
                        </span>
                    </div>
                    @if ($jadwal->lampiran)
                    <div class="ir">
                        <span class="ir-k">Lampiran</span>
                        <span class="ir-v">
                            <a href="{{ Storage::url($jadwal->lampiran) }}" target="_blank"
                               class="btn btn-xs btn-outline-primary" style="font-size:.72rem;padding:.2rem .55rem">
                                <i class="fa fa-download mr-1"></i>Unduh
                            </a>
                        </span>
                    </div>
                    @endif
                    @if ($jadwal->var_1)
                    <div class="ir">
                        <span class="ir-k">Grup Info</span>
                        <span class="ir-v">
                            <a href="{{ $jadwal->var_1 }}" target="_blank"
                               class="btn btn-xs btn-info" style="font-size:.72rem;padding:.2rem .55rem">
                                <i class="fab fa-telegram-plane mr-1"></i>Buka Grup
                            </a>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Kuota Peserta --}}
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-users mr-1 text-primary"></i>Kuota Peserta</span>
                </div>
                <div class="ic-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <div>
                            <span style="font-size:1.8rem;font-weight:700;color:{{ $kuotaColor }};line-height:1">{{ $verif }}</span>
                            <span class="text-muted" style="font-size:.85rem">
                                / {{ $kuota > 0 ? $kuota : '∞' }} kuota
                            </span>
                        </div>
                        <span style="font-size:1.1rem;font-weight:700;color:{{ $kuotaColor }}">{{ $kuotaPct }}%</span>
                    </div>
                    <div class="kuota-bar">
                        <div class="kuota-fill" style="width:{{ $kuotaPct }}%;background:{{ $kuotaColor }}"></div>
                    </div>
                    <div class="kuota-stats">
                        <div>
                            <div class="ks-val" style="color:#15803d">{{ $verif }}</div>
                            <div class="ks-lbl">Terverifikasi</div>
                        </div>
                        <div>
                            <div class="ks-val" style="color:#d97706">{{ $noverif }}</div>
                            <div class="ks-lbl">Menunggu</div>
                        </div>
                        <div>
                            <div class="ks-val" style="color:#dc2626">{{ $batalCount }}</div>
                            <div class="ks-lbl">Batal</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Deskripsi & Syarat --}}
            @if ($jadwal->deskripsi || $jadwal->syarat)
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-align-left mr-1 text-primary"></i>Deskripsi & Syarat</span>
                </div>
                <div class="ic-body">
                    @if ($jadwal->deskripsi)
                        <p class="font-w600 mb-1" style="font-size:.78rem">Deskripsi</p>
                        <div class="text-muted mb-3" style="font-size:.8rem">{!! $jadwal->deskripsi !!}</div>
                    @endif
                    @if ($jadwal->syarat)
                        <p class="font-w600 mb-1" style="font-size:.78rem">Syarat Pendaftaran</p>
                        <div class="text-muted" style="font-size:.8rem">{!! $jadwal->syarat !!}</div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- ── KOLOM KANAN ── --}}
        <div class="col-lg-5">

            {{-- Presensi hari ini --}}
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-clipboard-check mr-1 text-primary"></i>Presensi Hari Ini</span>
                    <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}" class="ic-link">Semua →</a>
                </div>
                <div class="ic-body" style="padding:.65rem .875rem">
                    @if ($sesiHariIni->isEmpty())
                        <p class="text-muted mb-0" style="font-size:.8rem">Tidak ada sesi presensi hari ini.</p>
                    @else
                        @foreach ($sesiHariIni as $s)
                        <div class="sesi">
                            <div>
                                <div class="sesi-nama">{{ \Illuminate\Support\Str::limit($s->nama_materi, 32) }}</div>
                                <div class="sesi-time">
                                    {{ substr($s->jam_mulai,0,5) }}–{{ substr($s->jam_selesai,0,5) }}
                                    @if ($s->is_aktif)
                                        <span class="badge badge-success ml-1" style="font-size:.58rem">Aktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-success font-w600">{{ $s->hadir }}</span>
                                <span class="text-muted" style="font-size:.75rem">/ {{ $s->total }}</span>
                                <div style="font-size:.62rem;color:#94a3b8">hadir</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Dokumen menunggu --}}
            @if ($dokMenunggu->isNotEmpty())
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-folder-open mr-1 text-warning"></i>Dokumen Menunggu</span>
                    <a href="{{ route('backend.diklat.dokumen_syarat.rekap', $jadwal->id) }}" class="ic-link">Rekap →</a>
                </div>
                <div class="ic-body" style="padding:.5rem .875rem">
                    @foreach ($dokMenunggu as $d)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.8rem">
                        <span class="text-muted">{{ $d->nama }}</span>
                        <span class="badge badge-warning">{{ $d->jumlah }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pendaftaran terbaru --}}
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-user-plus mr-1 text-primary"></i>Pendaftaran Terbaru</span>
                    <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta']) }}"
                       class="ic-link">Semua →</a>
                </div>
                <div class="ic-body" style="padding:.5rem .875rem">
                    @if ($pendaftaranTerbaru->isEmpty())
                        <p class="text-muted mb-0" style="font-size:.8rem">Belum ada pendaftaran.</p>
                    @else
                        @foreach ($pendaftaranTerbaru as $p)
                        <div class="act">
                            <div class="act-dot"
                                 style="background:{{ $p->batal ? '#dc2626' : ($p->verifikasi ? '#22c55e' : '#f59e0b') }}">
                            </div>
                            <div>
                                <div class="act-nama">{{ $p->nama_lengkap }}</div>
                                <div class="act-sub">
                                    {{ \Illuminate\Support\Str::limit($p->instansi, 32) }}
                                    &middot;
                                    {{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Akses cepat --}}
            <div class="ic">
                <div class="ic-head">
                    <span class="ic-title"><i class="fa fa-th mr-1 text-primary"></i>Akses Cepat Menu</span>
                </div>
                <div class="ic-body">
                    <div class="menu-grid">
                        @php
                        $menus = [
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'mata-pelatihan']), 'icon' => 'fa-book',      'label' => 'Mata Pelatihan'],
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'seminar']),        'icon' => 'fa-book-open', 'label' => 'Seminar'],
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'surat-tugas']),    'icon' => 'fa-file-alt',  'label' => 'Surat Tugas'],
                            ['url' => route('backend.diklat.dokumen_syarat.index', $jadwal->id),                                                                         'icon' => 'fa-folder',    'label' => 'Dok. Syarat'],
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'tautan']),          'icon' => 'fa-link',      'label' => 'Tautan'],
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'survei']),          'icon' => 'fa-chart-bar', 'label' => 'Survei'],
                            ['url' => route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'checklist']),       'icon' => 'fa-check',     'label' => 'Checklist'],
                            ['url' => route('backend.diklat.presensi.rekap_all', $jadwal->id),                                                                           'icon' => 'fa-table',     'label' => 'Rekap Presensi'],
                            ['url' => route('backend.diklat.dokumen_syarat.rekap', $jadwal->id),                                                                         'icon' => 'fa-copy',      'label' => 'Rekap Dokumen'],
                        ];
                        @endphp
                        @foreach ($menus as $m)
                        <a href="{{ $m['url'] }}" class="menu-item">
                            <i class="fa {{ $m['icon'] }}"></i>
                            {{ $m['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
