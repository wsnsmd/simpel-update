@extends('layouts.frontend')

@section('content')

    {{-- ═══════════════════════════════════════════════════════════
    HERO — profil peserta
    ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-body border-bottom py-4">
        <div class="container">
            <div class="d-flex align-items-center flex-wrap" style="gap:1.25rem">

                {{-- Avatar --}}
                @if ($fotoRow && $fotoRow->foto)
                    <img src="{{ Storage::url($fotoRow->foto) }}" class="rounded-circle flex-shrink-0" style="width:72px;height:72px;object-fit:cover;
                                                            border:3px solid var(--primary, #3f6ad8)" alt="Foto">
                @else
                    @php
                        $words = explode(' ', trim($nama));
                        $inisial = strtoupper(substr($words[0], 0, 1));
                        $inisial .= isset($words[1]) ? strtoupper(substr($words[1], 0, 1)) : '';
                    @endphp
                    <div class="item item-circle bg-primary-lighter flex-shrink-0" style="width:72px;height:72px;min-width:72px;
                                                            font-size:1.5rem;font-weight:700;color:inherit">
                        <span class="text-primary">{{ $inisial }}</span>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-grow-1" style="min-width:0">
                    <p class="text-muted font-size-sm mb-1">Selamat datang,</p>
                    <h2 class="font-w700 mb-1" style="font-size:1.3rem">{{ $nama }}</h2>
                    <span class="text-muted font-size-sm">
                        <i class="fa fa-id-card mr-1"></i>NIP: {{ $nip }}
                    </span>
                </div>

                {{-- Logout --}}
                <form action="{{ route('peserta.logout') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-sign-out-alt mr-1"></i>Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
    STAT CARDS
    ═══════════════════════════════════════════════════════════ --}}
    <div class="container mt-4">
        <div class="row">
            @php
                $statItems = [
                    ['val' => $stats->total, 'lbl' => 'Total Diklat', 'icon' => 'fa-calendar-alt', 'bg' => 'bg-primary-lighter', 'fg' => 'text-primary'],
                    ['val' => $stats->aktif, 'lbl' => 'Terverifikasi', 'icon' => 'fa-check-circle', 'bg' => 'bg-success-lighter', 'fg' => 'text-success'],
                    ['val' => $totalSertifikat, 'lbl' => 'Sertifikat', 'icon' => 'fa-certificate', 'bg' => 'bg-info-lighter', 'fg' => 'text-info'],
                    ['val' => $totalSurveyDone, 'lbl' => 'Survey Selesai', 'icon' => 'fa-poll', 'bg' => 'bg-warning-lighter', 'fg' => 'text-warning'],
                ];
            @endphp
            @foreach ($statItems as $s)
                <div class="col-6 col-md-3 mb-3">
                    <div class="block block-rounded block-link-pop mb-0 h-100">
                        <div class="block-content py-3 d-flex align-items-center" style="gap:.75rem">
                            <div class="item item-rounded {{ $s['bg'] }} flex-shrink-0">
                                <i class="fa {{ $s['icon'] }} {{ $s['fg'] }}"></i>
                            </div>
                            <div>
                                <div class="font-w700 {{ $s['fg'] }}" style="font-size:1.6rem;line-height:1">
                                    {{ $s['val'] }}
                                </div>
                                <div class="text-muted font-size-sm text-uppercase"
                                    style="font-size:.68rem;letter-spacing:.04em">
                                    {{ $s['lbl'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Shortcut: Upload Laporan Seminar --}}
            <div class="col-12 mb-3">
                <a href="{{ route('peserta.laporan.index') }}"
                    class="block block-rounded block-link-pop mb-0 d-flex align-items-center px-3 py-3"
                    style="gap:.75rem;text-decoration:none;border:1.5px dashed #3f6ad8;background:#f0f4ff">
                    <div class="item item-rounded bg-primary-lighter flex-shrink-0">
                        <i class="fa fa-link text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-w700 text-primary" style="font-size:.9rem">Upload Tautan Laporan Seminar</div>
                        <div class="text-muted font-size-sm">Bagikan link Google Drive / OneDrive laporan Anda kepada
                            penguji.</div>
                    </div>
                    <i class="fa fa-chevron-right text-primary flex-shrink-0"></i>
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('flash_success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-check-circle mr-1"></i>{{ session('flash_success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════
    RIWAYAT DIKLAT
    ═══════════════════════════════════════════════════════════ --}}
    <div class="container mb-5">

        {{-- Sub-header --}}
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:.5rem">
            <h5 class="font-w700 mb-0">
                <i class="fa fa-list-alt text-primary mr-2"></i>Riwayat Diklat
            </h5>
            <small class="text-muted">
                Halaman {{ $page }} dari {{ $totalPages }}
                <span class="badge badge-secondary ml-1">{{ $totalRows }}</span>
            </small>
        </div>

        @forelse ($riwayat as $r)
            @php
                $hasSertifikat = isset($sertifikat[$r->id]);
                $hasSurvey = isset($survey[$r->id]);
                $surveyDone = $hasSurvey && $survey[$r->id]->is_completed;
                $syaratList = $syaratPerJadwal[$r->jadwal_id] ?? collect();
                $dokumenList = $dokumenPeserta[$r->id] ?? collect();
                $dokumenById = $dokumenList->keyBy('dokumen_syarat_id');

                $dokumenWajibCount = $syaratList->where('wajib', true)->count();
                $dokumenTerpenuhi = $syaratList->where('wajib', true)->filter(function ($s) use ($dokumenById) {
                    return $dokumenById->has($s->id);
                })->count();

                $bolehGantiFoto = $r->registrasi_lengkap && !$hasSertifikat;

                // Presensi
                $presensiJadwal = $presensiPerJadwal[$r->jadwal_id] ?? null;
                $sesiDiklat = $presensiJadwal ? $presensiJadwal['sesi'] : collect();
                $mapPresensi = ($presensiJadwal && isset($presensiJadwal['presensi'][$r->id]))
                    ? $presensiJadwal['presensi'][$r->id] : [];
                $totalSesi = $sesiDiklat->count();
                $hadirSesi = 0;
                foreach ($sesiDiklat as $ss) {
                    $st = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->status : 'alpha';
                    if (in_array($st, ['hadir', 'terlambat']))
                        $hadirSesi++;
                }
                $pctHadir = $totalSesi > 0 ? round(($hadirSesi / $totalSesi) * 100) : 0;
                $pctClass = $pctHadir >= 80 ? 'bg-success' : ($pctHadir >= 50 ? 'bg-warning' : 'bg-danger');
            @endphp

            <div class="block block-rounded block-bordered block-link-shadow mb-3">

                {{-- ── Header kartu diklat ── --}}
                <div class="block-header block-header-default">
                    <div style="min-width:0;flex:1">
                        <h3 class="block-title font-w700 text-wrap-break-word">
                            {{ $r->diklat_nama }}
                        </h3>
                        <div class="d-flex align-items-center text-muted font-size-sm mt-1" style="gap:.4rem">
                            <i class="fa fa-calendar text-primary" style="font-size:.7rem"></i>
                            {{ \Carbon\Carbon::parse($r->tgl_awal)->format('d M Y') }}
                            –
                            {{ \Carbon\Carbon::parse($r->tgl_akhir)->format('d M Y') }}
                            <span class="mx-1">&middot;</span>
                            Kode: <strong>{{ $r->kode }}</strong>
                        </div>
                    </div>

                    {{-- Badge status --}}
                    <div class="block-options d-flex flex-wrap" style="gap:.25rem">
                        @if ($r->batal)
                            <span class="badge badge-danger">
                                <i class="fa fa-times-circle mr-1"></i>Batal
                            </span>
                        @elseif ($r->verifikasi && $r->konfirmasi)
                            <span class="badge badge-success">
                                <i class="fa fa-check-circle mr-1"></i>Aktif
                            </span>
                        @elseif ($r->verifikasi)
                            <span class="badge badge-primary">
                                <i class="fa fa-check mr-1"></i>Terverifikasi
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fa fa-clock mr-1"></i>Menunggu Verifikasi
                            </span>
                        @endif
                        @if ($hasSertifikat)
                            <span class="badge badge-info">
                                <i class="fa fa-certificate mr-1"></i>Sertifikat
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ── Tombol aksi utama ── --}}
                <div class="block-content block-content-sm bg-body-light py-2">
                    <div class="d-flex flex-wrap" style="gap:.35rem">
                        @if ($hasSertifikat)
                            <a href="{{ route('peserta.sertifikat.download', $r->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-download mr-1"></i>Download Sertifikat
                            </a>
                        @endif
                        @if ($bolehGantiFoto)
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                                data-target="#modalFoto{{ $r->id }}">
                                <i class="fa fa-camera mr-1"></i>Ganti Foto
                            </button>
                        @endif
                        @if ($surveyDone)
                            <span class="badge badge-success my-auto">
                                <i class="fa fa-poll mr-1"></i>Survey selesai
                            </span>
                        @elseif ($hasSurvey)
                            <span class="badge badge-warning my-auto">
                                <i class="fa fa-poll mr-1"></i>Survey belum diisi
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ── Persyaratan Dokumen ── --}}
                @if ($syaratList->isNotEmpty())
                    <div class="block-content">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="font-w700 font-size-sm text-uppercase text-muted mb-0" style="letter-spacing:.05em">
                                <i class="fa fa-folder-open mr-1 text-primary"></i>Persyaratan Dokumen
                            </h6>
                            @if ($dokumenWajibCount > 0)
                                <span class="badge {{ $dokumenTerpenuhi >= $dokumenWajibCount ? 'badge-success' : 'badge-warning' }}">
                                    {{ $dokumenTerpenuhi }}/{{ $dokumenWajibCount }} wajib
                                </span>
                            @endif
                        </div>

                        <div class="row" style="row-gap:.75rem">
                            @foreach ($syaratList as $syarat)
                                @php
                                    $uploaded = $dokumenById->get($syarat->id);
                                    $verified = $uploaded && $uploaded->verified_by_admin;
                                    $rejected = $uploaded && !$uploaded->verified_by_admin && $uploaded->verified_at;
                                    $canEdit = !$verified && !$r->batal;

                                    if ($verified) {
                                        $borderClass = 'border-success';
                                        $bgClass = 'bg-success-lighter';
                                    } elseif ($rejected) {
                                        $borderClass = 'border-danger';
                                        $bgClass = 'bg-danger-lighter';
                                    } elseif ($uploaded) {
                                        $borderClass = 'border-warning';
                                        $bgClass = '';
                                    } elseif ($syarat->wajib) {
                                        $borderClass = 'border-danger';
                                        $bgClass = '';
                                    } else {
                                        $borderClass = 'border-secondary';
                                        $bgClass = 'bg-body-light';
                                    }
                                @endphp
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="block block-rounded border {{ $borderClass }} border-2x mb-0 h-100 {{ $bgClass }}"
                                        style="box-shadow:none">
                                        <div class="block-content py-3">

                                            {{-- Badge posisi --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="font-w600 font-size-sm pr-2">{{ $syarat->nama }}</div>
                                                @if ($verified)
                                                    <span class="badge badge-success flex-shrink-0">
                                                        <i class="fa fa-check mr-1"></i>Terverifikasi
                                                    </span>
                                                @elseif ($syarat->wajib)
                                                    <span class="badge badge-danger flex-shrink-0">Wajib</span>
                                                @else
                                                    <span class="badge badge-secondary flex-shrink-0">Opsional</span>
                                                @endif
                                            </div>

                                            @if ($syarat->keterangan)
                                                <p class="text-muted font-size-sm mb-2">{{ $syarat->keterangan }}</p>
                                            @endif

                                            {{-- Status upload --}}
                                            @if ($uploaded)
                                                <div class="d-flex align-items-center mb-1 font-size-sm" style="gap:.3rem">
                                                    @if ($verified)
                                                        <i class="fa fa-check-circle text-success"></i>
                                                        <span class="text-success font-w600">Diterima</span>
                                                    @elseif ($rejected)
                                                        <i class="fa fa-times-circle text-danger"></i>
                                                        <span class="text-danger font-w600">Ditolak — upload ulang</span>
                                                    @else
                                                        <i class="fa fa-clock text-warning"></i>
                                                        <span class="text-warning font-w600">Menunggu verifikasi</span>
                                                    @endif
                                                </div>
                                                @if ($uploaded->catatan_admin)
                                                    <div class="alert alert-warning py-1 px-2 font-size-sm mb-1">
                                                        <i class="fa fa-comment-alt mr-1"></i>{{ $uploaded->catatan_admin }}
                                                    </div>
                                                @endif
                                                <div class="text-muted" style="font-size:.68rem">
                                                    {{ \Carbon\Carbon::parse($uploaded->uploaded_at)->format('d M Y H:i') }}
                                                </div>
                                            @else
                                                <div class="text-muted font-size-sm">
                                                    <i class="fa fa-upload mr-1"></i>Belum diunggah
                                                </div>
                                            @endif

                                            {{-- Tombol upload --}}
                                            @if ($canEdit)
                                                <button type="button"
                                                    class="btn btn-sm btn-block mt-2
                                                                                                                                   {{ $rejected ? 'btn-outline-danger' : ($uploaded ? 'btn-outline-secondary' : 'btn-outline-primary') }}"
                                                    data-toggle="modal" data-target="#modalDok{{ $r->id }}_{{ $syarat->id }}">
                                                    <i
                                                        class="fa fa-{{ $rejected ? 'redo' : ($uploaded ? 'sync-alt' : 'upload') }} mr-1"></i>
                                                    {{ $rejected ? 'Upload Ulang' : ($uploaded ? 'Ganti' : 'Upload') }}
                                                </button>
                                            @elseif ($verified)
                                                <div class="text-muted font-size-sm mt-2">
                                                    <i class="fa fa-lock mr-1"></i>Terkunci setelah diverifikasi
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── Presensi ── --}}
                @if ($totalSesi > 0)
                    <div class="block-content border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="font-w700 font-size-sm text-uppercase text-muted mb-0" style="letter-spacing:.05em">
                                <i class="fa fa-clipboard-check mr-1 text-primary"></i>Presensi
                            </h6>
                            <span class="font-size-sm text-muted">
                                {{ $hadirSesi }}/{{ $totalSesi }} sesi
                                <span
                                    class="{{ $pctClass === 'bg-success' ? 'text-success' : ($pctClass === 'bg-warning' ? 'text-warning' : 'text-danger') }} font-w600 ml-1">
                                    {{ $pctHadir }}%
                                </span>
                            </span>
                        </div>

                        <div class="progress mb-3" style="height:5px">
                            <div class="progress-bar {{ $pctClass }}" style="width:{{ $pctHadir }}%"></div>
                        </div>

                        {{-- List sesi --}}
                        @php
                            $stIcon = ['hadir' => 'fa-check-circle', 'terlambat' => 'fa-clock', 'izin' => 'fa-info-circle', 'sakit' => 'fa-heartbeat', 'alpha' => 'fa-times-circle'];
                            $stBadge = ['hadir' => 'badge-success', 'terlambat' => 'badge-warning', 'izin' => 'badge-primary', 'sakit' => 'badge-secondary', 'alpha' => 'badge-danger'];
                            $stLabel = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Tidak Hadir'];
                        @endphp
                        <div class="d-flex flex-column" style="gap:.3rem">
                            @foreach ($sesiDiklat as $ss)
                                @php
                                    $stSesi = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->status : 'alpha';
                                    $scanAt = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->scan_at : null;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between
                                                                                                px-3 py-2 rounded bg-body-light"
                                    style="gap:.5rem">
                                    <div style="flex:1;min-width:0">
                                        <div class="font-w600 font-size-sm text-wrap-break-word"
                                            style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                            {{ $ss->nama_materi }}
                                        </div>
                                        <div class="text-muted" style="font-size:.68rem">
                                            {{ \Carbon\Carbon::parse($ss->tanggal)->format('d M Y') }}
                                            &middot; {{ substr($ss->jam_mulai, 0, 5) }}–{{ substr($ss->jam_selesai, 0, 5) }}
                                            &middot; {{ $ss->jp }} JP
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="badge {{ $stBadge[$stSesi] ?? 'badge-danger' }}" style="font-size:.62rem">
                                            <i class="fa {{ $stIcon[$stSesi] ?? 'fa-times-circle' }} mr-1"></i>
                                            {{ $stLabel[$stSesi] ?? 'Tidak Hadir' }}
                                        </span>
                                        @if ($scanAt)
                                            <div class="text-muted" style="font-size:.63rem;margin-top:2px">
                                                {{ \Carbon\Carbon::parse($scanAt)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /block --}}

            {{-- ── Modal Ganti Foto ──────────────────────────────────── --}}
            @if ($bolehGantiFoto)
                <div class="modal fade" id="modalFoto{{ $r->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-w700">
                                    <i class="fa fa-camera text-primary mr-2"></i>Ganti Foto
                                </h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted font-size-sm mb-3">Format JPG/PNG, maksimal 1 MB.</p>
                                @if ($r->foto)
                                    <div class="text-center mb-3">
                                        <img src="{{ Storage::url($r->foto) }}" class="rounded-circle"
                                            style="width:80px;height:80px;object-fit:cover;border:3px solid #e0e7ef" alt="">
                                        <p class="text-muted font-size-sm mt-1 mb-0">Foto saat ini</p>
                                    </div>
                                @endif
                                <form action="{{ route('peserta.foto.upload', $r->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="border border-2x rounded text-center py-4 bg-body-light"
                                        style="border-style:dashed!important;cursor:pointer"
                                        onclick="document.getElementById('foto_{{ $r->id }}').click()">
                                        <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="text-muted font-size-sm mb-0">Klik untuk pilih foto</p>
                                        <small class="text-muted">JPG, PNG &middot; Maks 1 MB</small>
                                        <input type="file" id="foto_{{ $r->id }}" name="foto" accept="image/jpeg,image/png"
                                            class="d-none" onchange="labelFile(this,'lbl_foto_{{ $r->id }}')">
                                    </div>
                                    <small id="lbl_foto_{{ $r->id }}" class="text-muted d-block text-center mt-1"></small>
                                    <div class="d-flex justify-content-end mt-3" style="gap:.5rem">
                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fa fa-save mr-1"></i>Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Modal Upload Dokumen ──────────────────────────────── --}}
            @foreach ($syaratList as $syarat)
                @php
                    $uploaded = $dokumenById->get($syarat->id);
                    $verified = $uploaded && $uploaded->verified_by_admin;
                    $canEdit = !$verified && !$r->batal;
                @endphp
                @if ($canEdit)
                    <div class="modal fade" id="modalDok{{ $r->id }}_{{ $syarat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-w700">
                                        <i class="fa fa-file-upload text-primary mr-2"></i>
                                        @if ($uploaded && $uploaded->verified_at && !$uploaded->verified_by_admin)
                                            Upload Ulang: {{ $syarat->nama }}
                                        @elseif ($uploaded)
                                            Ganti: {{ $syarat->nama }}
                                        @else
                                            Upload: {{ $syarat->nama }}
                                        @endif
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    @if ($syarat->keterangan)
                                        <p class="text-muted font-size-sm mb-3">{{ $syarat->keterangan }}</p>
                                    @endif

                                    @if ($uploaded && $uploaded->verified_at && !$uploaded->verified_by_admin)
                                        <div class="alert alert-danger font-size-sm py-2">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                            <strong>Dokumen ditolak.</strong>
                                            @if ($uploaded->catatan_admin)
                                                Alasan: {{ $uploaded->catatan_admin }}
                                            @endif
                                            Silakan upload ulang file yang sesuai.
                                        </div>
                                    @elseif ($uploaded)
                                        <div class="alert alert-info font-size-sm py-2">
                                            <i class="fa fa-info-circle mr-1"></i>
                                            Sudah ada file. Upload baru untuk mengganti.
                                        </div>
                                    @endif

                                    <form action="{{ route('peserta.dokumen.upload', [$r->id, $syarat->id]) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="border border-2x rounded text-center py-4 bg-body-light"
                                            style="border-style:dashed!important;cursor:pointer"
                                            onclick="document.getElementById('dok_{{ $r->id }}_{{ $syarat->id }}').click()">
                                            <i class="fa fa-file-upload fa-2x text-muted mb-2"></i>
                                            <p class="text-muted font-size-sm mb-0">Klik untuk pilih file</p>
                                            <small class="text-muted">
                                                {{ strtoupper(str_replace(',', ', ', $syarat->format_izin)) }}
                                                &middot; Maks {{ round($syarat->max_size_kb / 1024, 1) }} MB
                                            </small>
                                            <input type="file" id="dok_{{ $r->id }}_{{ $syarat->id }}" name="dokumen" accept="{{ implode(',', array_map(function ($e) {
                                return '.' . trim($e); }, explode(',', $syarat->format_izin))) }}" class="d-none"
                                                onchange="labelFile(this,'lbl_dok_{{ $r->id }}_{{ $syarat->id }}')">
                                        </div>
                                        <small id="lbl_dok_{{ $r->id }}_{{ $syarat->id }}"
                                            class="text-muted d-block text-center mt-1"></small>
                                        <div class="d-flex justify-content-end mt-3" style="gap:.5rem">
                                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fa fa-upload mr-1"></i>Upload
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        @empty
            <div class="block block-rounded text-center py-5">
                <div class="item item-3x item-circle bg-body-light mx-auto mb-3">
                    <i class="fa fa-inbox fa-2x text-muted"></i>
                </div>
                <h5 class="text-muted mb-0">Belum ada riwayat diklat</h5>
            </div>
        @endforelse

        {{-- ── Paginasi ──────────────────────────────────────────── --}}
        @if ($totalPages > 1)
            <nav class="d-flex justify-content-center mt-4">
                <ul class="pagination">
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 1; $i <= $totalPages; $i++)
                        @if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2))
                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @elseif ($i == $page - 3 || $i == $page + 3)
                            <li class="page-item disabled">
                                <span class="page-link">&hellip;</span>
                            </li>
                        @endif
                    @endfor
                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

    </div>

@endsection

@section('js_after')
    <script>
        function labelFile(input, labelId) {
            var el = document.getElementById(labelId);
            if (el && input.files && input.files[0]) {
                el.textContent = input.files[0].name;
            }
        }
    </script>
@endsection