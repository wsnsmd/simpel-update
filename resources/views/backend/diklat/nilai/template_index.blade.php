@extends('layouts.backend')

@section('css_before')
<link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
<script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script>
@if (session('notifikasi'))
$.notify({ icon: 'fa fa-check mr-1', message: "{{ session('notifikasi') }}" }, {
    allow_dismiss: false, type: 'success', placement: { from: 'top', align: 'center' }
});
@endif

function hapusTemplate(id, nama) {
    Swal.fire({
        title: 'Hapus template?',
        text: '"' + nama + '" akan dihapus beserta semua komponennya.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
    }).then(function(r) {
        if (r.value) document.getElementById('form-hapus-' + id).submit();
    });
}

function cloneTemplate(id, nama) {
    Swal.fire({
        title: 'Duplikasi template?',
        text: '"' + nama + '" akan disalin sebagai template baru.',
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, duplikasi',
        cancelButtonText: 'Batal',
    }).then(function(r) {
        if (r.value) document.getElementById('form-clone-' + id).submit();
    });
}
</script>
@endsection

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap:.5rem">
        <div>
            <h2 class="h3 my-2">Template Penilaian</h2>
            <p class="text-muted mb-0 font-size-sm">
                Kelola template komponen nilai yang dapat digunakan di berbagai jadwal pelatihan.
            </p>
        </div>
        <button type="button" class="btn btn-primary btn-sm"
                data-toggle="modal" data-target="#modalTambah">
            <i class="fa fa-plus mr-1"></i> Buat Template Baru
        </button>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ $errors->first() }}
    </div>
    @endif

    @if (count($templates) === 0)
    <div class="block block-rounded text-center py-5">
        <i class="fa fa-clipboard-list fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-2">Belum ada template penilaian.</p>
        <button type="button" class="btn btn-primary btn-sm"
                data-toggle="modal" data-target="#modalTambah">
            <i class="fa fa-plus mr-1"></i> Buat Template Pertama
        </button>
    </div>
    @else
    <div class="block block-rounded">
        <div class="block-content p-0">
            <table class="table table-striped table-vcenter mb-0" style="font-size:.85rem">
                <thead class="thead-light">
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th>Nama Template</th>
                        <th class="text-center" style="width:100px">Tahun</th>
                        <th class="text-center" style="width:80px">Mode</th>
                        <th class="text-center" style="width:80px">PG Aspek</th>
                        <th class="text-center" style="width:80px">Aspek</th>
                        <th class="text-center" style="width:80px">Dipakai</th>
                        <th class="text-center" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $i => $t)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="font-w600">{{ $t->nama }}</div>
                            @if ($t->jenis_nama)
                            <small class="text-muted">{{ $t->jenis_nama }}</small>
                            @endif
                            @if ($t->deskripsi)
                            <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($t->deskripsi, 60) }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $t->tahun ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-secondary" style="font-size:.7rem">{{ $t->mode }}</span>
                        </td>
                        <td class="text-center font-w600">&gt; {{ $t->passing_grade_aspek }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $t->jumlah_aspek }} aspek</span>
                        </td>
                        <td class="text-center">
                            @if ($t->dipakai > 0)
                            <span class="badge badge-success">{{ $t->dipakai }} jadwal</span>
                            @else
                            <span class="text-muted font-size-sm">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('backend.diklat.nilai.template.komponen', $t->id) }}"
                               class="btn btn-sm btn-primary" title="Kelola Komponen">
                                <i class="fa fa-list"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-warning"
                                    data-toggle="modal"
                                    data-target="#modalEdit{{ $t->id }}"
                                    title="Edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>
                            <form id="form-clone-{{ $t->id }}"
                                  action="{{ route('backend.diklat.nilai.template.clone', $t->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-info"
                                        onclick="cloneTemplate({{ $t->id }}, '{{ addslashes($t->nama) }}')"
                                        title="Duplikasi">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </form>
                            <form id="form-hapus-{{ $t->id }}"
                                  action="{{ route('backend.diklat.nilai.template.destroy', $t->id) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger"
                                        onclick="hapusTemplate({{ $t->id }}, '{{ addslashes($t->nama) }}')"
                                        title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w700">
                    <i class="fa fa-plus-circle text-primary mr-2"></i> Buat Template Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('backend.diklat.nilai.template.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('backend.diklat.nilai._form_template')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save mr-1"></i> Simpan & Definisikan Komponen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit per template --}}
@foreach ($templates as $t)
<div class="modal fade" id="modalEdit{{ $t->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w700">
                    <i class="fa fa-edit text-warning mr-2"></i> Edit Template
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('backend.diklat.nilai.template.update', $t->id) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    @include('backend.diklat.nilai._form_template', ['item' => $t])
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
