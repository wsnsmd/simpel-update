@extends('layouts.backend')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">

    <style>
        .aspek-wrap {
            border: 1px solid #e4e9f0;
            border-radius: 10px;
            margin-bottom: 1rem;
            overflow: hidden
        }

        .aspek-head {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            padding: .7rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #bfdbfe
        }

        .aspek-title {
            font-weight: 700;
            color: #1e40af;
            font-size: .9rem
        }

        .aspek-meta {
            font-size: .72rem;
            color: #3b82f6;
            margin-top: 2px
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .82rem
        }

        .sub-table td,
        .sub-table th {
            padding: .45rem .875rem;
            border-bottom: 1px solid #f1f5f9
        }

        .sub-table th {
            background: #f8faff;
            font-weight: 600;
            font-size: .75rem;
            color: #475569
        }

        .sub-table tr:last-child td {
            border-bottom: none
        }

        .badge-penguji {
            background: #fef3c7;
            color: #92400e
        }

        .badge-operator {
            background: #dbeafe;
            color: #1e40af
        }

        .badge-fase {
            background: #d1fae5;
            color: #065f46
        }

        .total-bar {
            background: #f8faff;
            padding: .65rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #e4e9f0
        }
    </style>
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
    <div class="content">

        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap:.5rem">
            <div>
                <h2 class="h3 my-2">Komponen Nilai</h2>
                <p class="text-muted mb-0 font-size-sm">
                    Template: <strong>{{ $template->nama }}</strong>
                    @if ($template->tahun) ({{ $template->tahun }}) @endif
                    &bull; PG Aspek: <strong>&gt; {{ $template->passing_grade_aspek }}</strong>
                </p>
            </div>
            <div class="d-flex" style="gap:.5rem">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahAspek">
                    <i class="fa fa-plus mr-1"></i> Tambah Aspek
                </button>
                <a href="{{ route('backend.diklat.nilai.template') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Validasi total bobot --}}
        @php $totalBobot = $aspekList->sum('bobot'); @endphp
        @if (abs($totalBobot - 1) >= 0.001)
            <div class="alert alert-warning font-size-sm">
                <i class="fa fa-exclamation-triangle mr-1"></i>
                Total bobot saat ini <strong>{{ round($totalBobot * 100, 2) }}%</strong> —
                harus tepat <strong>100%</strong> agar kalkulasi nilai benar.
            </div>
        @else
            <div class="alert alert-success font-size-sm">
                <i class="fa fa-check-circle mr-1"></i>
                Total bobot <strong>100%</strong> — template siap digunakan.
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- Panduan --}}
        <div class="alert alert-info font-size-sm mb-4">
            <i class="fa fa-info-circle mr-1"></i>
            <strong>Cara membuat komponen:</strong>
            Tambah <strong>Aspek</strong> (induk, misal "Evaluasi Akademik 10%"),
            lalu tambah <strong>Sub-komponen</strong> di dalam setiap aspek (misal "Analisis Kepemimpinan 4%").
            Penilai dan fase sub-komponen mengikuti aspek induknya.
            Operator dapat menginput <em>semua</em> komponen; penguji hanya komponen ber-fase rancangan/akhir.
        </div>

        {{-- Daftar aspek --}}
        @forelse ($aspekList as $aspek)
            <div class="aspek-wrap">
                {{-- Header aspek --}}
                <div class="aspek-head">
                    <div>
                        <div class="aspek-title">
                            {{ $aspek->nama }}
                            <span class="badge badge-primary ml-1">{{ round($aspek->bobot * 100) }}%</span>
                            <span class="badge bdg-{{ $aspek->penilai === 'penguji' ? 'penguji' : 'operator' }} ml-1">
                                {{ $aspek->penilai }}
                            </span>
                            @if ($aspek->fase)
                                <span class="badge badge-fase ml-1">{{ $aspek->fase }}</span>
                            @endif
                        </div>
                        @if ($aspek->keterangan)
                            <div class="aspek-meta">{{ $aspek->keterangan }}</div>
                        @endif
                    </div>
                    <div class="d-flex" style="gap:.3rem">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                            data-target="#modalTambahSub{{ $aspek->id }}" title="Tambah sub-komponen">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal"
                            data-target="#modalEditAspek{{ $aspek->id }}" title="Edit aspek">
                            <i class="fa fa-pencil-alt"></i>
                        </button>
                        <form id="form-hapus-{{ $aspek->id }}"
                            action="{{ route('backend.diklat.nilai.komponen.destroy', $aspek->id) }}" method="POST"
                            class="d-inline">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="hapusKomponen({{ $aspek->id }}, '{{ addslashes($aspek->nama) }}')" title="Hapus aspek">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>

                {{-- Sub-komponen --}}
                @if ($aspek->sub->count() > 0)
                    <table class="sub-table">
                        <thead>
                            <tr>
                                <th style="width:35px" class="text-center">#</th>
                                <th>Sub-komponen</th>
                                <th style="width:80px" class="text-center">Bobot</th>
                                <th style="width:200px">Keterangan / Rubrik</th>
                                <th style="width:100px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aspek->sub as $j => $s)
                                <tr>
                                    <td class="text-center text-muted">{{ $j + 1 }}</td>
                                    <td class="font-w600">{{ $s->nama }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ round($s->bobot * 100, 1) }}%</span>
                                    </td>
                                    <td class="text-muted font-size-sm">
                                        {{ $s->keterangan ?: '—' }}
                                    </td>
                                    <td class="text-center">
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-3 py-3 text-muted font-size-sm">
                        <i class="fa fa-info-circle mr-1"></i>
                        Belum ada sub-komponen.
                        <a href="#" data-toggle="modal" data-target="#modalTambahSub{{ $aspek->id }}">
                            Tambah sekarang
                        </a>
                    </div>
                @endif

                {{-- Sub-total bobot sub-komponen --}}
                @if ($aspek->sub->count() > 0)
                    @php $subTotal = $aspek->sub->sum('bobot'); @endphp
                    <div style="padding:.3rem .875rem;background:#f8faff;border-top:1px solid #e4e9f0;
                                                                                font-size:.72rem;color:#64748b;text-align:right">
                        Total bobot sub-komponen: <strong>{{ round($subTotal * 100, 2) }}%</strong>
                        @if (abs($subTotal - $aspek->bobot) >= 0.001)
                            <span class="text-danger ml-1">
                                <i class="fa fa-exclamation-triangle"></i>
                                Tidak sesuai bobot aspek ({{ round($aspek->bobot * 100) }}%)
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Modal Edit Aspek --}}
            <div class="modal fade" id="modalEditAspek{{ $aspek->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-w700">Edit Aspek</h5>
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

            {{-- Modal Tambah Sub-komponen --}}
            <div class="modal fade" id="modalTambahSub{{ $aspek->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-w700">
                                Tambah Sub-komponen
                                <span class="text-muted font-w400">— {{ $aspek->nama }}</span>
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

            {{-- Modal Edit Sub --}}
            @foreach ($aspek->sub as $s)
                <div class="modal fade" id="modalEditSub{{ $s->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-w700">Edit Sub-komponen</h5>
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
            <div class="block block-rounded text-center py-5">
                <i class="fa fa-th-list fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-2">Belum ada aspek penilaian.</p>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahAspek">
                    <i class="fa fa-plus mr-1"></i> Tambah Aspek Pertama
                </button>
            </div>
        @endforelse

        {{-- Total bobot footer --}}
        @if ($aspekList->count() > 0)
            <div class="total-bar">
                <span class="font-w600 font-size-sm">Total Bobot Semua Aspek</span>
                <span class="font-w700 {{ abs($totalBobot - 1) < 0.001 ? 'text-success' : 'text-danger' }}"
                    style="font-size:1.1rem">
                    {{ round($totalBobot * 100, 2) }}%
                </span>
            </div>
        @endif

    </div>

    {{-- Modal Tambah Aspek --}}
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