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
            $.notify({ icon: 'fa fa-check mr-1', message: "{{ session('notifikasi') }}" }, {
                allow_dismiss: false, type: 'success',
                placement: { from: 'top', align: 'center' }
            });
        @endif

        function hapusSesi(id, nama) {
            Swal.fire({
                title: 'Hapus sesi?',
                text: '"' + nama + '" dan semua data presensinya akan dihapus.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, hapus'
            }).then(function (r) {
                if (r.value) document.getElementById('form-hapus-' + id).submit();
            });
        }
    </script>
@endsection

@section('content')

    {{-- ── Page Hero ─────────────────────────────────────────────── --}}
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div>
                    <h1 class="flex-sm-fill font-size-h3 font-w700 mt-2 mb-1">
                        <i class="fa fa-clipboard-check text-primary mr-2"></i>Presensi Peserta
                    </h1>
                    <p class="text-muted font-size-sm mb-0">
                        <i class="fa fa-graduation-cap mr-1"></i>{{ $jadwal->nama }}
                    </p>
                </div>
                <div class="d-flex flex-wrap mt-3 mt-sm-0" style="gap:.5rem">
                    <a href="{{ route('backend.diklat.presensi.rekap_all', $jadwal->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-table mr-1"></i>
                        <span class="d-none d-md-inline">Rekap </span>Semua
                    </a>
                    <a href="{{ route('backend.diklat.presensi.export_excel', $jadwal->id) }}"
                        class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel mr-1"></i>
                        <span class="d-none d-md-inline">Export </span>Excel
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fa fa-plus mr-1"></i>Tambah Sesi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-exclamation-circle mr-1"></i>{{ $errors->first() }}
            </div>
        @endif

        {{-- Info --}}
        <div class="block block-rounded block-bordered mb-4">
            <div class="block-content py-3">
                <div class="d-flex align-items-start" style="gap:.75rem">
                    <div class="item item-circle bg-info-lighter flex-shrink-0"
                        style="width:2.25rem;height:2.25rem;min-width:2.25rem">
                        <i class="fa fa-info-circle text-info" style="font-size:.85rem"></i>
                    </div>
                    <div class="font-size-sm text-muted">
                        Setiap sesi memiliki QR code unik yang hanya aktif selama sesi berlangsung.
                        Peserta scan QR → login SSO → presensi tercatat otomatis.
                        <span class="font-w600 text-body-color">
                            Total peserta aktif: {{ $totalPeserta }} orang.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Empty state ── --}}
        @if ($sesi->isEmpty())
            <div class="block block-rounded text-center py-6">
                <div class="item item-3x item-circle bg-body-light mx-auto mb-4">
                    <i class="fa fa-clipboard-list fa-2x text-muted"></i>
                </div>
                <h4 class="font-w400 text-muted mb-3">Belum ada sesi presensi</h4>
                <button type="button" class="btn btn-hero-primary" data-toggle="modal" data-target="#modalTambah">
                    <i class="fa fa-plus mr-2"></i>Tambah Sesi Pertama
                </button>
            </div>

        @else

                {{-- ── Stat ringkasan ── --}}
                @php
                    $totalJP = $sesi->sum('jp');
                    $sesiAktif = $sesi->filter(function ($s) {
                        return $s->isTokenValid();
                    })->count();
                @endphp
                <div class="row mb-4">
                    <div class="col-4">
                        <div class="block block-rounded mb-0 text-center">
                            <div class="block-content py-3">
                                <div class="font-w700 font-size-h3 text-primary">{{ $sesi->count() }}</div>
                                <div class="text-muted font-size-sm">Total Sesi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="block block-rounded mb-0 text-center">
                            <div class="block-content py-3">
                                <div class="font-w700 font-size-h3 text-warning">{{ $totalJP }}</div>
                                <div class="text-muted font-size-sm">Total JP</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="block block-rounded mb-0 text-center">
                            <div class="block-content py-3">
                                <div class="font-w700 font-size-h3 {{ $sesiAktif > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ $sesiAktif }}
                                </div>
                                <div class="text-muted font-size-sm">QR Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Tabel Desktop ── --}}
                <div class="block block-rounded d-none d-lg-block">
                    <div class="block-content block-content-full p-0">
                        <table class="table table-striped table-vcenter mb-0" style="font-size:.85rem">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:40px">No</th>
                                    <th>Materi / Sesi</th>
                                    <th class="text-center" style="width:100px">Tanggal</th>
                                    <th class="text-center" style="width:120px">Waktu</th>
                                    <th class="text-center" style="width:55px">JP</th>
                                    <th class="text-center" style="width:105px">Kehadiran</th>
                                    <th class="text-center" style="width:90px">QR</th>
                                    <th class="text-center" style="width:155px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sesi as $i => $s)
                                    @php
                                        $isAktif = $s->isTokenValid();
                                        $hadir = \App\PresensiPeserta::where('presensi_sesi_id', $s->id)
                                            ->whereIn('status', ['hadir', 'terlambat'])->count();
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted font-size-sm">{{ $i + 1 }}</td>
                                        <td>
                                            <div class="font-w600">{{ $s->nama_materi }}</div>
                                            @if ($s->widyaiswara)
                                                <div class="text-muted font-size-sm">
                                                    <i class="fa fa-chalkboard-teacher mr-1"></i>{{ $s->widyaiswara }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center font-size-sm">
                                            {{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}
                                        </td>
                                        <td class="text-center font-size-sm">
                                            <div>{{ substr($s->jam_mulai, 0, 5) }} – {{ substr($s->jam_selesai, 0, 5) }}</div>
                                            <div class="text-muted" style="font-size:.72rem">
                                                ±{{ $s->batas_terlambat }} mnt
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ $s->jp }} JP</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-w700 text-success">{{ $hadir }}</span>
                                            <span class="text-muted font-size-sm">/ {{ $totalPeserta }}</span>
                                            <div class="progress mt-1" style="height:3px">
                                                <div class="progress-bar bg-success"
                                                    style="width:{{ $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100) : 0 }}%">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($isAktif)
                                                <span class="badge badge-success">
                                                    <i class="fa fa-circle mr-1" style="font-size:.45rem;vertical-align:middle"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center" style="gap:.25rem">
                                                <a href="{{ route('backend.diklat.presensi.qr', $s->id) }}"
                                                    class="btn btn-sm btn-primary" title="Tampilkan QR" target="_blank">
                                                    <i class="fa fa-qrcode"></i>
                                                </a>
                                                <a href="{{ route('backend.diklat.presensi.rekap', $s->id) }}"
                                                    class="btn btn-sm btn-info" title="Rekap sesi ini">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#modalEdit{{ $s->id }}" title="Edit">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                <form id="form-hapus-{{ $s->id }}"
                                                    action="{{ route('backend.diklat.presensi.destroy', $s->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="hapusSesi({{ $s->id }}, '{{ addslashes($s->nama_materi) }}')"
                                                    title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-body-light">
                                <tr>
                                    <td colspan="4" class="text-right font-w600 font-size-sm">Total JP:</td>
                                    <td class="text-center font-w700 text-warning">{{ $totalJP }} JP</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ── Card Mobile (< lg) ── --}} <div class="d-lg-none">
                    @foreach ($sesi as $i => $s)
                        @php
                            $isAktif = $s->isTokenValid();
                            $hadir = \App\PresensiPeserta::where('presensi_sesi_id', $s->id)
                                ->whereIn('status', ['hadir', 'terlambat'])->count();
                            $pct = $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100) : 0;
                        @endphp
                        <div class="block block-rounded block-bordered mb-3">

                            {{-- Header kartu --}}
                            <div class="block-header block-header-default">
                                <div style="min-width:0;flex:1">
                                    <h3 class="block-title font-w700 text-wrap-break-word">
                                        {{ $s->nama_materi }}
                                    </h3>
                                    @if ($s->widyaiswara)
                                        <div class="text-muted font-size-sm">
                                            <i class="fa fa-chalkboard-teacher mr-1"></i>{{ $s->widyaiswara }}
                                        </div>
                                    @endif
                                </div>
                                <div class="block-options">
                                    @if ($isAktif)
                                        <span class="badge badge-success">
                                            <i class="fa fa-circle mr-1" style="font-size:.45rem;vertical-align:middle"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail info --}}
                            <div class="block-content py-3">
                                <div class="row text-center mb-3 no-gutters">
                                    <div class="col-4 border-right">
                                        <div class="font-size-sm text-muted">Tanggal</div>
                                        <div class="font-w600 font-size-sm">
                                            {{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-4 border-right">
                                        <div class="font-size-sm text-muted">Waktu</div>
                                        <div class="font-w600 font-size-sm">
                                            {{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }}
                                        </div>
                                        <div class="text-muted" style="font-size:.68rem">±{{ $s->batas_terlambat }} mnt</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="font-size-sm text-muted">JP</div>
                                        <div class="font-w700 text-warning">{{ $s->jp }} JP</div>
                                    </div>
                                </div>

                                {{-- Kehadiran --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-size-sm text-muted">Kehadiran</span>
                                    <span class="font-size-sm">
                                        <strong class="text-success">{{ $hadir }}</strong>
                                        <span class="text-muted">/ {{ $totalPeserta }}</span>
                                        <span class="text-muted ml-1">({{ $pct }}%)</span>
                                    </span>
                                </div>
                                <div class="progress mb-0" style="height:5px">
                                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>

                            {{-- Aksi --}}
                            <div class="block-content block-content-sm bg-body-light py-2">
                                <div class="d-flex flex-wrap" style="gap:.35rem">
                                    <a href="{{ route('backend.diklat.presensi.qr', $s->id) }}" class="btn btn-sm btn-primary"
                                        target="_blank">
                                        <i class="fa fa-qrcode mr-1"></i>QR
                                    </a>
                                    <a href="{{ route('backend.diklat.presensi.rekap', $s->id) }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-list mr-1"></i>Rekap
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#modalEdit{{ $s->id }}">
                                        <i class="fa fa-pencil-alt mr-1"></i>Edit
                                    </button>
                                    <form id="form-hapus-{{ $s->id }}" action="{{ route('backend.diklat.presensi.destroy', $s->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="hapusSesi({{ $s->id }}, '{{ addslashes($s->nama_materi) }}')">
                                        <i class="fa fa-trash mr-1"></i>Hapus
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach

                    {{-- Total JP footer mobile --}}
                    <div class="block block-rounded mb-0">
                        <div class="block-content py-3 d-flex justify-content-between align-items-center">
                            <span class="font-w600 text-muted font-size-sm">Total JP semua sesi</span>
                            <span class="font-w700 text-warning font-size-h4">{{ $totalJP }} JP</span>
                        </div>
                    </div>
            </div>

        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════
    MODAL TAMBAH SESI
    ═══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-w700">
                        <i class="fa fa-plus-circle text-primary mr-2"></i>Tambah Sesi Presensi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('backend.diklat.presensi.store', $jadwal->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @include('backend.diklat.presensi._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>Simpan &amp; Generate QR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
    MODAL EDIT PER SESI
    ═══════════════════════════════════════════════════════════ --}}
    @foreach ($sesi as $editSesi)
        <div class="modal fade" id="modalEdit{{ $editSesi->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-w700">
                            <i class="fa fa-pencil-alt text-warning mr-2"></i>Edit Sesi
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ route('backend.diklat.presensi.update', $editSesi->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-body">
                            @include('backend.diklat.presensi._form', ['itemSesi' => $editSesi])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-save mr-1"></i>Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

@endsection