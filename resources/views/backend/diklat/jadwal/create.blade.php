@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers(['datepicker', 'select2']);
            CKEDITOR.replace('deskripsi');
            CKEDITOR.replace('syarat');
        });

        $('.js-select2-min').select2({
            minimumResultsForSearch: Infinity
        });

        $('#jenis_diklat').on('select2:select', function (e) {
            var data = e.params.data;
            var current = $('#kurikulum');
            // var value = '';

            if(data.id != '') {
                var url = '{{ route('backend.diklat.kurikulum.get', ':id') }}';
                url = url.replace(':id', data.id);

                $.get(url, function (response) {
                    console.log(response);
                    if(response) {
                        current.prop('disabled', false);
                        current.html('<option value="">-- Pilih Kurikulum --');
                        $.each(response, function (i, obj) {
                            // var selected = (value && value == obj.select_value) ? "selected" : "";
                            $('<option value="' + obj.id + '">' + obj.nama + '</option>').appendTo("#kurikulum");
                        })
                        current.trigger('change');
                    }
                });
            }
            else {
                current.prop('disabled', true);
            }
        });

        $('#registrasi').on('select2:select', function (e) {
            var data = e.params.data;
            var reg_awal = $('#reg_awal');
            var reg_akhir = $('#reg_akhir');

            if(data.id == 1) {
                reg_awal.prop('disabled', false);
                reg_akhir.prop('disabled', false);
                reg_awal.prop('required', true);
                reg_akhir.prop('required', true);
            }
            else {
                reg_awal.prop('disabled', true);
                reg_akhir.prop('disabled', true);
                reg_awal.removeAttr('required');
                reg_akhir.removeAttr('required');
            }
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
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.diklat.jadwal.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Jadwal - Tambah Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.diklat.jadwal.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama">Nama Pelatihan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama Pelatihan..." value="{{ old('nama') }}" required>
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jenis_diklat">Jenis <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('jenis_diklat') ? ' is-invalid' : '' }}" id="jenis_diklat" name="jenis_diklat" style="width: 100%" required>
                                <option value="" selected>-- Pilih Jenis Pelatihan --</option>
                                @foreach ($jdiklat as $jd)
                                <option value="{{ $jd->id }}" {{ old('jenis_diklat') == $jd->id ? "selected":"" }}>{{ $jd->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('jenis_diklat'))
                            <div class="invalid-feedback">{{ $errors->first('jenis_diklat') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="tipe">Tipe <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('tipe') ? ' is-invalid' : '' }}" id="tipe" name="tipe" style="width: 100%" required>
                                <option value="" selected>-- Pilih Tipe --</option>
                                <option value="Bimtek">Bimtek</option>
                                <option value="Pelatihan">Pelatihan</option>
                                <option value="Ujian">Ujian</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar/Konferensi/Sarasehan</option>
                                <option value="Lokakarya">Lokakarya</option>
                                <option value="Kursus">Kursus</option>
                                <option value="Penataran">Penataran</option>
                            </select>
                            @if ($errors->has('tipe'))
                            <div class="invalid-feedback">{{ $errors->first('tipe') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="kurikulum">Kurikulum <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2 form-control{{ $errors->has('kurikulum') ? ' is-invalid' : '' }}" id="kurikulum" name="kurikulum" style="width: 100%" required disabled>
                                <option value="" selected>-- Pilih Kurikulum --</option>
                            </select>
                            @if ($errors->has('kurikulum'))
                            <div class="invalid-feedback">{{ $errors->first('kurikulum') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="lokasi">Lokasi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('lokasi') ? ' is-invalid' : '' }}" id="lokasi" name="lokasi" style="width: 100%" required>
                                <option value="" selected>-- Pilih Lokasi --</option>
                                @foreach ($lokasi as $lok)
                                <option value="{{ $lok->id }}" {{ old('lokasi') == $lok->id ? "selected":"" }}>{{ $lok->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('lokasi'))
                            <div class="invalid-feedback">{{ $errors->first('lokasi') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="kelas">Kelas <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('kelas') ? ' is-invalid' : '' }}" id="kelas" name="kelas" style="width: 100%" required>
                                <option value="" selected>-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                <option value="{{ $k->nama }}" {{ old('kelas') == $k->nama ? "selected":"" }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('kelas'))
                            <div class="invalid-feedback">{{ $errors->first('kelas') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="kuota">Kuota <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control{{ $errors->has('kuota') ? ' is-invalid' : '' }}" id="kuota" name="kuota" placeholder="Kuota..." value="{{ old('kuota') }}" required>
                            @if ($errors->has('kuota'))
                            <div class="invalid-feedback">{{ $errors->first('kuota') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="j_diklat">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="yyyy-mm-dd" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="form-control" id="tgl_awal" name="tgl_awal" placeholder="Awal" data-week-start="1" data-autoclose="true" data-today-highlight="true" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="tgl_akhir" name="tgl_akhir" placeholder="Akhir" data-week-start="1" data-autoclose="true" data-today-highlight="true" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="registrasi">Registrasi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('registrasi') ? ' is-invalid' : '' }}" id="registrasi" name="registrasi" style="width: 100%" required>
                                <option value="" selected>-- Pilih Registrasi --</option>
                                <option value="0">Internal</option>
                                <option value="1">Online</option>
                            </select>
                            @if ($errors->has('registrasi'))
                            <div class="invalid-feedback">{{ $errors->first('registrasi') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="status">Registrasi Lengkap <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('registrasi_lengkap') ? ' is-invalid' : '' }}" id="registrasi_lengkap" name="registrasi_lengkap" style="width: 100%" required>
                                <option value="" selected>-- Pilih Registrasi Lengkap --</option>
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                            @if ($errors->has('status'))
                            <div class="invalid-feedback">{{ $errors->first('registrasi_lengkap') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="reg_awal">Tanggal Registrasi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="yyyy-mm-dd" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="form-control" id="reg_awal" name="reg_awal" placeholder="Awal" data-week-start="1" data-autoclose="true" data-today-highlight="true" disabled>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="reg_akhir" name="reg_akhir" placeholder="Akhir" data-week-start="1" data-autoclose="true" data-today-highlight="true" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="panitia_nama">Nama Panitia <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('panitia_nama') ? ' is-invalid' : '' }}" id="panitia_nama" name="panitia_nama" placeholder="Nama Panitia..." value="{{ old('panitia_nama') }}" required>
                            @if ($errors->has('panitia_nama'))
                            <div class="invalid-feedback">{{ $errors->first('panitia_nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="panitia_telp">Telepon Panitia <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('panitia_telp') ? ' is-invalid' : '' }}" id="panitia_telp" name="panitia_telp" placeholder="Telepon Panitia..." value="{{ old('panitia_telp') }}" required>
                            @if ($errors->has('panitia_telp'))
                            <div class="invalid-feedback">{{ $errors->first('panitia_telp') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="panitia_email">Email Panitia <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control{{ $errors->has('panitia_email') ? ' is-invalid' : '' }}" id="panitia_email" name="panitia_email" placeholder="Email Panitia..." value="{{ old('panitia_email') }}" required>
                            @if ($errors->has('panitia_email'))
                            <div class="invalid-feedback">{{ $errors->first('panitia_email') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="var_1">Link Group</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('var_1') ? ' is-invalid' : '' }}" id="var_1" name="var_1" placeholder="Link Group (Telegram/WhatsApp)" value="{{ old('var_1') }}">
                            @if ($errors->has('var_1'))
                            <div class="invalid-feedback">{{ $errors->first('var_1') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="deskripsi">Deskripsi</label>
                        <div class="col-sm-9">
                            <textarea id="deskripsi" name="deskripsi"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="syarat">Syarat</label>
                        <div class="col-sm-9">
                            <textarea id="syarat" name="syarat"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="lampiran">Lampiran</label>
                        <div class="col-sm-9">
                            <input id="lampiran" name="lampiran" type="file">
                            <small class="form-text text-muted">Max file dengan ukuran <= 2 MB </small>
                            @if ($errors->has('foto'))
                            <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="status">Status <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="js-select2-min form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" id="status" name="status" style="width: 100%" required>
                                <option value="" selected>-- Pilih Status --</option>
                                <option value="0">Dibatalkan</option>
                                <option value="1">Dilaksanakan</option>
                            </select>
                            @if ($errors->has('status'))
                            <div class="invalid-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                                Kembali</a>
                            <input type="submit" name="more" value="Simpan &amp; Tambah Lagi" class="btn btn-sm btn-success">
                            <input type="submit" name="add" value="Simpan" class="btn btn-sm btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
