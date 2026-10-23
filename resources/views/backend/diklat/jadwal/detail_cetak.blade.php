@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">    
    <style>
        /* .ui-autocomplete { z-index:2147483647; } */
        .ui-autocomplete {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            float: left;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            list-style: none;
            /* font-size: 14px; */
            text-align: left;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            background-clip: padding-box;
            z-index:2147483647;
        }

        .ui-autocomplete > li > div {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333333;
            white-space: nowrap;
        }

        .ui-state-hover,
        .ui-state-active,
        .ui-state-focus {
            text-decoration: none;
            color: #262626;
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .ui-helper-hidden-accessible {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
            }
    </style>
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>    
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script>
        var jadwal_id = '{{ $jadwal->id }}'
        var kurikulum_id = '{{ $jadwal->kurikulum_id }}';
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['datepicker', 'validation']); 
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

        function showDialog(id, modal, title) {
            var url_form = "{{ route('backend.cetak.cetak', ':id') }}";
            var url_modal = "{{ route('backend.cetak.modal', ':id') }}";
            url_form = url_form.replace(':id', id);
            url_modal = url_modal.replace(':id', modal);

            $('#mdl-cetak-form').attr("action", url_form);
            $('#mdl-cetak-title').html('Cetak - ' + title);

            $.ajax({
                type: "POST",
                url: url_modal,
                success: function(data) {
                    $('#mdl-form-content').html(data);
                    $('#mdl-cetak').modal('show');
                    console.log(kurikulum_id);
                }
            });

            // $("#mdl-cetak").modal('show');
        }

        $('#mdl-cetak-form').submit(function(e) {
            $('#mdl-cetak').modal('hide'); //or  $('#IDModal').modal('hide');
        }); 
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Cetak</h1>
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

    <!-- Page Content -->
    <div class="content">
        <!-- Cetak -->
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Cetak</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th class="font-w700 text-center" style="width: 30px;">#</th>
                                <th class="font-w700" style="">Nama</th>                 
                                <th class="font-w700 text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($umum as $u)
                                <tr>
                                    <td class="font-w600 text-center">{{$loop->iteration}}.</td>
                                    <td class="font-w600">{{$u->nama}}</td>
                                    <td class="font-w600 text-center">
                                        @if($u->modal_id == 1)
                                        <form action="{{ route('backend.cetak.cetak', $u->id) }}" method="POST" target="_cetak">
                                        @csrf
                                        <input type="hidden" name="jadwal_id" value="{{$jadwal->id}}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-print mr-1"></i> Cetak
                                        </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="return showDialog({{ $u->id }}, {{ $u->modal_id }}, '{{ $u->nama }}')">
                                            <i class="fa fa-print mr-1"></i> Cetak
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Cetak -->

        <!-- Block Modal -->
        <div class="modal fade" id="mdl-cetak" tabindex="-1" role="dialog" aria-labelledby="mdl-cetak" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-cetak-form" method="POST" action="" autocomplete="off" target="_cetak">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{$jadwal->id}}">
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-cetak-title">Cetak</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content" id="mdl-form-content">
                                </div>
                                <div class="block-content block-content-full text-right bg-light">
                                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-print mr-1"></i> Cetak</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END Page Content -->     
@endsection
