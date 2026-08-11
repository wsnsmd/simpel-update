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

        function hapusKomponen(id, nama) {
            Swal.fire({
                title: 'Hapus komponen?',
                text: '"' + nama + '" dan semua sub-komponennya akan dihapus.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then(function (r) {
                if (r.value) document.getElementById('form-hapus-' + id).submit();
            });
        }
    </script>
@endsection

@section('content')

    {{-- ── Page Hero ───────────────────────────────────────────────── --}}
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div>
                    <h1 class="flex-sm-fill font-size-h3 font-w700 mt-2 mb-1">
                        <i class="fa fa-layer-group text-primary mr-2"></i>Komponen Nilai
                    </h1>
                    <p class="text-muted font-size-sm mb-0">
                        Template: <strong class="text-body-color">{{ $template->nama }}</strong>
                        @if ($template->tahun)
                            <span class="badge badge-secondary ml-1">{{ $template->tahun }}</span>
                        @endif
                        <span class="mx-1 text-muted">&bull;</span>
                        Passing grade aspek:
                        <strong class="text-primary">&gt; {{ $template->passing_grade_aspek }}</strong>
                    </p>
                </div>
                <div class="d-flex mt-3 mt-sm-0" style="gap:.5rem">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahAspek">
                        <i class="fa fa-plus mr-1"></i>
                        <span>Tambah Aspek</span>
                    </button>
                    <a href="{{ route('backend.diklat.nilai.template') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left mr-1"></i>
                        <span class="d-none d-sm-inline">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- ── Status Bobot + Error ────────────────────────────────── --}}
        @php $totalBobot = $aspekList->sum('bobot'); @endphp

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <div class="row mb-4">
            {{-- Status bobot total --}}
            <div class="col-md-8">
                @if (abs($totalBobot - 1) >= 0.001)
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        Total bobot saat ini <strong>{{ round($totalBobot * 100, 2) }}%</strong> —
                        harus tepat <strong>100%</strong> agar kalkulasi nilai benar.
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="fa fa-check-circle mr-1"></i>
                        Total bobot <strong>100%</strong> — template siap digunakan.
                    </div>
                @endif
            </div>

            {{-- Ringkasan angka --}}
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-content py-3 d-flex align-items-center justify-content-between">
                        <div class="text-center flex-fill border-right">
                            <div
                                class="font-w700 font-size-h3 {{ abs($totalBobot - 1) < 0.001 ? 'text-success' : 'text-danger' }}">
                                {{ round($totalBobot * 100, 2) }}%
                            </div>
                            <div class="font-size-sm text-muted">Total Bobot</div>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="font-w700 font-size-h3 text-primary">
                                {{ $aspekList->count() }}
                            </div>
                            <div class="font-size-sm text-muted">Aspek</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Panduan --}}
        <div class="block block-rounded block-bordered mb-4">
            <div class="block-content py-3">
                <div class="d-flex align-items-start">
                    <div class="item item-circle item-rounded bg-info-lighter mr-3 flex-shrink-0"
                        style="width:2.25rem;height:2.25rem;min-width:2.25rem">
                        <i class="fa fa-info-circle text-info" style="font-size:.85rem"></i>
                    </div>
                    <div class="font-size-sm text-muted">
                        Tambah <strong class="text-body-color">Aspek</strong> (induk, misal "Evaluasi Akademik 10%"),
                        lalu tambah <strong class="text-body-color">Sub-komponen</strong> di dalam setiap aspek.
                        Penilai dan fase sub-komponen mengikuti aspek induknya.
                        <span class="d-none d-md-inline">
                            Operator dapat menginput <em>semua</em> komponen;
                            penguji hanya komponen ber-fase rancangan/akhir.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Daftar Aspek ────────────────────────────────────────── --}}
        @forelse ($aspekList as $loop_index => $aspek)

            <div class="block block-rounded block-bordered mb-3">

                {{-- Header Aspek --}}
                <div class="block-header block-header-default">
                    <div class="d-flex align-items-center flex-wrap" style="gap:.4rem">

                        {{-- Nomor urut --}}
                        <div class="item item-circle bg-primary mr-1 flex-shrink-0"
                            style="width:1.75rem;height:1.75rem;min-width:1.75rem;font-size:.75rem;font-weight:700;color:#fff">
                            {{ $loop_index + 1 }}
                        </div>

                        <h3 class="block-title font-w700">{{ $aspek->nama }}</h3>

                        {{-- Badge bobot --}}
                        <span class="badge badge-primary">{{ round($aspek->bobot * 100) }}%</span>

                        {{-- Badge penilai --}}
                        @if ($aspek->penilai === 'penguji')
                            <span class="badge badge-warning">
                                <i class="fa fa-user-tie mr-1"></i>Penguji
                            </span>
                        @else
                            <span class="badge badge-info">
                                <i class="fa fa-user-cog mr-1"></i>Operator
                            </span>
                        @endif

                        {{-- Badge fase --}}
                        @if ($aspek->fase)
                            <span class="badge badge-success">
                                <i class="fa fa-flag mr-1"></i>{{ ucfirst($aspek->fase) }}
                            </span>
                        @endif

                        {{-- Keterangan --}}
                        @if ($aspek->keterangan)
                            <small class="text-muted d-none d-md-inline ml-1">
                                <i class="fa fa-comment-alt mr-1"></i>{{ $aspek->keterangan }}
                            </small>
                        @endif
                    </div>

                    {{-- Aksi Header --}}
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="modal"
                            data-target="#modalTambahSub{{ $aspek->id }}" title="Tambah sub-komponen">
                            <i class="fa fa-plus text-success"></i>
                        </button>
                        <button type="button" class="btn-block-option" data-toggle="modal"
                            data-target="#modalEditAspek{{ $aspek->id }}" title="Edit aspek">
                            <i class="fa fa-pencil-alt text-warning"></i>
                        </button>
                        <form id="form-hapus-{{ $aspek->id }}"
                            action="{{ route('backend.diklat.nilai.komponen.destroy', $aspek->id) }}" method="POST"
                            class="d-inline">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn-block-option"
                            onclick="hapusKomponen({{ $aspek->id }}, '{{ addslashes($aspek->nama) }}')" title="Hapus aspek">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </div>
                </div>

                {{-- Sub-komponen --}}
                @if ($aspek->sub->count() > 0)
                    <div class="block-content block-content-full p-0">
                        <table class="table table-vcenter table-striped mb-0" style="font-size:.82rem">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:40px">#</th>
                                    <th>Sub-komponen</th>
                                    <th class="text-center" style="width:80px">Bobot</th>
                                    <th class="d-none d-md-table-cell" style="width:220px">Keterangan</th>
                                    <th class="text-center" style="width:90px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aspek->sub as $j => $s)
                                    <tr>
                                        <td class="text-center text-muted font-size-sm">{{ $j + 1 }}</td>
                                        <td class="font-w600">
                                            {{ $s->nama }}
                                            @if ($s->keterangan)
                                                <div class="d-md-none text-muted font-w400 font-size-sm">
                                                    {{ $s->keterangan }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ round($s->bobot * 100, 1) }}%</span>
                                        </td>
                                        <td class="d-none d-md-table-cell text-muted font-size-sm">
                                            {{ $s->keterangan ?: '—' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center" style="gap:.25rem">
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                                    data-target="#modalEditSub{{ $s->id }}" title="Edit">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                <form id="form-hapus-{{ $s->id }}"
                                                    action="{{ route('backend.diklat.nilai.komponen.destroy', $s->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="hapusKomponen({{ $s->id }}, '{{ addslashes($s->nama) }}')" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Sub-total footer --}}
                    @php $subTotal = $aspek->sub->sum('bobot'); @endphp
                    <div class="block-content py-2 bg-body-light d-flex justify-content-between align-items-center">
                        <span class="font-size-sm text-muted">
                            <i class="fa fa-calculator mr-1"></i>
                            Total bobot sub-komponen
                        </span>
                        <div class="d-flex align-items-center" style="gap:.5rem">
                            <strong
                                class="{{ abs($subTotal - $aspek->bobot) >= 0.001 ? 'text-danger' : 'text-success' }} font-size-sm">
                                {{ round($subTotal * 100, 2) }}%
                            </strong>
                            @if (abs($subTotal - $aspek->bobot) >= 0.001)
                                <span class="badge badge-danger">
                                    <i class="fa fa-exclamation-triangle mr-1"></i>
                                    Harusnya {{ round($aspek->bobot * 100) }}%
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="fa fa-check"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                @else
                    {{-- Empty state sub --}}
                    <div class="block-content py-4 text-center">
                        <i class="fa fa-inbox text-muted fa-2x mb-2"></i>
                        <p class="text-muted font-size-sm mb-2">Belum ada sub-komponen.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                            data-target="#modalTambahSub{{ $aspek->id }}">
                            <i class="fa fa-plus mr-1"></i> Tambah Sub-komponen
                        </button>
                    </div>
                @endif

            </div>

            {{-- ── Modal Edit Aspek ──────────────────────────────── --}}
            <div class="modal fade" id="modalEditAspek{{ $aspek->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-w700">
                                <i class="fa fa-pencil-alt text-warning mr-2"></i>Edit Aspek
                            </h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="{{ route('backend.diklat.nilai.komponen.update', $aspek->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="modal-body">
                                @include('backend.diklat.nilai._form_aspek', ['item' => $aspek])
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

            {{-- ── Modal Tambah Sub ──────────────────────────────── --}}
            <div class="modal fade" id="modalTambahSub{{ $aspek->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-w700">
                                <i class="fa fa-plus-circle text-primary mr-2"></i>Tambah Sub-komponen
                                <small class="text-muted font-w400 d-block font-size-sm mt-1">{{ $aspek->nama }}</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="{{ route('backend.diklat.nilai.komponen.store.sub', [$template->id, $aspek->id]) }}"
                            method="POST">
                            @csrf
                            <div class="modal-body">
                                @include('backend.diklat.nilai._form_sub')
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

            {{-- ── Modal Edit Sub (loop) ─────────────────────────── --}}
            @foreach ($aspek->sub as $s)
                <div class="modal fade" id="modalEditSub{{ $s->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-w700">
                                    <i class="fa fa-pencil-alt text-warning mr-2"></i>Edit Sub-komponen
                                </h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <form action="{{ route('backend.diklat.nilai.komponen.update', $s->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="modal-body">
                                    @include('backend.diklat.nilai._form_sub', ['item' => $s])
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

        @empty

            {{-- ── Empty State ──────────────────────────────────────── --}}
            <div class="block block-rounded text-center py-6">
                <div class="item item-3x item-circle bg-body-light mx-auto mb-4">
                    <i class="fa fa-th-list fa-2x text-muted"></i>
                </div>
                <h4 class="font-w400 text-muted mb-2">Belum ada aspek penilaian</h4>
                <p class="text-muted font-size-sm mb-4">
                    Mulai dengan menambah aspek pertama untuk template ini.
                </p>
                <button type="button" class="btn btn-hero-primary" data-toggle="modal" data-target="#modalTambahAspek">
                    <i class="fa fa-plus mr-2"></i> Tambah Aspek Pertama
                </button>
            </div>

        @endforelse

        {{-- ── Footer Total Bobot ───────────────────────────────── --}}
        @if ($aspekList->count() > 0)
            <div class="block block-rounded mb-3">
                <div class="block-content py-3 d-flex justify-content-between align-items-center">
                    <span class="font-w600 text-muted">
                        <i class="fa fa-sigma mr-1"></i> Total Bobot Semua Aspek
                    </span>
                    <span class="font-w700 font-size-h4 {{ abs($totalBobot - 1) < 0.001 ? 'text-success' : 'text-danger' }}">
                        {{ round($totalBobot * 100, 2) }}%
                        @if (abs($totalBobot - 1) < 0.001)
                            <i class="fa fa-check-circle ml-1 font-size-base"></i>
                        @else
                            <i class="fa fa-exclamation-circle ml-1 font-size-base"></i>
                        @endif
                    </span>
                </div>
            </div>
        @endif

    </div>

    {{-- ── Modal Tambah Aspek ──────────────────────────────────────── --}}
    <div class="modal fade" id="modalTambahAspek" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-w700">
                        <i class="fa fa-plus-circle text-primary mr-2"></i> Tambah Aspek Penilaian
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('backend.diklat.nilai.komponen.store.aspek', $template->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @include('backend.diklat.nilai._form_aspek')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i> Simpan Aspek
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection