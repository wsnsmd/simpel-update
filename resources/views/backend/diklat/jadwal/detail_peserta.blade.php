@php
    $colSatker = "Satuan Kerja";
    $isASN = true;
    $canAddEdit = true;
    if (stripos($jadwal->nama, 'DPRD') !== false) {
        $colSatker = "Partai";
        $isASN = false;
    }
    if (!is_null($sertifikat) && $sertifikat->is_final == true)
        $canAddEdit = false;
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

        function createDataTable(selector, url, customColumns, checkboxColumn) {
            var commonColumns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                { data: 'foto_render', name: 'foto_render', orderable: false, searchable: false },
            ];

            @if($isASN)
                commonColumns.push({ data: 'nip', name: 'nip' });
            @endif

            commonColumns.push(
                { data: 'nama_lengkap', name: 'nama_lengkap' },
                { data: 'satker_nama', name: 'satker_nama' },
                { data: 'instansi', name: 'instansi' },
            );

            // Jika ada checkboxColumn, letakkan di paling depan (sebelum # dan Foto)
            var allColumns = checkboxColumn
                ? [checkboxColumn, ...commonColumns, ...customColumns]
                : [...commonColumns, ...customColumns];

            var dt = jQuery(selector).DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: url, type: "POST" },
                columns: allColumns,
                pageLength: 25,
                autoWidth: false,
                drawCallback: function () {
                    $(selector).removeClass('blur-content');
                    // Reset toolbar setelah redraw (paging/search)
                    if (checkboxColumn) updateBulkToolbar();
                }
            });

            return dt;
        }

        // --- Inisialisasi per tabel ---
        const colCenter = { orderable: false, searchable: false, class: 'text-center' };

        function initTableVerif() {
            createDataTable('#table-peserta-verif', "{{ route('backend.diklat.peserta.datatable.verif', $jadwal->id) }}", [
                { data: 'aksi', name: 'aksi', ...colCenter },
                { data: 'batal', name: 'batal', ...colCenter }
            ]);
        }

        function initTableNoVerif() {
            var cbCol = { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, class: 'text-center', width: '36px' };
            createDataTable('#table-peserta-noverif', "{{ route('backend.diklat.peserta.datatable.noverif', $jadwal->id) }}", [
                { data: 'verifikasi', name: 'verifikasi', ...colCenter },
                { data: 'aksi', name: 'aksi', ...colCenter }
            ], cbCol);
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

            // ── Bulk Action ───────────────────────────────────────────────
            function updateBulkToolbar() {
                var checked = document.querySelectorAll('#table-peserta-noverif .bulk-cb:checked');
                var all = document.querySelectorAll('#table-peserta-noverif .bulk-cb');
                var toolbar = document.getElementById('bulk-toolbar');
                var counter = document.getElementById('bulk-counter');
                var cbAll = document.getElementById('check-all-noverif');

                if (checked.length > 0) {
                    toolbar.style.display = 'flex';
                    counter.textContent = checked.length + ' peserta dipilih';
                } else {
                    toolbar.style.display = 'none';
                }
                if (cbAll) {
                    cbAll.indeterminate = checked.length > 0 && checked.length < all.length;
                    cbAll.checked = all.length > 0 && checked.length === all.length;
                }
            }

        function toggleCheckAll(source) {
            document.querySelectorAll('#table-peserta-noverif .bulk-cb').forEach(function (cb) {
                cb.checked = source.checked;
            });
            updateBulkToolbar();
        }

        function bulkAksi(aksi) {
            var ids = [];
            document.querySelectorAll('#table-peserta-noverif .bulk-cb:checked').forEach(function (cb) {
                ids.push(cb.value);
            });
            if (ids.length === 0) { alert('Pilih minimal satu peserta.'); return; }

            var labelMap = { setuju: 'memverifikasi', tolak: 'menolak' };
            var colorMap = { setuju: '#28a745', tolak: '#e74c3c' };

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda akan ' + labelMap[aksi] + ' ' + ids.length + ' peserta sekaligus.',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: colorMap[aksi],
            }).then(function (result) {
                if (!result.value) return;

                // Buat form dinamis — tidak bergantung elemen di DOM
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("backend.diklat.peserta.bulk_verifikasi", $jadwal->id) }}';
                form.style.display = 'none';
                document.body.appendChild(form);

                // CSRF token
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = $('meta[name="csrf-token"]').attr('content');
                form.appendChild(csrf);

                // Aksi
                var ia = document.createElement('input');
                ia.type = 'hidden'; ia.name = 'aksi'; ia.value = aksi;
                form.appendChild(ia);

                // IDs peserta
                ids.forEach(function (id) {
                    var ii = document.createElement('input');
                    ii.type = 'hidden'; ii.name = 'ids[]'; ii.value = id;
                    form.appendChild(ii);
                });

                form.submit();
            });
        }
        // ── End Bulk Action ───────────────────────────────────────────

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
                        onclick="event.preventDefault(); document.getElementById('export-form1').submit();">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
                                <i class="fa fa-upload text-info fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Ekspor Peserta</p>
                        </div>
                    </a>
                    <form id="export-form1" action="{{ route('backend.diklat.peserta.export') }}" method="post"
                        style="display: none;" target="_export">
                        @csrf
                        <input type="hidden" name="export" value="1">
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id}}">
                    </form>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                        onclick="event.preventDefault(); document.getElementById('export-form2').submit();">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
                                <i class="fa fa-school text-danger fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Ekspor Moodle</p>
                        </div>
                    </a>
                    <form id="export-form2" action="{{ route('backend.diklat.peserta.export') }}" method="post"
                        style="display: none;" target="_export">
                        @csrf
                        <input type="hidden" name="export" value="2">
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id}}">
                    </form>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                        onclick="event.preventDefault(); document.getElementById('export-form3').submit();">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
                                <i class="fa fa-file-image text-secondary fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Foto</p>
                        </div>
                    </a>
                    <form id="export-form3" action="{{ route('backend.diklat.peserta.export') }}" method="post"
                        style="display: none;" target="_export">
                        @csrf
                        <input type="hidden" name="export" value="3">
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id}}">
                    </form>
                </div>
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
                            <i class="fa fa-check-circle text-success mr-1"></i> Sudah Verifikasi
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
                                id="table-peserta-verif" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 60px;">Foto</th>
                                        @if($isASN)
                                        <th style="width: 12%;">NIP</th> @endif
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th>Instansi</th>
                                        <th class="text-center">Aksi</th>
                                        <th class="text-center">Batal</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-noverif" role="tabpanel">

                        {{-- Toolbar bulk (sticky, muncul saat ada yang dicentang) --}}
                        <div id="bulk-toolbar" style="display:none;background:#1e293b;color:#fff;
                                                    border-radius:8px;padding:.6rem 1rem;margin:.5rem 0;
                                                    align-items:center;justify-content:space-between;gap:.5rem;
                                                    position:sticky;top:56px;z-index:99">
                            <span id="bulk-counter" style="font-size:.85rem;font-weight:500"></span>
                            <div class="d-flex" style="gap:.4rem">
                                <button type="button" onclick="bulkAksi('setuju')" class="btn btn-sm btn-success">
                                    <i class="fa fa-check mr-1"></i> Verifikasi
                                </button>
                                <button type="button" onclick="bulkAksi('tolak')" class="btn btn-sm btn-warning">
                                    <i class="fa fa-times mr-1"></i> Tolak
                                </button>
                                <button type="button"
                                    onclick="document.querySelectorAll('#table-peserta-noverif .bulk-cb').forEach(function(c){c.checked=false;}); updateBulkToolbar();"
                                    class="btn btn-sm btn-outline-light">
                                    Batal Pilih
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter table-hover table-sm"
                                id="table-peserta-noverif" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 36px;">
                                            <input type="checkbox" id="check-all-noverif" onchange="toggleCheckAll(this)"
                                                title="Pilih semua">
                                        </th>
                                        <th class="text-center" style="width: 30px;">#</th>
                                        <th style="width: 60px;">Foto</th>
                                        @if($isASN)
                                        <th>NIP</th> @endif
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th>Instansi</th>
                                        <th style="width: 5%;">Verifikasi</th>
                                        <th style="width: 5%;">Aksi</th>
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
                                        <th style="width: 60px;">Foto</th>
                                        @if($isASN)
                                        <th>NIP</th> @endif
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th>Instansi</th>
                                        <th style="width: 5%;">Konfirmasi</th>
                                        <th style="width: 5%;">Aksi</th>
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
                                        <th style="width: 60px;">Foto</th>
                                        @if($isASN)
                                        <th>NIP</th> @endif
                                        <th>Nama</th>
                                        <th>{{ $colSatker }}</th>
                                        <th>Instansi</th>
                                        <th style="width: 8%;">Aksi</th>
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