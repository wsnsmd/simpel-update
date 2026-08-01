@extends('layouts.frontend')

@section('css_after')
<style>
.peserta-hero {
    background: #ffffff;
    border-bottom: 3px solid #353a3f;
    padding: 1.75rem 0 2rem; position: relative; overflow: hidden;
}
.peserta-hero::after {
    display: none;
}
.peserta-avatar {
    width:80px; height:80px; border-radius:50%; object-fit:cover;
    border:3px solid #bfdbfe;
}
.peserta-avatar-placeholder {
    width:80px; height:80px; border-radius:50%;
    background:#eff6ff;
    display:flex; align-items:center; justify-content:center;
    font-size:1.6rem; font-weight:500; color:#1d4ed8;
    border:3px solid #bfdbfe;
    letter-spacing:.05em;
}
.stat-card { border-radius:12px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.07); transition:transform .15s; }
.stat-card:hover { transform:translateY(-2px); }
.stat-card .stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
.diklat-card { border-radius:12px; border:1px solid #e4e9f0; box-shadow:0 2px 8px rgba(0,0,0,.04); transition:box-shadow .15s; overflow:hidden; margin-bottom:1.25rem; }
.diklat-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.10); }
.diklat-header { background:linear-gradient(90deg,#f8faff 0%,#fff 100%); border-bottom:1px solid #e8edf5; padding:1rem 1.25rem .75rem; }
.diklat-body { padding:1rem 1.25rem 1.25rem; }
.status-badge { display:inline-flex; align-items:center; gap:4px; font-size:.72rem; font-weight:600; padding:.3em .7em; border-radius:20px; }
.badge-aktif  { background:#d1fae5; color:#065f46; }
.badge-verif  { background:#dbeafe; color:#1e40af; }
.badge-tunggu { background:#fef3c7; color:#92400e; }
.badge-batal  { background:#fee2e2; color:#991b1b; }
.badge-sertif { background:#ede9fe; color:#5b21b6; }
.date-range { display:flex; align-items:center; gap:.5rem; font-size:.82rem; color:#6b7280; }
.date-range .dot { width:8px; height:8px; border-radius:50%; background:#2563a8; flex-shrink:0; }
.dokumen-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:.75rem; }
.dokumen-item { border:1.5px solid #e4e9f0; border-radius:10px; padding:.75rem; background:#fff; position:relative; transition:border-color .15s; }
.dokumen-item.done    { border-color:#86efac; background:#f0fdf4; }
.dokumen-item.missing { border-color:#fca5a5; background:#fff5f5; }
.dokumen-item.optional{ border-color:#e4e9f0; background:#fafafa; }
.dokumen-item.locked  { border-color:#a5b4fc; background:#f5f3ff; }
.dokumen-badge { position:absolute; top:.5rem; right:.5rem; font-size:.65rem; font-weight:700; padding:.2em .5em; border-radius:12px; }
.dokumen-badge.wajib    { background:#fee2e2; color:#991b1b; }
.dokumen-badge.opsional { background:#f1f5f9; color:#64748b; }
.dokumen-badge.verified { background:#d1fae5; color:#065f46; }
.upload-zone { border:2px dashed #c7d4e8; border-radius:8px; padding:1.25rem; text-align:center; background:#f8faff; cursor:pointer; transition:border-color .2s; }
.upload-zone:hover { border-color:#2563a8; }
/* Paging */
.peserta-paging .page-link { border-radius:8px !important; margin:0 2px; font-size:.82rem; }
/* .peserta-paging .page-item.active .page-link { background:#2563a8; border-color:#2563a8; } */
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="peserta-hero">
    <div class="container">
        <div class="d-flex align-items-center flex-wrap" style="gap:1.25rem">
            @if ($fotoRow && $fotoRow->foto)
                <img src="{{ Storage::url($fotoRow->foto) }}" class="peserta-avatar" alt="Foto">
            @else
                @php
                    $words    = explode(' ', trim($nama));
                    $inisial  = strtoupper(substr($words[0], 0, 1));
                    $inisial .= isset($words[1]) ? strtoupper(substr($words[1], 0, 1)) : '';
                @endphp
                <div class="peserta-avatar-placeholder">{{ $inisial }}</div>
            @endif
            <div class="flex-grow-1">
                <p class="mb-0 font-size-sm" style="color:#6b7280">Selamat datang,</p>
                <h2 class="font-w700 mb-1" style="font-size:1.4rem;color:#111827">{{ $nama }}</h2>
                <span class="font-size-sm" style="color:#9ca3af">
                    <i class="fa fa-id-card mr-1"></i> NIP: {{ $nip }}
                </span>
            </div>
            <form action="{{ route('peserta.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm px-3"
                        style="background:#fff;border:1px solid #e5e7eb;color:#374151;border-radius:6px">
                    <i class="fa fa-sign-out-alt mr-1"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="container" style="margin-top:1.5rem; position:relative; z-index:10">
    <div class="row">
        @foreach ([
            ['val' => $stats->total,    'lbl' => 'Total Diklat',   'icon' => 'fa-calendar-alt', 'bg' => '#eff6ff', 'fg' => '#1d4ed8'],
            ['val' => $stats->aktif,    'lbl' => 'Terverifikasi',  'icon' => 'fa-check-circle', 'bg' => '#f0fdf4', 'fg' => '#15803d'],
            ['val' => $totalSertifikat, 'lbl' => 'Sertifikat',     'icon' => 'fa-certificate',  'bg' => '#faf5ff', 'fg' => '#7c3aed'],
            ['val' => $totalSurveyDone, 'lbl' => 'Survey Selesai', 'icon' => 'fa-poll',         'bg' => '#fff7ed', 'fg' => '#c2410c'],
        ] as $s)
        <div class="col-6 col-md-3 mb-3 px-2">
            <div class="block block-rounded mb-0 stat-card h-100">
                <div class="block-content py-3 px-3 d-flex align-items-center" style="gap:.75rem">
                    <div class="stat-icon" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }}">
                        <i class="fa {{ $s['icon'] }}"></i>
                    </div>
                    <div>
                        <div style="font-size:1.6rem;font-weight:700;line-height:1;color:{{ $s['fg'] }}">{{ $s['val'] }}</div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:500;text-transform:uppercase;letter-spacing:.04em">{{ $s['lbl'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Flash --}}
<div class="container">
    @if (session('flash_success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-check-circle mr-1"></i> {{ session('flash_success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            {{ $errors->first() }}
        </div>
    @endif
</div>

{{-- Daftar diklat --}}
<div class="container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="font-w700 mb-0">
            <i class="fa fa-list-alt text-primary mr-2"></i>Riwayat Diklat
        </h5>
        <small class="text-muted">
            Halaman {{ $page }} dari {{ $totalPages }} &mdash; {{ $totalRows }} diklat
        </small>
    </div>

    @forelse ($riwayat as $r)
    @php
        $hasSertifikat  = isset($sertifikat[$r->id]);
        $hasSurvey      = isset($survey[$r->id]);
        $surveyDone     = $hasSurvey && $survey[$r->id]->is_completed;
        $syaratList     = isset($syaratPerJadwal[$r->jadwal_id]) ? $syaratPerJadwal[$r->jadwal_id] : collect();
        $dokumenList    = isset($dokumenPeserta[$r->id]) ? $dokumenPeserta[$r->id] : collect();
        $dokumenById    = $dokumenList->keyBy('dokumen_syarat_id');

        $dokumenWajibCount = $syaratList->where('wajib', true)->count();
        $dokumenTerpenuhi  = $syaratList->where('wajib', true)->filter(function($s) use ($dokumenById) {
            return $dokumenById->has($s->id);
        })->count();

        // Tombol foto: hanya tampil jika registrasi_lengkap=1 DAN sertifikat belum terbit
        $bolehGantiFor = $r->registrasi_lengkap && !$hasSertifikat;
    @endphp

    <div class="diklat-card">
        {{-- Header --}}
        <div class="diklat-header d-flex align-items-start justify-content-between flex-wrap" style="gap:.5rem">
            <div>
                <div class="font-w600" style="color:#1e293b;font-size:.95rem">{{ $r->diklat_nama }}</div>
                <div class="date-range mt-1">
                    <div class="dot"></div>
                    {{ \Carbon\Carbon::parse($r->tgl_awal)->format('d M Y') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($r->tgl_akhir)->format('d M Y') }}
                </div>
                <small class="text-muted">Kode: <span class="font-w600">{{ $r->kode }}</span></small>
            </div>
            <div class="d-flex flex-wrap" style="gap:.3rem">
                @if ($r->batal)
                    <span class="status-badge badge-batal"><i class="fa fa-times-circle"></i> Batal</span>
                @elseif ($r->verifikasi && $r->konfirmasi)
                    <span class="status-badge badge-aktif"><i class="fa fa-check-circle"></i> Aktif</span>
                @elseif ($r->verifikasi)
                    <span class="status-badge badge-verif"><i class="fa fa-check"></i> Terverifikasi</span>
                @else
                    <span class="status-badge badge-tunggu"><i class="fa fa-clock"></i> Menunggu Verifikasi</span>
                @endif
                @if ($hasSertifikat)
                    <span class="status-badge badge-sertif"><i class="fa fa-certificate"></i> Sertifikat tersedia</span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="diklat-body">
            <div class="d-flex flex-wrap mb-3" style="gap:.4rem">

                {{-- Download sertifikat --}}
                @if ($hasSertifikat)
                    <a href="{{ route('peserta.sertifikat.download', $r->id) }}"
                       class="btn btn-sm btn-primary">
                        <i class="fa fa-download mr-1"></i> Download Sertifikat
                    </a>
                @endif

                {{-- Ganti foto --}}
                @if ($bolehGantiFor)
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            style="border-radius:6px;font-size:.78rem;padding:.3rem .75rem"
                            data-toggle="modal" data-target="#modalFoto{{ $r->id }}">
                        <i class="fa fa-camera mr-1"></i> Ganti Foto
                    </button>
                @endif

                {{-- Survey --}}
                @if ($surveyDone)
                    <span class="status-badge badge-aktif"><i class="fa fa-poll"></i> Survey selesai</span>
                @elseif ($hasSurvey)
                    <span class="status-badge badge-tunggu"><i class="fa fa-poll"></i> Survey belum diisi</span>
                @endif

            </div>

            {{-- Persyaratan dokumen --}}
            @if ($syaratList->isNotEmpty())
                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <small class="font-w600 text-muted text-uppercase" style="font-size:.68rem;letter-spacing:.05em">
                        Persyaratan Dokumen
                    </small>
                    @if ($dokumenWajibCount > 0)
                        <small class="text-muted font-size-sm">
                            {{ $dokumenTerpenuhi }}/{{ $dokumenWajibCount }} wajib terpenuhi
                        </small>
                    @endif
                </div>
                <div class="dokumen-grid">
                    @foreach ($syaratList as $syarat)
                    @php
                        $uploaded  = $dokumenById->get($syarat->id);
                        $verified  = $uploaded && $uploaded->verified_by_admin;
                        $rejected  = $uploaded && !$uploaded->verified_by_admin && $uploaded->verified_at;
                        $canEdit   = !$verified && !$r->batal;
                        $itemClass = $verified ? 'locked' : ($uploaded ? ($rejected ? 'missing' : 'done') : ($syarat->wajib ? 'missing' : 'optional'));
                    @endphp
                    <div class="dokumen-item {{ $itemClass }}">
                        @if ($verified)
                            <span class="dokumen-badge verified"><i class="fa fa-check"></i> Diverifikasi</span>
                        @elseif ($syarat->wajib)
                            <span class="dokumen-badge wajib">Wajib</span>
                        @else
                            <span class="dokumen-badge opsional">Opsional</span>
                        @endif

                        <div class="font-w600 font-size-sm mb-1" style="padding-right:75px">{{ $syarat->nama }}</div>

                        @if ($syarat->keterangan)
                            <div class="text-muted mb-1" style="font-size:.72rem">{{ $syarat->keterangan }}</div>
                        @endif

                        {{-- Status --}}
                        @if ($uploaded)
                            <div class="d-flex align-items-center mb-1" style="gap:.3rem">
                                @if ($verified)
                                    <i class="fa fa-check-circle text-success" style="font-size:.8rem"></i>
                                    <span style="font-size:.72rem;color:#065f46">Diterima</span>
                                @elseif ($rejected)
                                    <i class="fa fa-times-circle text-danger" style="font-size:.8rem"></i>
                                    <span style="font-size:.72rem;color:#991b1b">Ditolak — upload ulang</span>
                                @else
                                    <i class="fa fa-clock text-warning" style="font-size:.8rem"></i>
                                    <span style="font-size:.72rem;color:#92400e">Menunggu verifikasi</span>
                                @endif
                            </div>
                            @if ($uploaded->catatan_admin)
                                <div style="background:#fff3cd;border-radius:6px;padding:.3rem .5rem;font-size:.72rem;margin-bottom:.4rem">
                                    <i class="fa fa-comment-alt mr-1"></i>{{ $uploaded->catatan_admin }}
                                </div>
                            @endif
                            <div style="font-size:.68rem;color:#94a3b8">
                                {{ \Carbon\Carbon::parse($uploaded->uploaded_at)->format('d M Y H:i') }}
                            </div>
                        @else
                            <div style="font-size:.72rem;color:#94a3b8"><i class="fa fa-upload mr-1"></i>Belum diunggah</div>
                        @endif

                        @if ($canEdit)
                            <button type="button"
                                    class="btn btn-sm btn-block mt-2"
                                    style="font-size:.72rem;padding:.25rem;border-radius:6px;background:{{ $rejected ? '#fff5f5' : ($uploaded ? '#f1f5f9' : '#eff6ff') }};color:{{ $rejected ? '#991b1b' : '#1d4ed8' }};border:1px solid {{ $rejected ? '#fca5a5' : '#bfdbfe' }}"
                                    data-toggle="modal"
                                    data-target="#modalDok{{ $r->id }}_{{ $syarat->id }}">
                                <i class="fa fa-{{ $rejected ? 'redo' : ($uploaded ? 'sync-alt' : 'upload') }} mr-1"></i>
                                {{ $rejected ? 'Upload Ulang' : ($uploaded ? 'Ganti' : 'Upload') }}
                            </button>
                        @elseif ($verified)
                            <div style="font-size:.68rem;color:#6b7280;margin-top:.4rem">
                                <i class="fa fa-lock mr-1"></i>Terkunci setelah diverifikasi
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Riwayat Presensi ──────────────────────────────────────── --}}
        @php
            $presensiJadwal = isset($presensiPerJadwal[$r->jadwal_id])
                ? $presensiPerJadwal[$r->jadwal_id]
                : null;
            $sesiDiklat = $presensiJadwal ? $presensiJadwal['sesi'] : collect();
            $mapPresensi = ($presensiJadwal && isset($presensiJadwal['presensi'][$r->id]))
                ? $presensiJadwal['presensi'][$r->id]
                : array();
            $totalSesi   = $sesiDiklat->count();
            $hadirSesi   = 0;
            foreach ($sesiDiklat as $ss) {
                $st = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->status : 'alpha';
                if (in_array($st, ['hadir','terlambat'])) $hadirSesi++;
            }
        @endphp

        @if ($totalSesi > 0)
        <div class="diklat-body" style="padding-top:0;border-top:1px solid #f0f4ff">
            <div class="mb-2 d-flex align-items-center justify-content-between">
                <small class="font-w600 text-muted text-uppercase" style="font-size:.68rem;letter-spacing:.05em">
                    <i class="fa fa-clipboard-check mr-1"></i> Presensi
                </small>
                <small class="text-muted font-size-sm">
                    {{ $hadirSesi }}/{{ $totalSesi }} sesi hadir
                    @if ($totalSesi > 0)
                        &middot;
                        <span class="{{ ($hadirSesi/$totalSesi) >= 0.8 ? 'text-success' : (($hadirSesi/$totalSesi) >= 0.5 ? 'text-warning' : 'text-danger') }} font-w600">
                            {{ round(($hadirSesi / $totalSesi) * 100) }}%
                        </span>
                    @endif
                </small>
            </div>

            {{-- Progress bar kehadiran --}}
            @if ($totalSesi > 0)
            @php $pctHadir = round(($hadirSesi / $totalSesi) * 100); @endphp
            <div class="progress mb-3" style="height:5px;border-radius:4px">
                <div class="progress-bar {{ $pctHadir >= 80 ? 'bg-success' : ($pctHadir >= 50 ? 'bg-warning' : 'bg-danger') }}"
                     style="width:{{ $pctHadir }}%"></div>
            </div>
            @endif

            {{-- List sesi --}}
            <div style="display:flex;flex-direction:column;gap:.3rem">
                @foreach ($sesiDiklat as $ss)
                @php
                    $stSesi = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->status : 'alpha';
                    $scanAt = isset($mapPresensi[$ss->id]) ? $mapPresensi[$ss->id]->scan_at : null;
                    $stIcon  = ['hadir'=>'fa-check-circle','terlambat'=>'fa-clock','izin'=>'fa-info-circle','sakit'=>'fa-heartbeat','alpha'=>'fa-times-circle'];
                    $stColor = ['hadir'=>'#065f46','terlambat'=>'#92400e','izin'=>'#1e40af','sakit'=>'#475569','alpha'=>'#991b1b'];
                    $stBg    = ['hadir'=>'#d1fae5','terlambat'=>'#fef3c7','izin'=>'#dbeafe','sakit'=>'#f1f5f9','alpha'=>'#fee2e2'];
                    $stLabel = ['hadir'=>'Hadir','terlambat'=>'Terlambat','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Tidak Hadir'];
                @endphp
                <div class="d-flex align-items-center justify-content-between"
                     style="padding:.35rem .5rem;border-radius:8px;background:#f8faff">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.78rem;font-weight:500;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $ss->nama_materi }}
                        </div>
                        <div style="font-size:.68rem;color:#94a3b8">
                            {{ \Carbon\Carbon::parse($ss->tanggal)->format('d M Y') }}
                            &middot; {{ substr($ss->jam_mulai,0,5) }}–{{ substr($ss->jam_selesai,0,5) }}
                            &middot; {{ $ss->jp }} JP
                        </div>
                    </div>
                    <div class="text-right ml-2" style="flex-shrink:0">
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:.68rem;font-weight:600;padding:.2em .55em;border-radius:12px;background:{{ $stBg[$stSesi] ?? '#fee2e2' }};color:{{ $stColor[$stSesi] ?? '#991b1b' }}">
                            <i class="fa {{ $stIcon[$stSesi] ?? 'fa-times-circle' }}" style="font-size:.6rem"></i>
                            {{ $stLabel[$stSesi] ?? 'Tidak Hadir' }}
                        </span>
                        @if ($scanAt)
                        <div style="font-size:.65rem;color:#94a3b8;margin-top:2px">
                            {{ \Carbon\Carbon::parse($scanAt)->format('H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Modal ganti foto --}}
    @if ($bolehGantiFor)
    <div class="modal fade" id="modalFoto{{ $r->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-w700"><i class="fa fa-camera text-primary mr-2"></i>Ganti Foto</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted font-size-sm mb-3">Format JPG/PNG, maksimal 1 MB.</p>
                    @if ($r->foto)
                        <div class="text-center mb-3">
                            <img src="{{ Storage::url($r->foto) }}"
                                 style="width:90px;height:90px;object-fit:cover;border-radius:50%;border:3px solid #e0e7ef" alt="">
                            <p class="text-muted font-size-sm mt-1 mb-0">Foto saat ini</p>
                        </div>
                    @endif
                    <form action="{{ route('peserta.foto.upload', $r->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-zone" onclick="document.getElementById('foto_{{ $r->id }}').click()">
                            <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-0 text-muted font-size-sm">Klik untuk pilih foto</p>
                            <small class="text-muted">JPG, PNG · Maks 1 MB</small>
                            <input type="file" id="foto_{{ $r->id }}" name="foto"
                                   accept="image/jpeg,image/png" class="d-none"
                                   onchange="labelFile(this,'lbl_foto_{{ $r->id }}')">
                        </div>
                        <small id="lbl_foto_{{ $r->id }}" class="text-muted d-block text-center mt-1"></small>
                        <div class="d-flex justify-content-end mt-3" style="gap:.5rem">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save mr-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal upload dokumen per syarat --}}
    @foreach ($syaratList as $syarat)
    @php
        $uploaded = $dokumenById->get($syarat->id);
        $verified = $uploaded && $uploaded->verified_by_admin;
        $canEdit  = !$verified && !$r->batal;
    @endphp
    @if ($canEdit)
    <div class="modal fade" id="modalDok{{ $r->id }}_{{ $syarat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
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

                    {{-- Pesan khusus jika ditolak --}}
                    @if ($uploaded && $uploaded->verified_at && !$uploaded->verified_by_admin)
                        <div class="alert alert-danger font-size-sm" style="padding:.5rem .75rem">
                            <i class="fa fa-exclamation-triangle mr-1"></i>
                            <strong>Dokumen ditolak.</strong>
                            @if ($uploaded->catatan_admin)
                                Alasan: {{ $uploaded->catatan_admin }}
                            @endif
                            Silakan upload ulang file yang sesuai.
                        </div>
                    @elseif ($uploaded)
                        <div class="alert alert-info font-size-sm" style="padding:.5rem .75rem">
                            <i class="fa fa-info-circle mr-1"></i>
                            Sudah ada file. Upload baru untuk mengganti.
                        </div>
                    @endif

                    <form action="{{ route('peserta.dokumen.upload', [$r->id, $syarat->id]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-zone"
                             onclick="document.getElementById('dok_{{ $r->id }}_{{ $syarat->id }}').click()">
                            <i class="fa fa-file-upload fa-2x text-muted mb-2"></i>
                            <p class="mb-0 text-muted font-size-sm">Klik untuk pilih file</p>
                            <small class="text-muted">
                                {{ strtoupper(str_replace(',', ', ', $syarat->format_izin)) }}
                                &middot; Maks {{ round($syarat->max_size_kb / 1024, 1) }} MB
                            </small>
                            <input type="file"
                                   id="dok_{{ $r->id }}_{{ $syarat->id }}"
                                   name="dokumen"
                                   accept="{{ implode(',', array_map(function($e){ return '.'.trim($e); }, explode(',', $syarat->format_izin))) }}"
                                   class="d-none"
                                   onchange="labelFile(this,'lbl_dok_{{ $r->id }}_{{ $syarat->id }}')">
                        </div>
                        <small id="lbl_dok_{{ $r->id }}_{{ $syarat->id }}" class="text-muted d-block text-center mt-1"></small>
                        <div class="d-flex justify-content-end mt-3" style="gap:.5rem">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-upload mr-1"></i> Upload
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
    <div class="block block-rounded">
        <div class="block-content text-center py-5">
            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada riwayat diklat</h5>
        </div>
    </div>
    @endforelse

    {{-- Paging --}}
    @if ($totalPages > 1)
    <nav class="d-flex justify-content-center mt-4">
        <ul class="pagination peserta-paging">
            {{-- Prev --}}
            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>

            {{-- Nomor halaman --}}
            @for ($i = 1; $i <= $totalPages; $i++)
                @if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2))
                    <li class="page-item {{ $i == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                    </li>
                @elseif ($i == $page - 3 || $i == $page + 3)
                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                @endif
            @endfor

            {{-- Next --}}
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
