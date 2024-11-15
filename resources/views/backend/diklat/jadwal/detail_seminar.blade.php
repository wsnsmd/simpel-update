@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">    
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script>
        jQuery(function(){
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
                pageLength: 10,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                scrollX: false,
            });

            Dashmix.helpers(['datepicker', 'tooltip']);
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

        jQuery(document).ready(function () { 
            $('#mdl-cetak').on('hidden.bs.modal', function() { 
                $('#mdl-cetak-form').trigger('reset');
            });

            $('#mdl-cetak-form').submit(function(e) {
                $('#mdl-cetak').modal('hide'); 
            });
        })

        function showCetak(id) {            
            var url_form = "{{ route('backend.diklat.seminar.print.form', ':id') }}";

            url_form = url_form.replace(':id', id);

            $("#mdl-cetak-form").attr("action", url_form);
            $('#mdl-cetak').modal('show');
        }        

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
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.diklat.jadwal.detail_hero')
    <!-- END Hero -->

    <!-- Quick Menu -->
    @if(Gate::check('isUser'))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.diklat.seminar.kelompok.create', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tambah Kelompok</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif
    <!-- END Quick Menu --> 

    <!-- Page Content -->
    <div class="content">
        <!-- Mata Pelatihan -->        
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Kelompok Seminar</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="font-w700 text-center" style="width: 30px;">#</th>
                                <th>Nama</th>
                                <th>Coach</th>
                                <th>Penguji</th>
                                <th style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($seminar as $s)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $s->kelompok }}</td>
                                <td>{{ $s->coach }}</td>
                                <td>{{ $s->penguji }}</td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.seminar.kelompok.destroy', $s->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.seminar.anggota.index', ['kelompok' => $s->id]) }}" class="btn btn-sm btn-success" title="Anggota" data-toggle="tooltip" data-placement="top">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <a href="#" onclick="showCetak({{$s->id}})" class="btn btn-sm btn-warning" title="Cetak Form" data-toggle="tooltip" data-placement="top">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <a href="{{ route('backend.diklat.seminar.kelompok.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $s->id]) }}" class="btn btn-sm btn-primary" title="Edit" data-toggle="tooltip" data-placement="top">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus" data-toggle="tooltip" data-placement="top">
                                                <i class="far fa-trash-alt"></i>
                                            </a>                               
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Mata Pelatihan -->

        <!-- Cetak Modal -->
        <div class="modal fade" id="mdl-cetak" tabindex="-1" role="dialog" aria-labelledby="mdl-jadwal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-cetak-form" method="POST" action="" autocomplete="off" target="_cetak">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-cetak-title">Cetak Formulir</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="form" class="control-label">Form <span class="text-danger">*</span></label>
                                        <select class="form-control" id="form" name="form" style="width: 100%;" required>
                                            <option value="" selected>-- Pilih Form --</option>
                                            @foreach ($cetak as $c)
                                            <option value="{{$c->id}}">{{$c->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat" class="control-label">Tempat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Cth. Samarinda..." required>
                                    </div>
                                    <div class="form-group">
                                        <label for="instansi">Tanggal <span class="text-danger">*</span></label>
                                        <input class="js-datepicker form-control" type="text" id="tanggal" name="tanggal" placeholder="YYYY-MM-DD" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" value="" required>
                                    </div>  
                                </div>
                                <div class="block-content block-content-full text-right bg-light">
                                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <!-- END Cetak Modal -->

    </div>
    <!-- END Page Content -->     
@endsection
