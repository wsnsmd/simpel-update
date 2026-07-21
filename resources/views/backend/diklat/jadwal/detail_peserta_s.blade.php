@php
    $colSatker = "Satuan Kerja";
    $isASN = true;
    $canAddEdit = true;
    if (stripos($jadwal->nama, 'DPRD') !== false) {
        $colSatker = "Partai";
        $isASN = false;
    }
@endphp

@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .dataTables_processing {
            position: absolute;
            top: 50% !important;
            left: 50% !important;
            width: 250px !important;
            margin-left: -125px !important;
            margin-top: -30px !important;
            padding: 20px !important;
            height: auto !important;
            text-align: center;
            color: #333;
            border: none !important;
            background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2) !important;
            border-radius: 0px !important;
            z-index: 1060 !important;
        }

        .blur-content {
            filter: blur(3px);
            opacity: 0.5;
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .dataTable {
            transition: all 0.2s ease;
        }
    </style>
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

        jQuery(function () {
            Dashmix.helpers(['core-bootstrap-tabs']);

            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4",
                sFilterInput: "form-control",
                sLengthSelect: "form-control"
            });

            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><span class="font-w600 mt-2 d-inline-block">Memuat data...</span>',
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

            initTableVerif();

            $(document).on('processing.dt', function (e, settings, processing) {
                var api = new $.fn.dataTable.Api(settings);
                var tableElement = api.table().node();

                if (processing) {
                    $(tableElement).addClass('blur-content');
                } else {
                    $(tableElement).removeClass('blur-content');
                }
            });

            $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
                var target = $(e.target).attr("href");

                if (target === '#tab-noverif' && !$.fn.DataTable.isDataTable('#table-peserta-noverif')) {
                    initTableNoVerif();
                } else if (target === '#tab-confirm' && !$.fn.DataTable.isDataTable('#table-peserta-confirm')) {
                    initTableConfirm();
                } else if (target === '#tab-batal' && !$.fn.DataTable.isDataTable('#table-peserta-batal')) {
                    initTableBatal();
                }

                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });

        function createDataTable(selector, url, customColumns) {
            const commonColumns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                { data: 'nip', name: 'nip' },
                { data: 'nama_lengkap', name: 'nama_lengkap' },
            ];

            // Tambahkan kolom Instansi/Satker tergantung logika Anda
            commonColumns.push({ data: 'instansi', name: 'instansi' });

            return jQuery(selector).DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: url, type: "POST" },
                columns: [...commonColumns, ...customColumns],
                pageLength: 25,
                autoWidth: false,
                drawCallback: function () {
                    $(selector).removeClass('blur-content');
                }
            });
        }

        const colCenter = { orderable: false, searchable: false, class: 'text-center' };

        // --- Fungsi Inisialisasi Per Tabel ---
        function initTableVerif() {
            createDataTable('#table-peserta-verif', "{{ route('backend.diklat.peserta.datatable.verif', $jadwal->id) }}", [
                { data: 'aksi', name: 'aksi', ...colCenter },
                { data: 'batal', name: 'batal', ...colCenter }
            ]);
        }

        function initTableNoVerif() {
            createDataTable('#table-peserta-noverif', "{{ route('backend.diklat.peserta.datatable.noverif', $jadwal->id) }}", [
                { data: 'verifikasi', name: 'verifikasi', ...colCenter },
                { data: 'aksi', name: 'aksi', ...colCenter }
            ]);
        }

        function initTableConfirm() {
            createDataTable('#table-peserta-confirm', "{{ route('backend.diklat.peserta.datatable.confirm', $jadwal->id) }}", [
                { data: 'konfirmasi', name: 'konfirmasi', ...colCenter },
                { data: 'aksi', name: 'aksi', ...colCenter }
            ]);
        }

        function initTableBatal() {
            createDataTable('#table-peserta-batal', "{{ route('backend.diklat.peserta.datatable.batal', $jadwal->id) }}", [
                { data: 'aksi', name: 'aksi', ...colCenter }
            ]);
        }

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
                    if (result.value) {
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

            if (id == 1) {
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
                    if (result.value) {
                        form.append('<input type="hidden" name="setuju" value="1" /> ');
                        form.submit();
                    }
                });
            }
            else if (id == 2) {
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
                    if (result.value) {
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

            if (id == 1) {
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
                    if (result.value) {
                        form.append('<input type="hidden" name="konfirmasi" value="1" /> ');
                        form.submit();
                    }
                });
            }
            else if (id == 2) {
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
                    if (result.value) {
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

            if (text) {
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
    @cannot('isViewer')
    @if(Gate::check('isCreator', $jadwal) || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
        <div class="pt-4 px-4 bg-body-dark rounded push">
            <div class="row row-deck">
                @if($canAddEdit)
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-rounded block-link-pop text-center d-flex align-items-center"
                            href="{{ route('backend.diklat.peserta.create', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
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
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                        onclick="event.preventDefault(); document.getElementById('import-form').submit();">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
                                <i class="fa fa-download text-warning fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Impor Peserta</p>
                        </div>
                    </a>
                    <form id="import-form" action="{{ route('backend.diklat.peserta.import', ['jadwal' => $jadwal->id]) }}"
                        method="post" style="display: none;">
                        @csrf
                    </form>
                </div>
                @if(!$jadwal->is_konfirmasi)
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                            onclick="event.preventDefault(); document.getElementById('jadwal-konfirmasi').submit();">
                            <div class="block-content">
                                <p class="mb-2 d-sm-block">
                                    <i class="fa fa-lock-open text-info fa-2x"></i>
                                </p>
                                <p class="font-w600 font-size-sm text-uppercase">Buka Konfirmasi Kehadiran</p>
                            </div>
                        </a>
                        <form id="jadwal-konfirmasi" action="{{ route('backend.diklat.peserta.konfirmasi.jadwal') }}" method="post"
                            style="display: none;">
                            @csrf
                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                            <input type="hidden" name="status" value="1">
                        </form>
                    </div>
                @else
                    <div class="col-6 col-md-4 col-xl-2">
                        <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                            onclick="event.preventDefault(); document.getElementById('jadwal-konfirmasi').submit();">
                            <div class="block-content">
                                <p class="mb-2 d-sm-block">
                                    <i class="fa fa-lock text-danger fa-2x"></i>
                                </p>
                                <p class="font-w600 font-size-sm text-uppercase">Tutup Konfirmasi Kehadiran</p>
                            </div>
                        </a>
                        <form id="jadwal-konfirmasi" action="{{ route('backend.diklat.peserta.konfirmasi.jadwal') }}" method="post"
                            style="display: none;">
                            @csrf
                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                            <input type="hidden" name="status" value="0">
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
    @endcannot
    <!-- END Quick Menu -->

    <!-- Page Content -->
    <div class="content">
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header bg-primary">
                <h3 class="block-title">Manajemen Peserta - {{ $jadwal->nama }}</h3>
            </div>
            <div class="block-content">
                <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-verif">
                            <i class="fa fa-check-circle text-success mr-1"></i> Terverifikasi
                            <span class="badge badge-pill badge-success ml-1">{{ $countVerif }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-noverif">
                            <i class="fa fa-question-circle text-warning mr-1"></i> Belum Verifikasi
                            <span class="badge badge-pill badge-warning ml-1">{{ $countNoVerif }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-confirm">
                            <i class="fa fa-envelope text-secondary mr-1"></i> Belum Konfirmasi
                            <span class="badge badge-pill badge-secondary ml-1">{{ $countConfirm }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-batal">
                            <i class="fa fa-times-circle text-danger mr-1"></i> Batal
                            <span class="badge badge-pill badge-danger ml-1">{{ $countBatal }}</span>
                        </a>
                    </li>
                </ul>

                <div class="block-content tab-content overflow-hidden mb-3">
                    <div class="tab-pane fade active show" id="tab-verif" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter table-hover table-sm"
                                id="table-peserta-verif" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 12%;">NIP</th>
                                        <th>Nama</th>
                                        <th>Instansi</th>
                                        <th class="text-center" style="width: 8%;">Aksi</th>
                                        <th class="text-center" style="width: 8%;">Batal</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-noverif" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter table-hover table-sm"
                                id="table-peserta-noverif" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 12%;">NIP</th>
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th class="text-center" style="width: 10%;">Verifikasi</th>
                                        <th class="text-center" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-confirm" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter table-hover table-sm"
                                id="table-peserta-confirm" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 12%;">NIP</th>
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th class="text-center" style="width: 10%;">Konfirmasi</th>
                                        <th class="text-center" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-batal" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter table-hover table-sm"
                                id="table-peserta-batal" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 12%;">NIP</th>
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th class="text-center" style="width: 8%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection