@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['validation']);
        });

        var action = '';
        var url_update = '';

        var loadSurtu = () => {
            var data = {
                jadwal_id: {{ $jadwal->id }},
            };
            var url = "{{ route('backend.diklat.surtu.load') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function (res) {
                    $('#surtu-load').html(res);
                }
            });
        }

        jQuery(document).ready(function () {
            loadSurtu();

            var form_tambah = $('#mdl-tambah-form').validate({
                messages: {
                    tipe: {
                        required: 'Tidak boleh kosong!'
                    },
                    keterangan: {
                        required: 'Tidak boleh kosong!'
                    }
                },
                submitHandler: function(form) {
                    if(action === 'add') {
                        request = $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{ route('backend.diklat.surtu.store') }}',
                            data: $(form).serialize(),
                            timeout: 3000
                        });
                    } else {
                        request = $.ajax({
                            type: 'PATCH',
                            cache: false,
                            url: url_update,
                            data: $(form).serialize(),
                            timeout: 3000
                        });
                    }
                    // Called on success.
                    request.done(function(msg) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi(msg.pesan);
                        loadSurtu();
                    });
                    // Called on failure.
                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi('Surat Tugas gagal ditambah/disimpan!', 'danger');
                        // log the error to the console
                        console.error(
                            "The following error occurred: " + textStatus, errorThrown
                        );
                    });
                    return false;
                }
            })

            $('#mdl-tambah').on('hidden.bs.modal', function() {
                form_tambah.resetForm();
                $('select[name=tipe]').val('');
                $('textarea[name=keterangan]').val('');
                $('.is-invalid').removeClass('is-invalid');
            });
        })

        function showTambah() {
            action = 'add';
            $("#mdl-form-title").html("Tambah Surat Tugas");
            $('#mdl-tambah').modal('show');
        }

        function showNotifikasi(msg, type='success') {
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
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Surat Tugas</h1>
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
    @if(Gate::check('isCreator', $jadwal) || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showTambah()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-plus-circle text-success fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tambah</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif
    <!-- END Quick Menu -->

    <!-- Page Content -->
    <div class="content">
        <!-- Surat Tugas-->
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Surat Tugas</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive" id='surtu-load'>
                </div>
            </div>
        </div>
        <!-- END Surat Tugas -->

        <!-- Modal Tambah -->
        <div class="modal fade" id="mdl-tambah" tabindex="-1" role="dialog" aria-labelledby="mdl-tambah" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-tambah-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title" id="mdl-form-title">Tambah Surat Tugas</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content" id="mdl-form-content">
                                    <div class="form-group">
                                        <label for="tipe" class="control-label">Tipe <span class="text-danger">*</span></label>
                                        <select class="form-control" id="tipe" name="tipe" style="width: 100%;" required>
                                            <option value="" selected>-- Pilih Tipe --</option>
                                            <option value="individu">Individu</option>
                                            <option value="panitia">Team/Panitia</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="keterangan" class="control-label">Keterangan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan..." required></textarea>
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
        <!-- END Modal Tambah -->
    </div>
    <!-- END Page Content -->
@endsection
