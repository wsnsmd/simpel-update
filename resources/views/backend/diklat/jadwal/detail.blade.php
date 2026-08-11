@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('content')
    @php
        $today = now()->toDateString();
        $isPast = $jadwal->tgl_akhir < $today;
        $isRun = $jadwal->tgl_awal <= $today && $jadwal->tgl_akhir >= $today;
        $isFuture = $jadwal->tgl_awal > $today;

        $statusLabel = $isRun ? 'Sedang Berjalan' : ($isFuture ? 'Akan Datang' : 'Selesai');
        $statusBadge = $isRun ? 'badge-success' : ($isFuture ? 'badge-warning' : 'badge-secondary');

        $verif = $statPeserta->verif ?? 0;
        $noverif = $statPeserta->noverif ?? 0;
        $batalCount = $statPeserta->batal ?? 0;
        $kuota = $jadwal->kuota > 0 ? $jadwal->kuota : 0;
        $kuotaPct = $kuota > 0 ? min(100, round(($verif / $kuota) * 100)) : 0;
        $kuotaClass = $kuotaPct >= 90 ? 'bg-danger' : ($kuotaPct >= 70 ? 'bg-warning' : 'bg-success');

        $detailBase = ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)];
    @endphp

    {{-- ═══════════════════════════════════════════════════════════
    HERO
    ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-gd-primary py-4 border-bottom border-black-op">
        <div class="content content-full py-0">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb breadcrumb-alt mb-0" style="background:transparent;padding:0;font-size:.75rem">
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.diklat.jadwal.index') }}" class="text-white-50">Jadwal</a>
                    </li>
                    <li class="breadcrumb-item text-white-50">Detail</li>
                </ol>
            </nav>

            {{-- Status badge --}}
            <div class="mb-2">
                <span class="badge {{ $statusBadge }}">
                    <i class="fa fa-circle mr-1" style="font-size:.45rem;vertical-align:middle"></i>
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Nama jadwal --}}
            <h1 class="font-size-h3 font-w700 text-white mb-2" style="line-height:1.35">
                {{ $jadwal->nama }}
            </h1>

            {{-- Meta info --}}
            <div class="d-flex flex-wrap text-white-75 font-size-sm" style="gap:.4rem .875rem">
                @if ($jadwal->jenis)
                    <span><i class="fa fa-tag mr-1"></i>{{ $jadwal->jenis }}</span>
                @endif
                <span>
                    <i class="fa fa-calendar mr-1"></i>
                    {{ \Carbon\Carbon::parse($jadwal->tgl_awal)->format('d M Y') }}
                    –
                    {{ \Carbon\Carbon::parse($jadwal->tgl_akhir)->format('d M Y') }}
                </span>
                @if ($jadwal->lokasi)
                    <span><i class="fa fa-map-marker-alt mr-1"></i>{{ $jadwal->lokasi }}</span>
                @endif
                @if ($jadwal->kelas)
                    <span><i class="fa fa-building mr-1"></i>{{ $jadwal->kelas }}</span>
                @endif
            </div>

            {{-- Stat chips --}}
            <div class="d-flex flex-wrap mt-3" style="gap:.5rem">
                <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'peserta'])) }}"
                    class="d-inline-flex align-items-center bg-white-10 border border-white-op text-white
                                      rounded px-3 py-2 text-decoration-none font-size-sm"
                    style="gap:.625rem;transition:background .15s"
                    onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background=''">
                    <div>
                        <div class="font-w700" style="font-size:1.1rem;line-height:1">{{ $verif }}</div>
                        <div class="text-white-75" style="font-size:.63rem;text-transform:uppercase;letter-spacing:.04em">
                            Terverifikasi</div>
                    </div>
                </a>
                <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'peserta'])) }}"
                    class="d-inline-flex align-items-center bg-white-10 border border-white-op text-white
                                      rounded px-3 py-2 text-decoration-none font-size-sm"
                    style="gap:.625rem;transition:background .15s"
                    onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background=''">
                    <div>
                        <div class="font-w700" style="font-size:1.1rem;line-height:1">{{ $noverif }}</div>
                        <div class="text-white-75" style="font-size:.63rem;text-transform:uppercase;letter-spacing:.04em">
                            Menunggu Verif</div>
                    </div>
                </a>
                <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}" class="d-inline-flex align-items-center bg-white-10 border border-white-op text-white
                                      rounded px-3 py-2 text-decoration-none font-size-sm"
                    style="gap:.625rem;transition:background .15s"
                    onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background=''">
                    <div>
                        <div class="font-w700" style="font-size:1.1rem;line-height:1">{{ $totalSesi }}</div>
                        <div class="text-white-75" style="font-size:.63rem;text-transform:uppercase;letter-spacing:.04em">
                            Sesi Presensi</div>
                    </div>
                </a>
                <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'sertifikat'])) }}"
                    class="d-inline-flex align-items-center bg-white-10 border border-white-op text-white
                                      rounded px-3 py-2 text-decoration-none font-size-sm"
                    style="gap:.625rem;transition:background .15s"
                    onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background=''">
                    <div>
                        <div class="font-w700" style="font-size:1.1rem;line-height:1">{{ $statSertifikat }}</div>
                        <div class="text-white-75" style="font-size:.63rem;text-transform:uppercase;letter-spacing:.04em">
                            Sertifikat</div>
                    </div>
                </a>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
    KONTEN
    ═══════════════════════════════════════════════════════════ --}}
    <div class="content">

        {{-- ── Quick Actions ─────────────────────────────────── --}}
        <div class="d-flex flex-wrap mb-4 pb-3 border-bottom" style="gap:.4rem">
            @can('isCreator', \App\Jadwal::find($jadwal->id))
                @cannot('isViewer')
                <a href="{{ route('backend.diklat.jadwal.edit', $jadwal->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-pencil-alt mr-1"></i> Edit
                </a>
                @endcannot
            @endcan
            <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'peserta'])) }}"
                class="btn btn-sm btn-primary">
                <i class="fa fa-users mr-1"></i> Kelola Peserta
            </a>
            <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-clipboard-check mr-1"></i> Presensi
            </a>
            <a href="{{ route('backend.diklat.dokumen_syarat.rekap', $jadwal->id) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-folder-open mr-1"></i> Rekap Dokumen
            </a>
            <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'sertifikat'])) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-certificate mr-1"></i> Sertifikat
            </a>
            <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'cetak'])) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-print mr-1"></i> Cetak
            </a>
        </div>

        <div class="row">

            {{-- ══ KOLOM KIRI ══════════════════════════════════ --}}
            <div class="col-lg-7">

                {{-- Informasi Pelatihan --}}
                <div class="block block-rounded block-bordered mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm text-uppercase font-w700">
                            <i class="fa fa-info-circle text-primary mr-1"></i>Informasi Pelatihan
                        </h3>
                    </div>
                    <div class="block-content block-content-full p-0">
                        <table class="table table-vcenter mb-0" style="font-size:.82rem">
                            <colgroup>
                                <col style="width:130px">
                                <col>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td class="text-muted font-size-sm">Tahun</td>
                                    <td class="font-w600">{{ $jadwal->tahun }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Kompetensi</td>
                                    <td>{{ $jadwal->jenis ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Kelas / Unit</td>
                                    <td>{{ $jadwal->kelas ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Lokasi</td>
                                    <td>{{ $jadwal->lokasi ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Tanggal</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->tgl_awal)->format('d M Y') }}
                                        –
                                        {{ \Carbon\Carbon::parse($jadwal->tgl_akhir)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Registrasi</td>
                                    <td>
                                        @if ($jadwal->registrasi)
                                            <span class="badge badge-success">Online (Publik)</span>
                                        @else
                                            <span class="badge badge-secondary">Internal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-size-sm">Panitia</td>
                                    <td>
                                        <div class="font-w600">{{ $jadwal->panitia_nama }}</div>
                                        <div class="text-muted font-size-sm mt-1">
                                            <i class="fa fa-phone mr-1"></i>{{ $jadwal->panitia_telp }}
                                            <span class="mx-1">&bull;</span>
                                            <i class="fa fa-envelope mr-1"></i>{{ $jadwal->panitia_email }}
                                        </div>
                                    </td>
                                </tr>
                                @if ($jadwal->lampiran)
                                    <tr>
                                        <td class="text-muted font-size-sm">Lampiran</td>
                                        <td>
                                            <a href="{{ Storage::url($jadwal->lampiran) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-download mr-1"></i>Unduh
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if ($jadwal->var_1)
                                    <tr>
                                        <td class="text-muted font-size-sm">Grup Info</td>
                                        <td>
                                            <a href="{{ $jadwal->var_1 }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fab fa-telegram-plane mr-1"></i>Buka Grup
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kuota Peserta --}}
                <div class="block block-rounded block-bordered mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm text-uppercase font-w700">
                            <i class="fa fa-users text-primary mr-1"></i>Kuota Peserta
                        </h3>
                    </div>
                    <div class="block-content mb-3">

                        {{-- Angka utama --}}
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <div>
                                <span
                                    class="font-w700 {{ $kuotaClass === 'bg-danger' ? 'text-danger' : ($kuotaClass === 'bg-warning' ? 'text-warning' : 'text-success') }}"
                                    style="font-size:1.8rem;line-height:1">{{ $verif }}</span>
                                <span class="text-muted ml-1">/ {{ $kuota > 0 ? $kuota : '∞' }} kuota</span>
                            </div>
                            <span
                                class="font-w700 font-size-h4 {{ $kuotaClass === 'bg-danger' ? 'text-danger' : ($kuotaClass === 'bg-warning' ? 'text-warning' : 'text-success') }}">
                                {{ $kuotaPct }}%
                            </span>
                        </div>

                        {{-- Progress bar Bootstrap --}}
                        <div class="progress mb-3" style="height:5px">
                            <div class="progress-bar {{ $kuotaClass }}" style="width:{{ $kuotaPct }}%"></div>
                        </div>

                        {{-- 3 stat angka --}}
                        <div class="row text-center no-gutters">
                            <div class="col-4 border-right">
                                <div class="font-w700 text-success font-size-h4">{{ $verif }}</div>
                                <div class="text-muted font-size-sm text-uppercase"
                                    style="font-size:.65rem;letter-spacing:.03em">Terverifikasi</div>
                            </div>
                            <div class="col-4 border-right">
                                <div class="font-w700 text-warning font-size-h4">{{ $noverif }}</div>
                                <div class="text-muted font-size-sm text-uppercase"
                                    style="font-size:.65rem;letter-spacing:.03em">Menunggu</div>
                            </div>
                            <div class="col-4">
                                <div class="font-w700 text-danger font-size-h4">{{ $batalCount }}</div>
                                <div class="text-muted font-size-sm text-uppercase"
                                    style="font-size:.65rem;letter-spacing:.03em">Batal</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi & Syarat --}}
                @if ($jadwal->deskripsi || $jadwal->syarat)
                    <div class="block block-rounded block-bordered mb-4">
                        <div class="block-header block-header-default">
                            <h3 class="block-title font-size-sm text-uppercase font-w700">
                                <i class="fa fa-align-left text-primary mr-1"></i>Deskripsi &amp; Syarat
                            </h3>
                        </div>
                        <div class="block-content">
                            @if ($jadwal->deskripsi)
                                <p class="font-w600 font-size-sm mb-1">Deskripsi</p>
                                <div class="text-muted font-size-sm mb-3">{!! $jadwal->deskripsi !!}</div>
                            @endif
                            @if ($jadwal->syarat)
                                <p class="font-w600 font-size-sm mb-1">Syarat Pendaftaran</p>
                                <div class="text-muted font-size-sm">{!! $jadwal->syarat !!}</div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- ══ KOLOM KANAN ══════════════════════════════════ --}}
            <div class="col-lg-5">

                {{-- Presensi Hari Ini --}}
                <div class="block block-rounded block-bordered mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm text-uppercase font-w700">
                            <i class="fa fa-clipboard-check text-primary mr-1"></i>Presensi Hari Ini
                        </h3>
                        <div class="block-options">
                            <a href="{{ route('backend.diklat.presensi.index', $jadwal->id) }}"
                                class="btn-block-option font-size-sm text-primary">
                                Semua →
                            </a>
                        </div>
                    </div>
                    <div class="block-content block-content-full p-0">
                        @if ($sesiHariIni->isEmpty())
                            <div class="px-4 py-3 text-muted font-size-sm">
                                Tidak ada sesi presensi hari ini.
                            </div>
                        @else
                            @foreach ($sesiHariIni as $s)
                                <div class="d-flex justify-content-between align-items-center
                                                                                px-4 py-3 border-bottom font-size-sm">
                                    <div>
                                        <div class="font-w600">
                                            {{ \Illuminate\Support\Str::limit($s->nama_materi, 32) }}
                                        </div>
                                        <div class="text-muted font-size-sm">
                                            {{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }}
                                            @if ($s->is_aktif)
                                                <span class="badge badge-success ml-1" style="font-size:.58rem">Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-success font-w600">{{ $s->hadir }}</span>
                                        <span class="text-muted">/ {{ $s->total }}</span>
                                        <div class="text-muted" style="font-size:.65rem">hadir</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Dokumen Menunggu --}}
                @if ($dokMenunggu->isNotEmpty())
                    <div class="block block-rounded block-bordered mb-4">
                        <div class="block-header block-header-default">
                            <h3 class="block-title font-size-sm text-uppercase font-w700">
                                <i class="fa fa-folder-open text-warning mr-1"></i>Dokumen Menunggu
                            </h3>
                            <div class="block-options">
                                <a href="{{ route('backend.diklat.dokumen_syarat.rekap', $jadwal->id) }}"
                                    class="btn-block-option font-size-sm text-primary">
                                    Rekap →
                                </a>
                            </div>
                        </div>
                        <div class="block-content block-content-full p-0">
                            @foreach ($dokMenunggu as $d)
                                <div class="d-flex justify-content-between align-items-center
                                                                                px-4 py-2 border-bottom font-size-sm">
                                    <span class="text-muted">{{ $d->nama }}</span>
                                    <span class="badge badge-warning">{{ $d->jumlah }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Pendaftaran Terbaru --}}
                <div class="block block-rounded block-bordered mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm text-uppercase font-w700">
                            <i class="fa fa-user-plus text-primary mr-1"></i>Pendaftaran Terbaru
                        </h3>
                        <div class="block-options">
                            <a href="{{ route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'peserta'])) }}"
                                class="btn-block-option font-size-sm text-primary">
                                Semua →
                            </a>
                        </div>
                    </div>
                    <div class="block-content block-content-full p-0">
                        @if ($pendaftaranTerbaru->isEmpty())
                            <div class="px-4 py-3 text-muted font-size-sm">
                                Belum ada pendaftaran.
                            </div>
                        @else
                            @foreach ($pendaftaranTerbaru as $p)
                                @php
                                    $dotClass = $p->batal
                                        ? 'bg-danger'
                                        : ($p->verifikasi ? 'bg-success' : 'bg-warning');
                                @endphp
                                <div class="d-flex align-items-start px-4 py-3 border-bottom font-size-sm" style="gap:.625rem">
                                    {{-- Dot status --}}
                                    <div class="rounded-circle {{ $dotClass }} flex-shrink-0 mt-1"
                                        style="width:7px;height:7px;min-width:7px"></div>
                                    <div>
                                        <div class="font-w600">{{ $p->nama_lengkap }}</div>
                                        <div class="text-muted font-size-sm">
                                            {{ \Illuminate\Support\Str::limit($p->instansi, 32) }}
                                            <span class="mx-1">&middot;</span>
                                            {{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Akses Cepat Menu --}}
                <div class="block block-rounded block-bordered mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title font-size-sm text-uppercase font-w700">
                            <i class="fa fa-th text-primary mr-1"></i>Akses Cepat
                        </h3>
                    </div>
                    <div class="block-content mb-3">
                        @php
                            $menus = [
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'mata-pelatihan'])), 'icon' => 'fa-book', 'label' => 'Mata Pelatihan'],
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'seminar'])), 'icon' => 'fa-book-open', 'label' => 'Seminar'],
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'surat-tugas'])), 'icon' => 'fa-file-alt', 'label' => 'Surat Tugas'],
                                ['url' => route('backend.diklat.dokumen_syarat.index', $jadwal->id), 'icon' => 'fa-folder', 'label' => 'Dok. Syarat'],
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'tautan'])), 'icon' => 'fa-link', 'label' => 'Tautan'],
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'survei'])), 'icon' => 'fa-chart-bar', 'label' => 'Survei'],
                                ['url' => route('backend.diklat.jadwal.detail', array_merge($detailBase, ['page' => 'checklist'])), 'icon' => 'fa-check', 'label' => 'Checklist'],
                                ['url' => route('backend.diklat.presensi.rekap_all', $jadwal->id), 'icon' => 'fa-table', 'label' => 'Rekap Presensi'],
                                ['url' => route('backend.diklat.dokumen_syarat.rekap', $jadwal->id), 'icon' => 'fa-copy', 'label' => 'Rekap Dokumen'],
                            ];
                        @endphp
                        <div class="row no-gutters" style="gap:.35rem 0">
                            @foreach ($menus as $m)
                                <div class="col-4 px-1 mb-1">
                                    <a href="{{ $m['url'] }}"
                                        class="btn btn-sm btn-outline-secondary w-100 text-left d-flex align-items-center"
                                        style="gap:.4rem;font-size:.75rem">
                                        <i class="fa {{ $m['icon'] }} text-muted" style="width:13px;text-align:center"></i>
                                        {{ $m['label'] }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection