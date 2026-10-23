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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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

        function filterClick(id) {
            var url = "{{ route('backend.diklat.peserta.jadwal.filter', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'html',
                success: function(data) {
                    $('#div-data').html(data);
                }
            });
            return false;
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
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Diklat</li>
                        <li class="breadcrumb-item active" aria-current="page">Peserta</li>
                    </ol>
                </nav>
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
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:void(0)" onclick="filterClick(1)">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-th-list text-gray-dark fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Semua</p>
                        </div>
                    </a>
                </div>                
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:void(0)" onclick="filterClick(2)">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-calendar-alt text-warning fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Akan Datang</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:void(0)" onclick="filterClick(3)">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-calendar-day text-info fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Berjalan</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:void(0)" onclick="filterClick(4)">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-calendar-check text-xinspire fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Selesai</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:void(0)" onclick="filterClick(5)">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-calendar-times text-danger fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Batal</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- END Quick Menu -->

        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Jadwal Pelatihan</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive" id="div-data">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>Nama</th>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Jenis Diklat</th>
                                <th>Kelas</th>
                                <th>Kuota</th>
                                <th>Status</th>
                                <th style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $j)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $j->nama }}
                                </td>
                                <td class="font-w600">
                                    {{ formatTanggal($j->tgl_awal) }} - {{ formatTanggal($j->tgl_akhir) }}
                                </td>             
                                <td class="font-w600">
                                    {{ $j->diklat_jenis_id }}
                                </td>          
                                <td class="font-w600">
                                    {{ $j->kelas }}
                                </td>
                                <td class="font-w600">
                                    {{ $j->kuota }} Orang
                                </td>
                                <td class="font-w600">
                                    {{ $j->status }}
                                </td>                                                                                      
                                <td class="text-center">
                                    <div class="btn-group">
                                        {{-- <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}" class="btn btn-sm btn-success">
                                            <i class="fa fa-cog"></i>
                                        </a> --}}
                                        <a href="{{ route('backend.diklat.peserta.show', ['id' => $j->id, 'slug' => str_slug($j->nama)]) }}" class="btn btn-sm btn-primary mr-1 mb-3">
                                            <i class="fa fa-users"></i>
                                            Peserta
                                        </a>                               
                                    </div>
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
