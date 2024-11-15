@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">    
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
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
                pageLength: 10,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                scrollX: false,
            });

            Dashmix.helpers(['tooltip']);
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
            $('#peserta').select2({
                dropdownParent: $('#mdl-anggota'),
                placeholder: '-- Pilih Peserta --',
                ajax: {
                    url: "{{ route('backend.diklat.seminar.ajax.peserta', ['jadwal' =>  $seminar->jid]) }}",
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

            $('#mdl-anggota').on('hidden.bs.modal', function() { 
                $("#mdl-anggota-form-method").remove();       
                $("#peserta").val('').trigger('change');
            });
        })

        function showAnggota(id)
        {
            if(typeof id === "undefined") {
                $("#mdl-anggota-title").html("Tambah Anggota");
                $("#mdl-anggota-form").attr("action", "{{ route('backend.diklat.seminar.anggota.store', ['kelompok' =>  $seminar->id]) }}");
                $('#mdl-anggota').modal('show');
            }
            else {
                var url_form = "{{ route('backend.diklat.seminar.anggota.update', ':id') }}";
                var url_edit = "{{ route('backend.diklat.seminar.anggota.edit', ':id') }}";

                url_form = url_form.replace(':id', id);
                url_edit = url_edit.replace(':id', id);

                $("#mdl-anggota-title").html("Edit Anggota");
                $("#mdl-anggota-form").attr("action", url_form);                
                $('#mdl-anggota-form').append('<input type="hidden" id="mdl-anggota-form-method" name="_method" value="PATCH">');
                
                $.ajax({
                    type: "get",
                    url: url_edit,
                    success: function(data) {
                        $("#peserta").select2("trigger", "select", {
                            data: { id: data.peid, text: data.nama }
                        });
                        $('#mdl-anggota').modal('show');
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

    <!-- Quick Menu -->
    @if(Gate::check('isUser'))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showAnggota()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tambah Anggota</p>
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
                <h3 class="block-title">Daftar Anggota - [ {{ $seminar->kelompok }} ]</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="font-w700 text-center" style="width: 30px;">#</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Instansi</th>
                                <th style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anggota as $a)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $a->nip }}</td>
                                <td>{{ $a->nama_lengkap }}</td>
                                <td>{{ $a->satker_singkat }} - {{ $a->instansi }}</td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.seminar.anggota.destroy', $a->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="javascript:;" onclick="return showAnggota({{$a->id}})" class="btn btn-sm btn-primary" title="Edit" data-toggle="tooltip" data-placement="top">
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

        <!-- Anggota Modal -->
        <div class="modal fade" id="mdl-anggota" tabindex="-1" role="dialog" aria-labelledby="mdl-jadwal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-anggota-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-anggota-title">Tambah Anggota</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="peserta" class="control-label">Peserta</label>
                                        <select class="form-control" id="peserta" name="peserta" style="width: 100%;">
                                        </select>
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
        <!-- END Anggota Modal -->

    </div>
    <!-- END Page Content -->     
@endsection
