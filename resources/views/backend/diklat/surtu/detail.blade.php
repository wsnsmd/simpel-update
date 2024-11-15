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
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const loadPegawai = () => {
            var data = {
                sid: {{ $surtu->id }},
            };
            var url = "{{ route('backend.diklat.surtu.load.pegawai') }}";
            $.ajax({
                type: 'post',
                url: url,
                data: data,
                success: function (res) {
                    $('#surtu-pegawai').html(res);
                }
            });
        }

        jQuery(function(){
            Dashmix.helpers(['core-bootstrap-tabs', 'datepicker', 'maxlength']);
            CKEDITOR.replace('dasar');
            CKEDITOR.replace('untuk');

            jQuery(document).ready(function () {
                loadPegawai();
                $('#pegawai').select2({
                    dropdownParent: $("#mdl-pegawai"),
                    placeholder: "-- Pilih Pegawai --",
                    // minimumInputLength: 3,
                    ajax: {
                        url: '{{ route("ajax.pegawai") }}',
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

                $('#mdl-pegawai-form').validate({
                    messages: {
                        pegawai: {
                            required: 'Pegawai tidak boleh kosong!'
                        }
                    },
                    submitHandler: function(form) {
                        request = $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{ route('backend.diklat.surtu.pegawai.add') }}',
                            data: $(form).serialize(),
                            timeout: 3000
                        });
                        // Called on success.
                        request.done(function(msg) {
                            $('#mdl-pegawai').modal('toggle');
                            showNotifikasi(msg.pesan);
                            loadPegawai();
                        });
                        // Called on failure.
                        request.fail(function (jqXHR, textStatus, errorThrown) {
                            $('#mdl-pegawai').modal('toggle');
                            showNotifikasi('Pegawai gagal ditambah/disimpan!', 'danger');
                            // log the error to the console
                            console.error(
                                "The following error occurred: " + textStatus, errorThrown
                            );
                        });
                        return false;
                    }
                })

                $('#mdl-cetak-form').submit(function () {
                    $('#mdl-cetak').modal('toggle');
                })

                $('#mdl-pegawai').on('hidden.bs.modal', function() {
                    $("#pegawai").val('').trigger('change');
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
        });

        const showPegawai = () =>
        {
            $('#mdl-pegawai').modal('show');
        }

        const showCetak = () => {
            $('#mdl-cetak').modal('show');
        }

        const showHapus = (id) => {
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
                    var data = {
                        id: id,
                    };
                    var url = "{{ route('backend.diklat.surtu.pegawai.del', ':id') }}";
                    url = url.replace(':id', id);
                    var request = $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: data,
                    });
                    request.done(function(msg) {
                        if(msg.status === 'success') {
                            showNotifikasi(msg.pesan);
                            loadPegawai();
                        } else {
                            showNotifikasi(msg.pesan, 'danger');
                        }
                    });
                    request.fail(function (jqXHR, textStatus, errorThrown){
                        showNotifikasi('Data pegawai gagal dihapus!', 'danger');
                        console.error(
                            "The following error occurred: " + textStatus, errorThrown
                        );
                    });
                }
            });
        }

        const showNotifikasi = (msg, type='success') => {
            var icon = type === 'success' ? 'fa fa-check mr-1' : 'fa fa-times mr-1';

            $.notify({
                icon: icon,
                message: msg
            }, {
                allow_dismiss: false,
                type: type,
                placement: {
                    from: "top",
                    align: "center"
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
                <h3 class="block-title">Surat Tugas - {{$surtu->keterangan}}</h3>
            </div>
            <div class="block-content block-content-full">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissable" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <p class="mb-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </p>
                </div>
                @endif
                <div class="row mb-3 border-bottom">
                    <div class="col-lg-12">
                        <div class="block block-rounded block-bordered">
                            <form action="{{ route('backend.diklat.surtu.update', $surtu->id) }}" method="POST" enctype="multipart/form-data" autocomplete="on" id="form-surat">
                            @csrf
                            @method('PATCH')
                            <ul class="nav nav-tabs-alt nav-tabs-block" data-toggle="tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#btabs-konten">Konten</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#btabs-ttangan">Tanda Tangan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#btabs-paraf">Paraf</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#btabs-pegawai">Pegawai</a>
                                </li>
                            </ul>
                            <div class="block-content tab-content overflow-hidden">
                                <div class="tab-pane fade active show" id="btabs-konten" role="tabpanel">
                                    <div class="form-group">
                                        <label for="tipe" class="control-label">Tipe <span class="text-danger">*</span></label>
                                        <select class="form-control" id="tipe" name="tipe" style="width: 100%;" required>
                                            <option value="" selected>-- Pilih Tipe --</option>
                                            <option value="individu" {{ $surtu->tipe === 'individu' ? 'selected':'' }}>Individu</option>
                                            <option value="panitia" {{ $surtu->tipe === 'panitia' ? 'selected':'' }}>Team/Panitia</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="keterangan" class="control-label">Keterangan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan..." required>{{ $surtu->keterangan }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="dasar" class="control-label">Dasar</label>
                                        <textarea class="form-control" id="dasar" name="dasar" placeholder="Dasar...">{{ $surtu->dasar }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="untuk" class="control-label">Untuk</label>
                                        <textarea class="form-control" id="untuk" name="untuk" placeholder="Untuk...">{{ $surtu->untuk }}</textarea>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="btabs-ttangan" role="tabpanel">
                                    <div class="form-group">
                                        <label for="an" class="control-label">Atas Nama</label>
                                        <select class="form-control" id="an" name="an" style="width: 100%;">
                                            <option value="" selected>-- Pilih Atas Nama --</option>
                                            <option value="0" {{ $surtu->an === 0 ? 'selected':'' }}>Tidak</option>
                                            <option value="1" {{ $surtu->an === 1 ? 'selected':'' }}>Ya</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat" class="control-label">Tempat</label>
                                        <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Cth. Samarinda..." value="{{ $surtu->tempat }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal" class="control-label">Tanggal</label>
                                        <input type="text" class="js-datepicker form-control" id="tanggal" name="tanggal" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="{{ $surtu->tanggal }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="jabatan" class="control-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Cth. Kepala, Plt. Kepala..." value="{{ $surtu->jabatan }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nip" class="control-label">NIP</label>
                                        <input class="js-maxlength form-control" type="text" id="nip" name="nip" placeholder="NIP..." minlength="18" maxlength="18" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" value="{{ $surtu->nip }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama" class="control-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama..." value="{{ $surtu->nama }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="pangkat" class="control-label">Pangkat</label>
                                        <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Pangkat..." value="{{ $surtu->pangkat }}">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="btabs-paraf" role="tabpanel">
                                    <div class="form-group">
                                        <label for="paraf1_nama" class="control-label">Paraf 1 - Nama</label>
                                        <input type="text" class="form-control" id="paraf1_nama" name="paraf1_nama" placeholder="Paraf 1 - Nama..." value="{{ $surtu->paraf1_nama }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="paraf1_jabatan" class="control-label">Paraf 1 - Jabatan</label>
                                        <input type="text" class="form-control" id="paraf1_jabatan" name="paraf1_jabatan" placeholder="Paraf 1 - Jabatan..." value="{{ $surtu->paraf1_jabatan }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="paraf2_nama" class="control-label">Paraf 2 - Nama</label>
                                        <input type="text" class="form-control" id="paraf2_nama" name="paraf2_nama" placeholder="Paraf 2 - Nama..." value="{{ $surtu->paraf2_nama }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="paraf2_jabatan" class="control-label">Paraf 2 - Jabatan</label>
                                        <input type="text" class="form-control" id="paraf2_jabatan" name="paraf2_jabatan" placeholder="Paraf 2 - Jabatan..." value="{{ $surtu->paraf2_jabatan }}">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="btabs-pegawai" role="tabpanel">
                                    <div class="row mb-3">
                                        <div class="col-lg-12">
                                            <div class="float-left">
                                                <a href="javascript:;" class="btn btn-primary btn-square" onclick="showPegawai()">
                                                    <i class="fas fa-plus-circle mr-2"></i>Tambah</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id='surtu-pegawai'>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="float-left">
                            <button type="submit" value="simpan" class="btn btn-square btn-primary" form="form-surat">
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                            <button type="button" value="cetak" class="btn btn-secondary btn-square" onclick="showCetak()">
                                <i class="fas fa-print mr-2"></i>Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Jadwal -->

        <!-- Pegawai Modal -->
        <div class="modal fade" id="mdl-pegawai" tabindex="-1" role="dialog" aria-labelledby="mdl-pegawai" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-pegawai-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <input type="hidden" id="sid" name="sid" value="{{ $surtu->id }}" />
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-wi-title">Tambah Pegawai</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="pegawai" class="control-label">Pegawai</label>
                                        <select class="form-control" id="pegawai" name="pegawai" style="width: 100%;">
                                        </select>
                                    </div>
                                    @if($surtu->tipe === 'panitia')
                                    <div class="form-group">
                                        <label for="keterangan" class="control-label">Jabatan Team/Kepanitian</label>
                                        <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Jabatan Kepanitian...">
                                        </select>
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
        <!-- END Pegawai Modal -->

        <!-- Cetak Modal -->
        <div class="modal fade" id="mdl-cetak" tabindex="-1" role="dialog" aria-labelledby="mdl-cetak" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form action="{{ route('backend.diklat.surtu.cetak', ['id' => $surtu->id]) }}" method="POST" id="mdl-cetak-form" target="_cetak">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title">Cetak</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group">
                                        <label for="template" class="control-label">Template</label>
                                        <select class="form-control" id="template" name="template" style="width: 100%;" required>
                                            <option value="" selected>-- Pilih Template --</option>
                                            <option value="1">Template 1</option>
                                            <option value="2">Template 2</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bawah" class="control-label">Margin Bawah</label>
                                        <input id="bawah" name="bawah" type="number" class="form-control" min="0" max="10" step=".1" value="2.5">
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-right bg-light">
                                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Cetak</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <!-- END Pegawai Modal -->

    </div>
    <!-- END Page Content -->
@endsection
