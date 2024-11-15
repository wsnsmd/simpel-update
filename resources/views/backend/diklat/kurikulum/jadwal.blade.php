@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
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
                pageLength: 25,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                scrollX: false,
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api(), data;
        
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
        
                    // Total over all pages
                    total = api
                        .column(4)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    
                    var jp = {{ $max_jp }};
                    if(jp == total) {
                        text = total + ' JP (Sesuai)';
                    }
                    else {
                        text = total + ' JP (Belum Sesuai)';
                    }
                    // Update footer
                    $( api.column(4).footer() ).html(text);
                }
            });

            Dashmix.helpers(['datepicker', 'tooltip', 'select2']);
        });
        jQuery(document).ready(function () {
            $('#fasilitator').select2({
                dropdownParent: $("#mdl-wi"),
                placeholder: "-- Pilih Widyaiswara --",
                // minimumInputLength: 3,
                ajax: {
                    url: '{{ route("ajax.widyaiswara") }}',
                    dataType: 'json',
                    delay: 50,
                    data: function (params) {                
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    },        
                    cache: true        
                }
            });

            $('#mdl-jadwal').on('hidden.bs.modal', function() { 
                $("#mdl-jadwal-form-method").remove();       
                $("#tanggal").val('');
                $("#jam_mulai").val('');
                $("#jam_akhir").val('');
                $("#jp").val('');
            });

            $('#mdl-wi').on('hidden.bs.modal', function() {   
                $("#mdl-wi-form-method").remove();     
                $("#fasilitator").val('').trigger('change');
                $("#butir").val('');
            });
        })

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

        function showJadwal(id)
        {
            if(typeof id === "undefined") {
                $("#mdl-jadwal-title").html("Tambah Jadwal");
                $("#mdl-jadwal-form").attr("action", "{{ route('backend.diklat.mapel.jadwal.store', ['jadwal' =>  $jadwal->id, 'mapel' => $mapel->id]) }}");
                $('#mdl-jadwal').modal('show');
            }
            else {
                var url_form = "{{ route('backend.diklat.mapel.jadwal.update', ':id') }}";
                var url_edit = "{{ route('backend.diklat.mapel.jadwal.edit', ':id') }}";

                url_form = url_form.replace(':id', id);
                url_edit = url_edit.replace(':id', id);

                $("#mdl-jadwal-title").html("Edit Jadwal");
                $("#mdl-jadwal-form").attr("action", url_form);                
                $('#mdl-jadwal-form').append('<input type="hidden" id="mdl-jadwal-form-method" name="_method" value="PATCH">');
                $('#mdl-jadwal').modal('show');

                $.ajax({
                    type: "get",
                    url: url_edit,
                    success: function(data) {
                        $("#tanggal").val(data.tanggal);
                        $("#jam_mulai").val(data.jam_mulai);
                        $("#jam_akhir").val(data.jam_akhir);
                        $("#jp").val(data.jp);
                        $("#mdl-jadwal").modal('show');
                    }
                });

                return false;
            }
        }

        function showWi(id)
        {
            if(typeof id === "undefined") {
                $("#mdl-wi-title").html("Tambah Widyaiswara");                
                $("#mdl-wi-form").attr("action", "{{ route('backend.diklat.mapel.wi.store', ['jadwal' =>  $jadwal->id, 'mapel' => $mapel->id]) }}");
                $('#mdl-wi').modal('show');
            }
            else {
                var url_form = "{{ route('backend.diklat.mapel.wi.update', ':id') }}";
                var url_edit = "{{ route('backend.diklat.mapel.wi.edit', ':id') }}";

                url_form = url_form.replace(':id', id);
                url_edit = url_edit.replace(':id', id);

                $("#mdl-wi-title").html("Edit Widyaiswara");
                $("#mdl-wi-form").attr("action", url_form);                
                $('#mdl-wi-form').append('<input type="hidden" id="mdl-wi-form-method" name="_method" value="PATCH">');
                $('#mdl-wi').modal('show');

                $.ajax({
                    type: "get",
                    url: url_edit,
                    success: function(data) {
                        $("#fasilitator").select2("trigger", "select", {
                            data: { id: data.fid, text: data.nama }
                        });
                        $("#butir").val(data.butir);
                        $("#mdl-wi").modal('show');
                    }
                });

                return false;
            }
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

    <!-- Page Content -->
    <div class="content">

        <!-- Jadwal -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Jadwal - {{$mapel->nama}}</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row mb-3 pb-3 border-bottom">
                    <div class="col-lg-12">
                        <div class="float-left">
                            <a href="javascript:;" class="btn btn-primary btn-square" onclick="showJadwal()">
                                <i class="fas fa-plus-circle mr-2"></i>Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="div-data">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Tanggal</th>
                                <th>Jam Mulai</th>
                                <th>Jam Akhir</th>
                                <th>Jumlah (JP)</th>
                                <th class="text-center" style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mapel_jadwal as $mj)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ $mj->tanggal }}
                                </td>
                                <td>
                                    {{ date_format(date_create($mj->jam_mulai), 'H:i') }}
                                </td>
                                <td>
                                    {{ date_format(date_create($mj->jam_akhir), 'H:i') }}
                                </td>
                                <td>
                                    {{ $mj->jp }}
                                </td>                                                             
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.mapel.jadwal.destroy', ['jadwal' =>  $jadwal->id, 'mapel' => $mapel->id, 'id' => $mj->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="javascript:;" onclick="return showJadwal({{$mj->id}})" class="btn btn-sm btn-primary" title="Edit" data-toggle="tooltip" data-placement="top">
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
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right">Total:</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Jadwal -->

        <!-- Widyaiswara -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Widyaiswara</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row mb-3 pb-3 border-bottom">
                    <div class="col-lg-12">
                        <div class="float-left">
                            <a href="javascript:;" class="btn btn-primary btn-square" onclick="showWi()">
                                <i class="fas fa-plus-circle mr-2"></i>Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="div-data">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Kode Butir</th>
                                <th class="text-center" style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wi as $w)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ $w->nip }}
                                </td>
                                <td>
                                    {{ $w->nama }}
                                </td>
                                <td>
                                    {{ $w->jabatan }}
                                </td>
                                <td>
                                    {{ $w->butir }}
                                </td>                                                             
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.mapel.wi.destroy', ['jadwal' =>  $jadwal->id, 'mapel' => $mapel->id, 'id' => $w->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="{{ route('backend.diklat.mapel.wi.cetak', ['id' => $w->id]) }}" class="btn btn-sm btn-success" title="Cetak" data-toggle="tooltip" data-placement="top" target="_cetak">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showWi({{$w->id}})" class="btn btn-sm btn-primary" title="Edit" data-toggle="tooltip" data-placement="top">
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
        <!-- END Widyaiswara -->

        <!-- Jadwal Modal -->
        <div class="modal fade" id="mdl-jadwal" tabindex="-1" role="dialog" aria-labelledby="mdl-jadwal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-jadwal-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-jadwal-title">Tambah Jadwal</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="instansi">Tanggal</label>
                                        <input class="js-datepicker form-control" type="text" id="tanggal" name="tanggal" placeholder="YYYY-MM-DD" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" value="" required>
                                    </div>  
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="jadwal_jpk" class="control-label">Jam Mulai</label>                                                
                                                <input type="text" class="form-control jadwalpicker" id="jam_mulai" name="jam_mulai" placeholder="HH:MM" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="jadwal_jpk" class="control-label">Jam Akhir</label>                                                
                                                <input type="text" class="form-control jadwalpicker" id="jam_akhir" name="jam_akhir" placeholder="HH:MM" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="jadwal_jpe" class="control-label">Jumlah (JP)</label>
                                        <input type="number" min="0" max="{{ $max_jp }}" class="form-control" id="jp" name="jp" placeholder="Jumlah JP" required>
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
        <!-- END Jadwal Modal -->

        <!-- Widyaiswara Modal -->
        <div class="modal fade" id="mdl-wi" tabindex="-1" role="dialog" aria-labelledby="mdl-jadwal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-wi-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-wi-title">Tambah Widyaiswara</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="fasilitator" class="control-label">Widyaiswara</label>
                                        <select class="form-control" id="fasilitator" name="fasilitator" style="width: 100%;">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="butir" class="control-label">Kode Butir Kegiatan</label>
                                        <input type="number" min="0" max="100" class="form-control" id="butir" name="butir" placeholder="Kode Butir Kegiatan" required>
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
        <!-- END Widyaiswara Modal -->

    </div>
    <!-- END Page Content -->
@endsection
