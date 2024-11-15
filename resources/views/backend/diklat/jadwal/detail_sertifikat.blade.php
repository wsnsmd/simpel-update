@php
    $colSatker = "Satuan Kerja";
    $isASN = true;
    if(stripos($jadwal->nama, 'DPRD') !== false)
    {
        $colSatker = "Partai";
        $isASN = false;
    }
@endphp
@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['datepicker', 'validation', 'select2']);
        });

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
            jQuery.extend(jQuery.validator.messages, {
                required: "Wajib diisi.",
            });

            var form_buat = $('#mdl-buat-form').validate({
                errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}
				},
                highlight: function ( element, errorClass, validClass ) {
					$( element ).addClass( "is-invalid" ).removeClass( "valid" );
				},
				unhighlight: function (element, errorClass, validClass) {
					$( element ).addClass( "valid" ).removeClass( "is-invalid" );
				}
            });

            $('#mdl-buat').on('hidden.bs.modal', function() {
                form_buat.resetForm();
                $('#mdl-buat-form').trigger('reset');
                // $('textarea[name=keterangan]').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('#generate').addClass('d-none');
            });

            $('#mdl-simpeg').on('hidden.bs.modal', function() {
                $('#mdl-simpeg-form').trigger('reset');
                $('#simpeg_kategori_id').empty();
                $('#simpeg_sub_kategori_id').empty();
                var option1 = document.createElement("option");
                var option2 = document.createElement("option");
                option1.text = '-- Pilih Jenis Dulu --';
                $('#simpeg_kategori_id').append(option1);
                option2.text = '-- Pilih Kategori Dulu --';
                $('#simpeg_sub_kategori_id').append(option2);
            });

            $('#is_generate').on('change', function () {
                if($('#is_generate').val() == true) {
                    $('#generate').removeClass('d-none');
                }
                else {
                    $('#generate').addClass('d-none');
                }
            });

            $('#simpeg_jenis').on('change', function () {
                $('#simpeg_kategori_id').empty();
                $('#simpeg_sub_kategori_id').empty();
                id = $('#simpeg_jenis').val();
                var url_kategori = "{{ route('backend.diklat.sertifikat.simasn.kategori', ':id') }}";
                url_kategori = url_kategori.replace(':id', id);

                $.ajax({
                type: 'GET',
                url: url_kategori,
                success: function(data) {
                    data.forEach(function(item) {
                        var option = document.createElement("option");
                        option.value = item.id;
                        option.text = item.kategori_diklat;
                        $('#simpeg_kategori_id').append(option);
                    });
                    if($('#simpeg_jenis').val() == 1) {
                        $('#simpeg_sub_kategori_id').empty();
                        var text_kategori = $('#simpeg_kategori_id option:selected').text();
                        var option = document.createElement("option");
                        option.value = $('#simpeg_kategori_id').val();
                        option.text = text_kategori;
                        $('#simpeg_sub_kategori_id').append(option);
                    }
                }
            });

                if($('#simpeg_jenis').val() == 2) {
                    var url_sub = "{{ route('backend.diklat.sertifikat.simasn.subkategori') }}";
                    $.ajax({
                        type: 'GET',
                        url: url_sub,
                        success: function(data) {
                            data.forEach(function(item) {
                                var option = document.createElement("option");
                                option.value = item.id;
                                option.text = item.sub_kategori_diklat;
                                $('#simpeg_sub_kategori_id').append(option);
                            });
                        }
                    });
                }
            })

            $('#simpeg_kategori_id').on('change', function () {
                if($('#simpeg_jenis').val() == 1) {
                    $('#simpeg_sub_kategori_id').empty();
                    var text_kategori = $('#simpeg_kategori_id option:selected').text();
                    var option = document.createElement("option");
                    option.value = $('#simpeg_kategori_id').val();
                    option.text = text_kategori;
                    $('#simpeg_sub_kategori_id').append(option);
                }
            })
        })

        function showBuat() {
            action = 'add';
            $('#mdl-buat').modal('show');
        }

        function showSpesimen(id) {
            $('#mdl-pos-spesimen').modal('show');
            var url = "{{ route('backend.diklat.sertifikat.spesimen.posisi', ':id') }}";
            var url_update = "{{ route('backend.diklat.sertifikat.spesimen.posisi.update', ':id') }}";
            url = url.replace(':id', id);
            url_update = url_update.replace(':id', id);
            $('#mdl-pos-spesimen-form').attr('action', url_update);

            $.ajax({
                type: 'GET',
                url: url,
                success: function(data) {
                    $('#bawah').val(data.spesimen_bawah);
                    $('#kiri').val(data.spesimen_kiri);
                    $('#bawah2').val(data.spesimen2_bawah);
                    $('#kiri2').val(data.spesimen2_kiri);
                    $('#mdl-pos-spesimen').modal('show');
                }
            });
        }

        function showUpload(id) {
            $('#mdl-upload').modal('show');
            var url_upload = "{{ route('backend.diklat.sertifikat.upload', ':id') }}";
            url_upload = url_upload.replace(':id', id);
            $('#mdl-upload-form').attr('action', url_upload);
            console.log(id);
        }

        function showSimpeg() {
            $('#mdl-simpeg').modal('show');
        }

        function showTesEmail() {
            $('#mdl-tes-email').modal('show');
        }
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Sertifikat</h1>
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
    @if(Gate::check('isCreator', $jadwal) && Gate::check('isUser'))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            @if(!$sertifikat)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showBuat()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Buat Sertifikat</p>
                    </div>
                </a>
            </div>
            @else
            @if(!$sertifikat->is_final)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.diklat.sertifikat.edit', ['id' => $sertifikat->id, 'jadwal' => $jadwal->id])}}">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-edit text-primary fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Edit Sertifikat</p>
                    </div>
                </a>
            </div>
            @if($sertifikat->import)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('export-template').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-upload text-info fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Template Peserta</p>
                    </div>
                </a>
                <form id="export-template" action="{{ route('backend.diklat.sertifikat.template.peserta') }}" method="post" style="display: none;" target="_template">
                    @csrf
                    <input type="hidden" name="export" value="1">
                    <input type="hidden" name="sertifikat_id" value="{{ $sertifikat->id}}">
                </form>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showImport()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-download text-warning fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Impor Peserta</p>
                    </div>
                </a>
            </div>
            @else
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('buat-peserta-form').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Buat Sertifikat Peserta</p>
                    </div>
                </a>
                <form id="buat-peserta-form" action="{{ route('backend.diklat.sertifikat.buat.peserta', ['jadwal' => $jadwal->id]) }}" method="post" style="display: none;">
                    @csrf
                </form>
            </div>
            @endif
            @endif
            @if($sertifikat && $sertifikat->is_final)
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showSimpeg()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-users text-info fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Upload SIMASN BKD</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('email-template-form').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-envelope-open-text text-danger fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Template Email</p>
                    </div>
                </a>
                <form id="email-template-form" action="{{ route('backend.diklat.sertifikat.email.template', ['jadwal' => $jadwal->id]) }}" method="get" style="display: none;">
                    @csrf
                </form>
            </div>
            @if(!is_null($email))
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('kirim-email-form').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-mail-bulk text-secondary fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Kirim Email</p>
                    </div>
                </a>
                <form id="kirim-email-form" action="{{ route('backend.diklat.sertifikat.kirim.email', ['jadwal' => $jadwal->id]) }}" method="post" style="display: none;">
                    @csrf
                    <input type="hidden" name="sertifikat_id" value="{{ $sertifikat->id }}" />
                </form>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showTesEmail()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-paper-plane text-warning fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tes Kirim Email</p>
                    </div>
                </a>
            </div>
            @endif
            @if(Gate::check('isAdmin'))
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="return showAlert(document.getElementById('hapus-form'));">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-trash text-danger fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Hapus</p>
                    </div>
                </a>
                <form id="hapus-form" action="{{ route('backend.diklat.sertifikat.destroy', $sertifikat->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            @endif
            @endif
            @endif
        </div>
    </div>
    @endif
    <!-- END Quick Menu -->

    <!-- Page Content -->
    <div class="content">
        @if($sertifikat)
        <!-- Sertifikat Peserta -->
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Sertifikat</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="font-w700 text-center" style="width: 30px; vertical-align: middle;" rowspan="2">#</th>
                                <th class="font-w700 text-center" style="width: 60px; vertical-align: middle;" rowspan="2">Nomor</th>
                                @if($isASN)
                                <th class="font-w700 text-center" style="width: 12%; vertical-align: middle;" rowspan="2">NIP</th>
                                <th class="font-w700 text-center" style="vertical-align: middle;" rowspan="2">ASN</th>
                                @endif
                                <th class="font-w700 text-center" style="vertical-align: middle;" rowspan="2">Nama</th>
                                <th class="font-w700 text-center" style="vertical-align: middle;" rowspan="2">{{ $colSatker }}</th>
                                <th class="font-w700 text-center" style="vertical-align: middle;" rowspan="2">Instansi</th>
                                <th class="font-w700 text-center" colspan="2">Status Kirim</th>
                                <th class="font-w700 text-center" style="width: 10%; vertical-align: middle;" rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th class="font-w700 text-center">Simpeg</th>
                                <th class="font-w700 text-center">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sertPeserta as $sp)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{-- <img src="{{ is_null($sp->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($sp->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;"> --}}
                                    {{ $sp->nomor }}
                                </td>
                                @if($isASN)
                                <td class="font-w600">
                                    {{ $sp->nip }}
                                </td>
                                <td class="font-w600">
                                    @if($sp->status_asn == 1)
                                        PNS
                                    @elseif($sp->status_asn == 2)
                                        PPPK
                                    @else
                                        Non-PNS
                                    @endif
                                </td>
                                @endif
                                <td class="font-w600">
                                    {{ $sp->nama_lengkap }}
                                </td>
                                <td class="font-w600">
                                    {{ $sp->satker_nama }}
                                </td>
                                <td class="font-w600">
                                    {{ $sp->instansi }}
                                </td>
                                <td class="font-w600 text-center">
                                    @if(is_null($sp->simpeg_at))
                                    <span class="badge badge-danger">Belum</span>
                                    @else
                                    <span class="badge badge-success">Sudah</span>
                                    @endif
                                </td>
                                <td class="font-w600">
                                    @if(is_null($sp->email_at))
                                    <span class="badge badge-danger">Belum</span>
                                    @else
                                    <span class="badge badge-success">Sudah</span>
                                    @endif
                                </td>
                                <td class="text-center fs-base">
                                    <div class="d-flex justify-content-center">
                                    @if($sertifikat->is_generate)
                                        <form action="{{ route('backend.diklat.sertifikat.cetak', $sp->spid) }}" method="POST" target="_cetak">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" title="Cetak">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </form>
                                        @if(!is_null($sertifikat->tsid))
                                            <button type="button" class="btn btn-sm btn-info mx-1" title="Posisi Spesimen" onclick="showSpesimen({{$sp->spid}})">
                                                <i class="fa fa-file-alt"></i>
                                            </button>
                                        @endif
                                    @endif
                                    @if($sertifikat->is_upload)
                                        <button type="button" class="btn btn-sm btn-warning" title="Upload" onclick="showUpload({{$sp->spid}})">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                        @if (!is_null($sp->upload))
                                        <a href="{{ asset(\Storage::url($sp->upload)) }}" class="btn btn-sm btn-success mx-1" title="Lihat" target="_blank">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endif
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Peserta Sertifikat -->

        <!-- Modal Posisi Spesimen -->
        <div class="modal fade" id="mdl-pos-spesimen" tabindex="-1" role="dialog" aria-labelledby="mdl-pos-spesimen" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-pos-spesimen-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Posisi Spesimen</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="kiri" class="control-label">Kiri-1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kiri" name="kiri" placeholder="10.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="bawah" class="control-label">Bawah-1 <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="bawah" name="bawah" placeholder="5.5" required>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="kiri2" class="control-label">Kiri-2</label>
                                        <input type="text" class="form-control" id="kiri2" name="kiri2" placeholder="10.0">
                                    </div>
                                    <div class="col-6">
                                        <label for="bawah2" class="control-label">Bawah-2</label>
                                        <input type="text" class="form-control" id="bawah2" name="bawah2" placeholder="5.5">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <div class="custom-control custom-checkbox custom-control-primary mb-1">
                                            <input class="custom-control-input" type="checkbox" value="" id="spesimen_semua" name="spesimen_semua">
                                            <label class="custom-control-label" for="spesimen_semua">Semua</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Modal Posisi Spesimen -->

        @if($sertifikat->is_upload)
        <!-- Modal Upload -->
        <div class="modal fade" id="mdl-upload" tabindex="-1" role="dialog" aria-labelledby="mdl-upload" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-upload-form" method="POST" action="" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Upload Sertifikat</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="file" class="control-label">File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="file" name="file" accept="application/pdf" required>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Modal Upload -->
        @endif

        <!-- Modal Simpeg -->
        <div class="modal fade" id="mdl-simpeg"role="dialog" aria-labelledby="mdl-simpeg" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
                <form id="mdl-simpeg-form" method="POST" action="{{ route('backend.diklat.sertifikat.kirim.simpeg') }}" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Upload SIMASN BKD</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="simpeg_jenis" class="control-label">Jenis Pelatihan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="simpeg_jenis" name="jenis" style="width: 100%;" required>
                                        <option value="" selected>-- Pilih Jenis --</option>
                                        <option value="1">Struktural</option>
                                        <option value="2">Non Struktural</option>
                                    </select>
                                </div>
                                <div id="simpeg_kategori" class="option-target">
                                    <div class="form-group">
                                        <label for="simpeg_kategori_id" class="control-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-control" id="simpeg_kategori_id" name="kategori" style="width: 100%;">
                                            <option value="" selected>-- Pilih Jenis Dulu --</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="simpeg_sub_kategori" class="option-target">
                                    <div class="form-group">
                                        <label for="simpeg_sub_kategori_id" class="control-label">Sub Kategori <span class="text-danger">*</span></label>
                                        <select class="js-select2 form-control" id="simpeg_sub_kategori_id" name="sub_kategori" style="width: 100%;">
                                            <option value="">-- Pilih Kategori Dulu --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Modal Simpeg -->

        @if($sertifikat->import)
        <!-- Modal Import -->
        <div class="modal fade" id="mdl-import"role="dialog" aria-labelledby="mdl-import" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-popin" role="document">
                <form id="mdl-import-form" method="POST" action="{{ route('backend.diklat.sertifikat.import.peserta') }}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Import Peserta</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="import_file" class="control-label">Template Peserta<span class="text-danger">*</span></label>
                                    <input type="file" id="import_file" name="import_file" class="form-control" required>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Modal Import -->
        <script>
            function showImport() {
                $('#mdl-import').modal('show');
            }
        </script>
        @endif

        @if(!is_null($email))
        <!-- Modal Tes Kirim Email -->
        <div class="modal fade" id="mdl-tes-email"role="dialog" aria-labelledby="mdl-tes-email" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-popin" role="document">
                <form id="mdl-tes-email-form" method="POST" action="{{ route('backend.diklat.sertifikat.kirim.email', ['jadwal' => $jadwal->id]) }}" autocomplete="off">
                    @csrf
                    <input type="hidden" name="sertifikat_id" value="{{ $sertifikat->id }}" />
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Tes Kirim Email</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="email_alamat" class="control-label">Alamat Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email_alamat" name="email_alamat" placeholder="xyz@domain.com" required>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Kirim</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Tes Kirim Email -->
         @endif

        @else
        <!-- Modal Buat -->
        <div class="modal fade" id="mdl-buat" tabindex="-1" role="dialog" aria-labelledby="mdl-buat" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
                <form id="mdl-buat-form" method="POST" action="{{ route('backend.diklat.sertifikat.store') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title" id="mdl-form-title">Buat Sertifikat</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="is_generate" class="control-label">Generate Otomatis <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_generate" name="is_generate" style="width: 100%;" required>
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="is_upload" class="control-label">Upload <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_upload" name="is_upload" style="width: 100%;" required>
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="is_import" class="control-label">Import <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_import" name="is_import" style="width: 100%;" required>
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="tempat" class="control-label">Tempat Sertifikat <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Cth. Samarinda..." required>
                                    </div>
                                    <div class="col-6">
                                        <label for="tanggal" class="control-label">Tanggal Sertifikat <span class="text-danger">*</span></label>
                                        <input type="text" class="js-datepicker form-control" id="tanggal" name="tanggal" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                                    </div>
                                </div>
                                <div id="generate" class="option-target d-none">
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="tsid" class="control-label">Template <span class="text-danger">*</span></label>
                                            <select class="form-control" id="tsid" name="tsid" style="width: 100%;" required>
                                                <option value="" selected>-- Pilih Template --</option>
                                                @foreach ($template as $tp)
                                                <option value="{{ $tp->id }}">{{ $tp->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="format_nomor" class="control-label">Format Nomor</span></label>
                                            <input type="text" class="form-control" id="format_nomor" name="format_nomor" placeholder="Cth. {N}/{m}/{y}" value="">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="barcode" class="control-label">Barcode <span class="text-danger">*</span></label>
                                            <select class="form-control" id="barcode" name="barcode" style="width: 100%;" required>
                                                <option value="" selected>-- Pilih Barcode --</option>
                                                <option value="0">Tidak</option>
                                                <option value="1">Ya</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="kualifikasi" class="control-label">Kualifikasi <span class="text-danger">*</span></label>
                                            <select class="form-control" id="kualifikasi" name="kualifikasi" style="width: 100%;" required>
                                                <option value="" selected>-- Pilih Kualifikasi --</option>
                                                <option value="0">Tidak</option>
                                                <option value="1">Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="fasilitasi" class="control-label">Fasilitasi</label>
                                            <input type="text" class="form-control" id="fasilitasi" name="fasilitasi" placeholder="Fasilitasi..." value="">
                                        </div>
                                    </div>
                                    <h2 class="content-heading pt-0 mb-3">Pejabat Penandatangan-1</h2>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="jabatan" class="control-label">Jabatan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Cth. Kepala, Plt. Kepala..." required>
                                        </div>
                                        <div class="col-6">
                                            <label for="nip" class="control-label">NIP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP..." min="18" maxlength="18" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="nama" class="control-label">Nama <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama..." required>
                                        </div>
                                        <div class="col-6">
                                            <label for="pangkat" class="control-label">Pangkat <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Pangkat..." required>
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="spesimen" class="control-label">Spesimen</label>
                                            <input type="file" class="form-control" id="spesimen" name="spesimen" accept="image/png">
                                        </div>
                                    </div>
                                    <h2 class="content-heading pt-0 mb-3">Pejabat Penandatangan-2</h2>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="jabatan2" class="control-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan2" name="jabatan2" placeholder="Cth. Kepala Bidang...">
                                        </div>
                                        <div class="col-6">
                                            <label for="nip2" class="control-label">NIP</label>
                                            <input type="text" class="form-control" id="nip2" name="nip2" placeholder="NIP..." min="18" maxlength="18">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="nama2" class="control-label">Nama</label>
                                            <input type="text" class="form-control" id="nama2" name="nama2" placeholder="Nama...">
                                        </div>
                                        <div class="col-6">
                                            <label for="pangkat2" class="control-label">Pangkat</label>
                                            <input type="text" class="form-control" id="pangkat2" name="pangkat2" placeholder="Pangkat...">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="spesimen2" class="control-label">Spesimen</label>
                                            <input type="file" class="form-control" id="spesimen2" name="spesimen2" accept="image/png">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Modal Buat -->
        @endif
    </div>
    <!-- END Page Content -->
@endsection
