@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        @if (session('notifikasi'))
        $.notify({
            icon: "fa fa-check mr-1",
            message: "{{ session('notifikasi') }}"
        }, {
            allow_dismiss: false, type: 'success',
            placement: { from: "top", align: "center" }
        });
        @endif

        function konfirmasiVerifikasi(form, aksi, nama) {
            var pesan = aksi === 'terima'
                ? 'Dokumen "' + nama + '" akan diterima.'
                : 'Dokumen "' + nama + '" akan ditolak. Pastikan catatan sudah diisi.';
            Swal.fire({
                title: aksi === 'terima' ? 'Terima dokumen?' : 'Tolak dokumen?',
                text: pesan,
                icon: aksi === 'terima' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: aksi === 'terima' ? '#28a745' : '#e74c3c',
                cancelButtonText: 'Batal',
                confirmButtonText: aksi === 'terima' ? 'Ya, terima' : 'Ya, tolak'
            }).then(function(result) {
                if (result.value) form.submit();
            });
        }
    </script>
@endsection

@section('content')
<div class="content">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-4">
        <div>
            <h2 class="h3 my-2">{{ $syarat->nama }}</h2>
        <div class="mt-2 mt-sm-0 d-flex" style="gap:.5rem">
            <a href="{{ route('backend.diklat.dokumen_syarat.download_by_syarat', $syarat->id) }}"
               class="btn btn-success btn-sm">
                <i class="fa fa-file-archive mr-1"></i> Download Semua (ZIP)
            </a>
            <a href="{{ route('backend.diklat.dokumen_syarat.index', $jadwal->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
        </div>
        <div class="mt-2 mt-sm-0">
            @if ($syarat->wajib)
                <span class="badge badge-danger badge-pill px-3 py-2">Wajib</span>
            @else
                <span class="badge badge-secondary badge-pill px-3 py-2">Opsional</span>
            @endif
        </div>
    </div>

    @if ($syarat->keterangan)
        <div class="alert alert-light border">
            <i class="fa fa-info-circle text-primary mr-2"></i>{{ $syarat->keterangan }}
        </div>
    @endif

    <!-- Statistik ringkas -->
    @php
        $totalUpload   = $dokumens->count();
        $totalVerified = $dokumens->where('verified_by_admin', true)->count();
        $totalTolak    = $dokumens->filter(function($d) { return !$d->verified_by_admin && $d->verified_at; })->count();
        $totalTunggu   = $dokumens->filter(function($d) { return !$d->verified_at; })->count();
    @endphp
    <div class="row mb-4">
        @foreach ([
            ['val' => $totalUpload,   'lbl' => 'Total Upload',        'color' => 'primary'],
            ['val' => $totalTunggu,   'lbl' => 'Menunggu Verifikasi', 'color' => 'warning'],
            ['val' => $totalVerified, 'lbl' => 'Diterima',            'color' => 'success'],
            ['val' => $totalTolak,    'lbl' => 'Ditolak',             'color' => 'danger'],
        ] as $s)
        <div class="col-6 col-md-3 mb-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="font-size-h2 font-w700 text-{{ $s['color'] }}">{{ $s['val'] }}</div>
                    <div class="text-muted font-size-sm">{{ $s['lbl'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tabel dokumen -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Daftar Upload Peserta</h3>
        </div>
        <div class="block-content p-0">
            @if ($dokumens->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada peserta yang mengupload dokumen ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-vcenter mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px" class="text-center">No</th>
                                <th>Peserta</th>
                                <th class="text-center" style="width:100px">Waktu Upload</th>
                                <th class="text-center" style="width:100px">Status</th>
                                <th class="text-center" style="width:80px">File</th>
                                <th style="width:260px">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dokumens as $i => $d)
                            @php
                                $verified = $d->verified_by_admin;
                                $rejected = !$d->verified_by_admin && $d->verified_at;
                                $waiting  = !$d->verified_at;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-w600">{{ $d->peserta->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $d->peserta->nip }}</small><br>
                                    <small class="text-muted">{{ $d->peserta->instansi }}</small>
                                </td>
                                <td class="text-center font-size-sm">
                                    {{ \Carbon\Carbon::parse($d->uploaded_at)->format('d M Y') }}<br>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($d->uploaded_at)->format('H:i') }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($verified)
                                        <span class="badge badge-success">Diterima</span>
                                        <br><small class="text-muted">{{ $d->verified_by }}</small>
                                    @elseif ($rejected)
                                        <span class="badge badge-danger">Ditolak</span>
                                        <br><small class="text-muted">{{ $d->verified_by }}</small>
                                    @else
                                        <span class="badge badge-warning">Menunggu</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('backend.diklat.dokumen_peserta.file', $d->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Buka file">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    @if (!$verified)
                                    <form action="{{ route('backend.diklat.dokumen_peserta.verifikasi', $d->id) }}"
                                          method="POST"
                                          id="form-verif-{{ $d->id }}">
                                        @csrf
                                        <div class="input-group input-group-sm mb-1">
                                            <input type="text" name="catatan_admin"
                                                   class="form-control form-control-sm"
                                                   placeholder="Catatan (wajib jika tolak)"
                                                   value="{{ $d->catatan_admin }}"
                                                   maxlength="300">
                                        </div>
                                        <div class="d-flex" style="gap:.3rem">
                                            <button type="button"
                                                    class="btn btn-sm btn-success flex-fill"
                                                    onclick="
                                                        document.querySelector('#form-verif-{{ $d->id }} [name=aksi]').value='terima';
                                                        konfirmasiVerifikasi(document.getElementById('form-verif-{{ $d->id }}'), 'terima', '{{ addslashes($d->peserta->nama_lengkap) }}')
                                                    ">
                                                <i class="fa fa-check mr-1"></i> Terima
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger flex-fill"
                                                    onclick="
                                                        document.querySelector('#form-verif-{{ $d->id }} [name=aksi]').value='tolak';
                                                        konfirmasiVerifikasi(document.getElementById('form-verif-{{ $d->id }}'), 'tolak', '{{ addslashes($d->peserta->nama_lengkap) }}')
                                                    ">
                                                <i class="fa fa-times mr-1"></i> Tolak
                                            </button>
                                        </div>
                                        <input type="hidden" name="aksi" value="">
                                    </form>
                                    @else
                                        <div class="text-muted font-size-sm">
                                            <i class="fa fa-lock mr-1"></i>Sudah diverifikasi
                                        </div>
                                        @if ($d->catatan_admin)
                                            <small class="text-muted">{{ $d->catatan_admin }}</small>
                                        @endif
                                    @endif
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
@endsection
