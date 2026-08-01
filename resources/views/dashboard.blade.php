@extends('layouts.backend')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/fullcalendar/fullcalendar.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/locale/id.js') }}"></script>
    <script>
        $('#cal-bulan').fullCalendar({
            themeSystem: "bootstrap4",
            firstDay: 1,
            editable: false,
            locale: "id",
            weekNumbers: true,
            // eventLimit: true,
            events: "{{ route('ajax.kalendar') }}",
        });
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Beranda</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">&nbsp;</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        {{-- ── Stat cards ──────────────────────────────────────────── --}}
        <div class="row">
            @foreach ([
                ['val' => $statDiklatAktif,      'lbl' => 'Diklat Berjalan',      'icon' => 'fa-chalkboard',       'bg' => '#eff6ff', 'fg' => '#1d4ed8'],
                ['val' => $statTotalPeserta,      'lbl' => 'Peserta Tahun Ini',    'icon' => 'fa-users',            'bg' => '#f0fdf4', 'fg' => '#15803d'],
                ['val' => $statDokumenMenunggu,   'lbl' => 'Dok. Menunggu Review', 'icon' => 'fa-file-alt',         'bg' => '#fef3c7', 'fg' => '#92400e'],
                ['val' => $statPresensiHariIni,   'lbl' => 'Presensi Hari Ini',    'icon' => 'fa-clipboard-check',  'bg' => '#faf5ff', 'fg' => '#7c3aed'],
            ] as $s)
            <div class="col-6 col-xl-3 mb-4">
                <div class="block block-rounded mb-0 h-100"
                     style="border-left:4px solid {{ $s['fg'] }}">
                    <div class="block-content py-3 px-4 d-flex align-items-center" style="gap:1rem">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:{{ $s['bg'] }};color:{{ $s['fg'] }};font-size:1.2rem">
                            <i class="fa {{ $s['icon'] }}"></i>
                        </div>
                        <div>
                            <div style="font-size:1.8rem;font-weight:700;line-height:1;color:{{ $s['fg'] }}">
                                {{ $s['val'] }}
                            </div>
                            <div class="text-muted" style="font-size:.72rem;font-weight:500;text-transform:uppercase;letter-spacing:.04em">
                                {{ $s['lbl'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            {{-- ── Kolom kiri ──────────────────────────────────────── --}}
            <div class="col-md-8">

                {{-- Kalender --}}
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Kalendar Kegiatan</h3>
                    </div>
                    <div class="block-content">
                        <div id="cal-bulan" class="p-xl-4"></div>
                    </div>
                </div>

                {{-- Pendaftaran terbaru --}}
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default d-flex justify-content-between align-items-center">
                        <h3 class="block-title">Pendaftaran Terbaru</h3>
                    </div>
                    <div class="block-content p-0">
                        @if ($pendaftaranTerbaru->isEmpty())
                            <p class="text-muted p-3 mb-0">Belum ada pendaftaran.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter mb-0" style="font-size:.82rem">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Peserta</th>
                                        <th>Diklat</th>
                                        <th class="text-center" style="width:90px">Status</th>
                                        <th class="text-center" style="width:110px">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendaftaranTerbaru as $p)
                                    <tr>
                                        <td>
                                            <div class="font-w600">{{ $p->nama_lengkap }}</div>
                                            <small class="text-muted">{{ $p->instansi }}</small>
                                        </td>
                                        <td class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($p->diklat_nama, 45) }}
                                        </td>
                                        <td class="text-center">
                                            @if ($p->verifikasi)
                                                <span class="badge badge-success">Terverifikasi</span>
                                            @else
                                                <span class="badge badge-warning">Menunggu</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ── Kolom kanan ─────────────────────────────────────── --}}
            <div class="col-md-4">

                {{-- Diklat sedang berjalan --}}
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-chalkboard text-primary mr-1"></i>
                            Diklat Berjalan
                        </h3>
                    </div>
                    <div class="block-content p-0">
                        @if ($diklatBerjalan->isEmpty())
                            <p class="text-muted p-3 mb-0 font-size-sm">Tidak ada diklat yang sedang berjalan hari ini.</p>
                        @else
                            @foreach ($diklatBerjalan as $d)
                            <div class="px-3 py-2 border-bottom">
                                <div class="font-w600 font-size-sm">
                                    {{ \Illuminate\Support\Str::limit($d->nama, 40) }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($d->tgl_awal)->format('d M') }}
                                        –
                                        {{ \Carbon\Carbon::parse($d->tgl_akhir)->format('d M Y') }}
                                    </small>
                                    <small>
                                        <span class="text-success font-w600">{{ $d->jumlah_peserta }}</span>
                                        <span class="text-muted">/ {{ $d->kuota }}</span>
                                    </small>
                                </div>
                                {{-- Progress bar kuota --}}
                                @if ($d->kuota > 0)
                                @php $pct = min(100, round(($d->jumlah_peserta / $d->kuota) * 100)); @endphp
                                <div class="progress mt-1" style="height:4px">
                                    <div class="progress-bar bg-{{ $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success') }}"
                                         style="width:{{ $pct }}%"></div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Sesi presensi hari ini --}}
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-clipboard-check text-purple mr-1"></i>
                            Presensi Hari Ini
                        </h3>
                    </div>
                    <div class="block-content p-0">
                        @if ($sesiHariIni->isEmpty())
                            <p class="text-muted p-3 mb-0 font-size-sm">Tidak ada sesi presensi hari ini.</p>
                        @else
                            @foreach ($sesiHariIni as $s)
                            <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="font-w600 font-size-sm">
                                        {{ \Illuminate\Support\Str::limit($s->nama_materi, 28) }}
                                    </div>
                                    <small class="text-muted">
                                        {{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    @if ($s->is_aktif)
                                        <span class="badge badge-success" style="font-size:.65rem">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary" style="font-size:.65rem">Selesai</span>
                                    @endif
                                    <div style="font-size:.72rem;margin-top:2px">
                                        <span class="text-success font-w600">{{ $s->hadir }}</span>
                                        <span class="text-muted">/ {{ $s->total }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Dokumen menunggu verifikasi --}}
                @if ($dokumenMenunggu->isNotEmpty())
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-file-alt text-warning mr-1"></i>
                            Dokumen Menunggu
                        </h3>
                    </div>
                    <div class="block-content p-0">
                        @foreach ($dokumenMenunggu as $d)
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="font-size-sm">{{ $d->nama }}</span>
                            <span class="badge badge-warning">{{ $d->jumlah }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
    <!-- END Page Content -->
@endsection
