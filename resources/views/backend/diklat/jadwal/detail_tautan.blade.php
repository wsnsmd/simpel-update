@php
    $shortCode = substr(md5($jadwal->nama), 0, 6);
    $qrcode = route('jadwal.tautan', ['jadwal' => $jadwal->id, 'hash' => $shortCode]);
@endphp

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

        jQuery(function () {
            Dashmix.helpers(['validation']);
        });

        var action = '';
        var url_update = '';

        var loadTautan = () => {
            var data = {
                jadwal_id: {{ $jadwal->id }},
            };
            var url = "{{ route('backend.diklat.tautan.load') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function (res) {
                    $('#tautan-load').html(res);
                }
            });
        }

        jQuery(document).ready(function () {
            loadTautan();

            var form_tambah = $('#mdl-tambah-form').validate({
                messages: {
                    title: {
                        required: 'Titel tidak boleh kosong!'
                    },
                    url: {
                        required: 'Tautan tidak boleh kosong!'
                    }
                },
                submitHandler: function (form) {
                    if (action === 'add') {
                        request = $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{ route('backend.diklat.tautan.store') }}',
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
                    request.done(function (msg) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi(msg.pesan);
                        loadTautan();
                    });
                    // Called on failure.
                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        $('#mdl-tambah').modal('toggle');
                        showNotifikasi('Tautan gagal ditambah/disimpan!', 'danger');
                        // log the error to the console
                        console.error(
                            "The following error occurred: " + textStatus, errorThrown
                        );
                    });
                    return false;
                }
            })

            $('#mdl-tambah').on('hidden.bs.modal', function () {
                form_tambah.resetForm();
                $('input[name=title]').val('');
                $('textarea[name=url]').val('');
                $('select[name=is_active]').prop('selectedIndex', 0);
                $('.is-invalid').removeClass('is-invalid');
            });
        })

        function showTambah() {
            action = 'add';
            $("#mdl-form-title").html("Tambah Tautan");
            $('#mdl-tambah').modal('show');
        }

        function showEdit(id) {
            action = 'edit';

            var url = "{{ route('backend.diklat.tautan.show', ':id') }}";
            url_update = "{{ route('backend.diklat.tautan.update', ':id') }}";
            url = url.replace(':id', id);
            url_update = url_update.replace(':id', id);

            $('#mdl-form-title').html('Edit Tautan');

            $.ajax({
                type: 'GET',
                url: url,
                success: function (data) {
                    $('#title').val(data.title);
                    $('#url').val(data.url);
                    $('#is_active option[value="' + data.is_active + '"]').prop('selected', true);
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
                    console.log('data ' + id + ' dihapus ');
                    var data = {
                        id: id,
                    };
                    var url = "{{ route('backend.diklat.tautan.destroy', ':id') }}";
                    url = url.replace(':id', id);
                    console.log(url);
                    var request = $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: data,
                    });
                    request.done(function (msg) {
                        if (msg.status === 'success') {
                            showNotifikasi(msg.pesan);
                            loadTautan();
                        } else {
                            showNotifikasi(msg.pesan, 'danger');
                        }
                    });
                    request.fail(function (jqXHR, textStatus, errorThrown) {
                        showNotifikasi('Tautan gagal dihapus!', 'danger');
                        console.error(
                            "The following error occurred: " + textStatus, errorThrown
                        );
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
                placement: {
                    from: "top",
                    align: "center"
                }
            });
        }

        function copyText() {
            const txtUrl = $('#txtUrl');
            txtUrl.select();
            txtUrl[0].setSelectionRange(0, 99999);

            try {
                document.execCommand('copy');
                showNotifikasi('URL berhasil disalin');
            } catch (err) {
                showNotifikasi('URL gagal disalin', 'error');
            }

        }

    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Tautan</h1>
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
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="block block-rounded block-link-pop text-center d-flex align-items-center">
                        <div class="block-content">
                            <p class="mb-2 d-sm-block">
                                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->backgroundColor(255, 255, 255)->generate($qrcode)) !!}"
                                    class="img-fluid" style="height: 120px;">
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
    <!-- END Quick Menu -->

    <!-- Page Content -->
    <div class="content">
        <!-- Tautan-->
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Tautan</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="input-group mb-3">
                    <input type="text" value="{!! $qrcode !!}" class="form-control" id="txtUrl" readonly>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" onclick="copyText()" title="Salin Tautan">
                            <i class="fa fa-copy mr-1"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive" id='tautan-load'>
                </div>
            </div>
        </div>
        <!-- END Tautan -->

        <!-- Modal Tambah -->
        <div class="modal fade" id="mdl-tambah" tabindex="-1" role="dialog" aria-labelledby="mdl-tambah" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popin" role="document">
                <form id="mdl-tambah-form" method="POST" action="" autocomplete="off">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title" id="mdl-form-title">Tambah Tautan</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content" id="mdl-form-content">
                                <div class="form-group">
                                    <label for="title" class="control-label">Titel <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Titel..."
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="url" class="control-label">Tautan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="url" name="url" placeholder="Tautan..." rows="4"
                                        required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="is_active" class="control-label">Aktif <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="is_active" name="is_active" style="width: 100%;"
                                        required>
                                        @foreach (['1' => 'Ya', '0' => 'Tidak'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
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