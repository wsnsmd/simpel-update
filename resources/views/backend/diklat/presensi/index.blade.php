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
    $.notify({ icon: "fa fa-check mr-1", message: "{{ session('notifikasi') }}" }, {
        allow_dismiss: false, type: 'success',
        placement: { from: "top", align: "center" }
    });
    @endif

    function hapusSesi(id, nama) {
        Swal.fire({
            title: 'Hapus sesi?',
            text: '"' + nama + '" dan semua data presensinya akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus'
        }).then(function(r) {
            if (r.value) document.getElementById('form-hapus-' + id).submit();
        });
    }
</script>
@endsection

@section('content')
<div class="content">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-4">
        <div>
            <h2 class="h3 my-2">Presensi Peserta</h2>
            <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
        </div>
        <div class="d-flex mt-2 mt-sm-0" style="gap:.5rem">
            <a href="{{ route('backend.diklat.presensi.rekap_all', $jadwal->id) }}"
               class="btn btn-info btn-sm">
                <i class="fa fa-table mr-1"></i> Rekap Semua
            </a>
            <a href="{{ route('backend.diklat.presensi.export_excel', $jadwal->id) }}"
               class="btn btn-success btn-sm">
                <i class="fa fa-file-excel mr-1"></i> Export Excel
            </a>
            <button type="button" class="btn btn-primary btn-sm"
                    data-toggle="modal" data-target="#modalTambah">
                <i class="fa fa-plus mr-1"></i> Tambah Sesi
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="alert alert-info font-size-sm">
        <i class="fa fa-info-circle mr-2"></i>
        Setiap sesi memiliki QR code unik yang hanya aktif selama sesi berlangsung.
        Peserta scan QR → login SSO → presensi tercatat otomatis.
        Total peserta aktif: <strong>{{ $totalPeserta }} orang</strong>.
    </div>

    @if ($sesi->isEmpty())
        <div class="block block-rounded">
            <div class="block-content text-center py-5">
                <i class="fa fa-clipboard-list fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada sesi presensi. Tambahkan sesi pertama.</p>
            </div>
        </div>
    @else
        <div class="block block-rounded">
            <div class="block-content p-0">
                <table class="table table-striped table-vcenter mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:40px" class="text-center">No</th>
                            <th>Materi / Sesi</th>
                            <th class="text-center" style="width:90px">Tanggal</th>
                            <th class="text-center" style="width:120px">Waktu</th>
                            <th class="text-center" style="width:50px">JP</th>
                            <th class="text-center" style="width:100px">Kehadiran</th>
                            <th class="text-center" style="width:80px">Status QR</th>
                            <th class="text-center" style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sesi as $i => $s)
                        @php
                            $isAktif = $s->isTokenValid();
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="font-w600">{{ $s->nama_materi }}</div>
                                @if ($s->widyaiswara)
                                    <small class="text-muted">
                                        <i class="fa fa-chalkboard-teacher mr-1"></i>{{ $s->widyaiswara }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center font-size-sm">
                                {{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}
                            </td>
                            <td class="text-center font-size-sm">
                                {{ substr($s->jam_mulai, 0, 5) }} – {{ substr($s->jam_selesai, 0, 5) }}
                                <br><small class="text-muted">±{{ $s->batas_terlambat }} mnt</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $s->jp }} JP</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $hadir = \App\PresensiPeserta::where('presensi_sesi_id', $s->id)
                                        ->whereIn('status', ['hadir', 'terlambat'])->count();
                                @endphp
                                <span class="font-w600 text-success">{{ $hadir }}</span>
                                <span class="text-muted">/ {{ $totalPeserta }}</span>
                            </td>
                            <td class="text-center">
                                @if ($isAktif)
                                    <span class="badge badge-success">
                                        <i class="fa fa-qrcode mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('backend.diklat.presensi.qr', $s->id) }}"
                                   class="btn btn-sm btn-primary" title="Tampilkan QR" target="_blank">
                                    <i class="fa fa-qrcode"></i>
                                </a>
                                <a href="{{ route('backend.diklat.presensi.rekap', $s->id) }}"
                                   class="btn btn-sm btn-info" title="Rekap sesi ini">
                                    <i class="fa fa-list"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-warning"
                                        data-toggle="modal"
                                        data-target="#modalEdit{{ $s->id }}"
                                        title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                                <form id="form-hapus-{{ $s->id }}"
                                      action="{{ route('backend.diklat.presensi.destroy', $s->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="hapusSesi({{ $s->id }}, '{{ addslashes($s->nama_materi) }}')"
                                        title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td colspan="4" class="text-right font-w600">Total JP:</td>
                            <td class="text-center font-w600">{{ $sesi->sum('jp') }} JP</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Modal Tambah Sesi --}}
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w700">
                    <i class="fa fa-plus-circle text-primary mr-2"></i> Tambah Sesi Presensi
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
                        <i class="fa fa-save mr-1"></i> Simpan & Generate QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit per sesi --}}
@foreach ($sesi as $editSesi)
<div class="modal fade" id="modalEdit{{ $editSesi->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w700">
                    <i class="fa fa-edit text-warning mr-2"></i> Edit Sesi
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
                        <i class="fa fa-save mr-1"></i> Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
