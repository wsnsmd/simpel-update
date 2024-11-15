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

        function showVerifikasi(form, id) {
            var e = Swal.mixin({
                        buttonsStyling: !1,
                        customClass: {
                            confirmButton: "btn btn-success m-1",
                            cancelButton: "btn btn-danger m-1",
                            input: "form-control"
                        }
                    });

            if(id == 1) {
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
                    if(result.value) {
                        form.append('<input type="hidden" name="setuju" value="1" /> ');
                        form.submit();
                    }
                });
            }
            else if(id == 2) {
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
                    if(result.value) {
                        form.append('<input type="hidden" name="setuju" value="2" /> ');
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

            if(text) {
                form.append('<input type="hidden" name="batal_ket" value="' + text + '" /> ');
                form.submit();
            }
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
                        <li class="breadcrumb-item">Pelatihan</li>
                        <li class="breadcrumb-item">Peserta</li>
                        <li class="breadcrumb-item active" aria-current="page">Data</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Overview -->
        <div class="row invisible" data-toggle="appear">
            <div class="col-md-2">
                <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-users fa-3x text-info"></i>
                            </div>
                            <div class="font-size-h4 font-w600">{{ $jadwal->kuota }} Orang</div>
                            <div class="text-muted">Kuota Peserta</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-user-check fa-3x text-success"></i>
                            </div>
                            <div class="font-size-h4 font-w600">{{ $pes_verif->count() }} Orang</div>
                            <div class="text-muted">Sudah di Verifikasi</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-user-edit fa-3x text-warning"></i>
                            </div>
                            <div class="font-size-h4 font-w600">{{ $pes_noverif->count() }} Orang</div>
                            <div class="text-muted">Belum di Verifikasi</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2">
                <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-user-minus fa-3x"></i>
                            </div>
                            <div class="font-size-h4 font-w600">{{ $pes_tolak->count() }} Orang</div>
                            <div class="text-muted">Verifikasi di Tolak</div>
                        </div>
                    </div>
                </a>
            </div>            
            <div class="col-md-2">
                <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-user-times fa-3x text-danger"></i>
                            </div>
                            <div class="font-size-h4 font-w600">{{ $pes_batal->count() }} Orang</div>
                            <div class="text-muted">Batal</div>
                        </div>
                    </div>
                </a>
            </div>            
        </div>
        <!-- END Overview -->
        <!-- Quick Menu -->
        <div class="pt-4 px-4 bg-body-dark rounded push">
            <div class="row row-deck">
                <div class="col-4 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.diklat.peserta.create', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-plus-circle text-success fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Tambah Peserta</p>
                        </div>
                    </a>
                </div>
                <div class="col-4 col-md-4 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;">
                        <div class="block-content">
                            <p class="mb-2 d-none d-sm-block">
                                <i class="fa fa-download text-gray fa-2x"></i>
                            </p>
                            <p class="font-w600 font-size-sm text-uppercase">Impor Peserta</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- END Quick Menu -->            
        <!-- Dynamic Table Full -->
        <div class="block block-bordered block-fx-shadow block-themed">
            <div class="block-header bg-success">
                <h3 class="block-title">Peserta - Sudah di Verifikasi</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 60px;">Foto</th>
                                <th style="width: 12%;">NIP</th>   
                                <th>Nama</th>                         
                                <th>Satker</th>
                                <th>Instansi</th>                                
                                <th style="width: 5%;">Aksi</th>
                                <th style="width: 5%;">Batal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_verif as $pv)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ is_null($pv->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pv->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                                </td>
                                <td class="font-w600">
                                    {{ $pv->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pv->nama_lengkap }}
                                </td>             
                                <td class="font-w600">
                                    {{ $pv->satker_nama }}
                                </td>          
                                <td class="font-w600">
                                    {{ $pv->instansi }}
                                </td>                                                                                             
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pv->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">   
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pv->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="far fa-trash-alt"></i>
                                            </a>                               
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.batal', $pv->id) }}" method="POST">
                                        @csrf
                                        <div class="btn-group">   
                                            <a href="javascript:;" onclick="return showBatal($(this).closest('form'));" class="btn btn-sm btn-danger" title="Batal"><i class="fa fa-times"></i></a>
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
        <!-- Dynamic Table Full -->
        <div class="block block-bordered block-fx-shadow block-themed">
            <div class="block-header bg-warning">
                <h3 class="block-title">Peserta - Belum di Verifikasi</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 60px;">Foto</th>
                                <th style="width: 12%;">NIP</th>   
                                <th>Nama</th>                         
                                <th>Satker</th>
                                <th>Instansi</th>
                                <th style="width: 5%;">Verifikasi</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_noverif as $pn)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ is_null($pn->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pn->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                                </td>
                                <td class="font-w600">
                                    {{ $pn->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pn->nama_lengkap }}
                                </td>             
                                <td class="font-w600">
                                    {{ $pn->satker_nama }}
                                </td>          
                                <td class="font-w600">
                                    {{ $pn->instansi }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.verifikasi', $pn->id) }}" method="POST">
                                        @csrf
                                        <div class="btn-group">   
                                            <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 1);" class="btn btn-sm btn-success" title="Setuju"><i class="fa fa-check"></i></a>
                                            <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 2);" class="btn btn-sm btn-danger" title="Tolak"><i class="fa fa-times"></i></a>
                                        </div>
                                    </form>
                                </td>                                
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pn->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')                                        
                                        <div class="btn-group">   
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pn->id]) }}" class="btn btn-sm btn-primary" title="Edit">
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
        <!-- Dynamic Table Full -->
        <div class="block block-bordered block-fx-shadow block-themed">
            <div class="block-header bg-danger">
                <h3 class="block-title">Peserta - Batal</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">#</th>
                                <th style="width: 60px;">Foto</th>
                                <th style="width: 12%;">NIP</th>   
                                <th>Nama</th>                         
                                <th>Satker</th>
                                <th>Instansi</th>
                                <th style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pes_batal as $pb)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ is_null($pb->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pb->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                                </td>
                                <td class="font-w600">
                                    {{ $pb->nip }}
                                </td>
                                <td class="font-w600">
                                    {{ $pb->nama_lengkap }}
                                </td>             
                                <td class="font-w600">
                                    {{ $pb->satker_nama }}
                                </td>          
                                <td class="font-w600">
                                    {{ $pb->instansi }}
                                </td>                                                             
                                <td class="text-center">
                                    <form action="{{ route('backend.diklat.peserta.destroy', $pb->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">   
                                            <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pb->id]) }}" class="btn btn-sm btn-primary" title="Edit">
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
