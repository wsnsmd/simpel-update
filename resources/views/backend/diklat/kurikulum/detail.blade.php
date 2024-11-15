<?php
    switch ($kurikulum->jenis_belajar) 
    {
        case 2:
            $col_total = 4;
            break;
        
        default:
            $col_total = 3;
            break;
    }
?>

@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
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
                    .column( {{ $col_total }} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( {{ $col_total }} ).footer() ).html(
                    total + ' JP'
                );
            }  
            });

            $('[data-toggle="tooltip"]').tooltip();   
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

        function clearModalForm() {
            $("#nama").val("");
            $("#jpk").val("");
            $("#jpe").val("");
        }

        function addMapel() {
            clearModalForm();
            $("#mdl-mapel-form-method").remove();
            $("#mdl-mapel-title").html("Tambah Mata Pelatihan");
            $("#mdl-mapel-form").attr("action", "{{ route('backend.diklat.mapel.store', $kurikulum->id) }}");
            $("#mdl-mapel").modal('show');
        }

        function editMapel(id) {
            clearModalForm();
            var url_form = "{{ route('backend.diklat.mapel.update', ':id') }}";
            var url_edit = "{{ route('backend.diklat.mapel.show', ':id') }}";

            url_form = url_form.replace(':id', id);
            url_edit = url_edit.replace(':id', id);

            $("#mdl-mapel-title").html("Edit Mata Pelatihan");
            $("#mdl-mapel-form").attr("action", url_form);
            $("#mdl-mapel-form-method").remove();
            $('#mdl-mapel-form').append('<input type="hidden" id="mdl-mapel-form-method" name="_method" value="PATCH">');

            $.ajax({
                type: "get",
                url: url_edit,
                success: function(data) {
                    $("#nama").val(data.nama);
                    $("#jpk").val(data.jpk);
                    @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                    $("#jpe").val(data.jpe);
                    @endif
                    $("#mdl-mapel").modal('show');
                }
            });

            return false;
        }
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                                <li class="breadcrumb-item">Pelatihan</li>
                            <li class="breadcrumb-item">Kurikulum</li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
        </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <!-- Quick Menu -->
        <div class="pt-4 px-4 bg-body-dark rounded push">
            <div class="row row-deck">
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="return addMapel()">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-plus-circle text-success fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Tambah Data</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- END Quick Menu -->

        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Mata Pelatihan / Materi</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Nama</th>
                                @if ($kurikulum->jenis_belajar == 1 || $kurikulum->jenis_belajar == 2)
                                <th>Klasikal (JP)</th>
                                @endif
                                @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                                <th>E-Learning (JP)</th>    
                                @endif
                                <th>Jumlah (JP)</th>
                                <th style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mapel as $m)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $m->nama }}
                                </td>
                                @if ($kurikulum->jenis_belajar == 1 || $kurikulum->jenis_belajar == 2)
                                <td class="font-w600">
                                    {{ $m->jpk }}
                                </td>
                                @endif
                                @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                                <td class="font-w600">
                                    {{ $m->jpe }}
                                </td>
                                @endif
                                <td class="font-w600">
                                    {{ $m->jpk + $m->jpe }}
                                </td>                                                             
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.mapel.destroy', $m->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn btn-sm btn-primary" onclick="return editMapel({{$m->id}})">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger">
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
                                <th colspan="{{ $col_total }}" style="text-align:right">Total:</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->

    <!-- Mapel Block Modal -->
    <div class="modal fade" id="mdl-mapel" tabindex="-1" role="dialog" aria-labelledby="mdl-mapel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-mapel-form" method="POST" action="">
                    @csrf
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title" id="mdl-mapel-title">Tambah Mata Pelatihan</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="form-group">
                                    <label for="nama" class="control-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                @if ($kurikulum->jenis_belajar == 1 || $kurikulum->jenis_belajar == 2)
                                <div class="form-group">
                                    <label for="jpk" class="control-label">Klasikal (JP) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="110" step=".01" class="form-control" id="jpk" name="jpk" required>
                                </div>
                                @endif
                                @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                                <div class="form-group">
                                    <label for="jpe" class="control-label">E-Learning (JP) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="110" step=".01" class="form-control" id="jpe" name="jpe" required>
                                </div>
                                @endif
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
        <!-- END Mapel Block Modal -->
        
@endsection
