@php
    $colSatker = "Satuan Kerja";
    $isASN = true;
    $canAddEdit = false;
    if(stripos($jadwal->nama, 'DPRD') !== false)
    {
        $colSatker = "Partai";
        $isASN = false;
    }
    if(is_null($sertifikat))
        $canAddEdit = true;
@endphp

@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        var jadwal_id = '{{ $jadwal->id }}'
        var kurikulum_id = '{{ $jadwal->kurikulum_id }}';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            // Dashmix.helpers(['datepicker', 'validation']);
            // Override a few default classes
            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4",
                sFilterInput:  "form-control",
                sLengthSelect: "form-control"
            });

            // Override a few defaults
            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
                    emptyTable: "Tidak ada data tersedia",
                    infoEmpty: "Halaman 0 dari 0",
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    info: "Halaman <strong>_PAGE_</strong> dari <strong>_PAGES_</strong>",
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>'
                    }
                }
            });

            jQuery('.js-dataTable-full').dataTable({
                pageLength: 25,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
            });
        });

        @if (session('success'))
        $.notify({
            icon: "fa fa-check mr-1",
            message: "{{ session('success') }}"
        }, {
            allow_dismiss: false,
            type: 'success',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @elseif (session('error'))
        $.notify({
            icon: "fa fa-times mr-1",
            message: "{{ session('error') }}"
        }, {
            allow_dismiss: false,
            type: 'danger',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @endif

        function showAlert(form) {
            var e = Swal.mixin({
                        buttonsStyling: !1,
                        customClass: {
                            confirmButton: "btn btn-success m-1",
                            cancelButton: "btn btn-danger m-1",
                            input: "form-control"
                        }
                    });

            e.fire({
                title: 'Apakah anda yakin',
                text: 'Anda tidak akan dapat mengembalikan data anda',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                customClass: {
                    confirmButton: "btn btn-danger m-1",
                    cancelButton: "btn btn-secondary m-1"
                },
                html: !1
            }).then((result) => {
                if(result.value) {
                    form.submit();
                }
            });
        }

        function showVerifikasi(form, id) {
            var e = Swal.mixin({
                        buttonsStyling: !1,
                        customClass: {
                            confirmButton: "btn btn-success m-1",
                            cancelButton: "btn btn-danger m-1",
                            input: "form-control"
                        }
                    });

            if(id == 1) {
                e.fire({
                    title: 'Apakah anda yakin',
                    text: 'Melakukan verifikasi dan menyetujui peserta?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Setuju',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    html: !1
                }).then((result) => {
                    if(result.value) {
                        form.append('<input type="hidden" name="setuju" value="1" /> ');
                        form.submit();
                    }
                });
            }
            else if(id == 2) {
                e.fire({
                    title: 'Apakah anda yakin',
                    text: 'Melakukan verifikasi dan menolak peserta?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-danger m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    html: !1
                }).then((result) => {
                    if(result.value) {
                        form.append('<input type="hidden" name="setuju" value="2" /> ');
                        form.submit();
                    }
                });
            }
        }

        function showKonfirmasi(form, id) {
            var e = Swal.mixin({
                        buttonsStyling: !1,
                        customClass: {
                            confirmButton: "btn btn-success m-1",
                            cancelButton: "btn btn-danger m-1",
                            input: "form-control"
                        }
                    });

            if(id == 1) {
                e.fire({
                    title: 'Apakah anda yakin',
                    text: 'Melakukan konfirmasi manual peserta?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    html: !1
                }).then((result) => {
                    if(result.value) {
                        form.append('<input type="hidden" name="konfirmasi" value="1" /> ');
                        form.submit();
                    }
                });
            }
            else if(id == 2) {
                e.fire({
                    title: 'Apakah anda yakin',
                    text: 'Mengirim ulang email konfirmasi peserta?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-warning m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    html: !1
                }).then((result) => {
                    if(result.value) {
                        form.append('<input type="hidden" name="konfirmasi" value="2" /> ');
                        form.submit();
                    }
                });
            }
        }

        async function showBatal(form) {
            var e = Swal.mixin({
                        buttonsStyling: !1,
                        customClass: {
                            confirmButton: "btn btn-danger m-1",
                            cancelButton: "btn btn-secondary m-1",
                            input: "form-control",
                        }
                    });

            const { value: text } = await e.fire({
                title: 'Apakah anda yakin',
                text: 'Melakukan pembatalan pada peserta?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                input: 'textarea',
                inputPlaceholder: 'Keterangan Batal...',
                inputAttributes: {
                    'aria-label': 'Keterangan Batal'
                },
                html: !1,
                inputValidator: (value) => {
                    return !value && 'Tidak boleh kosong!'
                }
            });

            if(text) {
                form.append('<input type="hidden" name="batal_ket" value="' + text + '" /> ');
                form.submit();
            }
        }
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Peserta</h1>
                    <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Jadwal</li>
                            <li class="breadcrumb-item">Detail</li>
                            <li class="breadcrumb-item active" aria-current="page">{{$jadwal->nama}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Quick Menu -->
    @if(Gate::check('isCreator', $jadwal) || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            @if($canAddEdit)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.diklat.peserta.create', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tambah Peserta</p>
                    </div>
                </a>
            </div>
            @endif
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('import-form').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-download text-warning fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Impor Peserta</p>
                    </div>
                </a>
                <form id="import-form" action="{{ route('backend.diklat.peserta.import', ['jadwal' => $jadwal->id]) }}" method="post" style="display: none;">
                    @csrf
                </form>
            </div>
            @if(!$jadwal->is_konfirmasi)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('jadwal-konfirmasi').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-lock-open text-info fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Buka Konfirmasi Kehadiran</p>
                    </div>
                </a>
                <form id="jadwal-konfirmasi" action="{{ route('backend.diklat.peserta.konfirmasi.jadwal') }}" method="post" style="display: none;">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <input type="hidden" name="status" value="1">
                </form>
            </div>
            @else
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('jadwal-konfirmasi').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-lock text-danger fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tutup Konfirmasi Kehadiran</p>
                    </div>
                </a>
                <form id="jadwal-konfirmasi" action="{{ route('backend.diklat.peserta.konfirmasi.jadwal') }}" method="post" style="display: none;">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <input type="hidden" name="status" value="0">
                </form>
            </div>
            @endif
        </div>
    </div>
    @endif
    <!-- END Quick Menu -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <!-- Peserta Verifikasi -->
        <div class="block block-bordered block-themed">
            <div class="block-header bg-success">
                <h3 class="block-title">Sudah di Verifikasi</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 12%;">NIP</th>
                                <th>Nama</th>
                                <th>Instansi</th>
                                @if((Gate::check('isCreator', $jadwal) || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <th style="width: 5%;">Aksi</th>
                                <th style="width: 5%;">Batal</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_verif as $pv)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $pv->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pv->nama_lengkap }}
                                </td>
                                <td class="font-w600">
                                    {{ $pv->instansi }}
                                </td>
                                @if((Gate::check('isCreator', $jadwal) || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pv->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pv->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.batal', $pv->id) }}" method="POST">
                                        @csrf
                                        <div class="btn-group">
                                            <a href="javascript:;" onclick="return showBatal($(this).closest('form'));" class="btn btn-sm btn-danger" title="Batal"><i class="fa fa-times"></i></a>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Peserta Verifikasi -->

        <!-- Peserta Belum Verifikasi -->
        <div class="block block-bordered block-themed">
            <div class="block-header bg-warning">
                <h3 class="block-title">Belum di Verifikasi</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 12%;">NIP</th>
                                <th>Nama</th>
                                <th>Instansi</th>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <th style="width: 5%;">Verifikasi</th>
                                <th style="width: 5%;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_noverif as $pn)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $pn->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pn->nama_lengkap }}
                                </td>
                                <td class="font-w600">
                                    {{ $pn->instansi }}
                                </td>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.verifikasi', $pn->id) }}" method="POST">
                                        @csrf
                                        <div class="btn-group">
                                            <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 1);" class="btn btn-sm btn-success" title="Setuju"><i class="fa fa-check"></i></a>
                                            <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 2);" class="btn btn-sm btn-danger" title="Tolak"><i class="fa fa-times"></i></a>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pn->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pn->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Peserta Belum Verifikasi -->
        <!-- Peserta Belum Konfirmasi -->
        <div class="block block-bordered block-themed">
            <div class="block-header bg-secondary">
                <h3 class="block-title">Belum Konfirmasi Email</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 12%;">NIP</th>
                                <th>Nama</th>
                                <th>Instansi</th>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <th style="width: 5%;">Konfirmasi</th>
                                <th style="width: 5%;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_confirm as $pc)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $pc->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pc->nama_lengkap }}
                                </td>
                                <td class="font-w600">
                                    {{ $pc->instansi }}
                                </td>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.konfirmasi', $pc->id) }}" method="POST">
                                        @csrf
                                        <div class="btn-group">
                                            <a href="javascript:;" onclick="return showKonfirmasi($(this).closest('form'), 1);" class="btn btn-sm btn-success" title="Konfirmasi Manual"><i class="fa fa-check"></i></a>
                                            <a href="javascript:;" onclick="return showKonfirmasi($(this).closest('form'), 2);" class="btn btn-sm btn-warning" title="Kirim Ulang Email"><i class="fa fa-paper-plane"></i></a>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pc->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pc->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Peserta Belum Konfirmasi -->
        <!-- Peserta Batal -->
        <div class="block block-bordered block-themed">
            <div class="block-header bg-danger">
                <h3 class="block-title">Batal</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 12%;">NIP</th>
                                <th>Nama</th>
                                <th>Instansi</th>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <th style="width: 8%;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_batal as $pb)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $pb->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pb->nama_lengkap }}
                                </td>
                                <td class="font-w600">
                                    {{ $pb->instansi }}
                                </td>
                                @if((Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3)) && $canAddEdit)
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pb->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pb->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Peserta Batal -->
    </div>
    <!-- END Page Content -->
@endsection
