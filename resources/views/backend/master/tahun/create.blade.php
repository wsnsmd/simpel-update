@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->  
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <script>
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
    @include('backend.master.tahun.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Tahun - Tambah Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.master.tahun.store') }}" method="POST" autocomplete="off">
                    @csrf                                       
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="tahun">Tahun <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control{{ $errors->has('id') ? ' is-invalid' : '' }}" id="tahun" name="tahun" placeholder="Tahun..." value="{{ old('tahun') }}" required>
                            @if ($errors->has('tahun'))
                            <div class="invalid-feedback">{{ $errors->first('tahun') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="aktif">Aktif <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-control" id="aktif" name="aktif" style="width: 100%;" data-placeholder="-- Pilih aktif --" required>
                                <option value="">-- Pilih Aktif --</option>
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                            @if ($errors->has('aktif'))
                            <div class="invalid-feedback">{{ $errors->first('aktif') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-2 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-10">
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
