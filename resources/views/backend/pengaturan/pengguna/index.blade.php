@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">

    <style type="text/css">
        .foto {
            width: 60px;
            height: 80px;
            border:1px solid #333;
            border-radius: 8px;
        }
    </style>
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
                autoWidth: false
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
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.pengaturan.pengguna.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Quick Menu -->
        <div class="pt-4 px-4 bg-body-dark rounded push">
            <div class="row row-deck">
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.pengguna.create') }}">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
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
                <h3 class="block-title">Pengguna</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Foto</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Group</th>
                                <th style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                            <tr>
                                <td>
                                    <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ is_null($u->photo) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url($u->photo)) }}" alt="">
                                </td>
                                <td class="font-w600">{{ $u->name }}</td>
                                <td class="font-w600">{{ $u->username }}</td>
                                <td class="font-w600">{{ $u->email }}</td>
                                <td class="font-w600">{{ $u->usergroup }}</td>
                                <td class="text-center">
                                    <form action="{{ route('backend.pengguna.destroy', $u->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">   
                                            <a href="{{ route('backend.pengguna.edit', $u->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
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
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
