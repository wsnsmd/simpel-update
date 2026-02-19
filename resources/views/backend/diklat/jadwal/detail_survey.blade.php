@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        jQuery(function () {
            Dashmix.helpers(['validation']);
        });

        var action = '';
        var url_update = '';

        var loadSurvey = () => {
            var data = { jadwal_id: {{ $jadwal->id }} };
            var url = "{{ route('backend.diklat.survei.load') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function (res) {
                    $('#survei-load').html(res);
                }
            });
        }

        $.validator.addMethod("jsonOrEmpty", function (value, element) {
            if (!value) return true;
            try { JSON.parse(value); return true; } catch (e) { return false; }
        }, "Params harus berupa JSON yang valid!");

        jQuery(document).ready(function () {
            loadSurvey();

            var form_tambah = $('#mdl-tambah-form').validate({
                rules: {
                    survey_name: { required: true },
                    survey_code: { required: true },
                    is_mandatory: { required: true },
                    params: { jsonOrEmpty: true }
                },
                messages: {
                    survey_name: { required: 'Nama survei tidak boleh kosong!' },
                    survey_code: { required: 'Kode survei tidak boleh kosong!' },
                    is_mandatory: { required: 'Wajib dipilih!' },
                    params: { jsonOrEmpty: 'Params harus berupa JSON yang valid!' }
                },
                submitHandler: function (form) {
                    let request;

                    if (action === 'add') {
                        request = $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{ route('backend.diklat.survei.store') }}',
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

                    request.done(function (msg) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi(msg.pesan);
                        loadSurvey();
                    });

                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi('Survei gagal ditambah/disimpan!', 'danger');
                        console.error("The following error occurred: " + textStatus, errorThrown);
                    });

                    return false;
                }
            });

            $('#mdl-tambah').on('hidden.bs.modal', function () {
                form_tambah.resetForm();
                $('input[name=survey_name]').val('');
                $('input[name=survey_code]').val('');
                $('textarea[name=params]').val('');
                $('select[name=is_mandatory]').prop('selectedIndex', 0);
                $('.is-invalid').removeClass('is-invalid');
            });
        });

        function showTambah() {
            action = 'add';
            $("#mdl-form-title").html("Tambah Survei");
            $('#mdl-tambah').modal('show');
        }

        function showEdit(id) {
            action = 'edit';

            var url = "{{ route('backend.diklat.survei.show', ':id') }}";
            url_update = "{{ route('backend.diklat.survei.update', ':id') }}";
            url = url.replace(':id', id);
            url_update = url_update.replace(':id', id);

            $('#mdl-form-title').html('Edit Survei');

            $.ajax({
                type: 'GET',
                url: url,
                success: function (data) {
                    $('#survey_name').val(data.survey_name);
                    $('#survey_code').val(data.survey_code);
                    $('#params').val(data.params || '');
                    $('#is_mandatory option[value="' + data.is_mandatory + '"]').prop('selected', true);
                    $('#mdl-tambah').modal('show');
                }
            });
        }

        function showHapus(id) {
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
                if (result.value) {
                    var data = { id: id };
                    var url = "{{ route('backend.diklat.survei.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var request = $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: data,
                    });

                    request.done(function (msg) {
                        if (msg.status === 'success') {
                            showNotifikasi(msg.pesan);
                            loadSurvey();
                        } else {
                            showNotifikasi(msg.pesan, 'danger');
                        }
                    });

                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        showNotifikasi('Survei gagal dihapus!', 'danger');
                        console.error("The following error occurred: " + textStatus, errorThrown);
                    });
                }
            });
        }

        function showNotifikasi(msg, type = 'success') {
            var icon = type === 'success' ? 'fa fa-check mr-1' : 'fa fa-times mr-1';

            $.notify({
                icon: icon,
                message: msg
            }, {
                allow_dismiss: false,
                type: type,
                placement: { from: "top", align: "center" }
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
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Survei</h1>
                    <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Jadwal</li>
                            <li class="breadcrumb-item">Detail</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
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
                    <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;"
                        onclick="showTambah()">
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
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Survei</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive" id="survei-load"></div>
            </div>
        </div>

        <!-- Modal Tambah/Edit -->
        <div class="modal fade" id="mdl-tambah" tabindex="-1" role="dialog" aria-labelledby="mdl-tambah" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-tambah-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title" id="mdl-form-title">Tambah Survei</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="survey_name" class="control-label">Nama Survei <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="survey_name" name="survey_name"
                                        placeholder="Nama Survei..." required>
                                </div>

                                <div class="form-group">
                                    <label for="survey_code" class="control-label">Kode Survei <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="survey_code" name="survey_code"
                                        placeholder="Kode Survei..." required>
                                    <small class="form-text text-muted">Contoh: SKM_2026_01 (hindari spasi).</small>
                                </div>

                                <div class="form-group">
                                    <label for="params" class="control-label">Params (JSON)</label>
                                    <textarea class="form-control" id="params" name="params"
                                        placeholder='Contoh: {"nama_widyaiswara":"Bahrun"}' rows="4"></textarea>
                                    <small class="form-text text-muted">
                                        Opsional. Jika diisi harus JSON valid.
                                    </small>
                                </div>


                                <div class="form-group">
                                    <label for="is_mandatory" class="control-label">Wajib <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="is_mandatory" name="is_mandatory" style="width: 100%;"
                                        required>
                                        @foreach (['1' => 'Ya', '0' => 'Tidak'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="block-content block-content-full text-right bg-light">
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary btn-submit">Simpan</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <!-- END Modal -->
    </div>
    <!-- END Page Content -->
@endsection