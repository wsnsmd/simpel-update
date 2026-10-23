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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        @if (session('notifikasi'))
            $.notify({
                icon: "fa fa-check mr-1",
                message: "{{ session('notifikasi') }}"
            }, {
                allow_dismiss: false, type: 'success',
                placement: { from: "top", align: "center" }
            });
        @endif

        // Hapus syarat
        function hapusSyarat(id, nama) {
            Swal.fire({
                title: 'Hapus syarat?',
                text: '"' + nama + '" dan semua file yang sudah diupload peserta akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, hapus'
            }).then(function (result) {
                if (result.value) {
                    document.getElementById('form-hapus-' + id).submit();
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="content">
        <!-- Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-4">
            <div>
                <h2 class="flex-sm-fill h3 my-2">Persyaratan Dokumen</h2>
                <h2 class="flex-sm-fill h5 my-2 text-muted font-w400 font-size-sm">{{ $jadwal->nama }}</h2>
            </div>
            <div class="flex-sm-00-auto ml-sm-3 d-flex" style="gap:.5rem">
                <a href="{{ route('backend.diklat.dokumen_syarat.download_all', $jadwal->id) }}"
                    class="btn btn-sm btn-success">
                    <i class="fa fa-file-archive mr-1"></i> Download Semua
                </a>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambah">
                    <i class="fa fa-plus mr-1"></i> Tambah Syarat
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Info -->
        <div class="alert alert-info">
            <i class="fa fa-info-circle mr-2"></i>
            Dokumen di bawah ini akan diminta kepada peserta saat mendaftar dan dapat diunggah ulang dari dashboard peserta.
            Syarat bertanda <span class="badge badge-danger">Wajib</span> harus dipenuhi sebelum admin memverifikasi
            pendaftaran.
        </div>

        <!-- Daftar syarat -->
        @if ($daftarSyarat->isEmpty())
            <div class="block block-rounded">
                <div class="block-content text-center py-5">
                    <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada persyaratan dokumen untuk pelatihan ini.</p>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fa fa-plus mr-1"></i> Tambah Syarat Pertama
                    </button>
                </div>
            </div>
        @else
            <div class="block block-rounded">
                <div class="block-content p-0">
                    <table class="table table-striped table-vcenter mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:40px" class="text-center">No</th>
                                <th>Nama Dokumen</th>
                                <th>Keterangan</th>
                                <th class="text-center" style="width:120px">Format</th>
                                <th class="text-center" style="width:80px">Maks</th>
                                <th class="text-center" style="width:80px">Status</th>
                                <th class="text-center" style="width:80px">Upload</th>
                                <th class="text-center" style="width:120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarSyarat as $i => $s)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <span class="font-w600">{{ $s->nama }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted font-size-sm">
                                            {{ $s->keterangan ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <code class="font-size-sm">{{ strtoupper(str_replace(',', ', ', $s->format_izin)) }}</code>
                                    </td>
                                    <td class="text-center font-size-sm">
                                        {{ round($s->max_size_kb / 1024, 1) }} MB
                                    </td>
                                    <td class="text-center">
                                        @if ($s->wajib)
                                            <span class="badge badge-danger">Wajib</span>
                                        @else
                                            <span class="badge badge-secondary">Opsional</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('backend.diklat.dokumen_syarat.list_upload', $s->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Lihat upload">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('backend.diklat.dokumen_syarat.download_by_syarat', $s->id) }}"
                                            class="btn btn-sm btn-outline-success" title="Download ZIP">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                            data-target="#modalEdit{{ $s->id }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        <form id="form-hapus-{{ $s->id }}"
                                            action="{{ route('backend.diklat.dokumen_syarat.destroy', $s->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="hapusSyarat({{ $s->id }}, '{{ addslashes($s->nama) }}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Tambah Syarat (di luar semua loop) --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-w700">
                        <i class="fa fa-plus-circle text-primary mr-2"></i> Tambah Syarat Dokumen
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('backend.diklat.dokumen_syarat.store', $jadwal->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @include('backend.diklat.dokumen_syarat._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Edit per syarat --}}
    {{-- ============================================================ --}}
    @foreach ($daftarSyarat as $editSyarat)
        <div class="modal fade" id="modalEdit{{ $editSyarat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-w700">
                            <i class="fa fa-edit text-warning mr-2"></i> Edit Syarat Dokumen
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ route('backend.diklat.dokumen_syarat.update', $editSyarat->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            @include('backend.diklat.dokumen_syarat._form', ['itemSyarat' => $editSyarat])
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