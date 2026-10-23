@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/nestable2/jquery.nestable.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/nestable2/jquery.nestable.min.js') }}"></script>
    <script>
        jQuery(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
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

            jQuery(".js-dataTable-simple").dataTable({
                pageLength: 25,
                lengthMenu: false,
                searching: false,
                autoWidth: false,
                dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-6'i><'col-sm-6'p>>"
            });

            jQuery(".js-nestable-simple").nestable({
                group: 1,
                maxDepth: 1,
            }).on('change', function(e) {
                var list = e.length ? e : $(e.target);
                var jsonString = JSON.stringify(list.nestable('serialize'));
                $.post("{{route('backend.master.instansi.sort')}}", {instansi: jsonString}, function (data) {
                    console.log(data);
                });
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
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.master.instansi.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Quick Menu -->
        <div class="pt-4 px-4 bg-body-dark rounded push">
            <div class="row row-deck">
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.master.instansi.create') }}">
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
                <h3 class="block-title">Instansi</h3>
            </div>
            <div class="block-content block-content-full">
                <ul class="nav nav-tabs nav-tabs-block nav-main-dark" data-toggle="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab-detail"><i class="fa fa-align-justify mr-1"></i> Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab-materi"><i class="fa fa-sort mr-1"></i> Urutan</a>
                    </li>                    
                </ul>
                <div class="block-content tab-content overflow-hidden">
                    <div class="tab-pane fade active show" id="tab-detail" role="tabpanel">
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-simple">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">#</th>
                                    <th>Nama</th>
                                    <th>Ket.</th>
                                    <th style="width: 1%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($instansi as $i)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-w600">
                                        {{ $i->nama }}
                                    </td>
                                    <td class="font-w600">
                                        {{ $i->ket }}
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('backend.master.instansi.destroy', $i->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="btn-group">                                    
                                                <a href="{{ route('backend.master.instansi.edit', $i->id) }}" class="btn btn-sm btn-primary" title="Edit">
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
                    <div class="tab-pane fade" id="tab-materi" role="tabpanel">
                        <div class="js-nestable-simple dd">
                            <ol class="dd-list">
                                @foreach ($instansi as $i)
                                <li class="dd-item" data-id="{{$i->id}}">
                                    <div class="dd-handle"><i class="fa fa-sort mr-1"></i> {{$i->nama}}</div>
                                </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
